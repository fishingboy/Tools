<?
if ($CTOC_INCLUDE != 1) { 
	$CTOC_INCLUDE = 1;
//-------------------------------------------
class CToc
{
    var $tree;
    
    function CToc()
    {
        $this->tree = new CTree("xms", "", 1);
        //$this->tree->liType = 1;
    }
    
    function setCheck($id, $hint)
    {
        $this->tree->setCheck($id, $hint);
    }
    function addNode($id, $name, $parentID, $type)
    {
        $this->tree->addNode($id, $name, $parentID, $type);
    }
    
    function show() { $this->tree->show(); }
}

//-------------------------------------------
}
?>