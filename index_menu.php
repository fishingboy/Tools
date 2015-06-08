<?
    ini_set("display_errors", "Off");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>工具箱</title>
    <link rel="icon" type="image/ico" href="site.ico"></link>
    <link rel="shortcut icon" href="site.ico"></link>
    <style>@import URL('style.css');</style>
    <style>
        li {font-size:13px; list-style-type: decimal;}
    </style>
</head>
<body>
<?
    $dir = opendir(".");
    $items = array();
    while($file = readdir($dir))
    {
        $items[] = $file;
    }
    sort($items);

    echo "<div style='text-align:center; font-size:13px; font-weight:bold'>====  工具列表  ====</div>";
    echo "<ul style='margin-top:0px; margin-bottom:0px;'>";
    foreach ($items as $i => $file)
    {
        if (is_file($file) && strstr($file, ".php") && !ignore_file($file))
        {
            // $hint = $func[$file];
            $hint    = get_file_tag($file, "title");
            $program = explode(".", $file)[0];
            echo "<li>
                    <a href='index.php?program={$program}' target='_top'>$file</a><br>
                    <span style='color:#aaa'>$hint</span>
                  </li>";
        }
    }
    echo "</ul>";

    echo "<div style='border-bottom:1px dotted #333; margin:10px 0;'></div>";
?>
</body>
</html>
<?
    function ignore_file($file)
    {
        $ignore_list = array(
                             "index.php",
                             "index_menu.php",
                             "index_main.php",
                             "common.php",
                             "define.php",
                             "code_finder.php",
                             "firephp.php",
                             "http_clear_log.php",
                             "define.sample.php",
                             "view.php",
                             "const.php",
                             "const_sample.php",
                             );

        $local_list = array(
                             "phpinfo.php",
                             "bigdump.php",
                             "sql_str.php",
                             "phpunit_tester.php",
                             "locale_diff.php",
                             "get_path_filelist.php",
                             "db_search.php",
                             "db_schema.php",
                             "db_compare.php",
                             "data_import.php",
                             );
        // 忽略清單
        foreach ($ignore_list as $value)
        {
            if ($file == $value) return true;
        }

        // 本機工具清單(線上環境隱藏)
        if (ENV == 'ONLINE')
        {
            foreach ($local_list as $value)
            {
                if ($file == $value) return true;
            }
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

    function get_file_tag($file, $tag)
    {
        $content = file_get_contents($file);
        $content2 = strtolower($content);
        $pos1 = strpos($content2, "<$tag>");
        $pos2 = strpos($content2, "</$tag>");
        $len = strlen($tag);
        if ($pos1)
            return substr($content, $pos1+$len+2, $pos2-$pos1-$len-2);
        else
            return "";
    }
