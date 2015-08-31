<?php
    include ("common.php");
    include ("debug_tool.php");
    include_once('const.php');

    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    $start = ($start) ? intval($start) : 1;
    $fmReplace = (isSet($fmReplace)) ? $fmReplace : 1;
    switch ($action)
    {
        case "fmAddLineNo":
            $value = cancel_line($str);
            if ($fmReplace) $value = replace_rule($value);
            if ($fmSort)
            {
                $distinct = ($fmClear) ? 0 : 1;
                $value = sort_path($value, $distinct);
            }
            $value = rtrim(create_line($value));
            break;
        case "sign_match":
            $value = parser::parse($str);
            break;
        case "stripslashes":
            $value = stripslashes($str);
            break;
        case "addslashes":
            $value = addslashes($str);
            break;
        case "strip_tags":
            $value = strip_tags($str);
            break;
        case "urlencode":
            $value = urlencode($str);
            break;
        case "urldecode":
            $value = urldecode($str);
            break;
        case "stripline":
            $value = ereg_replace("\n", " ", $str);
            $value = ereg_replace("\r", "", $value);
            break;
        case "json_encode":
            $value = addslashes(json_encode($str));
            break;
        case "json_decode":
            include_once ("lib/firephp_helper.php");
            $json_obj = json_decode($str);
            fb_log($json_obj);
            $value = print_r($json_obj, true);
            break;
        case "json_to_php_code":
            include_once ("lib/firephp_helper.php");
            $json_obj = json_decode($str);
            fb_log($json_obj);
            $value = array_to_code($json_obj);
            break;
        case "md5":
            $value = md5($str);
            break;
        case "sha1":
            $value = sha1($str);
            break;
        case "base64_encode":
            $value = base64_encode($str);
            break;
        case "base64_decode":
            $value = base64_decode($str);
            break;
        case "rawurlencode":
            $value = rawurlencode($str);
            break;
        case "rawurldecode":
            $value = rawurldecode($str);
            break;
        case "htmlspecialchars":
            $value = htmlspecialchars($str, ENT_QUOTES);
            break;
        case "htmlspecialchars_decode":
            $value = htmlspecialchars_decode($str);
            break;
        case "unique":
            $value = get_unique($str);
            break;
        case "unserialize":
            $value = unserialize($str);
            $value = print_r($value, true);
            break;
        case "toSlash":
            $value = str_replace("\\", "/", $str);
            break;
        case "toBackslash":
            $value = str_replace("/", "\\", $str);
            break;
        case "strip_nl":
            $value = str_replace("\n", " ", $str);
            $value = str_replace("\r", " ", stripslashes($value));
            break;
        case "php_octal":
            $value = addslashes(php_base_str($str, 8));
            break;
        case "php_hex":
            $value = addslashes(php_base_str($str, 16));
            break;
        case "ascii":
            $len = strlen($str);
            for ($i=0; $i<$len; $i++)
            {
                $c = $str[$i];
                $value .= "'$c' => " . ord($c) . "\n";
            }
            $value = addslashes($value);
            break;
        case "open_browser":
            $value = open_browser($str);
            break;
        case "curl_test":
            $value = curl_test($str);
            break;
    }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>文字處理器</title>
<style>@import URL("style.css");</style>
<script src='<?= BASE_DIR ?>/dom.js'></script>
<script>
    function btnSubmit(ctrl)
    {
        $("action").value = ctrl.id;
        $("form1").submit();
    }
    function result_to_input()
    {
        $("str").value = $("newStr").value;
    }

    function getKey(e)
    {
        var e = (!e) ? window.event : e;
    	var key = e.keyCode;
    	switch(key)
		{
			// ctrl + enter
			case 13:
                if (e.ctrlKey)
                {
					var ctrl = $('fmAddLineNo');
                    btnSubmit(ctrl);
                    e.returnValue = false;
                    e.cancelBubble = true;
				    return true;
                }
                break;
            // ESC
			// case 27:
                // note_cancel(id);
                // e.returnValue = false;
                // e.cancelBubble = true;
                // return true;
                // break;
			default:
				return true;
		}
    }
</script>
<style>
    textarea {font-size:14px; font-family: "Yahei Consolas Hybrid"; width:100%; height:200px}
    input.button {font-size:15px; font-family: "Yahei Consolas Hybrid"; border: 1px solid #aaa; margin:2px;}
    input.group1 {background: #88F;}
    input.group2 {background: #8f8;}
    input.group3 {background: #f88;}
    input.group4 {background: #CAC;}
    input.group5 {background: #CCA;}
    input.group6 {background: #ACC;}
</style>
</head>
<body>
<div style='margin:8px'>
<pre>
==  文字處理器  ==
</pre>
<form id=form1 action='text_editor.php' method=POST>
    <input id=action name=action type=hidden>
    <!-- 開始行號: <input type=text name=start value='<?php echo $start ?>'> -->
    <textarea id=str name=str style='width:100%; height:200px' onfocus='this.select()' onkeypress='getKey(event)'><?php echo $str ?></textarea>
    <!-- <input type=checkbox id='fmSort' name='fmSort' value='1' <?php echo ($fmSort) ? "checked" : ""?>>排序 -->
    <!-- <input type=checkbox id='fmReplace' name='fmReplace' value='1' <?php echo ($fmReplace) ? "checked" : ""?>>拿掉絕對路徑 -->
    <!-- <input type=checkbox id='fmClear' name='fmClear' value='1' <?php echo ($fmClear) ? "checked" : ""?>>清空說明 -->
    <!-- <input type=checkbox id='fmEngWord' name='fmEngWord' value='1' <?php echo ($fmEngWord) ? "checked" : ""?>>英文序號 -->
    <br>
    <!-- <input class='button'        type='button' id='fmAddLineNo' name='fmAddLineNo' value='產生行號' onclick='btnSubmit(this)'> -->
    <input class='button'        type='button' id='strip_tags' name='strip_tags' value='strip_tags' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='stripline' name='stripline' value='去換行符號' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='strip_nl' name='strip_nl' value='換行轉空白' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='sign_match' name='sign_match' value='括號匹配' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='md5' name='md5' value='md5' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='sha1' name='sha1' value='sha1' onclick='btnSubmit(this)'>
    <input class='button group1' type='button' id='stripslashes' name='stripslashes' value='stripslashes' onclick='btnSubmit(this)'>
    <input class='button group1' type='button' id='addslashes' name='addslashes' value='addslashes' onclick='btnSubmit(this)'>
    <input class='button group2' type='button' id='urlencode' name='urlencode' value='urlencode' onclick='btnSubmit(this)'>
    <input class='button group2' type='button' id='urldecode' name='urldecode' value='urldecode' onclick='btnSubmit(this)'>
    <input class='button group3' type='button' id='json_encode' name='json_encode' value='json_encode' onclick='btnSubmit(this)'>
    <input class='button group3' type='button' id='json_decode' name='json_decode' value='json_decode' onclick='btnSubmit(this)'>
    <input class='button group3' type='button' id='json_to_php_code' name='json_to_php_code' value='json_to_php_code' onclick='btnSubmit(this)'>
    <input class='button group4' type='button' id='base64_encode' name='base64_encode' value='base64_encode' onclick='btnSubmit(this)'>
    <input class='button group4' type='button' id='base64_decode' name='base64_decode' value='base64_decode' onclick='btnSubmit(this)'>
    <input class='button group5' type='button' id='rawurlencode' name='rawurlencode' value='rawurlencode' onclick='btnSubmit(this)'>
    <input class='button group5' type='button' id='rawurldecode' name='rawurldecode' value='rawurldecode' onclick='btnSubmit(this)'>
    <input class='button group6' type='button' id='htmlspecialchars'  name='htmlspecialchars'  value='htmlspecialchars'     onclick='btnSubmit(this)'>
    <input class='button group6' type='button' id='htmlspecialchars_decode' name='htmlspecialchars_decode' value='htmlspecialchars_decode' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='unique' name='unique' value='取唯一值' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='unserialize' name='unserialize' value='unserialize' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='toSlash' name='toSlash' value='-> /' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='toBackslash' name='toBackslash' value='-> \' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='php_octal' name='php_octal' value='PHP八進位字串' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='php_hex' name='php_hex'     value='PHP十六進位字串' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='ascii' name='ascii'     value='ASCII' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='open_browser' name='open_browser'     value='開啟網址' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='curl_test' name='curl_test'     value=' CURL 測試網址' onclick='btnSubmit(this)'>
    <input class='button'        type='button' id='fmCopy' name='fmCopy' value='   ↑   ' onclick='result_to_input()'>
    <textarea id=newStr name=newStr style='width:100%; height:350px' onfocus='this.select()'><? if ($str) echo htmlspecialchars($value, ENT_QUOTES); ?></textarea>
</form>
</div>
</body>
</html>
<?php
    function replace_rule($str)
    {
        $str = ereg_replace('[A-Z]{1}:[\\]{2}TMS[\\]{2}[a-z_0-9-]+[\\]{2}', "", $str);
        $str = ereg_replace('[\\]{2}', '/', $str);
        return $str;
    }

    function sort_path($str, $distinct=1)
    {
        $strs = explode("\n", $str);

        $str2 = array(); $count = -1;
        for ($i=0; $i<count($strs)-1; $i++)
        {
            if (ereg("^[ ]*[a-z]", $strs[$i]))
            {
                $count++;
                $str2[$count][key] = trim($strs[$i]);
            }
            else
            {
                $str2[$count][str] .= $strs[$i] . "\n";
            }
        }

        usort($str2, sort_path_cmp);

        $str = ""; $pKey = "";
        for ($i=0; $i<count($str2); $i++)
        {
            if (!$distinct || $pKey != $str2[$i][key])
                $str .= "    " . $str2[$i][key] . "\n" . $str2[$i][str];
            else
                $str .= $str2[$i][str];

            $pKey = $str2[$i][key];
        }

        return $str;
    }
    function sort_path_cmp($a, $b)
    {
        $a_path = str_path($a[key]);
        $b_path = str_path($b[key]);
        if ($a_path == $b_path)
            if ($a[key] == $b[key])
                return strcasecmp($b[str], $a[str]);
            else
                return strcasecmp($a[key], $b[key]);
        else
            return strcasecmp($a_path, $b_path);
    }
    function str_path($str)
    {
        return ereg_replace("[a-zA-z0-9_.-]+$", "", $str);
    }
    function path_depth($path)
    {
        $depth  = count(explode("/", $path));
        $depth2 = count(explode("\\", $path));
        return ($depth > $depth2) ? $depth -1 : $depth2 -1;
    }

    function create_line($str)
    {
        global $start, $fmEngWord;

        $arr = explode("\n", $str);
        $line = $start;
        $str2 = "";
        for($i=0; $i<count($arr); $i++)
        {
            if (eregi("^[ ]*[0-9a-z_/.-]+$", trim($arr[$i])))
            {
                $line_word = ($fmEngWord) ? chr(ord('a') + $line - 1) : $line;
                $arr[$i] = ereg_replace("(^[ ]*)", "    $line_word. ", $arr[$i]);
                $line++;
            }

            $str2 .= "{$arr[$i]}\n";
        }
        return $str2;
    }

    function create_line2($str)
    {
        global $start, $fmEngWord;

        $arr = explode("\n", $str);
        $line = $start;
        $str2 = "";
        for($i=0; $i<count($arr); $i++)
        {
            $line_word = ($fmEngWord) ? chr(ord('a') + $line - 1) : $line;
            $arr[$i] = ereg_replace("(^[ ]*)", "\\1$line_word. ", $arr[$i]);
            $line++;

            $str2 .= "{$arr[$i]}\n";
        }
        return $str2;
    }

    function cancel_line($str)
    {
        global $start;

        $arr = explode("\n", $str);
        $line = $start;
        $str2 = "";
        for($i=0; $i<count($arr); $i++)
        {
            $str2 .= ereg_replace("(^[ \t]*)[0-9a-z]+\. ", "\\1", $arr[$i]) . "\n";
        }
        return $str2;
    }

    if ( !function_exists('htmlspecialchars_decode') )
    {
        function htmlspecialchars_decode($string,$style=ENT_COMPAT)
        {
            $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
            if($style === ENT_QUOTES){ $translation['&#039;'] = '\''; }
            return strtr($string,$translation);
        }
    }

    function get_unique($str)
    {
        $arr = explode("\n", $str);
        $result = array_unique($arr);
        return implode("\n", $result);
    }

    function php_base_str($str, $base=8)
    {
        $result = "";
        $len = strlen($str);
        for ($i=0; $i<$len; $i++)
        {
            $char = $str[$i];
            $asc = ord($char);
            $tmp = "";
            while ($asc > 0)
            {
                $d = $asc % $base;
                if ($d >= 10) $d = chr($d + 55);
                $tmp = $d . $tmp;
                $asc = intval($asc/$base);
            }

            if ($base == 8)
                $result .= "\\" . $tmp;
            else
                $result .= '\x' . $tmp;
        }
        return $result;
    }

    /**
     * 將陣列轉為 php 程式碼的格式，方便程式內測試
     */
    function array_to_code($obj='', $depth=0)
    {
        $space_out = str_repeat(' ', $depth * 4);
        $space_in = str_repeat(' ', ($depth+1) * 4);

        if (is_array($obj) || is_object($obj))
        {
            $array_body = array();
            foreach ($obj as $key => $value)
            {
                $array_body[] = "{$space_in}\"{$key}\" => " . array_to_code($value, $depth+1);
            }
            $array_body = implode(",\n", $array_body);

            $code = "";
            if (is_object($obj)) $code = "(object) ";
            $code .= "array\n";
            $code .= "{$space_out}(\n";
            $code .= "{$array_body}\n";
            $code .= "{$space_out})";

            return $code;
        }

        switch (gettype($obj))
        {
            case 'string':
                return '"' . $obj . '"';

            case 'boolean':
                return ($obj) ? 'TRUE' : 'FALSE';

            default:
                return $obj;
        }
    }

    /**
     * 開啟瀏灠器
     * @param  string $str 網址(一行一個)
     * @return void
     */
    function open_browser($str='')
    {
        $tmp = explode("\n", $str);
        $html = "<script>";
        foreach ($tmp as $url)
        {
            $url = trim($url);
            $html .= "window.open('{$url}');\n";
        }
        $html .= "alert('已開啟網址!!!')";
        $html .= "</script>";

        echo $html;
    }

    /**
     * 括號匹配
     * @author Leo.Kuo
     */
    class parser
    {
        public static function parse($str)
        {
            $output = "";
            $flag = 0;
            $depth = 0;
            $depth = 0;
            while ($str)
            {
                $str = trim($str);
                $pos_left  = strpos($str, '(');
                $pos_right = strpos($str, ')');

                if ($pos_left === FALSE && $pos_right === FALSE)
                {
                    $output .= $str;
                    break;
                }

                $flag = ($pos_left > $pos_right) ? '-' : '+';
                if ($flag == '+' && $pos_left !== FALSE)
                {
                    $space_out = str_repeat(" ", $depth * 4);
                    $space_in = str_repeat(" ", ($depth+1) * 4);
                    $content = trim(substr($str, 0, $pos_left));
                    if ($content) $output .= "{$space_out}$content\n";
                    $output .= "{$space_out}(\n";
                    $str = substr($str, $pos_left+1);
                    $depth++;
                }
                else
                {
                    $depth--;
                    $space_out = str_repeat(" ", $depth * 4);
                    $space_in = str_repeat(" ", ($depth+1) * 4);
                    $content = trim(substr($str, 0, $pos_right));

                    if ($content) $output .= "{$space_in}$content\n";
                    $output .= "{$space_out})\n";
                    $str = substr($str, $pos_right+1);
                }
            }
            return $output;
        }
    }

    /**
     * 開啟瀏灠器
     * @param  string $str 網址(一行一個)
     * @return void
     */
    function curl_test($str='')
    {
        $tmp = explode("\n", $str);

        foreach ($tmp as $url)
        {
            $url = trim($url);
            $response_code = curl_info($url);
            $msg = ($response_code == 200) ?  'ok' : "HTTP ERROR: " . $response_code;
            $value .= "{$url} ..................{$msg}\n";
        }
        return $value;
    }

    /**
     * 回傳 HTTP RESPONSE CODE
     * @param  string $url 網址
     * @return integer     HTTP RESPONSE CODE
     */
    function curl_info($url)
    {
        $timeout = 10;
        $curl = curl_init($url);
        if (substr($url, 0, 5) == "https")
        {
            @ curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
            @ curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            @ curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        else
        {
            @ curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP);
        }
        @ curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        @ curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        @ curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        $data = curl_exec($curl);

        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        return $response_code;
    }

/* End of file text_editor.php */
/* Location: text_editor.php */