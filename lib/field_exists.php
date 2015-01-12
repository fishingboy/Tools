<?
    function field_exists($table, $field)
    {
        global $DB;
        
        $fields = mysql_list_fields($DB, $table);
        $columns = mysql_num_fields($fields);
        
        for ($i = 0; $i < $columns; $i++) 
            if (mysql_field_name($fields, $i) == "$field") return true;

        return false;
    }
?>