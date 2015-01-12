<?php
    // if ($_SERVER['REMOTE_ADDR'] != "59.120.37.117" && $REMOTE_ADDR != "127.0.0.1") exit;
    $url = $_POST['fmUrl'];
    echo "\$url = $url <br>";
    if ($url != "")
    {
        $html = getHtml($url);
    }
    
    function getHtml($url)
    {
		$timeout = 10;
		$curl = curl_init($url);
		if (substr($url, 0, 5) == "https")
    	{ 
    	    @ curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
    	    @ curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); 
            @ curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);               
    	}
    	else
    	{
		    @ curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP);		    
		}
		@ curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);  
        @ curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        @ curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        
		$data = curl_exec($curl);
		
		$response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		if ($response_code != "200")
        {
            return "ERROR 404!!";
        }
        return $data;
    }   
?>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<html>
<head>
<title>取得網頁原始碼</title>
</head>
<body>
<form action=get_html.php method=post>
    <input type=text id=fmUrl name=fmUrl value="<?= $url ?>" style='width:90%'>
    <input type=submit value='送出'>
</form>
<textarea id=fmHtml name=fmHtml style='width:100%; height:600px'><?= $html ?></textarea>
</body>
</html>