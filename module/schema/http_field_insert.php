<?
    include "../../lib/common.php";

    // $id   = $id;
    // $note = $note;
    // $note = mysql_real_escape_string(htmlspecialchars(stripslashes($note), ENT_QUOTES));

    $sql = "UPDATE `schema` SET note='$note', writeTime=now() WHERE id='$id'";
    $rr = mysql_db_query("db_schema", $sql);
    if (!$rr)
    {
        printf("error %d: %s\n", mysql_errno(), mysql_error());
    }
?>