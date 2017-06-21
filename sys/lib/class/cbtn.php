<?php
    function createBtn($cb, $value, $id=0)
    {
        return createBtnHtml($cb, $value, $id);
    }
    function createBtnHtml($cb, $value, $id=0)
    {
        $id = ($id) ? "id=$id" : "";
        return "<span $id class=btn onclick='$cb' onmouseover='this.className=\"btnOver\"' onmouseout='this.className=\"btn\"'>$value</span>";        
    }
?>

<script>
    function createBtn(value, cb)
    {
        return "<span class=btn onclick='javascript:" + cb + "' onmouseover='this.className=\"btnOver\"' onmouseout='this.className=\"btn\"'>" + value + "</span>";
    }
</script>