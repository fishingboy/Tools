<?php
    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    $start = ($fmStart) ? $fmStart : 1;
    $end   = ($fmEnd)   ? $fmEnd   : 100;
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>字串產生器</title>
<style>@import URL("style.css");</style>
</head>
<body>
<pre>
==  字串產生器  ==
  [i]   = 以序號取代
  [rnd] = 以隨機變數取代
</pre>
<form action='create_str.php' method=POST>
    <textarea id=str name=str style='width:100%; height:350px' onfocus='this.select()'><?= stripslashes($str) ?></textarea>
    開始序號：<input type=text id=fmN name=fmStart value='<?= $start ?>'>
    結束序號：<input type=text id=fmN name=fmEnd value='<?= $end ?>'>
    <input type=submit value='產生'>
    <textarea id=newStr name=newStr style='width:100%; height:400px' onfocus='this.select()'><?php if ($str) echo stripslashes(createStr($str, $fmStart, $fmEnd)); ?></textarea>
</form>
</body>
</html>
<?php
    function createStr($str, $start, $end)
    {
        $start = ($start) ? $start : 1;
        $end = ($end) ? $end : 1000;
        $strList = "";
        for ($i=$start; $i<=$end; $i++)
        {
            $tempStr = $str;
            $tempStr = str_replace("[i]", $i ,$tempStr);
            $tempStr = str_replace("[rnd]", mt_rand(1,10000) ,$tempStr);
            $strList .= $tempStr . "\n";
        }
        return $strList;
    }
?>
