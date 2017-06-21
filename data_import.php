<?php
    include ("common.php");
    // TODO: 先使用這隻解完 register_globals 的問題，有空再整個重寫
    include_once('lib/register_globals.php');

    function strip_quote($str)
    {
        return ereg_replace ("^\"(.*)\"$", "\\1", $str);
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>資料匯入</title>
<script>
	function changeCharset(csv_name)
	{
	    var charseteCtrl = document.getElementById("fmCharset");
		var charset = charseteCtrl.options[charseteCtrl.selectedIndex].value;
		window.location.href = "user_import.php?fmSubmit=yes&csv_name=" + csv_name + "&reSubmit=1&charset=" + charset;
	}
</script>
</head>
<body topmargin=0px leftmargin=0px rightmargin=0px bottommargin=1px scroll=yes>
<?php
	if ($fmAction == "yes")  // write to DB
	{
		$handle = fopen ("tmp/$csv_name", "r");

		$total_rows = 0;
		$over_write_rows = 0;
		$success_rows = 0;
        $cells = array();

        $enc_charset = ($enc_charset) ? $enc_charset : "big-5";
        $html = "";
        while (!feof ($handle))
		{
			$buffer = fgets($handle);
			if (trim($buffer == "")) continue;
			if ($enc_charset != "UTF-8")
			{
				$buffer = iconv($enc_charset, "UTF-8//IGNORE", $buffer);
			}
			$total_rows++;
			$csv_data = explode(",", $buffer);
	    	$num = count($csv_data);

            for ($i=0; $i<$num; $i++)
            {
                $csv_data[$i] = strip_quote(trim($csv_data[$i]));
            }

            if ($total_rows == 1)
            {
                $tableName = $csv_data[0];
                if ($clear_table)
                {
                    db_query($_DB, "TRUNCATE TABLE `$tableName`;");
                }
                continue;
            }
            if ($total_rows == 2)
            {
                $fields = "";
                for ($i=0; $i<$num; $i++)
                {
                    if ($i != $num-1)
                        $fields .= "{$csv_data[$i]},";
                    else
                        $fields .= "{$csv_data[$i]}";
                }
                continue;
            }

            $rows = "";
            for ($i=0; $i<$num; $i++)
            {
                if ($i != $num-1)
                    $rows .= "'" . addslashes($csv_data[$i]) . "',";
                else
                    $rows .= "'" . addslashes($csv_data[$i]) . "'";
            }
            $sql = "INSERT INTO $tableName ($fields) VALUE ($rows);";
            db_query($_DB, $sql);
	    }

        $total_rows -= 2;

	    fclose ($handle);
        echo "<h4>SETP.3 匯入完成</h4>";
        echo "$total_rows 筆記錄匯入完成。";
		exit;
	}




    /*************** SETP.2 check csv data  ***************/
	if ($fmSubmit == "yes")
	{
		if (!$reSubmit) // first time
		{
            if (!is_uploaded_file($_FILES['csv_file']['tmp_name']))
            {
                echo "<p align='center'>檔案上傳失敗!";  // no csv file
                echo "<input type='button' class='button' onclick='history.back()' value='返回'><p>";
                exit;
            }
            else
            {
                $csv_name = date("YmdHis") . ".csv";
                copy($csv_file, "tmp/$csv_name");
            }
        }

		$handle = fopen ("tmp/$csv_name", "r");

		$total_rows = 0;
		$over_write_rows = 0;
		$success_rows = 0;
        $cells = array();

        $enc_charset = ($enc_charset) ? $enc_charset : "big-5";
        $html = "";
        while (!feof ($handle))
		{
			$buffer = fgets($handle);
			if (trim($buffer == "")) continue;
			if ($enc_charset != "UTF-8")
			{
				$buffer = iconv($enc_charset, "UTF-8//IGNORE", $buffer);
			}
			$total_rows++;
			$csv_data = explode(",", $buffer);
	    	$num = count($csv_data);

            for ($i=0; $i<$num; $i++)
            {
                $csv_data[$i] = strip_quote(trim($csv_data[$i]));
            }

            if ($total_rows == 1)
            {
                $tableName = $csv_data[0];
                continue;
            }
            if ($total_rows == 2)
            {
                $fields = "";
                for ($i=0; $i<$num; $i++)
                {
                    if ($i != $num-1)
                        $fields .= "{$csv_data[$i]},";
                    else
                        $fields .= "{$csv_data[$i]}";
                }
                continue;
            }

            $rows = "";
            for ($i=0; $i<$num; $i++)
            {
                if ($i != $num-1)
                    $rows .= "'" . addslashes($csv_data[$i]) . "',";
                else
                    $rows .= "'" . addslashes($csv_data[$i]) . "'";
            }
            $sql = "INSERT INTO $tableName ($fields) VALUE ($rows);";
            $html .= "$sql \n";
	    }

	    fclose ($handle);

        $selected = array();
        $selected[$enc_charset] = "selected";
?>
        <h4>SETP.2 預灠資料</h4>
        編碼:
		<select class=selectBox03 id='fmCharset' name='fmCharset' onchange='changeCharset("$csv_name")'>
            <option value='UTF-8' <?= $selected['UTF-8'] ?>>UTF-8</option>
            <option value='big5' <?= $selected['big-5'] ?>>big-5</option>
            <option value='GBK' <?= $selected['GBK'] ?>>GBK</option>
		</select>

        <form method=post style='margin:0px'>
            <input type=hidden name=fmAction value='yes'>
            <input type=hidden name=csv_name value='<?= $csv_name ?>'>
            <input type=hidden name=_DB value='<?= $_DB ?>'>
            <input type=hidden name=enc_charset value='<?= $enc_charset ?>'>
            <input type=hidden name=clear_table value='<?= $clear_table ?>'>
            <textarea style='width:90%; height:400px'><?= $html ?></textarea><br>
            <input type=submit value='確定'>
        </form>
<?php
		exit;
	}
?>



<?php
    /***************  SETP.1  ***************/
    $db_list = mysql_list_dbs();
    $i = 0; $db_opt = $db_opt2 = "";
    $cnt = mysql_num_rows($db_list);
    while ($i < $cnt)
    {
        $db_name = mysql_db_name($db_list, $i);
        $selected = ($db_name == "lms") ? "selected" : "";
        $db_opt .=  "<option value='$db_name' $selected>$db_name</option>\n";
        $i++;
    }

?>
<h4>SETP.1 匯入資料</h4>
<form method=post enctype='multipart/form-data'>
    <input type=hidden name=fmSubmit value='yes'>
    <div style='border:1px solid #ccc; padding:3px; margin:3px; width:300px'>
        資料庫: <select name=_DB><?= $db_opt ?></select><br>
        <input type='file' id='csv_file' name='csv_file'>

    </div>
    <input type=checkbox name=clear_table value='1'> 清空資料表
    <input type=submit value='匯入'>
</form>
<p>說明:
<ol>
    <li>第 1 行: 資料表名稱</li>
    <li>第 2 行: 欄位名稱</li>
    <li>第 3 行以後，是資料</li>
</ol>
</body>
</html>