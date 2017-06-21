<?php
if ($CTREE_INCLUDE != 1) { $CTREE_INCLUDE = 1;
//-------------------------------------------

class CNode
{
    var $id;
    var $name;    
    var $parentID;
    var $type;          // folder or doc
    var $children;
    
    var $checked;
    
    function CNode($id, $name, $parentID, $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parentID = $parentID;
        $this->type = $type;
        $this->checked = "";
    }
    
    function addChild($id) { $this->children[count($this->children)] = $id; }
    function numChildren() { return count($this->children); }
}


class CTree
{
    var $node;
    var $type; 
    var $liType;   
    var $identSpace;

    var $selectedID;
    var $showRoot;
    var $expandAll;
    var $expandID;
    var $dirSelectable;
    var $nodeSelectable;
    var $icons;
    

    var $js_parent;
    var $js_node;

    var $iconExpand;
    var $iconHide;
    var $iconNull;
    var $iconFolder;  
    
    function CTree($rootName="xms", $type="", $liType=0, $identSpace = 16)
    {       
        $this->type = $type;
        $this->liType = $liType;
        $this->identSpace = $identSpace;
        $this->addNode(0, $rootName, -1, 0); // root node

        $this->selectedID = -1;
        $this->showRoot = 0;
        $this->expandAll = 0;
        $this->expandID = 0;       
        $this->dirSelectable = 1;
        $this->nodeSelectable = 1;
        if (($type == "radio") || ($this->liType == 1) ) 
        		$this->expandAll = 1;
  
        $this->iconFolder = "/sys/res/icon/ctree_folder.gif"; 
        
        if ($this->liType == 1)
        {
    		$this->iconExpand = "/sys/res/icon/ctree_expand_toc.gif";
	        $this->iconHide = "/sys/res/icon/ctree_hide_toc.gif";
	        $this->iconNull = "/sys/res/icon/ctree_null_toc.gif";   
      	}
      	else
      	{
	        $this->iconExpand = "/sys/res/icon/ctree_expand.gif";
	        $this->iconHide = "/sys/res/icon/ctree_hide.gif";
	        $this->iconNull = "/sys/res/icon/ctree_null.gif";   
	    }  
    }

    function setCheck($id, $hint)
    {
        $this->node[$id]->checked = $hint;
    }
    function addNode($id, $name, $parentID, $type)
    {
        $this->node[$id] = new CNode($id, $name, $parentID, $type);
        $this->js_parent .= "ctree_parent[$id] = $parentID; \n";
        $this->js_node .= "ctree_node[ctree_node.length] = $id; \n";
    }
    function setSelected($id)
    {
        $this->selectedID = $id;
    }
    function setIcon($type, $icon)
    {
        $this->icons[$type] = "<img src='$icon' valign=absmiddle>";
    }

 
    
    function show()
    {
        // construct children's relation
    	while (list($id, $nn) = each ($this->node))
    	{
    	    if ($this->node[$nn->parentID])
                $this->node[$nn->parentID]->addChild($id);    
    	}
    	

    	$currNode = $this->node[0];
        $depth = 0;

        if ($this->showRoot)
            $this->dfs($this->node[0], $depth, "");
        else
        {
            for ($i=0; $i<count($currNode->children); $i++)
                $this->dfs($this->node[$currNode->children[$i]], $depth, $i + 1); 
        }
        
        $this->writeJS();
    }
    
   

    function dfs($currNode, $depth, $prefix)
    {                
        $id = $currNode->id;

        $icon = (isset($this->icons[$currNode->type])) ? $this->icons[$currNode->type] : "";
        $width = $height = "11px";
       
                
        $name = "$currNode->name";
        switch ($currNode->type)
        {
            case 0:   
            	$img_style = ($this->liType == 1) ? "style=\"cursor:pointer; margin: 0px 1px 0px 0px\"" : "style=\"cursor:pointer\"";             
                $imgsrc = ($currNode->numChildren() == 0) ? "src='$this->iconNull'" : "src='$this->iconExpand'";
                $nodeCtrl = "<img id='ctrl{$id}' $img_style onclick='ctree_ctrlClick($id)' $imgsrc  align=absmiddle>";
                
                $onevent = "onclick='ctree_ctrlClick($currNode->id)' onmouseover='ctree_nodeOver($currNode->id)' onmouseout='ctree_nodeOut($currNode->id)'";
                break;

            default:
                // folder
                if ($this->type == "")
                {
                    $nodeCtrl =  "<img src='$this->iconFolder' align=absmiddle>";
                    if ($icon != "")
                    {
                        $nodeCtrl = $icon;
                        $icon = "";
                    }
                }
                else
                {
                    $nodeCtrl =  "<input type={$this->type} style='width:$width; height:$height' id='item{$id}' name=ctree>";
				}
				$onevent = "onclick='ctree_nodeClick($currNode->id)' onmouseover='ctree_nodeOver($currNode->id)' onmouseout='ctree_nodeOut($currNode->id)'";
                break;        
        }

        
        if ($this->type != "") $onevent = "onclick='ctree_itemClick($currNode->id)'";

                
       
        // for list item
        $wrap = "noWrap=1";    
        $init_space = 13;  
        $valign = $check = "";  
        if ($this->liType == 1)
        {
        	$init_space = 28;    
            $this->identSpace = 40;
            $prefix2 = "";
            if ($depth == 0) $prefix2 = ".";
            if ($currNode->checked) $check = "<img src='/sys/res/icon/check.gif' title='$currNode->checked' align=absmiddle>";
            
            if ($currNode->type == 0)
            	$nodeCtrl = $nodeCtrl . "{$check}{$prefix}{$prefix2}";
            else
            	$nodeCtrl = "{$check}{$prefix}{$prefix2}";
            $onevent = "";            
            $wrap = "";     
            $valign = "valign=top";        
        }
        
       
        
        // add context menu event for all type of ctree
        //$onevent = "$onevent oncontextmenu='ctree_OnContextMenu($currNode->id); event.cancelBubble=true; event.returnValue=false; return false;'";

        $tab_space = $depth * $this->identSpace + $init_space;    
        $wh = "";
        if ($this->liType <> 1)
        {
            if ($tab_space < 12)
        	    $tab_space2 = 0;
            else
        	    $tab_space2 = $tab_space - 12;    
        	$wh =  "width=$tab_space2 height=16";      
        }  
        echo "<table class=ctree cellspacing=1px>
        	  <tr>
                <td class=ctree width=$tab_space nowrap align=right $valign><img src='/sys/res/icon/menu_linebottom.gif'  align=absmiddle $wh>$nodeCtrl</td>
                <td class=ctree id=node{$currNode->id} $onevent $wrap $valign>$icon $name</td>
              </tr></table>";



        if ($currNode->numChildren() > 0) 
        {
        	echo "<span id=child{$id} style='display:none'>\n";
            for ($i=0; $i<count($currNode->children); $i++)
                $this->dfs($this->node[$currNode->children[$i]], $depth + 1, "{$prefix}." . ($i+1));
            echo "</span>\n";
        }
    }
    
    
    
    function writeJS()
    {        
        echo "
            <script>
                var ctree_dirSelectable = 0;
                var ctree_nodeSelectable = 0;
                var ctree_selectedID = $this->selectedID;
                var ctree_parent = new Array();
                $this->js_parent
                if (ctree_selectedID != -1) window.setTimeout('ctree_nodeClick(' + ctree_selectedID + ')', 400);

                var ctree_node = new Array();
                $this->js_node
                
                if ($this->dirSelectable) ctree_dirSelectable = 1;
                if ($this->nodeSelectable) ctree_nodeSelectable = 1;
                if ($this->expandAll) window.setTimeout('ctree_expandAll()', 500);
                if ($this->expandID) window.setTimeout('ctree_expandNode($this->expandID)', 500);

                function ctree_getItems()
                {
                    var ret = '';
                    for (i=0; i<ctree_node.length; i++)
                    {
                        var id = ctree_node[i];
                        var obj = document.getElementById('item' + id);
                        if (obj && obj.checked)
                        {
                            if (ret != '') ret = ret + '#';
                            ret = ret + id;
                        }
                    }
                    return ret;
                }

                function ctree_OnContextMenu(id)
                {
                    try { ctreeOnContextMenu(id); } catch (e) {}                    
                }
                function ctree_itemClick(id)
                {
                    var obj = document.getElementById('item' + id);
                    if (obj) obj.checked = !obj.checked;
                    
                    // expand this node if possible
                    ctree_expandNode(id);
                }
                function ctree_nodeOut(id)
                {
                    document.getElementById('node' + id).className = (id == ctree_selectedID) ? 'ctreeSelected' : 'ctree';
                }
                function ctree_nodeOver(id)
                {
                    document.getElementById('node' + id).className = 'ctreeOver';
                }
                function ctree_nodeClick(id)
                {                
                    ctree_selectNode(id);
                    //alert(id);
                    try { ctreeOnClick(id); } catch (e) {}
                }
                function ctree_selectNode(id)
                {  
                	if (ctree_nodeSelectable == 0) return;          	
                    var obj = document.getElementById('node' + ctree_selectedID);
                    if (obj) obj.className = 'ctree';
                    
                    //if (!document.getElementById('child' + id) || ctree_dirSelectable)
                    
                    obj = document.getElementById('ctrl' + id);
                    if ( (obj) && (ctree_dirSelectable == 0)) {}
                    else 
                    {
                        // not folder or folder selectable
                        ctree_selectedID = id;
                    }
                    
                    
                    obj = document.getElementById('node' + ctree_selectedID);
                    if (obj) 
					{
						obj.className = 'ctreeSelected';
                    }
					else   //sam add for recursive exception 2006/09/02
					{
						obj = document.getElementById('node' + id);
						if (!obj)						
							return;
					}
                    
                    // expand this node if possible
                    ctree_expandNode(id);

                    // expand ancestor
                    var parentID = ctree_parent[id];
                    while (parentID != -1)
                    {
                        obj = document.getElementById('child' + parentID);
                        if (obj)
                        {
                            document.getElementById('child' + parentID).style.display = 'block';
                            obj = document.getElementById('ctrl' + parentID);
                            if (obj) obj.src = '$this->iconHide';
                        }
                        parentID = ctree_parent[parentID];
                    }
                }


                function ctree_expandAll()
                {
                    for (i=0; i<ctree_node.length; i++)
                    {
                        ctree_expandNode(ctree_node[i]);
                    }
                }
  				              

                function ctree_expandNode(id)
                {
                    var obj = document.getElementById('child' + id);
                    if (obj)
                    {
                        obj.style.display = 'block';

                        // chang icon if possible (not radio type)
                        obj = document.getElementById('ctrl' + id);
                        if (obj)
                        {
                            if (obj.src.indexOf('ctree_null') > 0) return;
                            obj.src = '$this->iconHide';
                        }
                    }
                }
                
                function ctree_collapseAll()
                {
                    for (i=0; i<ctree_node.length; i++)
                    {
                        ctree_collapseNode(ctree_node[i]);
                    }
                }
                
                function ctree_collapseNode(id)
                {
                    var obj = document.getElementById('child' + id);
                    if (obj)
                    {
                    		// chang icon if possible (not radio type)
                        var imgobj = document.getElementById('ctrl' + id);
                        if (imgobj)
                        {
                        		obj.style.display = 'none';

                            if (imgobj.src.indexOf('ctree_null') > 0) return;
                            imgobj.src = '$this->iconExpand';
                        }
                    }
                }
                
                function ctree_ctrlClick(id)
                {
                    ctrl = document.getElementById('child' + id);
                    if (!ctrl) return;
                    
                    if (ctrl.style.display == 'none')
                    {
                        ctrl.style.display = 'block';
                        document.getElementById('ctrl' + id).src = '$this->iconHide';
                    }
                    else
                    {
                        ctrl.style.display = 'none';
                        document.getElementById('ctrl' + id).src = '$this->iconExpand';
                    }
                }
            </script>
        ";
    }
}



//-------------------------------------------
}
?>