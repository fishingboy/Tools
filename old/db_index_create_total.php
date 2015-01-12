<?
    $CURR_VER = "7.0.0817C";
    $SAVE_VER = "7.0.0803C";
?>
<html>
<head>
<title>LMS 資料庫索引語法建立工具</title>
<style>
    body {font-size:15px; width:400px}
    td {padding:0px 5px 0px 5px}
    .txtright {text-align:right}
    .em    {color:#f00}
    .light {color:#aaa};
    .form {border:1px solid #aaa; padding:5px}
    .title1 {font-size:20px; font-weight:bold; padding:10px 0px 10px 0px}
    .title2 {font-weight:bold; margin:30px 0px 3px 0px; border:1px solid #777; padding:3px; background:#ffc}
    .title3 {font-weight:bold; padding:10px 0px 0px 0px;}
</style>
</head>
<body>
    <?php
        $fp = fopen("ver_viewer.txt", "r");
        while ($line = fgets($fp))
        {
            $line = trim($line);
            if ($line[0] == "=") continue;
        
            $tmp = explode('#', $line);
            $url = $tmp[0];

            $ver = get_version($url);
            if ($ver == 'not_found') continue;
            if ($ver >= $SAVE_VER)
            {
                $db1 = getDbSchema($url);
                add_create_index_str($db1);
            }
        }
        $str = implode("\n", $result_array);
    ?>
    
    <div class=title1><?= $url ?> index</div>
    <div class=form>
        <form method="post" action="<?= $PHP_SELF; ?>">
            <table>
                <tr>
                    <td class=txtright>source:</td>
                    <td><input type="text" size="32" name="url" value="<?= $url ?>"></td>
                </tr>
            </table>
            <input type="submit" value="submit">
        </form>
    </div>
    <div class=title2 style='width:500px'>Create Table Index</div>
    <textarea style='width:1400px; height:500px'><?= $str ?></textarea>
</body>
</html>
<?php
function getDbSchema($url)
{
    $schema = file_get_contents("$url/sys/db_sync_srv.php?op=dbSchema");
    return unserialize($schema);
}

$result_array = array();
function add_create_index_str($db_schema)
{
    global $result_array;
    
    $str = "";
    $ignore_list = array(
                         "^evaluate_result_[0-9]+$", 
                         "^event_result_[0-9]+$",
                         "^poll_result_[0-9]+$",
                         "^quiz_score_[0-9]+$",
                         "^quiz_score_[0-9]+_tmp$",
                         "^vote_signup_result_[0-9]+$",
                         "^user_ep_[0-9]+$",
                         "^score_final$"
                         );

    foreach ($db_schema as $table => $table_obj)
    {
        foreach ($ignore_list as $rule)
        {
            if (ereg($rule, $table)) continue 2;
        }
        
        foreach ($table_obj as $field => $field_attr)
        {
            if ($field_attr['Key'] == "MUL")
            {
                $value = "create_db_index('$table', '$field');";
                if (!in_array($value, $result_array))
                {
                    $result_array[] = $value;
                }
            }
        }
    }
    return $str;
}
    function get_version($url)
    {
        $timeout = 10;
		$curl = curl_init("$url/ver.txt");
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
            return "not_found";
        }
        
        return $data;
    }
?>