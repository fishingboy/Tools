<?
include ("xls/Writer.php");

class XLS
{
    var $row_no;
    var $workbook;

    
    
    function XLS($xls_name)
    {
        
        // Creating a workbook
        $this->workbook = new Spreadsheet_Excel_Writer($xls_name);
        $this->workbook->setVersion(8);
                
        // Creating format
        $this->title_format = & $this->workbook->addFormat();
        $this->title_format->setSize(12);
        $this->title_format->setBold();
        $this->title_format->setAlign('center');
        $this->title_format->setAlign('vcenter');
        $this->title_format->setBorder(1);
        $this->title_format->setPattern(1);
        $this->title_format->setFgColor('yellow');
        $this->title_format->setMerge();          
        
        $this->category_format = & $this->workbook->addFormat();
        $this->category_format->setBold();
        $this->category_format->setAlign('center');
        $this->category_format->setBorder(1);
        $this->category_format->setPattern(1);
        $this->category_format->setFgColor('lime');
        $this->category_format->setMerge();
                
        $this->field_format =& $this->workbook->addFormat();        
        $this->field_format->setSize(10);
        $this->field_format->setAlign('center');
        $this->field_format->setBorder(1);
        $this->field_format->setPattern(1);
        $this->field_format->setFgColor('cyan');        
                
        $this->data_format =& $this->workbook->addFormat();
        $this->data_format->setSize(8); 
        $this->data_format->setAlign('vcenter');
        $this->data_format->setTextWrap();
        
        $this->score_data_format =& $this->workbook->addFormat();        
        $this->score_data_format->setSize(8); 
        $this->score_data_format->setAlign('center');
        $this->score_data_format->setAlign('vcenter');
        $this->score_data_format->setTextWrap();         

        $this->score_title_format =& $this->workbook->addFormat();        
        $this->score_title_format->setSize(8); 
        $this->score_title_format->setBold();
        $this->score_title_format->setAlign('center');
        $this->score_title_format->setAlign('vcenter');      
        $this->score_title_format->setMerge(); 
    }



    function AddSheet($sheet_name, $caption, $nColumn)
    {
        $this->row_no = 0;
        $this->worksheet =& $this->workbook->addWorksheet($sheet_name);
        $this->worksheet->setInputEncoding("utf-8");
            
        // Set page margin
        $this->worksheet->setMargins(0.75);
        $this->worksheet->setMarginLeft(0.75);
        $this->worksheet->setRow(0, 24);         
        
        //--------title---------- 
        $this->worksheet->write($this->row_no, 0, $caption, $this->title_format);
        for ($i=1; $i<$nColumn; $i++)
        {
            $this->worksheet->write($this->row_no, $i, "", $this->title_format);
        }
    }
    
    
    function SetColumnFormat($format)
    {
        //--------fields name------- 
        $this->row_no++;
        while (list($n, $val) = each($format))
        {
            $this->worksheet->setColumn(0, $n, $val[1]);    // title 
            $this->worksheet->write($this->row_no, $n, $val[0], $this->field_format);
        }
    }
   
    function SetData($data)
    {
        $this->row_no++;
        while (list($n, $val) = each($data))
        {
        	if ($val[1] == "string")
            	$this->worksheet->writeString($this->row_no, $n, $val[0], $this->data_format);        
        	else if ($val[1] == "number")
        		$this->worksheet->write($this->row_no, $n, $val[0], $this->score_data_format);
            else if ($val[1] == "title")
            {     
                $this->worksheet->write($this->row_no, 0, $val[0], $this->score_title_format);
                for ($i=1; $i<4; $i++)
                {
                    $this->worksheet->write($this->row_no, $i, "", $this->score_title_format);
                }
            }    
            else
                $this->worksheet->write($this->row_no, $n, $val[0], $this->score_data_format);
        }
    }    
    
    function FlushXLS()
    {
        $this->workbook->close();
    }
}
?>