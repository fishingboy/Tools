<?
    //查詢資料時同時查詢資料總筆數
    $objs = db_query("SELECT * FROM table where a=1");
    $row = mysql_fetch_row(db_query("SELECT FOUND_ROWS()"));
    $totalRows = $row[0];
?>