<?php
if (!isset($DEFINE_DBLIST)) { $DEFINE_DBLIST = "1";
//-----------------------------------------------------------------------

class SQLList
{
    var $pageNum;
    var $records;
    var $currPage;
    var $totalRows;
    var $currRows;
    
    function SQLList($param)
    {
        // 參數設定.
        $page      = (isset($param["page"])) ? $param["page"] : 1;
        $pageSize  = (isset($param["pageSize"])) ? $param["pageSize"] : 20;
        $sql        = trim($param["sql"]); 
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS " . substr($sql, 6);                
        
        // 讀取 records
        $start = ($page - 1) * $pageSize;
        $limit = "LIMIT $start, $pageSize";
        
        $sql = "$sql $limit";
	    $this->records = db_query($sql);
	    $this->currRows = mysql_num_rows($this->records);	  
        
        $row = mysql_fetch_row(db_query("SELECT FOUND_ROWS()"));
        $row_count = $row[0];

    
        // 設定 pageNum.            
        $p  = intval(($row_count - 1) / $pageSize);
	    $this->pageNum = ($p < 0) ? 1 : $p+1;
	    if ($page > $this->pageNum) $page = $this->pageNum;
	    
	    
	    // 目前的頁碼.
	    $this->currPage = $page;
	    
	    // 目前的總筆數.
	    $this->totalRows = $row_count;	    
	    
	    
	    // 讀取 records
        //$start = ($page - 1) * $pageSize;
        //$limit = "LIMIT $start, $pageSize";
        
        //$sql = "$sql $limit";
	    //$this->records = db_query($sql);
    }
    
    
    function showIndex($url)
    {
        global $msgPage;
        //echo "<span class='pagelink'>Total: $this->totalRows</span> ";
        for ($i=1; $i<=$this->pageNum; $i++)
        {
            if ($i == $this->currPage)
                echo "<span class='pagecurrent'>$i</span> ";
            else
            {
                $page_url = (strpos($url, "?") === false) ? "<a href='$url?page=$i' class=black>$i</a>" : "<a href='$url&page=$i' class=black>$i</a>";
                echo "<span class='pagelink'>$page_url</span> ";
            }
        }
    }
}


//-----------------------------------------------------------------------
}
?>