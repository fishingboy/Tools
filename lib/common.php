<?
// ini_set("display_errors", "Off");
include('define.php');

// database connection
if ( !($link = mysql_pconnect($DB_SERVER, $DB_LOGIN, $DB_PASSWD)))
{
    printf("error %d: %s\n", mysql_errno(), mysql_error());
    exit(0);
}
mysql_query("SET NAMES 'utf8'");

header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");

function db_object($db)
{
    return mysql_fetch_object($db);
}

function db_query($sql)
{
    global $DB;
    if ( !($rr = mysql_db_query("$DB", $sql)) )
    {
        printf("error %d: %s\n", mysql_errno(), mysql_error());
        exit(0);
    }
    return $rr;
}
function filelog($msg, $del=0)
{
    $op = ($del==1) ? "w" : "a";
    global $WEB_ROOT;
    $fp = fopen("$WEB_ROOT/tmp/log.txt", $op);
    fwrite($fp, date("H:i:s") . ": $msg\n");
    fclose($fp);
}
?>