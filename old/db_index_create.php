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
        $url = (isset($_POST['url'])) ? $_POST['url'] : "";
        if (strpos($url, 'http://') === false)
        {
            $url = "http://$url";
        }
        define('DB_NAME', $dbName); // make it global
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
    <?
        if ($url)
        {
            $db1 = getDbSchema($url);
            $str = create_index_str($db1);
        }
    ?>
    <textarea style='width:1400px; height:500px'><?= $str ?></textarea>
</body>
</html>
<?php
function getDbSchema($url)
{
    $schema = file_get_contents("$url/sys/db_sync_srv.php?op=dbSchema");
    return unserialize($schema);
}

function create_index_str($db_schema)
{
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
                $str .= "create_db_index('$table', '$field');\n";
            }
        }
    }
    return $str;
}

function print_assoc($ary)
{
    foreach($ary as $key => $value) 
    {
        echo " &nbsp; &nbsp; $key => $value <br>";
    }
}

function db_query($sql)
{
    static $link = 0;

    if ($link == 0) 
    {
        if ( !($link = mysql_pconnect(DB_SERVER, DB_LOGIN, DB_PASSWD))) 
        {
            printf("error %d: %s\n", mysql_errno(), mysql_error());
            exit(0);
        }
        mysql_query("SET NAMES 'utf8'");
    }
    
	if ( !($rr = mysql_db_query(DB_NAME, $sql)) )
	{
		printf("error %d: %s\n", mysql_errno(), mysql_error());
		exit(0);
	}
	return $rr;
}
?>