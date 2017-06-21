<?php
if (!isset($DEFINE_DBRECORD)) { $DEFINE_DBRECORD = "1";
//-----------------------------------------------------------------------

class DBRecord
{
    var $tableName;
    var $keyName;
    var $key;
    var $data;
    
    function DBRecord($tableName, $keyName="id", $key="")
    {
        $this->tableName = $tableName;
        $this->keyName = $keyName;

        if ($key != "")
        {
            $this->key = $key;
            $this->data = mysql_fetch_array(db_query("SELECT * FROM $this->tableName WHERE $this->keyName='$this->key' LIMIT 1"));
            return;
        }
        
        
        // $key == "", create a record       
        if ($this->keyName == "id")
        {
            // auto increment
            db_query("INSERT INTO $this->tableName SET $this->keyName=''");
            $this->key = mysql_insert_id();
        }
        else
        {
            // key value will be replaced by update()
            $this->key = rand();
            db_query("INSERT INTO $this->tableName SET $this->keyName='$this->key'");
        }
    }
    
    
    function getAssignment($data)
    {
        $flag = 0;
        while (list($field, $value) = each($data))
        {
            if ($flag == 0)
            {
                $assignments = "$field='$value'";
                $flag = 1;
            }
            else
                $assignments .= ", $field='$value'";
        }
        reset($data);
        
        return $assignments;
    }
    
    
    function update($data)
    {
        $this->data = $data;
        $assignments = $this->getAssignment($data);        
		db_query("UPDATE $this->tableName SET $assignments WHERE $this->keyName='$this->key' LIMIT 1");
    }

    
    function delete()
    {
        db_query("DELETE FROM $this->tableName WHERE $this->keyName='$this->key' LIMIT 1");
    }
    
    
    // used in extended class
    function getValueSet($access)
    {
        $valueSet = "";
        $items = split(" ", trim($access));
        for ($i=0; $i<count($items); $i++)
        {
            $item = trim($items[$i]);
            if ($i > 0) $valueSet .= ", ";
            $c = $item[0];
            if ($c >= "0" && $c <= "9")
                $valueSet .= "($this->key, '$item', '')";
            else
                $valueSet .= "($this->key, -1, '$item')";
        }
        return $valueSet;
    }
}

//-----------------------------------------------------------------------
}
?>