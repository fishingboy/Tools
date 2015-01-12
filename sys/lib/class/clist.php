<?
$lstOutputJS = 1;
class CList
{
	var $id;
	var $type;
	var $width;
	var $align;
	var $numFields;
	var $msgNoData;
	var $chkData;

	var $nrows;

	function CList($id, $width='100%', $type='', $msgNoData='')
	{
        global $lstOutputJS;
        
		$this->id = $id;
		$this->type = $type;
		$this->nrows = 0;
		$this->msgNoData = $msgNoData;
		$this->numFields = 0;		

		if ($lstOutputJS == 1)
		{
			$this->OutputJS();
			$lstOutputJS = 0;
		}

		echo "<div class=tableBox><table class=table id=$this->id>\n";
	}


	function SetColumnFormat($format)
	{
		echo "<tr class=header>\n";
		if (!empty($format))
			$this->numFields = count($format);
		while (list($n, $val) = each($format))
		{
			$title = $val[0];
			$width = $this->width[$n] = ($val[1] == '') ? '' : "width={$val[1]}";
			$style = (isset($val[2])) ? $val[2] : "";
			$this->align[$n] = '';

			if ( ($this->type == 'checkbox') && ($n == 0) )
				echo "\t<td $width class=td align=center><input class=cb type={$this->type} id={$this->id}_itemall onclick='_lstCheckAllOnClick(\"{$this->id}\")'> $title</td>\n";
			else
				echo "\t<td $width class=td align=center>$title</td>\n";
		 }
		echo "</tr>\n";
	}

	function SetDataAlign($col, $align)
	{
		$this->align[$col] = "align=$align";
	}

	function SetData($data, $checked='', $disabled='')
	{
		$this->chkData[$this->nrows] =  0;
		
		if ($checked=='checked') 
		{
			$class = 'selected';
			$this->chkData[$this->nrows] =  1;
		}
		else
		{		
            $class = ($this->nrows % 2) ? "row" : "row2";
			if ($disabled=='disabled') $this->chkData[$this->nrows] =  2;
		}
		
		echo "<tr id={$this->id}_tr{$this->nrows} class=$class  onmouseover='_lstMouseOver(\"{$this->id}\", $this->nrows)' onmouseout='_lstMouseOut(\"{$this->id}\", $this->nrows)'>\n";
		while (list($n, $val) = each($data))
		{
			if ( ($this->type != "") && ($n == 0) )
			{
				echo "\t<td class=td {$this->width[$n]} {$this->align[$n]}><input class=cb type={$this->type} name={$this->id}_item id={$this->id}_item{$this->nrows} value={$val} onclick='_lstItemOnClick(event, \"{$this->id}\")' $checked $disabled></td>\n";
			}
			else
			{
				echo "\t<td class=td {$this->width[$n]} {$this->align[$n]}>{$val}</td>\n";
			}
		}
		echo "</tr>\n";
		$this->nrows ++;

	}

	function Close()
	{
		global $msgNoRec;
		
        $this->OutputJS2();

        if (!empty($this->msgNoData))
			$msgNoData = $this->msgNoData;
		else
			$msgNoData = $msgNoRec;

		if ($this->nrows == 0)
		{
			if ($this->numFields>0) 
			{
				echo "<tr class=row2>\n";
				echo "\t<td class=td colspan={$this->numFields} align=center>$msgNoData</td>\n";
				echo "</tr>\n";
			} 
			else
			{
				  echo "<div class=blackfont align=center><p>$msgNoData</p><hr size=1 color=#e8e8e8></div>";
			}
		}
		echo "</table></div>\n";
              
        if ($this->type == "checkbox")
            echo "<script>_lstCheckClearAll('$this->id')</script>";
	}
	
	function OutputJS2()
	{
        echo "
    		<script>		
    		tid = \"{$this->id}\";
    		tid = tid.substr(1);			
    		tblChk[tid] = new Array();
    		";

		for ($i=0; $i< $this->nrows; $i++)
		{			
		    echo "tblChk[tid][$i] = " . $this->chkData[$i] . ";\n";				
		}
		echo "</script>";	
	}

	function OutputJS()
	{
        echo "
		<script>
		var _lstLastClick = \"\";
		var tid = \"{$this->id}\";
		tid = tid.substr(1);	
		var tblChk = new Array();
		";

echo '
function _lstMouseOver(tblID, id)
{
	document.getElementById(tblID + "_tr" + id).className = "rowOver";
}

function _lstMouseOut(tblID, id)
{
	document.getElementById(tblID + "_tr" + id).className = (id % 2) ? \'row\': \'row2\' ;

	var ctrl = document.getElementById(tblID + "_item" + id);
	if (ctrl && ctrl.checked)
	{
		document.getElementById(tblID + "_tr" + id).className = "selected";
		return;
	}
}

function _lstSetItem(tblID, id, bChecked)
{
	var ctrl;
	for (i=0; ; i++)
	{
		ctrl = document.getElementById(tblID + "_item" + i);
		if (!ctrl) break;
		if (ctrl.value == id)
		{
			ctrl.checked = bChecked;
			break;
		}
	}
	_lstItemOnClick(event, tblID);
}

function _lstGetItem(tblID, delim)
{
	var ret = "";
	var tid = tblID.substr(1);	
	var ctrl;
	delim = delim || "#";	
	
	for (i=0; ; i++)
	{
		ctrl = document.getElementById(tblID + "_item" + i);
		if (!ctrl) break;
		if (tblChk[tid][i] == 1) ret = (ret == "") ? ctrl.value : ret + delim + ctrl.value;
	}
	return ret;
}

function _lstItemOnClick(aEvent, tblID)
{
	// process shift multi-selection control, follows Gmail behavior	
	var e = (aEvent) ? aEvent : window.event;
	
	//var e = window.event;		
	if (navigator.userAgent.indexOf("MSIE") != -1)
		var currClick = e.srcElement.id;
	else
		var currClick = e.target.id;	
	
	var end = parseInt(currClick.substr(currClick.lastIndexOf("item") + 4), 10);	
	var bChecked = document.getElementById(currClick).checked;	
	var tid = tblID.substr(1);	
	if (bChecked)
	{
			tblChk[tid][end] = 1;
			document.getElementById(tblID + "_tr" + end).className = "selected";
	}
	else
	{
			tblChk[tid][end] = 0;
			document.getElementById(tblID + "_tr" + end).className = (end % 2) ? "row" : "row2";
	}
	
		
	tblChk[tid][end] = (bChecked) ? 1 : 0;
	
	if (e.shiftKey && _lstLastClick)
	{
		var start = parseInt(_lstLastClick.substr(_lstLastClick.lastIndexOf("item") + 4), 10);

		if (start > end)
		{
			var tmp = end;
			end = start;
			start = tmp;
		}		
		
		for (i=start; i<=end; i++)
		{				
					if (document.getElementById(tblID + "_item" + i).disabled) continue;
					
					document.getElementById(tblID + "_item" + i).checked = bChecked;
					
					if (bChecked)
					{
							tblChk[tid][i] = 1;
							document.getElementById(tblID + "_tr" + i).className = "selected";
					}
					else
					{
							tblChk[tid][i] = 0;
							document.getElementById(tblID + "_tr" + i).className = (i % 2) ? "row" : "row2";
					}
		}
	}
	
	_lstLastClick = currClick;

	var val = true;		
	for (i=0; i< tblChk[tid].length ; i++)
	{			
			if (tblChk[tid][i] == 0) 
			{				
				val = false;		
				break;
			}			
	}

	ctrl = document.getElementById(tblID + "_itemall");
	if (ctrl) ctrl.checked = val;
}

function _lstCheckAllOnClick(tblID)
{
	var val = document.getElementById(tblID + "_itemall").checked;
	var tid = tblID.substr(1);	
	var ctrl;
	var tblChkVal = (val) ? 1 : 0;
	var rowClass = "";
    
	for (i=0; ; i++)
	{
		ctrl = document.getElementById(tblID + "_item" + i);
		if (!ctrl) break;
		if (ctrl.disabled) continue;
		ctrl.checked = val;
		tblChk[tid][i] = tblChkVal;
        rowClass = (i % 2) ? "row" : "row2";
		document.getElementById(tblID + "_tr" + i).className = (val) ? "selected" : rowClass;
	}
}
function _lstCheckClearAll(tblID)
{
	var tid = tblID.substr(1);	
	var ctrl;
	document.getElementById(tblID + "_itemall").checked = 0 ;
	for (i=0; ; i++)
	{
		ctrl = document.getElementById(tblID + "_item" + i);
		if (!ctrl) break;
        if (!tblChk[tid][i]) 
        {
            ctrl.checked = 0;
            document.getElementById(tblID + "_tr" + i).className = (i % 2) ? "row" : "row2";
        }
        else
        {
            ctrl.checked = "checked";
            document.getElementById(tblID + "_tr" + i).className = "selected";
        }
	}
}
</script>';
	}
}



function SetPageNavi($curr, $total, $callback, $prev_msg='PREV', $next_msg='NEXT', $pages = 10)
{
    global $msgPageNumError;
	// $pages = 10;

    $prev_msg = "Prev";
    $next_msg = "Next";
    
	$h = ($curr > 1) ? "<span class=PageNavItem><a href='javascript:$callback($curr-1)'>$prev_msg</a></span>" : "<span class=PageNavItem>$prev_msg</span>";

	$start = ($curr < $pages) ? 1 : $start = $curr - (int)($pages / 2);
	$end = $start + $pages - 1;
	if ($end > $total) $end = $total;

	for ($i=$start; $i<=$end; $i++)
	{
		if ($i == $curr)
			$h .= "<span class=PageCurr>$i</span>";
		else
			$h .= "<span class=PageNavItem><a href='javascript:$callback($i)'>$i</a></span>";
	}

	$h .= ($curr < $total) ? "<span class=PageNavItem><a href='javascript:$callback($curr+1)'>$next_msg</a></span>" : "<span class=PageNavItem>$next_msg</span>";

    $pageNav_js = "";
    if ($total > $pages)
    {
        $h .= "
               <span class=PageNavItem>
                  Go: <input type=text id='PageCombo' onclick='showPageCombo(); this.select()' onkeypress='pageComboEnter(event)' value='$curr'>
                  / $total
               </span>
           ";
        
        $pageNav_js = "   
            <script src='/sys/lib/js/combo.js'></script>
            <script>
                function pageComboEnter(e)
                {
                    var e = (!e) ? window.event : e;    
                    var key = e.keyCode;
                    switch(key)
                    {
                        // enter
                        case 13:			        
                        		goPage('PageCombo', \$V('PageCombo'));	
                                e.returnValue = false;
                                e.cancelBubble = true;
                        	    return true;
                        		break;
                        default:
                        	return true;
                    }
                }
                function showPageCombo()
                {
                    var comboList = new Array($total);
                	var i=0;
                	for(var j=1; j<=$total; j++)
                	{
                		comboList[i++] = j;
                	}
                	showCombo('PageCombo', comboList, 'goPage')
                }
                function goPage(comboName, val)
                {
                    if (!\$isInt(val) || val < 1 || val > $total)
                    {
                        alert('$msgPageNumError');
                        \$(comboName).focus();
                        \$(comboName).select();
                        return;                        
                    }
                    $callback(val);
                }
            </script>
        ";
    }
    
    echo "$pageNav_js <span id=PageNav> $h </span>";
}
?>
