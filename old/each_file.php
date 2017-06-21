<?php
    // <title>跑遍目錄下所有檔案(檢查錯誤)</title>
    $G_INDEX = 0;
    
    ob_start();
    
    $rootPath = "D:/TMS/www_lms";
    $rootHost = "http://lms";
    
    echo "eachFile($rootPath);<br><br>";
    eachFile($rootPath);
    
    flush();
    ob_end_flush();

    function eachFile($path, $curr_path="")
    {
        global $rootHost, $G_INDEX;

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
            elseif (eregi("[a-z0-9_].php", $file))
            {
                if ($file == "each_file.php" || $file == "each_url.php") continue;
                    
                $url = ($curr_path) ? "$rootHost/$curr_path/$file" : "$rootHost/$file";
                
                $ch = curl_init($url); 
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 5);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                $html = curl_exec($ch);
                curl_close ($ch);
                if (!$html) return;
                
                $G_INDEX++;
                $error = searchError($html);
                if ($error)
                {
                    echo "<span style='font-size:24px'><b><span style='color:green'> " . $G_INDEX++ . ". $url</span></b> : $error</span><br>";
                }
                echo "$G_INDEX. <a href='$url'>$url</a> Finish!!<br>";
                
                print str_repeat(" ", 4096);
                ob_flush();
            }
        }
    }
    
    function searchError($html)
    {
        $error_array = array('Parse error', 'Fatal error', 'database error');
        foreach ($error_array as $key => $value)
        {
            if (eregi($value, $html) !== false)
            {
                return $value;
            }
        }
        return false;
    }
?>