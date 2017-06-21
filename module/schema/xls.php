<?php
include ("../../lib/common.php");
include ("../../sys/lib/class/xls/Writer.php");

if (isset($_POST['table']))
{
    $table_filter = "";
    foreach ($_POST['table'] as $key => $value)
    {
        $table_filter .= ($table_filter) ? "," : "";
        $table_filter .= "'$value'";
    }
    $table_filter = " AND _table IN ($table_filter)";
}

$dbName = "lms";
create_xls($dbName, $table_filter);

function create_xls($dbName, $table_filter)
{
    global $WEB_ROOT;
    
	$tmp = tempnam("$WEB_ROOT/tmp", 'TOC');
	@ unlink($tmp);
    
    /* 建立工作表 */
    $workbook = new Spreadsheet_Excel_Writer($tmp);
    $workbook->setVersion(8);
            
    /* 建立格式 */
    $title_format = & $workbook->addFormat();
    $title_format->setSize(12);
    $title_format->setBold();
    $title_format->setAlign('center');
    $title_format->setAlign('vcenter');
    $title_format->setBorder(0);
    $title_format->setPattern(1);
    $title_format->setFgColor('silver');
    $title_format->setMerge();          
    
    $table_title_format = & $workbook->addFormat();
    $table_title_format->setSize(12);
    $table_title_format->setBold();
    $table_title_format->setAlign('center');
    $table_title_format->setAlign('vcenter');
    $table_title_format->setBorder(0);
    $table_title_format->setPattern(1);
    $table_title_format->setFgColor('lime');
    $table_title_format->setMerge();          

    $table_header_format = & $workbook->addFormat();
    $table_header_format->setSize(11);
    $table_header_format->setBold();
    $table_header_format->setAlign('center');
    $table_header_format->setAlign('vcenter');
    $table_header_format->setBorder(0);
    $table_header_format->setPattern(1);
    $table_header_format->setFgColor('cyan');
    
    $field_format =& $workbook->addFormat();        
    $field_format->setSize(10);
    $field_format->setAlign('center');
    $field_format->setAlign('top');

    $field_format2 =& $workbook->addFormat();        
    $field_format2->setSize(10);
    $field_format2->setAlign('left');
    $field_format2->setAlign('top');

    /* 建立工作表 */
    $row_no = 0;
    $worksheet =& $workbook->addWorksheet($sheet_name);
    $worksheet->setInputEncoding("utf-8");
        
    // Set page margin
    $worksheet->setMargins(0.75);
    $worksheet->setMarginLeft(0.75);
    $worksheet->setRow(0, 24);         
    
    /* 設定欄寬 */
    $col_no = 0;
    $worksheet->setColumn(0, $col_no++, 15);
    $worksheet->setColumn(0, $col_no++, 15);
    $worksheet->setColumn(0, $col_no++, 10);
    $worksheet->setColumn(0, $col_no++, 15);
    $worksheet->setColumn(0, $col_no++, 70);
    
    /* 欄位資料 */
    $col_no = 0;
    for ($i=0; $i<count($ary); $i++)
    {
        $worksheet->write($row_no, $col_no++, $ary[$i], $format[$i]);
    }

    /* 大標題 */
    // $worksheet_title = "資料字典 - LMS (" . date("Y-m-d") . ")";
    $worksheet_title = "資料字典 - LMS";
    $worksheet->write($row_no, 0, $worksheet_title, $title_format);
    $nColumn = 5;
    for ($i=1; $i<$nColumn; $i++)
    {
        $worksheet->write($row_no, $i, "", $title_format);
    }
    $row_no++;

    $objs = mysql_db_query("db_schema", "SELECT * FROM tbl WHERE db='$dbName' $table_filter ORDER BY _table");
    while ($obj = mysql_fetch_object($objs))
    {
        $table = $obj->_table;
        $note = $obj->note;

        /* 表格標題 */
        $worksheet->write($row_no, 0, "$table : $note", $table_title_format);
        $nColumn = 5;
        for ($i=1; $i<$nColumn; $i++)
        {
            $worksheet->write($row_no, $i, "", $table_title_format);
        }
        $row_no++;

        $col_no = 0;
        $worksheet->write($row_no, $col_no++, "欄位", $table_header_format);
        $worksheet->write($row_no, $col_no++, "型態", $table_header_format);
        $worksheet->write($row_no, $col_no++, "Null", $table_header_format);
        $worksheet->write($row_no, $col_no++, "預設值", $table_header_format);
        $worksheet->write($row_no, $col_no++, "註解", $table_header_format);
        $row_no++;
        
        $tobjs = mysql_db_query("db_schema", "SELECT * FROM `schema` WHERE db='$dbName' AND _table='$table' AND status='1'");
        while ($tobj = mysql_fetch_object($tobjs))
        {
            $note = $tobj->note;
            filelog("{$_POST['fmNotNull']} && $note == ''");
            if ($_POST['fmNotNull'] && $note == "") continue;

            $sn++;
            $col_no = 0;
            $worksheet->write($row_no, $col_no++, $tobj->_field, $field_format2);
            $worksheet->write($row_no, $col_no++, $tobj->_type, $field_format2);
            $worksheet->write($row_no, $col_no++, $tobj->_null, $field_format);
            $worksheet->write($row_no, $col_no++, $tobj->_default, $field_format);
            $worksheet->write($row_no, $col_no++, $note, $field_format2);
            $row_no++;
        }

        $col_no = 0;
        $worksheet->write($row_no, $col_no++, "", $field_format2);
        $worksheet->write($row_no, $col_no++, "", $field_format2);
        $worksheet->write($row_no, $col_no++, "", $field_format);
        $worksheet->write($row_no, $col_no++, "", $field_format);
        $worksheet->write($row_no, $col_no++, "", $field_format2);
        $row_no++;
    }
    
    $workbook->close();

	@ $n = filesize($tmp);
	if ($sn > 1)
	{
	    if (($HTTPS))
	    {
			header("Pragma: private");
			header("Cache-control: private, must-revalidate");
		}
		else
		{	
			header("Pragma: public");
			header("Expires: 0");				    
		}			
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: inline; filename="' . $dbName . '_schema.xls";');
		header('Content-Transfer-Encoding: binary');
		header("Content-Length: $n");
		readfile($tmp);
	}
	@ unlink($tmp);   
}
?>