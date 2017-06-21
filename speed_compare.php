<?php
    include ("common.php");
    include ("lib/timer.php");
    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    $fmRepeat = ($fmRepeat) ? intval($fmRepeat) : 1000;


    if ($fmSubmit)
    {
        // URL1
        $t1 = new timer();
        $t2 = new timer();

        $t1->start();
        for ($i=1; $i<=$fmRepeat; $i++)
        {
            runURL($fmURL1);
        }
        $t1->stop();
        echo $t1->spent() . "<br>";

        // URL2
        if ($fmURL2)
        {
            $t2->start();
            for ($i=1; $i<=$fmRepeat; $i++)
            {
                runURL($fmURL2);
            }
            $t2->stop();
            echo $t2->spent() . "<br>";

            $percent = (($t2->TimeSpent - $t1->TimeSpent) / $t1->TimeSpent) * 100;
            echo "URL1 : URL2 = " . number_format($percent, 4) . "%<br>";
        }
    }
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>兩網頁速度比較</title>
<script src='dom.js'></script>
</head>
<body style='align:left'>
<pre>
==  兩網頁速度比較  ==
</pre>
<form action='speed_compare.php' method=POST>
    URL1：<input type=text class=Text id=fmURL1 name=fmURL1 value='<?= $fmURL1 ?>' style='width:600px'><br>
    URL2：<input type=text class=Text id=fmURL2 name=fmURL2 value='<?= $fmURL2 ?>' style='width:600px'><br>
    次數：<input type=text class=Text id=fmRepeat name=fmRepeat value='<?= $fmRepeat ?>'>
    <input type=submit name=fmSubmit value='執行'>
</form>
</body>
</html>
<?php
    function runURL($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $cache = curl_exec($curl);
        $response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return $cache;
    }
?>