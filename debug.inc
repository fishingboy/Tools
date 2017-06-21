<?php
    $info = isset($info) ? $info : 1; 
     
    // if ($NO_RETURN_URL && $info) return;
    
    

    ob_start();
	$SELF_PATH = dirname(__FILE__);
    include_once ("$SELF_PATH/FirePHPCore/fb.php");
    $firephp = FirePHP::getInstance(true);
    // $_fp = $firephp;

    if (!$_GET)
    {
        if (function_exists("_getGet"))
        {
            $_GET  = _getGet();
            $_POST = _getPost();
            
            foreach ($_GET as $key => $value)
            {
                $$key = $value;
            }
            foreach ($_POST as $key => $value)
            {
                $$key = $value;
            }
        }
    }
     
    if ($info)
    {
        // GLOBAL
        $firephp->group('GLOBAL VARS');
        $firephp->log($_SESSION, '$_SESSION');
        $firephp->log($_COOKIE, '$_COOKIE');
        $firephp->log($_GET, '$_GET');
        $firephp->log($_POST, '$_POST');
        $firephp->groupEnd();

        // COMMON VAR
        $firephp->group('COMMON VARS');
        $firephp->log($USER_ACCOUNT  , '$USER_ACCOUNT');
        $firephp->log($USER_NAME     , '$USER_NAME');
        $firephp->log($USER_ID       , '$USER_ID');
        $firephp->log($USER_EMAIL    , '$USER_EMAIL');
        $firephp->log($cookie_account, '$cookie_account');
        $firephp->log($WEB_ROOT      , '$WEB_ROOT');
        $firephp->log($PRIV          , '$PRIV');
        $firephp->log($SYSTEM_NAME   , '$SYSTEM_NAME');
        $firephp->log($ADM_DIVID     , '$ADM_DIVID');
        $firephp->log($USER_ADMIN    , '$USER_ADMIN');
        $firephp->groupEnd();

        register_shutdown_function("view_include");
    }

    
	//搜尋inlcude檔案內容
	function search_include_files($keys)
	{
	    $arr = get_included_files();
	    foreach ($arr as $file)
		{
	        $fp = fopen($file,"r");
	        $line = 0;
	        $file_result_count=0;
	        while ($str =  fgets($fp))
			{
                foreach ($keys as $key)
                {
                    $line++;
                    if (stristr($str, $key))
                    {
                        $str = htmlspecialchars($str);
                        $mark = "<font color=red>$key</font>";
                        // $str = eregi_replace($key, $mark, $str);
                        $str = str_replace($key, $mark, $str);
                        $file_result_count++;
                        if ($file_result_count == 1)
                        {
                            $link = "<a href='http://tools/view.php?filename=$file'>$file</a>";
                            // $open = ", <a href='http://tools/sys/notepad.php?file=" . urlencode($file) . "'>(notepad++)</a>";
                            $search_result .= "<b>File： $link</b>$open<BR>\n";
                        }
                        $search_result .="<li>line #$line:$str<BR>\n";
                    }
                }
	        }
	        if ($file_result_count) $search_result .= "<br>";
	        fclose($fp);
	    }
	    return $search_result;
	}

    function view_include()
    {
        global $_GET, $_POST, $firephp;

		if (count($_GET))
		{
			foreach ($_GET as $key => $value)
			{
				$$key = $value;
			}
		}
		if (count($_POST))
		{
			foreach ($_POST as $key => $value)
			{
				$$key = $value;
			}
		}
        
        /////找出include檔案...
        $arr = get_included_files();

        $html = "";
        $html .= "<hr style='border:1px solid red'>\n";
        $html .= "<div align=left><dir>\n";
        $html .= "<h3> $PHP_SELF 所 included 的檔案：</h3>\n";

        $i = 0;
        foreach ($arr as $value)
        {
			if (strpos($value, "http://") !== false) continue;
            $i++;
            // $open = ", <a href='http://tools/sys/notepad.php?file=" . urlencode($value) . "'>(notepad++)</a>";
            $html .= "<li style='font-size:12px; list-style-type: decimal;'><a href='http://tools/view.php?filename=$value'>$value</a>$open</li>\n";
        }
        $html .= "</dir></div>\n";

		
		__print_r($_SESSION, 'session');
		

        $html .= "\$search_key = $search_key<br>";
        if ($search_key)
        {
            $html .= "<div align=left><hr style='border:1px solid #ccc'><h2>查詢inlcude檔案--</h2>
            <h3>關鍵字：<font color='red'>$search_key</font></h3><br>\n";

            $key = explode(",", $search_key);
            for($j=0; $j<count($key); $j++) $key[$j] = trim($key[$j]);

            $html .= search_include_files($key);
            $html .= "</div>\n";
        }

        //將$HTTP_GET_VARS取出，呼叫自已時需要....
		if (count($_GET))
		{
			foreach($_GET as $key => $value)
			{
				if ($key !="search_key")
				{
					$hidden_text .="<input type=hidden name=$key value=$value>\n";
				}
			}
		}
        // $html .= $hidden_text;
        
        $html .= "
            <hr style='border:1px solid red'>
            <div align=left>
                <form action='{$_SERVER[PHP_SELF]}' method=get>
                    $hidden_text
                    查詢inlcude檔案--<br>
                    請輸入查詢關鍵字：<input type=text name=search_key value='$search_key'><br>
                    <input type=submit>
                </form>
            </div>";
        echo $html;
    }
	
	function __print_r($arr, $name)
	{
		echo "<div style='text-align:left; margin:100px auto'>";
		echo "<h1>$name</h1>";
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		echo "</div>";
	}
?>