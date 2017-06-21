<?php
    // <title>跑遍目錄下所有檔案(檢查錯誤)</title>
    $G_INDEX = 0;
    
    ob_start();
    
    $rootPath = "D:/TMS/www_lms/course/quiz";

    echo "<div style='font-weight:bold; margin-bottom:20px;'>eachFile($rootPath);</div>";
    eachFile($rootPath);
    
    flush();
    ob_end_flush();

    function eachFile($path, $curr_path="")
    {
        global $rootHost, $rootPath, $G_INDEX;

        //echo "\$path = $path <br>";
        $dir = opendir($path);
        while($file = readdir($dir))
        {   
            if ($file == "." || $file == ".." || strpos('.svn', $file) !== false){}
            elseif (is_dir("$path/$file"))
            {
                if ($file != "sysdata" &&
                    $file != "upload_tmp" &&
                    $file != "patch")
                {
                    $curr_path2 = ($curr_path) ? "$curr_path/$file" : $file;
                    eachFile("$path/$file", $curr_path2);
                }
            }
            elseif (eregi("[a-z0-9_].php", $file) || eregi("[a-z0-9_].inc", $file))
            {
                if ($file == "each_file.php" || $file == "each_url.php") continue;
                    
                $file_path = ($curr_path) ? "$rootPath/$curr_path/$file" : "$rootPath/$file";
                
                format_code($file_path);
                
                $G_INDEX++;                
                echo "$G_INDEX. $file_path<br>";
                
                print str_repeat(" ", 4096);
                ob_flush();
            }
        }
    }
    
    function format_code($file)
    {
        $content = file_get_contents($file);
        $tmp = explode("\n", $content);
        $cnt = count($tmp);
        $tmp2 = array();
        for ($i=0; $i<$cnt; $i++)
        {
            $str = $tmp[$i];
            $str = str_replace("\t", "    ", $str);
            $str = str_replace("mysql_fetch_object(", "db_object(", $str);
            $str = rtrim($str);
            $tmp2[] = $str;
        }
        $content2 = implode("\r\n", $tmp2);
        
        $fp = fopen($file, "w");
        fwrite($fp, $content2);
        fclose($fp);
    }
?>