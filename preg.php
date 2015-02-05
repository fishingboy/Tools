<?
    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    function create_preg_replace_code($arg1, $arg2, $str)
    {
        $str  = addslashes($str);
        $arg1 = str_replace("\\", "\\\\", $arg1);
        $code = <<<HTML
<?
    \$str = "$str";
    echo "src =  " . htmlspecialchars(\$str, ENT_QUOTES) . "<br>";
    \$str = preg_replace("$arg1", "$arg2", \$str);
    echo "dest = " . htmlspecialchars(\$str, ENT_QUOTES) . "<br>";
?>
HTML;
        return $code;
    }

    function create_preg_match_code($arg1, $str)
    {
        $str  = addslashes($str);
        $arg1 = str_replace("\\", "\\\\", $arg1);
        $code = <<<HTML
<?
    \$str = "$str";
    echo "src =  " . htmlspecialchars(\$str, ENT_QUOTES) . "<br>";
    \$result = (preg_match("$arg1", \$str)) ? "True" : "False";
    echo "\$result<br>";
?>
HTML;
        return $code;
    }

    $arg1 = stripslashes($arg1);
    $arg2 = stripslashes($arg2);
    $str = stripslashes($str);

    if ($fmSubmit && $lang == "php")
    {
        switch ($fmFunc)
        {
            case "preg_replace":
                $result = preg_replace($arg1, $arg2, $str);
                $code = create_preg_replace_code($arg1, $arg2, $str);
                break;
            case "preg_match":
                $result = (preg_match($arg1, $str)) ? "True" : "False";
                $code = create_preg_match_code($arg1, $str);
                break;
        }
    }
    $lang = ($lang) ? $lang : "php";
    $checked[$lang] = "checked";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>正規表示式測試器(preg)</title>
<style>@import URL("style.css");</style>
<script src='/dom.js'></script>
<script>
    function result_to_input()
    {
        $("str").value = $("newStr").value;
    }
    <?
        if ($fmSubmit && $lang == "js")
        {
            switch ($fmFunc)
            {
                case "preg_replace":
                    $result = preg_replace($arg1, $arg2, $str);
                    break;
                case "preg_match":
                    $result = (preg_match($arg1, $str)) ? "True" : "False";
                    break;
            }
        }
    ?>
</script>
</head>
<body>
<pre>
==  正規表示式測試器  ==
 .  任意 1 個
 *  0 個以上
 +  1 個以上
 ?  0 或 1 個
 ^  開頭
 $  結尾
</pre>
<form id=form1 action='preg.php' method=POST>
    <input id=action name=action type=hidden>
    <?
        $func = array("preg_match", "preg_replace");
        foreach ($func as $value)
        {
            $selected = ($fmFunc == $value) ? "selected" : "";
            $opt .= "<option value='$value' $selected>$value</option>";
        }
        $select = "<select id=fmFunc name=fmFunc>
                      $opt
                   </select>";

    ?>
    function: <?= $select ?><br>
    pattern: <input class=Text type=text style='width:800px' name=arg1 value='<?= htmlspecialchars($arg1, ENT_QUOTES) ?>'><br>
    取代成: <input class=Text type=text style='width:800px' name=arg2 value='<?= htmlspecialchars($arg2, ENT_QUOTES) ?>'><br>
    <input type=radio id=lang_php name=lang value='php' <?= $checked[php] ?>>PHP
    <input type=radio id=lang_js  name=lang value='js'  <?= $checked[js] ?>>JavaScript
    <input type=submit id=fmSubmit name=fmSubmit value='確定'><br><br>
    來源字串:<br>
    <textarea id=str name=str style='width:100%; height:150px' onfocus='this.select()'><?= htmlspecialchars($str, ENT_QUOTES) ?></textarea>
    目的字串:<br>
    <textarea id=newStr name=newStr style='width:100%; height:150px' onfocus='this.select()'><?= $result ?></textarea><br><br>
    PHP程式碼:<br>
    <textarea style='width:100%; height:135px;' name=code onfocus='this.select()'><?= htmlspecialchars($code, ENT_QUOTES) ?></textarea><br>
</form>
</body>
</html>