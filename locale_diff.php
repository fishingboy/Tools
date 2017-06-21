<?php
    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    function get_php_var($file)
    {
        if (file_exists($file))
        {
            include ($file);
            unset($file);
            $arr = get_defined_vars();
        }
        return ($arr) ? $arr : array();
    }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>字詞檔比對</title>
<style>@import URL("style.css");</style>
<script src='/dom.js'></script>
<script>
function changeDoamin()
{
    window.location.href = "locale_diff.php?domain=" + $V('fmDomain');
}
</script>
</head>
<body>
<div style='margin:0 auto; width:1000px; text-align:center'>
<div style='font-weight:bold; font-size:20px; margin-bottom:10px; text-align:center'>====  字詞檔比對  ====</div>
<?php
    $domain_path = ($_GET['domain']) ? $_GET['domain'] : "www_lms";
    unset($selected);
    $selected[$domain_path] = "selected";
    echo "<select id=fmDomain name=fmDomain onchange='changeDoamin()'>
            <option value='www_lms'  {$selected[www_lms]}>lms</option>
            <option value='www_lms2' {$selected[www_lms2]}>lms2</option>
          </select>";

    $arr_left  = get_php_var("D:/TMS/$domain_path/sys/res/lang/zh-tw/locale.php");
    $arr_right = get_php_var("D:/TMS/$domain_path/sys/res/lang/en-us/locale.php");
    $arr_tmp = $arr_right;

    $arr_total = array_merge($arr_left, $arr_right);
    $total_count = count($arr_total);

    $html = $rows = "";
    $left_count = 0;
    foreach ($arr_left as $key => $value)
    {
        if (!isset($arr_right[$key]))
        {
            $left_count++;
            $rows .=
                 "<tr>
                    <td style='background:#ddf'>$key</td>
                    <td style='background:#eee'>$value</td>
                  </tr>";
            unset($arr_left[$key]);
        }
        else
        {
            unset($arr_tmp[$key]);
        }
    }
    $html .=
         "<table id=table_left style='width:100%; margin-bottom:30px;'>
            <tr style='background:#cfc'><th colspan=2>左邊獨有($left_count)</th></tr>
            $rows
          </table>";


    $rows = "";
    $right_count = 0;
    foreach ($arr_tmp as $key => $value)
    {
            $right_count++;
            $rows .=
             "<tr>
                <td style='background:#ddf'>$key</td>
                <td style='background:#eee'>$value</td>
              </tr>";
        unset($arr_right[$key]);
    }
    $html .=
         "<table id=table_right style='width:100%; margin-bottom:30px;'>
            <tr style='background:#cfc'><th colspan=2>右邊獨有($right_count)</th></tr>
            $rows
          </table>";



    $rows = $rows2 = "";
    $unfinish_count = $finish_count = 0;
    foreach ($arr_left as $key => $value)
    {
        if ($arr_left[$key] == $arr_right[$key])
        {
            $unfinish_count++;
            $rows .=
                 "<tr>
                    <td style='background:#ddf'>$key</td>
                    <td style='background:#eee'>$value</td>
                  </tr>";
        }
        else
        {
            $finish_count++;
            $rows2 .=
                 "<tr>
                    <td style='background:#ddf'>$key</td>
                    <td style='background:#eee'>
                        $value<br>
                        {$arr_right[$key]}
                    </td>
                  </tr>";
        }
    }


    $html .=
         "<table id=table_unfinish style='width:100%; margin-bottom:30px;'>
            <tr style='background:#cfc'><th colspan=2>尚未翻譯($unfinish_count)</th></tr>
            $rows
          </table>";


    $html .=
         "<table id=table_finish style='width:100%; margin-bottom:30px;'>
            <tr style='background:#cfc'><th colspan=2>已完成翻譯($finish_count)</th></tr>
            $rows2
          </table>";


    /* 顯示 */
    echo "<table style='width:100%; margin-bottom:30px;'>
            <tr style='background:#cfc'><th colspan=2>統計($total_count)</th></tr>
            <tr>
                <td style='background:#ddf'><a href='#table_left'>左邊獨有</a></td>
                <td style='background:#eee'>$left_count</td>
            </tr>
            <tr>
                <td style='background:#ddf'><a href='#table_right'>右邊獨有</a></td>
                <td style='background:#eee'>$right_count</td>
            </tr>
            <tr>
                <td style='background:#ddf'><a href='#table_unfinish'>未翻譯</a></td>
                <td style='background:#eee'>$unfinish_count</td>
            </tr>
            <tr>
                <td style='background:#ddf'><a href='#table_finish'>已翻譯</a></td>
                <td style='background:#eee'>$finish_count</td>
            </tr>
          </table>";
    echo $html;
?>
</div>
</body>
</html>