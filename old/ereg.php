<?
    // include ('globals_on.php');

    $arg1 = ereg_replace("\\\\", "\\", stripslashes($arg1));
    $arg2 = ereg_replace("\\\\", "\\", stripslashes($arg2));
    
    echo "$arg1  === $arg2 <br>";
    $str = stripslashes($str);

    if ($fmSubmit && $lang == "php")
    {
        switch ($fmFunc)
        {
            case "ereg_replace":
                $result = eregi_replace($arg1, $arg2, $str);
                break;
            case "ereg":
                $result = (eregi($arg1, $str)) ? "True" : "False";
                break;
        }
    }
    $lang = ($lang) ? $lang : "php";
    $checked[$lang] = "checked";
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>正規表示式測試器</title>
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
                case "ereg_replace":
                    $result = ereg_replace($arg1, $arg2, $str);
                    break;
                case "ereg":
                    $result = (ereg($arg1, $str)) ? "True" : "False";
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
<form id=form1 action='ereg.php' method=POST>
    <input id=action name=action type=hidden>
    <?
        $func = array("ereg", "ereg_replace");
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
    來源: <input class=Text type=text style='width:300px' name=arg1 value='<?= htmlspecialchars($arg1, ENT_QUOTES) ?>'><br>
    目的: <input class=Text type=text style='width:300px' name=arg2 value='<?= htmlspecialchars($arg2, ENT_QUOTES) ?>'>
    <textarea id=str name=str style='width:100%; height:200px' onfocus='this.select()'><?= $str ?></textarea>
    <input type=radio id=lang_php name=lang value='php' <?= $checked[php] ?>>PHP
    <input type=radio id=lang_js  name=lang value='js'  <?= $checked[js] ?>>JavaScript
    <input type=submit id=fmSubmit name=fmSubmit value='確定'>
    <textarea id=newStr name=newStr style='width:100%; height:350px' onfocus='this.select()'><?= $result ?></textarea>
</form>
</body>
</html>