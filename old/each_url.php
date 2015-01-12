<?
    // <title>暴力跑遍整個網站(檢查錯誤)</title>
    $G_URL_ARR = array();
    $G_URL_ARR_CHK_NUM = array();
    $G_INDEX = 0;
    $SEARCH_LEVEL = 2;
    
    ob_start();
    
    $rootUrl = "http://lms/";
    // $rootUrl = "http://lms/course.php?courseID=1";
    // $rootUrl = "http://lms/board.php?courseID=780";
    // $rootUrl = "http://lms/cmap/index.php";
    // $rootUrl = "http://lms/blog.php?user=home&f=portfolio";
    // $rootUrl = "http://lms/sys/adm/index.php";
    $rootHost = "http://lms";
    
    echo "eachUrl($rootUrl);<br><br>";
    eachUrl($rootUrl);
    
    flush();
    ob_end_flush();

    function eachUrl($url, $depth=1)
    {
        global $SEARCH_LEVEL, $G_INDEX;
        // if ($depth > $SEARCH_LEVEL) return ;
        
        $currPath = "http://lms";
        if (strpos($url, '/'))
        {
            $curr_url_path = explode("/", $url);
            unset($curr_url_path[count($curr_url_path)]);
            $curr_url_path = implode("/", $curr_url_path) . "/";
        }
        
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
        $nodes = getUrl($html, $currPath);
        foreach ($nodes as $key => $value)
        {
            eachUrl($value, $currPath, $depth+1);
            print str_repeat(" ", 4096);
            ob_flush();

        }
        echo "$G_INDEX. <a href='$url'>$url</a> Finish!!<br>";
    }
    
    function getUrl($html, $currPath)
    {
        global $rootHost, $G_URL_ARR, $G_URL_ARR_CHK_NUM, $SEARCH_LEVEL;
        
        $url_arr = array();
        $pos1 = $pos2 = 0;
        while(1)
        {
            //單引號
            $pos = strpos($html, "href='", $pos2+1);
            if ($pos === false) break;
            $pos1 = $pos;

            $pos = strpos($html, "'", $pos1+6);
            if ($pos === false) break;
            $pos2 = $pos;
            
            $url = checkUrl(substr($html, $pos1+6, ($pos2-$pos1-6)), $currPath);
            if ($url) 
            {   
                $url_format = getUrlFormat($url);
                // if (!in_array($url_format, $G_URL_ARR))
                if (!in_array($url_format, $G_URL_ARR) || 
                    $G_URL_ARR_CHK_NUM[$url_format] <= $SEARCH_LEVEL)
                {
                    $url_arr[] = $url;
                    $G_URL_ARR[] = $url_format;
                    $G_URL_ARR_CHK_NUM[$url_format]++;
                }
            }
        } 
        $pos1 = $pos2 = 0;
        while(1)
        {
            // 雙引號
            $pos = strpos($html, 'href="', $pos2+1);
            if ($pos === false) break;
            $pos1 = $pos;

            $pos = strpos($html, '"', $pos1+6);
            if ($pos === false) break;
            $pos2 = $pos;
            
            $url = checkUrl(substr($html, $pos1+6, ($pos2-$pos1-6)), $currPath);
            if ($url) 
            {   
                $url_format = getUrlFormat($url);
                // if (!in_array($url_format, $G_URL_ARR))
                if (!in_array($url_format, $G_URL_ARR) || 
                    $G_URL_ARR_CHK_NUM[$url_format] <= $SEARCH_LEVEL)
                {
                    $url_arr[] = $url;
                    $G_URL_ARR[] = $url_format;
                    $G_URL_ARR_CHK_NUM[$url_format]++;
                }
            }
        } 
        return $url_arr;
    }
    
    function checkUrl($url, $currPath)
    {
        global $rootHost;
        
        if (strpos($url, '#'))
        {
            $pos = strpos($url, '#');
            $url = substr($url, 0, $pos);
        }
        
        if (eregi("javascript", $url))
        {
            return false;
        }
        else if (strpos($url, 'https://') === 0 ||
                 strpos($url, '$') !== false ||
                 strpos($url, '" +') !== false ||
                 strpos($url, '"+') !== false
                 )
        {
            return false;
        }
        else if (strpos($url, 'http://') === 0)
        {
            if  (strpos($url, 'http://lms' !== false))
                return $url;
            else
                return false;
        }
        else if (strpos($url, '../') === 0)
        {
            return "$rootHost/$url";
        }
        else if (strpos($url, '/') === 0)
        {
            return $rootHost.$url;
        }
        else
            return "$currPath/$url";
            
    }

    function getUrlFormat($url)
    {
        $url = explode("&", $url);
        for ($i=0; $i<count($url); $i++)
        {
            $item = explode("=", $url[$i]);
            $url[$i] = $item[0];
        }
        return implode("&", $url);
    }
    
    function searchError($html)
    {
        // $error_array = array('Parse error', 'Fatal error', 'database error', '權限不足');
        $error_array = array('權限不足');
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