<?php
    include_once "const.php";

    class Url_editor
    {
        public static function parse($url)
        {
            $tmp = explode('?', $url);
            $ret = array();

            // 取出網址
            $ret['host'] = $tmp[0];

            // 取出參數
            if (isset($tmp[1]))
            {
                $gets = explode('&', $tmp[1]);
                foreach ($gets as $param_str)
                {
                    $tmp2 = explode('=', $param_str);
                    $ret['get'][] = array
                    (
                        'key'   => $tmp2[0],
                        'value' => urldecode($tmp2[1])
                    );
                }
            }
            return $ret;
        }

        public static function make($param)
        {
            $url = $param['host'];
            if (isset($param['get']))
            {
                $get_args = array();
                foreach ((array) $param['get'] as $param_obj)
                {
                    $get_args[] = "{$param_obj['key']}=" . urlencode($param_obj['value']);
                }
                $url .= '?' . implode('&', $get_args);
            }
            return $url;
        }

        public function make_form($param)
        {
            $host = htmlspecialchars($param['host'], ENT_QUOTES);
            $html = "<div><textarea id='fm_host' name='fm_host' type='text'>$host</textarea></div>";
            if (isset($param['get']))
            {
                $i = 0;
                foreach ((array) $param['get'] as $param_obj)
                {
                    $i++;
                    $key   = htmlspecialchars($param_obj['key'], ENT_QUOTES);
                    $value = htmlspecialchars(urldecode($param_obj['value']), ENT_QUOTES);
                    $html .= "<div id='param_{$i}' class='param'>
                                <input type='button' value='-' onclick='\$page.remove_param($i)'>
                                <textarea id='fm_key_{$i}' name='fm_key[$i]' class='key' type='text'>$key</textarea>
                                =>
                                <textarea id='fm_value_{$i}' name='fm_value[$i]' type='text' class='value'>$value</textarea>
                              </div>";
                }
            }
            return $html;
        }
    }

    if ($_POST['fm_action'] == 'build')
    {
        $url_object = array();
        $url_object['host'] = $_POST['fm_host'];
        $count = count($_POST['fm_key']);
        // for ($i=1; $i<=$count; $i++)
        foreach ($_POST['fm_key'] as $i => $v)
        {
            $url_object['get'][] = array('key' => $_POST['fm_key'][$i], 'value' => $_POST['fm_value'][$i]);
        }
        $url = Url_editor::make($url_object);
    }
    else
    {
        // $url = ($_POST['fm_url']) ? $_POST['fm_url'] : "http://a-zh-tw-www-show-main.idc1.ux:80/solr/a-zh-tw-www-show-main/www?wt=json&sort=%24IS_SALEQTY+desc%2C%24UITOX_PRICE+asc&rows=30&q=%28ISLIFEEXPIRED%3A0++AND+SM_STATUS%3A1++AND+%28IS_ORGI_ITEM%3A0++OR++IS_ORGI_ITEM%3A1++%29+AND+%28%28%28SM_SEQ%3A%28201410AM150000050+OR+201409AM180000190+OR+201409AM240000075+OR+201409AM100000006+OR+201409AM260000161+OR+201404AM210000036+OR+201409AM040000005+OR+201409AM040000008+OR+201409AM040000007+OR+201409AM090000221+OR+201409AM090000220+OR+201409AM090000219+OR+201407AM070000517+OR+201407AM070000516+OR+201406AM270000276+OR+201407AM070000039+OR+201407AM070000038+OR+201407AM070000037+OR+201404AM160000882+OR+201404AM160000881+OR+201404AM180000085+OR+201404AM180000084+OR+201406AM050000445+OR+201406AM050000444+OR+201406AM040000714+OR+201406AM030000458+OR+201405AM270000148+OR+201406AM300000439+OR+201404AM170000335+OR+201404AM170000334+OR+201404AM140000588+OR+201404AM140000587+OR+201408AM280000715%29%29+%29+AND+WS_SEQ%3AAW000013++%29+%29+-%28SM_SEQ%3A201404AM210000051+%29&facet=true&facet.mincount=1&facet.limit=-1&facet.field=CP_SEQ&facet.field=NEED_AV_SEQ&facet.field=NEED_AT_SEQ&group=true&group.field=NEW_SPEC_SEQ&group.sort=SALEQTY+desc&group.facet=true&group.ngroups=true&fl=SM_SEQ%2CSM_TITLE%2CSM_NAME%2CCOLOR%2CIT_SIZE%2CSM_PRICE%2CSM_PIC%2CIS_ORGI_ITEM%2CSM_PIC_SIZE&stats=true&stats.field=SM_PRICE&indent=true";
        $url = ($_POST['fm_url']) ? $_POST['fm_url'] : "";
    }

    $url_object = Url_editor::parse($url);
?>
<html>
<head>
<base href="<?php echo BASE_URL ?>/">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>網址編輯器</title>
<script type="text/javascript" src='sys/lib/js/jquery.js'></script>
<script type="text/javascript" src='sys/lib/js/jquery-ui.js'></script>
<script type="text/javascript" src='sys/lib/js/jquery.tmpl.js'></script>
<script type="text/javascript" src='sys/lib/js/jquery.tmpl.html.js'></script>
<style type="text/css">
#fm_url      {width:100%; height:180px}
#fm_host     {width:98%; height:25px;}
#fm_url_make {width:200px; background: #EFE;}
#fm_url_link {width:200px; background: #EFE;}
.param       {padding:2px;}
.key         {width:160px; height:25px;}
.value       {width:85%; height:25px;}
input[type='button'] {background: #EFE;}
</style>
<script id="tmpl_get_param" type="text/x-jquery-tmpl">
<div id='param_${seq}' class="param">
    <textarea type="text" class="key" name="fm_key[${seq}]" id="fm_key_${seq}"></textarea>
    =&gt;
    <textarea class="value" type="text" name="fm_value[${seq}]" id="fm_value_${seq}"></textarea>
    <input type='button' value='-' onclick='$page.remove_param(${seq})'>
</div>
</script>
<script type="text/javascript">
var $page =
{
    params_count : 0,

    init : function(param)
    {
        this.params_count = (param.params_count) ? param.params_count : this.params_count;

        $("textarea").bind('focus', function ()
        {
            this.select();
        });
    },

    add_param : function()
    {
        this.params_count++;
        $('#tmpl_get_param').tmpl(
        {
            seq : this.params_count
        }).appendTo('#url_parser');
    },

    remove_param : function(id)
    {
        $('#param_' + id).remove();
    },

    doit : function(action)
    {
        $('#fm_action')[0].value = action;
        $('#form_editor')[0].submit();
    }
}

$(function()
{
    $page.init(
    {
        params_count : <?php echo count($url_object['get'])?>
    });

    <?php
        // 直接連結網頁
        $tabid = (isset($_POST['fm_tabid'])) ? $_POST['fm_tabid'] : md5(time());
        if ($_POST['fm_action'] == 'link')
        {
            echo "window.open('{$url}', '_url_editor_{$tabid}');";
        }
    ?>
});
</script>
</head>
<body>
<pre>
==  網址編輯器  ==
</pre>
<form id='form_editor' method='post'>
    <input id='fm_action' name='fm_action' type='hidden' value=''>
    <input id='fm_tabid' name='fm_tabid' type='hidden' value='<?php echo $tabid ?>'>
    網址:
    <textarea id='fm_url' name='fm_url'><?= $url ?></textarea>
    <input name='fm_url_parse' type='button' value='網址解析' onclick='$page.doit("parse")'>
    <hr>
    <div id='url_parser'><?php echo Url_editor::make_form($url_object); ?></div>
    <div>
        <input id='fm_add_param' type='button' value='+' onclick='$page.add_param()'>
        <input id='fm_url_make' name='fm_url_make' type='button' value='重新產生網址' onclick='$page.doit("build")'>
        <input id='fm_url_link' name='fm_url_link' type='button' value='連結新網址' onclick='$page.doit("link")'>
    </div>
</form>
</body>
</html>