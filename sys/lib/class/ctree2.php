<?php
/** mod -star-0201 
* Tree Class and TreeNode Class
*
*   -CNode
*       -Constructor
*       -public methods
*           -addChild       ->新增Child於Node下的$children陣列
*           -numChildren    ->取得目前Node所擁有的children數量
*   -CTree
*       -Constructor
*           -新增root node "xms" with name=xms, id=0, parentid=-1, type=0
*       -public methods
*           -addNode         ->新增Tree Node ,同時將該Node之id資料加入js_node (產生js變數宣告使用)
*           -setSelected     ->設定目前Tree中,被選取的Node id
*           -setIcon         ->設定node Restype所對應的icons src
*           -show            ->呼叫 private function dfs顯示Tree
*       -private methods
*           -setExpandParent ->遞迴設定節點展開屬性 , 輸入目標節點, 其與所有父節點均會被設定 "展開子節點標籤"
*           -dfs             ->輸出Tree HTML
*/

// Tree Node which is stored one wiki Table's record (tree element)
class CNode
{
    var $id;			    // node's id (wiki's id)
    var $name; 			    // node's display format (HTML)
    var $parentID;          // node's parent's id
    var $type;              // node's resType , indicate the ResTpye of the wiki's record 

	var $bExpandChild; 		// flag->to decide whether to expand this node's children (1:expand 0:collapse)	
	var $children;  		// array-> to store the node's children ,index by Num 0,1,2,...
     
    function CNode($id, $name, $parentID, $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->parentID = $parentID;
        $this->type = $type;	
		$this->bExpandChild = 0;
    }
    
    function addChild($id) { $this->children[count($this->children)] = $id; }
    function numChildren() { return count($this->children); }
}

// Tree element which is stored all wiki's records in one course(courseID)
class CTree
{
	var $type; 					// 'uk property
    var $liType;                // 'uk property
    var $identSpace;			// "display distance" between two neighbor nodes in tree (unit:px)  
    var $initSpace;
    var $valign;
    var $wrap;                  // forbid the word wrap in table td/th 

	var $showRoot;              // flag-> to decide whether to show the root node (which is not necessary to be a wiki record)
	var $selectedID;            // node id -> to indicate which node is selected 

    var $iconExpand;			// path of expanded node's control icon
    var $iconHide;				// path of collapsed node's control icon
    var $iconNull;				// path of node's control icon witt no children
    var $iconFolder;            // 'uk property
    var $icons;                 // array -> to store each img's src of each node type
    var $imgStyle;
    
	var $node;					// array -> to store all nodes, index by "id",value by "CNode" object

    var $js_parent;             // string ->stored all nodes and its parent information to declare in js later
    var $js_node;               // string ->stored all nodes' id to declare in js later
    
    function CTree($rootName="xms", $type="", $liType=0, $identSpace = 16)
    {       
        $this->type = $type;
        $this->liType = $liType;
        $this->identSpace = $identSpace;			
        
		$this->showRoot = 0;							      
		$this->selectedID = -1;								
		$this->iconFolder = "/sys/res/icon/";
        $this->js_parent = "";
        $this->js_node = "";
		
        if ($this->liType == 1)
        {   		   
            $this->iconExpand = $this->iconFolder."treeexpand.png";
	        $this->iconHide = $this->iconFolder."treehide.png";
	        $this->iconNull = $this->iconFolder."ctree_null_toc.gif";   
            $this->imgStyle = "cursor:pointer; margin: 3px 0px 0px 0px;";
            
            $this->initSpace = 10;
            $this->wrap = "";     
            $this->valign = "valign='top'";
            $this->identSpace = 20;
            
      	}
      	else
      	{
	        $this->iconExpand = $this->iconFolder."ctree_expand.gif";
	        $this->iconHide = $this->iconFolder."ctree_hide.gif";
	        $this->iconNull = $this->iconFolder."ctree_null.gif"; 
            $this->imgStyle = "cursor:pointer";
            
            $this->initSpace = 13;
            $this->wrap = "noWrap";     
            $this->valign = "";   
            $this->identSpace = $identSpace;            
	    }  

		$this->addNode(0, $rootName, -1, 0);    	
    }
    
    function addNode($id, $name, $parentID, $type)
    {
        $this->node[$id] = new CNode($id, $name, $parentID, $type);	
		$this->js_parent .= "ctree_parent[$id] = $parentID; \n";
    }
    
    function setSelected($id) { $this->selectedID = $id; }
    function setIcon($type, $icon) { $this->icons[$type] = "<img src='{$icon}' valign=absmiddle>"; }
    
    // show tree layout 
    function show()
    {
        // construct children's relation
		// cause records have already been ordered by sn, this loop will form the complete tree structure
    	while (list($id, $nn) = each ($this->node)) 
    	{
            if ($id != 0) $this->node[$nn->parentID]->addChild($id); 
    	}
		
		// set "expand" state to selected node and it's all parents
    	$this->setExpandParent($this->selectedID);
		
    	// init the current node to rootnode(ex:xms) node[0]
        $currNode = $this->node[0];  
        $depth = 0;

		// judge either to show root node or to show all first level nodes
        // dfs會將node底下所有的chilredn都顯示
        if ($this->showRoot) // display one root node
        {
            $this->dfs($this->node[0], $depth);
        }
        else                 // display all first level node (parentId=0) 
        {
            $countChildren = count($currNode->children);
            for ($i=0; $i<$countChildren; $i++)
            {
                $this->dfs($this->node[$currNode->children[$i]], $depth);
            }
        }
        
        // dynamic generate tree js code
        $this->writeJS();
    }
    
    //-----------------------Vice Functions Start-----------------------------------------------------------------
	// add -star-0121 
    // recursively setup target node which children need to been expanded  
	function setExpandParent($id)
	{
		$this->node[$id]->bExpandChild = 1;
			
		$pid = $this->node[$id]->parentID;
		if ($pid != -1) $this->setExpandParent($pid);  // pid = -1 :root node's parent (doesn't exist)
	}
    // create tree element (node by node) 
    function dfs($currNode, $depth)
    {                
        $id = $currNode->id;
        $nodeDOM = $currNode->name;
        $icon = (isset($this->icons[$currNode->type])) ? $this->icons[$currNode->type] : "";
        $this->js_node .= "ctree_node[ctree_node.length] = $id; \n";    // added in order
        
		// apply node control (style/ img src/ js)
        $img_style = "style='{$this->imgStyle}'";
		$img_src = "src='{$this->iconNull}'";
        if ($currNode->numChildren() > 0) 
			$img_src = ($currNode->bExpandChild == 1) ? "src='{$this->iconHide}'" : "src='{$this->iconExpand}'";	
		
        $nodeCtrl = "<img id='ctrl{$id}' $img_style onclick='ctree_ctrlClick({$id})' $img_src  align='absmiddle'>";
		
        // setup node event (onclick/ mouse over/ mouse out)   note: this.id include nodeType
		$onevent = "onclick='ctree_nodeClick($id)' onmouseover='ctree_nodeOver($id)' onmouseout='ctree_nodeOut($id)'";
                
        // space spec for listed item
        $wrap = $this->wrap;    	
        $valign = $this->valign;  
        $tab_space = $depth * $this->identSpace + $this->initSpace;    
        $wh = "";
		
        // 'uk don't know what kind of list when litype!=1 , $wh沒用到
        if ($this->liType != 1) 
        {
            if ($tab_space < 12)
        	    $tab_space = 0;     
            else
        	    $tab_space -= 12;    
        	$wh =  "width='{$tab_space}' height='16'";      
        }
   
        $nodeclass = ($id == $this->selectedID) ? "ctreeSelected" : "ctree";
        
		echo "<table class=ctree cellspacing=1px>
        	  <tr>
                <td class=ctree width='$tab_space' nowrap align='right' $valign>$nodeCtrl</td>
                <td class={$nodeclass} id=node{$currNode->id} $onevent $wrap $valign>$icon $nodeDOM</td>
              </tr>
              </table>";
              
        if ($currNode->numChildren() > 0)  //recursive building for tree
        {
			if ($currNode->bExpandChild == 1)   
                echo "<span id='child{$id}' style='display:block'>";
			else                				
                echo "<span id='child{$id}' style='display:none'>";
				
            for ($i=0; $i<count($currNode->children); $i++)
                $this->dfs($this->node[$currNode->children[$i]], $depth + 1);

            echo "</span>";
        }
    }
    
    function writeJS()
    {        
        echo "<script>
                var ctree_parent = new Array();
				$this->js_parent
                var ctree_node = new Array();
                $this->js_node
                var ctree_selectedID = $this->selectedID;
                
                function ctree_nodeOut(id)
                {
                    if (id != ctree_selectedID) \$j('#node'+id).attr('class', 'ctree');
                }
                function ctree_nodeOver(id)
                {
                    if (id != ctree_selectedID) \$j('#node'+id).attr('class', 'ctreeOver');
                }
                function ctree_ctrlClick(id)
                {
                    ctrl = document.getElementById('child' + id);
                    if (!ctrl) return;
                    
                    if (ctrl.style.display == 'none')
                    {
                        ctrl.style.display = 'block';
                        document.getElementById('ctrl' + id).src = '{$this->iconHide}';
                    }
                    else
                    {
                        // 控制關閉時, 將子節點全關閉.
                        ctree_collapseAllchildren(id); 
                        ctrl.style.display = 'none';
                        document.getElementById('ctrl' + id).src = '{$this->iconExpand}';
                        
                    }
                }
				function ctree_nodeClick(id)
				{   
                    // scroll top
                    window.scroll(0,0);
                    
                    var ctrl = document.getElementById('child' + id);
                    
                    // expand if there are children nodes
                    if (ctrl)
                    {
                        if (ctrl.style.display == 'block')
                        {
                            ctree_ctrlClick(id);
                            return ;
                        }
                        else
                        {
                            ctrl.style.display = 'block';
                            document.getElementById('ctrl' + id).src = '{$this->iconHide}';
                        }
                    }                  
                    
                    // get content and focus node if the click event happens to different node
                    if(id != ctree_selectedID)
                    {
                        getContentPage(id); 
                        ctree_selectNode(id);
                        \$hidePopup(); //隱藏顯示的 url popup.
					}   
				}

                function ctree_selectNode(id)
                {  
                    obj = document.getElementById('node' + ctree_selectedID);       
                    if (obj) 
					{
						obj.className = 'ctree';
                    }
                    
                    ctree_selectedID = id;
                    
                    obj = document.getElementById('node' + ctree_selectedID);
                    if (!obj) return; 

                    obj.className = 'ctreeSelected';
 
                    var parentID = ctree_parent[id];                   
                    while (parentID != 0)
                    {
                        obj = document.getElementById('child' + parentID);
                        if (obj)
                        {
                            obj.style.display = 'block';
                            obj = document.getElementById('ctrl' + parentID);
                            if (obj) obj.src = '$this->iconHide';
                        }
                        parentID = ctree_parent[parentID];
                    }
                    return;
                }
                // recursively collapse all children node when node collpases
                function ctree_collapseAllchildren(id)
                {
                    var num =new Array();
                    var k=0;
                    for (var i=0; i<ctree_node.length; i++)
                    {
                        if(ctree_parent[ctree_node[i]] == id)
                        {            
                            num[k++] = ctree_node[i];
                            ctree_collapseAllchildren(ctree_node[i]);                           
                        }
                    }
                    for (var j=0; j<num.length; j++)
                    ctree_collapseNode(num[j]);
                }
                                            
                function ctree_collapseNode(id)
                {
                    var obj = document.getElementById('child' + id);
                    if (obj)
                    {
                    	// change icon if possible (not radio type)
                        var imgobj = document.getElementById('ctrl' + id);
                        if (imgobj)
                        {
                            obj.style.display = 'none';

                            if (imgobj.src.indexOf('ctree_null') > 0) return;
                            imgobj.src = '{$this->iconExpand}';
                        }
                    }
                }
				
            </script>
        ";
    }
    //-----------------------Vice Functions End-----------------------------------------------------------------
}
?>