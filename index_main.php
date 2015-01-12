<?
    ini_set("display_errors", "Off");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>工具箱</title>
    <link rel="icon" type="image/ico" href="site.ico"></link>
    <link rel="shortcut icon" href="site.ico"></link>
</head>
<body>
<?
    $func = array("create_log.php" => "處理記錄產生器",
                  "create_str.php" => "字串產生器",
                  "db_search.php"  => "資料庫搜尋器",
                  "db_search2.php" => "資料庫工具(B)",
                  "ereg.php"       => "正規表示式工具(B)");
                  
    $dir = opendir(".");
    while($file = readdir($dir))
    {
        if (is_file($file) && strstr($file, ".php"))
        {
            $hint = $func[$file];
            echo "<li><a href='$file'>$file</a> $hint";
        }
    }
    
    echo "<hr>";
    echo "<h3>patch 檔</h1>";
    $path = "cmd";
    $dir = opendir("$path");
    while($d = readdir($dir))
    {
        $path2 = "$path/$d";
        if (!is_dir($path2) || ignore_dir($d)) continue;
        
        echo "<br><b>$path2</b><br>";
        $dir2 = opendir("$path2");
        while($f = readdir($dir2))
        {
            $file = "$path2/$f";
            if (is_file($file) && strstr($file, ".php") && !ignore_file($f))
            {
                // $hint = $func[$f];
                echo "<li><a href='$file' target=_blank>$file</a> $hint </li>";
            }
        }
    }
    
    
    function ignore_file($file)
    {
        $ignore_list = array("common.php", "define.php");
        foreach ($ignore_list as $value)
        {
            if ($file == $value) return true;
        }
        return false;
    }
    function ignore_dir($dir)
    {
        $ignore_list = array(".", "..", ".svn");
        foreach ($ignore_list as $value)
        {
            if ($dir == $value) return true;
        }
        return false;
    }
?>
<pre>
/* 開發計畫: 鍵盤選取執行 */
</pre>
</body>
</html>