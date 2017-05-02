<?php

ini_set("display_errors", "Off");
if (!isset($INCLUDE_COMMON_PHP))
{    
	$INCLUDE_COMMON_PHP = 1;
	include_once('define.php');
	
    // database connection
  	if ( !($link = mysql_pconnect($DB_SERVER, $DB_LOGIN, $DB_PASSWD)))
  	{
        printf("error %d: %s\n", mysql_errno(), mysql_error());
        exit(0);
  	}
  	mysql_query("SET NAMES 'utf8'");
	
	header("Pragma: no-cache");
	header("Cache-Control: no-cache, must-revalidate");
	
}

if (!ini_get('register_globals'))
{
    if ($_SESSION)
    {
        foreach ($_SESSION  as $key => $value) 
        {
            $$key = $value;
        }
    }
    if ($_COOKIE)
    {
        foreach ($_COOKIE  as $key => $value) 
        {
            $$key = $value;
        }
    }
    if ($_POST)
    {
        foreach ($_POST  as $key => $value) 
        {
            $$key = $value;
        }
    }
    if ($_GET)
    {
        foreach ($_GET as $key => $value) 
        {
            $$key = $value;
        }
    }
}


function create_dir($path)
{
    if (!is_dir($path))
    {
        mkdir($path, 0777);
    }
}

function delete_dir($path)
{
	exec_log("rm -rf $path", $argv, $argc);
}

function mk_dir($dir)
{
	if (is_dir($dir))
		return true;

	if (($n = strrpos($dir, '/')) && ($n > 0))
	{
		if (!mk_dir(substr($dir, 0, $n)))
			return false;
	}
	return @mkdir($dir, 0777);
}

function db_object($db)
{
    return mysql_fetch_object($db);
}
function db_query($db, $sql)
{
    // global $DB;
	if ( !($rr = mysql_db_query($db, $sql)) )
	{
		printf("error %d: %s\n", mysql_errno(), mysql_error());
		exit(0);
	}
	return $rr;
}
function exec_log($cmd, &$output, &$ret)
{
    global $PLATFORM;
    
	$output = false;
	$ret = 0;
	
	if ($PLATFORM == "uniz")
	{
	    exec($cmd, $output, $ret);
		return;    
    }

	@ $fp = fsockopen('127.0.0.1', 12121, $errno, $errstr, 10);
	if ($fp)
	{
		$pwd = getcwd();
		@ fwrite($fp, "RUN\t$cmd\t$pwd\n");
		while (!feof($fp))
		{
			@ $s = fgets($fp, 32768);
			$output[]= $s;
		}
		@ fclose($fp);
	}
	else
	{
		global $WEB_ROOT, $EXEC_DEBUG;

		if (!$EXEC_DEBUG)
		{
			exec($cmd, $output, $ret);
			return;
		}

		$path = "$WEB_ROOT/data/log";
		create_dir($path);

		$log_name = date("Y-m-d", time()) . "_debug.txt";
		$logfile = "$path/$log_name";

		$logid = rand(1, 1000);
		$fp = fopen($logfile, "a+");
		fwrite($fp, $logid . ", [S][{$_SERVER['PHP_SELF']}] $cmd: " . date("m-d H:i:s", time()) . "\n");
		fclose($fp);

		exec($cmd, $output, $ret);

		$fp = fopen($logfile, "a+");
		fwrite($fp, $logid . ", [E][{$_SERVER['PHP_SELF']}] $cmd: " . date("m-d H:i:s", time()) . "\n\n");
		fclose($fp);
	}
}
function filelog($msg, $del=0)
{
    $op = ($del==1) ? "w" : "a";
    global $WEB_ROOT;
    $fp = fopen("D:/TMS/www_tools/log.txt", $op);
    fwrite($fp, date("H:i:s") . ": $msg\n");
    fclose($fp);
}

?>