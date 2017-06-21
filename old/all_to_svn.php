<?php
    /*  <title>SVN 專案匯入工具(cmd)</title>
        請在 VisualSVN 啟動 cmd 模式
        再下 D:/TMS/php/php all_to_svn.php    
    */

    /********* 參數設定 **********/
    $src_path[]  = "C:/lms_package/src";
    $src_path[]  = "C:/lms_package/src2";
    $src_path[]  = "C:/lms_package/src3";
    $src_path[]  = "C:/lms_package/src4";
    $dest_path[] = "C:/lms_package/dest";
    $dest_path[] = "C:/lms_package/dest";
    $dest_path[] = "C:/lms_package/dest";
    $dest_path[] = "C:/lms_package/dest2";
    
    $svn_src_path[]  = "D:/Repositories/lms_bak";
    $svn_src_path[]  = "D:/Repositories/www_lms";
    $svn_src_path[]  = "D:/Repositories/wbkuo";
    $svn_src_path[]  = "D:/Repositories/wbkuo";
    $svn_dest_path[] = "D:/Repositories/lms_package";
    $svn_dest_path[] = "D:/Repositories/lms_package";
    $svn_dest_path[] = "D:/Repositories/lms_package";
    $svn_dest_path[] = "D:/Repositories/lms_package";
    
    $svn_src_url[]  = "https://wbkuo-pc:2013/svn/lms_bak/";
    $svn_src_url[]  = "https://wbkuo-pc:2013/svn/www_lms/trunk";
    $svn_src_url[]  = "https://wbkuo-pc:2013/svn/wbkuo/www_lms/";
    $svn_src_url[]  = "https://wbkuo-pc:2013/svn/wbkuo/www_lms-branches/1220_secure/";
    $svn_dest_url[] = "https://wbkuo-pc:2013/svn/lms_package/trunk";

    
    
    if (file_exists("C:/lms_package/svn_cmd.txt"))
    {
        unlink("C:/lms_package/svn_cmd.txt");
    }
    if (file_exists("C:/lms_package/ci.txt"))
    {
        unlink("C:/lms_package/ci.txt");
    }

    run_cmd("rem " . date("Y-m-d H:i:s"));
	$total = 0;
    for ($j=0; $j<count($src_path); $j++)
    {
        chdir($src_path[$j]);
        run_cmd("svn cleanup");
        
        $vers = get_all_version($src_path[$j]);
		$cnt = count($vers);
		run_cmd("rem {$src_path[$j]} versions = $cnt");
		$total += $cnt;
        $t = 0;
        foreach ($vers as $i => $ver)
        {
            $t++;
            chdir($src_path[$j]);
            run_cmd("svn sw {$svn_src_url[$j]}@$ver");
            run_cmd("robocopy {$src_path[$j]} {$dest_path[$j]} /E /PURGE /XD sysdata upload_tmp .svn");
            $commit_yes = commit_folder($dest_path[$j], "{$svn_src_url[$j]} VER:$ver");
            if ($commit_yes)
            {
                sleep(1);
                $dest_ver = get_curr_version($dest_path[$j]);
                copy_logfile($svn_src_path[$j], $ver, $svn_dest_path[$j], $dest_ver);
                sleep(1);
            }
        }
    }
	run_cmd("rem total = " . $total);
    run_cmd("rem " . date("Y-m-d H:i:s"));

    /*******************************************************************************************/
    // 取得所有版本號
    function get_all_version($path)
    {
        if (!is_dir($path))
        {
            echo "folder is not exists!!";
            exit;
        }
        
        chdir($path);

        if (file_exists("$path/../log.txt"))
        {
            unlink("$path/../log.txt");
        }
        run_cmd("svn log --stop-on-copy -r 0:HEAD > ../log.txt");

        $fp = fopen("$path/../log.txt", "r");
        $version = array();
        while ($line = fgets($fp))
        {
            $line = trim($line);
            if (ereg("^[-]+$", $line))
            {
                $is_info = 1;
            }
            else
            {
                if ($is_info && substr($line, 0, 1) == "r" && strpos($line, "wbkuo"))
                {
                    $tmp = explode("|", substr($line, 1));
                    $ver = trim($tmp[0]);
                    $version[] = $ver;
                }
                $is_info = 0;
            }
        }
        
        sort($version);
        return $version;
    }
    
    // 取得 SVN 資訊
    function get_curr_version($path)
    {
        chdir($path);

        if (file_exists("$path/../info.txt"))
        {
            unlink("$path/../info.txt");
        }
        run_cmd("svn log --stop-on-copy -r HEAD > ../info.txt");

        $fp = fopen("$path/../info.txt", "r");
        $version = array();
        while ($line = fgets($fp))
        {
            $line = trim($line);
            if (ereg("^[-]+$", $line))
            {
                $is_info = 1;
            }
            else
            {
                if ($is_info && substr($line, 0, 1) == "r")
                {
                    $tmp = explode("|", substr($line, 1));
                    $ver = trim($tmp[0]);
                    $version = $ver;
                    break;
                }
                $is_info = 0;
            }
        }
        
        return $version;
    }
    
    // commit 
    function commit_folder($path, $msg)
    {
        chdir($path);

        if (file_exists("$path/../status.txt"))
        {
            unlink("$path/../status.txt");
        }

        sleep(1);
        run_cmd("svn status > ../status.txt");
        
        $fp = fopen("$path/../status.txt", "r");
        $i = 0;
        while ($line = fgets($fp))
        {
            $i++;
            $status = substr($line, 0, 1);
            $file = trim(substr($line, 1));
            switch ($status)
            {        
                case "?":
                    $cmd = "svn add \"$file\"";
                    break;
                case "!":
                    $cmd = "svn delete \"$file\"";
                    break;
                case "D":
                case "A":
                case "M":
                    $cmd = "";
                    break;
                default:
                    $cmd = "rem special $file status = $status";
                    break;
            }
            
            if ($cmd)
            {
                run_cmd($cmd);
            }
        }

        if ($i > 0)
        {
            $ret = run_cmd("svn ci -m \"$msg\"");

            $fp = fopen("C:/lms_package/ci.txt", "a");
            fwrite($fp, "svn ci -m \"$msg\"\n$ret\n\n");
            fclose($fp);

            if (strpos($ret, "Committed revision") === false)
            {
                $fp = fopen("C:/lms_package/ci.txt", "a");
                fwrite($fp, "Commit Error: \nsvn ci -m \"$msg\"\n\n");
                fclose($fp);
                echo "Commit Error: \nsvn ci -m \"$msg\"\n\n";
                exit;
            }
            
            return true;
        }
        else
        {
            $fp = fopen("C:/lms_package/ci.txt", "a");
            fwrite($fp, "svn ci -m \"$msg\"\nNo Diff!!\n\n");
            fclose($fp);
            return false;
        }
    }
    
    function copy_logfile($src, $src_ver, $dest, $dest_ver)
    {
        copy("$src/db/revprops/0/$src_ver", "$dest/db/revprops/0/$dest_ver");
        echo "copy('$src/db/revprops/0/$src_ver', '$dest/db/revprops/0/$dest_ver');\n";
    }
    
    // 執行指令
    function run_cmd($cmd)
    {
        echo "====    $cmd    ====\n";
        $fp = fopen("C:/lms_package/svn_cmd.txt", "a");
        fwrite($fp, $cmd . "\n");
        fclose($fp);

        $ret = system($cmd);
        return $ret;
    }
?>