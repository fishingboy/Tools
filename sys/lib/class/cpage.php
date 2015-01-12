<?    
    function createPage($curr, $total, $url, $psize=0, $prev_msg='Prev', $next_msg='Next', $pages=10)
    {
        $h = "<span class=pageBox>";
        $h .= createPageHtml($curr, $total, $url, $psize, $prev_msg, $next_msg, $pages);
        $h .= "</span>";
        return $h;
    }
    function createPageHtml($curr, $total, $url, $psize=0, $prev_msg='Prev', $next_msg='Next', $pages=10)
    {
        global $msgPageNumError, $PAGE_SIZE, $msgPageSize;    
        $prev_msg = ($prev_msg) ? $prev_msg : "Prev";
		$next_msg = ($next_msg) ? $next_msg : "Next";
    	$connect_f = (stristr($url, "?")) ? "&" : "?";
    	$prev_i = $curr - 1;
    	
    	$h = ($curr > 1) ? "<span class=item><a href='{$url}{$connect_f}&page=$prev_i'>$prev_msg</a></span>" : "<span class=item>$prev_msg</span>";
    	$start = ($curr < $pages) ? 1 : $start = $curr - (int)($pages / 2);
    	$end = $start + $pages - 1;
    	if ($end > $total) $end = $total;
    
    	for ($i=$start; $i<=$end; $i++)
    	{
    		if ($i == $curr)
    			$h .= "<span class=curr>$i</span>";
    		else
    			$h .= "<span class=item><a href='{$url}{$connect_f}page=$i'>$i</a></span>";
    	}
    	$next_i = $curr + 1;
    	
    	$h .= ($curr < $total) ? "<span class=item><a href='{$url}{$connect_f}page=$next_i'>$next_msg</a></span>" : "<span class=item>$next_msg</span>";
        
        if ($total > $pages)
        {
            $h .= "
                   <span class=item>
                      Go: <input type=text id='PageCombo' class=input onclick='showPageCombo(); this.select()' onkeypress='pageComboEnter(event)' value='$curr'>
                      / $total
                   </span>
               ";
        }
        
        //=== page size control ===
        if ($psize && ($total > 1 || $psize != $PAGE_SIZE))
        {           
            $h .= " |  $msgPageSize: <select id='pageSize' onchange='changePageSize()'>";
            if (strpos(",50,100,200,500,1000,", ",$PAGE_SIZE,") === false)
            {
                $h .= ($psize == $PAGE_SIZE) ? "<option value=$PAGE_SIZE selected>$PAGE_SIZE</option>" : "<option value=$PAGE_SIZE>$PAGE_SIZE</option>"; 
            }
            $h .= ($psize == 50) ? "<option value=50 selected>50</option>" : "<option value=50>50</option>"; 
            $h .= ($psize == 100) ? "<option value=100 selected>100</option>" : "<option value=100>100</option>"; 
            $h .= ($psize == 200) ? "<option value=200 selected>200</option>" : "<option value=200>200</option>"; 
            $h .= ($psize == 500) ? "<option value=500 selected>500</option>" : "<option value=500>500</option>"; 
            $h .= ($psize == 1000) ? "<option value=500 selected>1000</option>" : "<option value=1000>1000</option>"; 
            $h .= "</select>";
        }                   
        // ===
        $js = "
            <script>
                var comboTotal = $total;
                var comboUrl = \"{$url}{$connect_f}page=\";
                var comboValue = '';
            </script>
        ";
    	return "$js <span class=page>$h</span>";
    }
?>


<script>
    function pageComboEnter(e)
    {
        var e = (!e) ? window.event : e;    
        var key = e.keyCode;
        switch(key)
        {
            // enter
            case 13:			        
            		goPage('PageCombo', $V('PageCombo'));	
                    e.returnValue = false;
                    e.cancelBubble = true;
            	    return true;
            		break;
            default:
            	return true;
        }
    }
    function showPageCombo(total)
    {
        var comboList = new Array(comboTotal);
    	var i=0;
    	for(var j=1; j<=comboTotal; j++)
    	{
    		comboList[i++] = j;
    	}
    	showCombo('PageCombo', comboList, 'goPage', 'top')
    }
    function goPage(comboName, val)
    {
        if (!$isInt(val) || val < 1 || val > comboTotal)
        {
            alert('<?= $msgPageNumError ?>');
            $(comboName).focus();
            $(comboName).select();
            return;                        
        }
        window.location.href = comboUrl + val;
    }
    function changePageSize()    
    {
        var ps = $V("pageSize");
        window.location.href = comboUrl + "1&psize=" + ps;
    }
                    
                    
                    
    function showPercentCombo()
    {
    	var comboList = new Array(11);
    	var i=0;
    	for(var j=100; j>=0; j-=10)
    	{
    		comboList[i++] = j;
    	}
    	showCombo("fmScorePercent", comboList, "comboReturn", "top")
    }
    
    //combo
    function showCombo(comboName, values, cb, align)
    {
    	$hidePopup();
        $(comboName).setAttribute("autocomplete","off");
        
    	$EvtListener(document, "mouseup", comboEventHide);
    	$EvtListener(document, "keyup"  , comboEventHide);
    
		align = (align) ? align : "bottom";
        var maxHeight = 200;
    	var a = $area($(comboName));
        var curr = -1;
        comboValue = $V(comboName);
        
        //drop list.
    	var _body = "<div id=combo class=combo style='width:" + (a.width-2) + "px; overflow:auto' onmouseup='$EvtCancelBubble(event)'>";
    	for (var i=0; i<values.length; i++)
    	{
            if (values[i].toString() == comboValue)
            {
                curr = i;
                _body += "<div id=comboItem" + i + " class=curr onclick='" + cb + "(\"" + comboName + "\", " + values[i] + ")' >" + values[i] + "</div>";
            }
            else
                _body += "<div id=comboItem" + i + " class=combo onclick='" + cb + "(\"" + comboName + "\", " + values[i] + ")' onmouseover='this.className=\"over\"' onmouseOut='this.className=\"combo\"'>" + values[i] + "</div>";
    	}
    	_body += "</div>";
    
        var browserScreenHeight = $getBrowserSize().height;
        if (document.body.scrollHeight > browserScreenHeight) 
            var bodyHeight = document.body.scrollHeight;
        else
            var bodyHeight = browserScreenHeight;
    	$showPopup2(_body, a.left, a.top+a.height, a.width-2, 50);
    
        //limit height.
        var combo_ctrl = $("combo");
        if (combo_ctrl.offsetHeight > maxHeight)
        {
            combo_ctrl.style.height = maxHeight + "px";
            combo_ctrl.style.width = a.width + 15;
        }
    
        //overflow.  
        var a2 = $area(combo_ctrl);
        var newTop = a2.top - a2.height - a.height - 4;    
        if ( (align == "top") || ((a2.top + a2.height > bodyHeight) && newTop > 0) )
        {
            $("_popup").style.top = newTop + "px";
            if ($("_popupMask")) $("_popupMask").style.top = newTop  + "px";
        }
        
        //scrolling.
        if (curr != -1) $("combo").scrollTop = $("comboItem" + curr).offsetTop;
    }
    
    function comboReturn(comboName, val)
    {
    	$(comboName).value=val;		
    	$hidePopup();
    }	
    
    function comboEventHide(e)
    {
    	var _hide   = false;
        var _cancel = false;
    
        var evt = (window.event)   ? event : e;
        var src = (evt.srcElement) ? evt.srcElement.id : evt.target.id;
        
    	if (gBrowser.isIE) { var _key=evt.keyCode; var _button=evt.button; if(_button==1 || _key == 27) _hide=true; if (_key == 27) _cancel=true; }
    	if (gBrowser.isNS) { var _key=evt.keyCode; var _button=evt.button; if(_button==0 || _key == 27) _hide=true; if (_key == 27) _cancel=true; }
    
        if (_cancel)
        {
            $(src).value = comboValue;
            $(src).blur();
        }
        if (_hide)
    	{
    		if (gBrowser.isIE) { document.detachEvent("onkeyup",comboEventHide); document.detachEvent("onmouseup", comboEventHide);}
    		if (gBrowser.isNS) { document.removeEventListener("keyup", comboEventHide, false); document.removeEventListener("mouseup", comboEventHide, false);}
    		$hidePopup();
    	}
    }
</script>





<script>
    function createPage(curr, total, func)
	{
		var pages = 10, _html = "";
		_html += (curr > 1) ? "<span class=item><a href='JavaScript:" + func + ", " + (curr-1) + ")'>Prev</a></span>" : "<span class=item>Prev</span>";
		
		var start = (curr < pages) ? 1 : curr - pages / 2;
		var end   = start + pages - 1;
		if (end > total) end = total;
		
		for (var i=start; i<=end; i++)
		{
			if (i == curr)
				_html += "<span class=curr>" + i + "</span>";
			else
				_html += "<span class=item><a href='JavaScript:" + func + ", " + i + ")'>" + i + "</a></span>";
		}
		_html += (curr < total) ? "<span class=item><a href='JavaScript:" + func + ", " + (curr+1) + ")'>Next</a></span>" : "<span class=item>Next</span>";
		
		return "<span class=page>" + _html + "</span>";
	}
</script>