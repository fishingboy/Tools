<?
    $NO_RETURN_URL = 1;
    include ("../../../common.php");

    $res = array("quota" => $msgSU_QuotaNotEnough,
                 "limit" => $msgSU_FileLimit,
                 "empty" => $msgSU_FileEmpty
                );

    echo "var gRes = " . json_encode($res) . ";";
?>