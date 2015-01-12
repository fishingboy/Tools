<?
include ("../../sys/lib/class/xls/Writer.php");

$dbName = "lms";
$schema = get_schema($dbName);
create_xls($schema);
// header("Location : schema.xls");

function get_schema($dbName)
{
    return 0;
}

function create_xls($schema)
{
    $xls_name = "schema.xls";
    unlink($xls_name);
    
    /* 建立工作表 */
    $workbook = new Spreadsheet_Excel_Writer($xls_name);
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
    // $table_title_format->setColor('silver');
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
    // $field_format->setBorder(1);
    // $field_format->setPattern(1);
    // $field_format->setFgColor('white');        

    $field_format2 =& $workbook->addFormat();        
    $field_format2->setSize(10);
    $field_format2->setAlign('center');
    $field_format2->setBorder(1);
    $field_format2->setPattern(1);
    $field_format2->setFgColor('gray');        

    $format[$i] =& $workbook->addFormat();        
    $format[$i]->setSize(10);
    $format[$i]->setAlign('center');
    $format[$i]->setBorder(1);
    $format[$i]->setPattern(1);
    $format[$i]->setFgColor('cccccc');        

    $ary = array(
                    'aqua',
                    'cyan',
                    'black',
                    'blue',
                    'brown',
                    'magenta',
                    'fuchsia',
                    'gray',
                    'grey',
                    'green',
                    'lime',
                    'navy',
                    'orange',
                    'purple',
                    'red',
                    'silver',
                    'white',
                    'yellow'
                );
                
    for ($i=0; $i<count($ary); $i++)
    {
        $format[$i] =& $workbook->addFormat();        
        $format[$i]->setSize(10);
        $format[$i]->setAlign('center');
        $format[$i]->setBorder(1);
        $format[$i]->setPattern(1);
        $format[$i]->setFgColor($ary[$i]);        
    }
    
    
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
    $row_no++;
    $worksheet->write($row_no, 0, '資料字典 - LMS', $title_format);
    $nColumn = 5;
    for ($i=1; $i<$nColumn; $i++)
    {
        $worksheet->write($row_no, $i, "", $title_format);
    }

    // $row_no++;
    // $worksheet->write($row_no, 0, '', $title_format);
    // $nColumn = 5;
    // for ($i=1; $i<$nColumn; $i++)
    // {
        // $worksheet->write($row_no, $i, "", $title_format);
    // }

    /* 表格標題 */
    $row_no++;
    $worksheet->write($row_no, 0, 'account_apply : 社群帳號申請', $table_title_format);
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
    $col_no = 0;
    $worksheet->write($row_no, $col_no++, "resType", $field_format);
    $worksheet->write($row_no, $col_no++, "tinyint(4)", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "0", $field_format);
    $worksheet->write($row_no, $col_no++, "1:文件", $field_format);
    
    $row_no++;
    $col_no = 0;
    $worksheet->write($row_no, $col_no++, "resType", $field_format);
    $worksheet->write($row_no, $col_no++, "tinyint(4)", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "0", $field_format);
    $worksheet->write($row_no, $col_no++, "1:文件", $field_format);
    

    $row_no++;
    $col_no = 0;
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    

    /* 表格標題 */
    $row_no++;
    $worksheet->write($row_no, 0, 'account_apply : 社群帳號申請', $table_title_format);
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
    $col_no = 0;
    $worksheet->write($row_no, $col_no++, "resType", $field_format);
    $worksheet->write($row_no, $col_no++, "tinyint(4)", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "0", $field_format);
    $worksheet->write($row_no, $col_no++, "1:文件", $field_format);
    
    $row_no++;
    $col_no = 0;
    $worksheet->write($row_no, $col_no++, "resType", $field_format);
    $worksheet->write($row_no, $col_no++, "tinyint(4)", $field_format);
    $worksheet->write($row_no, $col_no++, "", $field_format);
    $worksheet->write($row_no, $col_no++, "0", $field_format);
    $worksheet->write($row_no, $col_no++, "1:文件", $field_format);

    $workbook->close();
}
?>