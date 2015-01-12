<?
    $filter = (isset($_GET['filter'])) ? $_GET['filter'] : array(0,1,1,0);
    
    $FILTER_ARR = array("Undefined variable", "Parse error", "Fatal error", "Undefined index");
    // $FILTER_ARR = array("Undefined variable");
    $inputs = "";
    foreach ($FILTER_ARR as $key => $value)
    {
        $filter[$key] = (isset($filter[$key])) ? $filter[$key] : 0;
        $checked = ($filter[$key]) ? "checked" : "";
        $inputs .= "<input type=checkbox name=filter[$key] value='1' $checked onclick='f1.submit()'> $value ";
    }

    // function
    function line_clear($str)
    {
        $str = ereg_replace("^[^]]+[]] PHP ", "", $str);
        if (strpos($str, "Undefined index") !== false)
        {
            $_str = explode(":", $str);
            $_str[2] = ereg_replace("^[ ]+([^ ]+) in D", "", $_str[2]);
            $str = implode(":", $_str);
        }
        else if (strpos($str, "Undefined offset") !== false)
        {
            $_str = explode(":", $str);
            $_str[2] = ereg_replace("^[ ]+([^ ]+) in D", "", $_str[2]);
            $str = implode(":", $_str);
        }
        return $str;
    }
    
    function line_format($str)
    {
        if (strpos($str, "Warning") !== false)
        {
            return "<span style='color:red'>$str</span>";
        }
        else if (strpos($str, "Undefined variable") !== false)
        {
            $_str = explode(":", $str);
            $_str[2] = ereg_replace("^[ ]+([^ ]+) in D", " <span style='font-weight:bold; color:green;'>\\1</span> in", $_str[2]);
            $_str[3] = str_replace("\\XMS\\www_lms\\", " ", $_str[3]);
            $_str[3] = ereg_replace("([0-9a-zA-Z._\\]+) on", "<span style='font-weight:bold; color:blue;'>\\1</span> on", $_str[3]);
            $_str[3] = ereg_replace("on line ([0-9]+)", "on line <span style='font-weight:bold; color:green;'>\\1</span>", $_str[3]);
            $str = implode(":", $_str);
            return $str;
        }
        else if (strpos($str, "Undefined index") !== false)
        {
            $_str = explode(":", $str);
            // $_str[2] = ereg_replace("^[ ]+([^ ]+) in D", "<span style='font-weight:bold; color:green;'>\\1</span> in ", $_str[2]);
            $_str[3] = str_replace("\\XMS\\www_lms\\", " ", $_str[3]);
            $_str[3] = ereg_replace("([0-9a-zA-Z._\\]+) on", "<span style='font-weight:bold; color:blue;'>\\1</span> on", $_str[3]);
            $_str[3] = ereg_replace("on line ([0-9]+)", "on line <span style='font-weight:bold; color:green;'>\\1</span>", $_str[3]);
            $str = implode(":", $_str);
        }
        else if (strpos($str, "Undefined offset") !== false)
        {
            $_str = explode(":", $str);
            // $_str[2] = ereg_replace("^[ ]+([^ ]+) in D", "<span style='font-weight:bold; color:green;'>\\1</span> in ", $_str[2]);
            $_str[3] = str_replace("\\XMS\\www_lms\\", " ", $_str[3]);
            $_str[3] = ereg_replace("([0-9a-zA-Z._\\]+) on", "<span style='font-weight:bold; color:blue;'>\\1</span> on", $_str[3]);
            $_str[3] = ereg_replace("on line ([0-9]+)", "on line <span style='font-weight:bold; color:green;'>\\1</span>", $_str[3]);
            $str = implode(":", $_str);
        }
        else if (strpos($str, "Parse error") !== false)
        {
            $_str = explode(":", $str);
            // $_str[2] = ereg_replace("^[ ]+([^ ]+) in D", "<span style='font-weight:bold; color:green;'>\\1</span> in ", $_str[2]);
            $_str[2] = str_replace("\\XMS\\www_lms\\", " ", $_str[2]);
            $_str[2] = ereg_replace("([0-9a-zA-Z._\\]+) on", "<span style='font-weight:bold; color:blue;'>\\1</span> on", $_str[2]);
            $_str[2] = ereg_replace("on line ([0-9]+)", "on line <span style='font-weight:bold; color:green;'>\\1</span>", $_str[2]);
            $str = implode(":", $_str);
        }
        else if (strpos($str, "Fatal error") !== false)
        {
            $_str = explode(":", $str);
            // $_str[2] = ereg_replace("^[ ]+([^ ]+) in D", "<span style='font-weight:bold; color:green;'>\\1</span> in ", $_str[2]);
            $_str[2] = str_replace("\\XMS\\www_lms\\", " ", $_str[2]);
            $_str[2] = ereg_replace("([0-9a-zA-Z._\\]+) on", "<span style='font-weight:bold; color:blue;'>\\1</span> on", $_str[2]);
            $_str[2] = ereg_replace("on line ([0-9]+)", "on line <span style='font-weight:bold; color:green;'>\\1</span>", $_str[2]);
            $str = implode(":", $_str);
        }
        return $str;
    }

?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Error Log 檢視器</title>
<script src='dom.js'></script>
<script src='/sys/lib/js/jquery.js'></script>
<script>
    function clear_log()
    {
        $j.ajax({url:"http_clear_log.php", complete: onFinish});
    }
    function onFinish()
    {
        window.location.reload();
    }
    var reloadTime = 100000;
    
    function enableAutoReload()
    {
        var sec = $V('fmReloadTime');
        if ($('fmAutoReload').checked)
        {
            window.location.href = "errlog_view.php?reload=1&sec=" + sec;
        }
        else
            window.location.href = "errlog_view.php?reload=0&sec=" + sec;
    }
    
    function autoReload()
    {
        if ($('fmAutoReload').checked)
            window.location.reload();
        else
            window.setTimeout('autoReload()', reloadTime)
        
    }
    window.onload = function()
    {
        reloadTime = $V('fmReloadTime') * 1000;
        window.setTimeout('autoReload()', reloadTime)
    }
</script>
</head>
<body style='align:left'>
<pre>
</pre>
<form name=f1 action='errlog_view.php' method=GET>
<div style='border:1px solid #ccc; padding:5px; background:#cff'>
    <?= $inputs ?>
    <input type=button value='清除LOG' onclick='clear_log()'>
</div>
<input type=checkbox id=fmAutoReload value='1' <?= ($_GET['reload']) ? "checked" : "" ?> onclick='enableAutoReload()'> 自動 Reload
, <input type=text id=fmReloadTime value='<?= (($_GET['sec']) ? $_GET['sec'] : 3)?>' size=2 onchange='enableAutoReload()'>秒
</form>
<div style='border:1px solid #ccc; padding:10px; height:600px; width=100%; overflow-y:auto'>
<?
    $file = "c:/xms/phperr.log";
    if (!is_file($file)) exit;
    
    $fp = fopen($file, "r");
    $cache_arr = array();
    while ($line = fgets($fp))
    {
        $line = line_clear($line);
        if (array_key_exists($line, $cache_arr)) continue;
        $cache_arr["$line"] = 1;

        $line = line_format($line);
        $key = 0;
        foreach ($FILTER_ARR as $key => $value)
        {
            if (strpos($line, $value) !== false)
            {
                if ($filter[$key]) echo "$line<br>";
                continue 2;
            }
        }
        // if ($key == count($FILTER_ARR) -1)
        // {
            // echo "$line<br>";
        // }
    }
?>
</div>
</body>
</html>