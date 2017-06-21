<?php
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>測試資料產生器</title>
<style>@import URL("style.css");</style>
<script src='/dom.js'></script>
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
    
</script>
</head>
<body>
<pre>
==  測試資料產生器  ==
</pre>
<form id=form1 action='ereg.php' method=POST>
    <input id=action name=action type=hidden>
    來源: <input class=Text type=text style='width:300px' name=arg1 value='<?= $arg1 ?>'><br>
    目的: <input class=Text type=text style='width:300px' name=arg2 value='<?= $arg2 ?>'>
    <textarea id=str name=str style='width:100%; height:200px' onfocus='this.select()'><?= $str ?></textarea>
    <input type=radio id=lang_php name=lang value='php' <?= $checked[php] ?>>PHP
    <input type=radio id=lang_js  name=lang value='js'  <?= $checked[js] ?>>JavaScript
    <input type=button id=fmSubmit name=fmSubmit value='確定' onclick='btnSubmit()'>
    <textarea id=newStr name=newStr style='width:100%; height:350px' onfocus='this.select()'><?= $value ?></textarea>
</form>
</body>
</html>