<?
$btnOutputJS = 1;
$btnID = 1;
$btnCallback = $mnuCallback = "_nullCallback";

class CButton
{
    var $mnuItem;
    var $mnuData;
    var $mnuEnabled;
    var $id;
    var $caption;
    var $icon;
    var $align;

    function CButton($id, $caption, $icon='')
    {
        global $btnOutputJS;
        global $btnID;

        if ($btnOutputJS == 1)
        {
            $this->OutputJS();
            $btnOutputJS = 0;
        }
        $this->id = $id;
        $this->caption = $caption;
        if ($icon != '') $this->icon = "<img src='$icon' align=absmiddle border=0>";

        $this->mnuItem = 0;
        $this->align = 'bottom';
        $this->mnuData = '';
        $this->mnuEnabled = '';
    }

    function addMenu($caption, $icon='', $id='')
    {
        if ($icon != '') $icon = "<img src='$icon' align=absmiddle border=0>";
		if ($id == '')
			$id = $this->mnuItem;

        $this->mnuData .= ($this->mnuData) ? "," : ""; 
        $this->mnuData .= "{caption:\"$caption\", icon:\"$icon\", id:\"$id\"}";
        $this->mnuEnabled .= "_cmnuEnabled[\"cmnu{$this->id}_{$id}\"] = 1; \n";
        $this->mnuItem ++;
	    return $this->mnuItem;
    }

    function close()
    { 
		$z = '"' . $this->id . '"';
        $type = ($this->mnuItem > 0) ? "<img id=cbtntype{$this->id} value='$this->align' src='/sys/res/icon/cbtn_arrow.gif' align=absmiddle>" : '';
        echo "<span id=cbtn{$this->id} class=cbtn onclick='btnOnclick(event)' onmouseover='_btnMouseover($z, 0)' onmousedown='_btnMousedown($z)' onmouseup='_btnMouseover($z, 0)' onmouseout='_btnMouseout($z)'>{$this->icon} {$this->caption}{$type}</span>\n";

        if ($this->mnuItem > 0) 
        {
            echo "<script>_cmnuData[\"cmnu{$this->id}\"] = [$this->mnuData];</script>\n";
        }
            
		if ($this->mnuEnabled != '')
	        echo "<script>{$this->mnuEnabled}</script>\n";
    }


    function OutputJS()
    {
        global $btnCallback, $mnuCallback;
        echo "
<script>
var cbtnTimer = 0;
var _cmnuEnabled = new Array();
var _cmnuData = new Array();
function _nullCallback(){ }

function btnOnclick(aEvent)
{
	if (navigator.userAgent.indexOf('MSIE') != -1)
		var eID = window.event.srcElement.id;
	else
		var eID = aEvent.target.id;	
	
	
	cbtnHideAll();
	if (eID.indexOf('cmnu') != -1)
	{	
		var mnuID = eID.substr(4, eID.indexOf('_') - 4);
		var itemID = eID.substr(eID.indexOf('_') + 1);
		var id = 'cmnu' + mnuID + '_' + itemID;		
		if (_cmnuEnabled[id] == 1)
		{
			{$mnuCallback}(mnuID, itemID);
		}
		return;
	}
	
	if (eID.indexOf('cbtn') != -1)   //with menu
	{	
		var id = eID.substr(4, 10);  // get button ID
        var items = _cmnuData['cmnu' + id];
		if (items)
		{
            cmnuShow(id);
            return;
		}
			
		{$btnCallback}( eID.substr(4, 10) );
		return;
	}
}

function cbtnHideAll()
{
	for (i=0; i<50;i++)
	{
		var ctrl = document.getElementById('cmnu' + i);
		if (ctrl) 
		{
            \$remove(ctrl);
            
            var ctrl2 = document.getElementById('cmnuMask');
            if (ctrl2) \$remove(ctrl2);
	    }
	}
}

function _mnuGetHeight(id) { return document.getElementById('cmnu' + id).clientHeight; }

function _mnuEnableItem(mnuID, itemID, flag)
{
	var id = 'cmnu' + mnuID + '_' + itemID;
	_cmnuEnabled[id] = flag;
}

function _mnuMouseover(mnuID, itemID)
{
	var id = 'cmnu' + mnuID + '_' + itemID;
	var ctrl = document.getElementById(id);
	var style = (_cmnuEnabled[id] == 1) ? 'cbtn_menuOver' : 'cbtn_menuDisabled';
	ctrl.className = style;

	if (cbtnTimer != 0) window.clearTimeout(cbtnTimer);
}

function _mnuMouseout(mnuID, itemID)
{
	var id = 'cmnu' + mnuID + '_' + itemID;
	var ctrl = document.getElementById(id);
	var style = (_cmnuEnabled[id] == 1) ? 'cbtn_menu' : 'cbtn_menuDisabled';
	ctrl.className = style;

	if (cbtnTimer != 0)
	{
		window.clearTimeout(cbtnTimer);
		cbtnTimer = window.setTimeout('cbtnHideAll()', 1000);
	}
}

function cbtnSetCaption(id, value)
{
	var ctrl = document.getElementById('cbtn' + id);
	if (ctrl)
	{
		var img = '';
		var pos = ctrl.innerHTML.indexOf('>');
		if (pos > 0) img = ctrl.innerHTML.substr(0, pos+2);

		ctrl.innerHTML = img + value;
	}
}

function cbtnShow(id) { _btnSetDisplay(id, 'block'); }
function cbtnHide(id) { _btnSetDisplay(id, 'none'); }
function _btnSetDisplay(id, value)
{
	var ctrl = document.getElementById('cbtn' + id);
	if (ctrl) ctrl.style.display = value;
}

function _btnMousedown(id)
{
	document.getElementById('cbtn' + id).className = 'cbtnDown';
}

function _btnMouseout(id)
{
	document.getElementById('cbtn' + id).className = 'cbtn';
	var mnuCtrl = document.getElementById('cmnu' + id);
	if (mnuCtrl)
		cbtnTimer = window.setTimeout('cbtnHideAll()', 1000);
}

function _btnMouseover(id)
{
	if (cbtnTimer != 0) window.clearTimeout(cbtnTimer);

	var ctrl = document.getElementById('cbtn' + id);
	var _pos = \$area(ctrl);
    ctrl.className = 'cbtnOver';	

	cbtnHideAll();

	var ctrl2 = document.getElementById('cbtntype' + id);
	if (!ctrl2) return;	

    cmnuShow(id);
}

function cmnuShow(id)
{ 
    var mnuID = 'cmnu' + id;
    var items = _cmnuData[mnuID];
    
    var _body  = \"<table cellspacing=1 class=cbtn_menu style='table-layout:auto; display:block'>\";
    for (var i=0; i<items.length; i++)
    {
        _id = 'cmnu' + id + '_' + items[i].id;
        _body += '<tr><td id=' + _id + ' class=cbtn_menu ';
        _body += 'onclick=\'btnOnclick(event, 0)\' onmouseover=\'_mnuMouseover(\\\"' + id + '\\\", \\\"' + items[i].id + '\\\")\' onmouseout=\'_mnuMouseout(\\\"' + id + '\\\", \\\"' + items[i].id + '\\\")\'>';
        _body += items[i].icon + ' ' + items[i].caption + '</td></tr>';
    }
    _body += \"</table>\";
    
    var _el = \$E('DIV');
    _el.id = mnuID;
    _el.style.position = 'absolute';
    _el.style.display = 'block';
    _el.innerHTML = _body;
    \$append(_el);

    var a = \$area(\$('cbtn' + id));
    var a2 = \$area(\$(mnuID));
    
    var ctrl2 = document.getElementById('cbtntype' + id);
    var align = ctrl2.getAttribute('value');	
    switch (align)
    {
        case 'top':
            var _left = a.left;
            var _top  = a.top - a2.height -1;
            break;
        case 'bottom':
            var _left = a.left;
            var _top  = a.top + a.height + 1;
            break;
        case 'right':
            var _left = a.left + a.width + 1;
            var _top  = a.top + 1;
            break;
    }	
    _el.style.left = _left + 'px';
    _el.style.top  = _top + 'px';
    
    if (gBrowser.isIE)    
    {
        var a = \$area(_el);
        var _el = \$E('DIV');

        _el.id = 'cmnuMask';
        _el.className = 'popupMask';
        \$append(_el);

        _el.style.width  = a.width  + 'px';
        _el.style.height = a.height + 'px';
        _el.style.left = _left + 'px';
        _el.style.top  = _top + 'px';
        _el.innerHTML = \"<iframe id=if_popupMask style='width:\" + a.width + \"px; height:\" + a.height + \"px'></iframe>\";
    }
}
</script>
";
    }
}
?>