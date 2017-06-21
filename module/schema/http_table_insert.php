<?php
    include "../../lib/common.php";
    $sql = "UPDATE tbl SET note='$note', writeTime=now() WHERE id='$id'";
    $rr = mysql_db_query("db_schema", $sql);
    if (!$rr)
    {
        printf("error %d: %s\n", mysql_errno(), mysql_error());
    }
?>