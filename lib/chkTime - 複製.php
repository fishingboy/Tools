<?
    //比對 hh:mm
    function chkTime($time)
    {
        if (!ereg("^[0-9]{1,2}:[0-9]{2}$", $time)) return false;

        $arr = explode(":", $time);
      
        $hour = intval($arr[0]);
        if ($hour > 23 || $hour < 0) return false;
        $min = intval($arr[1]);
        if ($min > 59 || $min < 0) return false;

        return true;
    }

    //比對 yy:mm:dd
    function chkDate($time)
    {
        if (!ereg("^[0-9]{4}-[0-9]{2}-[0-9]{2}$", $time)) return false;

        $arr = explode("-", $time);
        return checkDate($arr[1], $arr[2], $arr[0]);
    }
    

    //測試
    echo chkDate("1991-09-09") ? "True" : "False";
    echo "<br>";
    echo chkDate("1991-13-09") ? "True" : "False";
    echo "<br>";
    echo chkDate("1991-09-32") ? "True" : "False";
    echo "<br>";
    echo chkDate("1991-02-29") ? "True" : "False";
    echo "<br>";
?>