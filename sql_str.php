<?php
    include ("common.php");
    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    $_DB = ($_DB) ? $_DB : "test";
    $start = ($fmStart) ? $fmStart : 1;
    $end   = ($fmEnd)   ? $fmEnd   : 100;
    $rnd_start = ($fmRndStart) ? $fmRndStart : 1;
    $rnd_end = ($fmRndEnd) ? $fmRndEnd : 100000;

    if ($fmSubmit)
    {
        include ("lib/timer.php");
        $query_time = new timer();
        $str = stripslashes($str);
        $query_time->start();
        queryStr($str, $start, $end, $rnd_start, $rnd_end);
        $query_time->stop();
        $t = $end - $start + 1;
        echo "$_DB 執行SQL " . $query_time->spent() . ": $start - $end ($t)<br><br>";
        echo nl2br($str) . "<hr><br>";
    }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>執行 SQL (批次)</title>
<script src='dom.js'></script>
<script>
    function delID()
    {
        var str = $V("str");
        if (str.match(/`id`/))
        {
            str = str.replace(/([(])`id`,/g, "$1");
            str = str.replace(/([(])[0-9]+,/g, "$1");
            $("str").value = str;
        }
    }
</script>
</head>
<body style='align:left'>
<pre>
==  sql 產生器  ==
  [i]   = 以序號取代
  [rnd] = 以隨機變數取代
</pre>
<form action='sql_str.php' method=POST>
    資料庫：
    <select id=_DB name=_DB>
        <?php
            $db_list =  mysql_list_dbs();
            $cnt = mysql_num_rows($db_list);

            for($i=0; $i<$cnt; $i++)
            {
                $dbName =  mysql_db_name($db_list, $i);
                $selected = ($_DB == $dbName) ? "selected" : "";
                echo "<option value='$dbName' $selected>$dbName";
            }
        ?>
    </select>
    開始序號：<input type=text class=Text id=fmN name=fmStart value='<?= $start ?>'>
    結束序號：<input type=text class=Text id=fmN name=fmEnd value='<?= $end ?>'>
    <br>
    <b>亂數範圍</b>
    開始：<input type=text class=Text id=fmN name=fmRndStart value='<?= $rnd_start ?>'>
    結束：<input type=text class=Text id=fmN name=fmRndEnd value='<?= $rnd_end ?>'>
    <br>
    <input type=button name=fmDelID value='拿掉id' onclick='delID()'>
    <input type=submit name=fmSubmit value='執行 query'>
    <textarea id=str name=str style='width:100%; height:350px' onfocus='this.select()'><?= stripslashes($str) ?></textarea>
</form>
</body>
</html>
<?php
    function queryStr($str, $start, $end, $rnd_start, $rnd_end)
    {
        global $_DB;

        $start = ($start) ? $start : 1;
        $end = ($end) ? $end : 1000;
        $strList = "";
        for ($i=$start; $i<=$end; $i++)
        {
            $tempStr = $str;
            $tempStr = str_replace("[i]", $i ,$tempStr);
            $tempStr = str_replace("[rnd]", mt_rand($rnd_start, $rnd_end) ,$tempStr);
            $tempStr = str_replace("[rnd2]", mt_rand($rnd_start, $rnd_end) ,$tempStr);
            $tempStr = str_replace("[rnd3]", mt_rand($rnd_start, $rnd_end) ,$tempStr);
            $strList .= $tempStr . "\n";
            db_query($_DB, $tempStr);
        }
        // echo $strList . "<br>";
        return $strList;
    }
?>