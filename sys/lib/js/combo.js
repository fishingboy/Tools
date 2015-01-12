var comboValue = "";

function showPercentCombo()
{
	var comboList = new Array(11);
	var i=0;
	for(var j=100; j>=0; j-=10)
	{
		comboList[i++] = j;
	}
	showCombo("fmScorePercent", comboList, "comboReturn")
}

//combo
function showCombo(comboName, values, cb)
{
	$hidePopup();
    $(comboName).setAttribute("autocomplete","off");
    
	$EvtListener(document, "mouseup", comboEventHide);
	$EvtListener(document, "keyup"  , comboEventHide);

    var maxHeight = 200;
	var a = $area($(comboName));
    var curr = -1;
    comboValue = $V(comboName);
    
    //drop list
	var _body = "<div id=combo class=combo style='width:" + (a.width-2) + "px; overflow:auto' onmouseup='$EvtCancelBubble(event)'>";
	for (var i=0; i<values.length; i++)
	{
        if (values[i].toString() == comboValue)
        {
            curr = i;
            _body += "<div id=comboItem" + i + " class=comboCurr onclick='" + cb + "(\"" + comboName + "\", " + values[i] + ")' >" + values[i] + "</div>";
        }
        else
            _body += "<div id=comboItem" + i + "class=comboOut onclick='" + cb + "(\"" + comboName + "\", " + values[i] + ")' onmouseover='this.className=\"comboOver\"' onmouseOut='this.className=\"comboOut\"'>" + values[i] + "</div>";
	}
	_body += "</div>";

    var browserScreenHeight = $getBrowserSize().height;
    if (document.body.scrollHeight > browserScreenHeight) 
        var bodyHeight = document.body.scrollHeight;
    else
        var bodyHeight = browserScreenHeight;

    $showPopup2(_body, a.left, a.top+a.height, a.width-2, 50);

    //height
    var combo_ctrl = $("combo");
    if (combo_ctrl.offsetHeight > maxHeight)
    {
        combo_ctrl.style.height = maxHeight;
        combo_ctrl.style.width = a.width + 15;
    }

    //check if overflow    
    var a2 = $area(combo_ctrl);
    var newTop = a2.top - a2.height - a.height - 4;    
    if ((a2.top + a2.height > bodyHeight) && newTop > 0)
    {
        $("_popup").style.top = newTop;
        if ($("_popupMask")) $("_popupMask").style.top = newTop;
    }    
   
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