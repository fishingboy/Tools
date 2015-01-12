function trim(str)
{
    return str.replace(/^\s+/g, '').replace(/\s+$/g, '');
}

function windowOpen(_url, _title, _width, _height, _scrollbars, _toolbar, _menubar, _location)
{
    var posX = screen.width / 2 - (_width / 2);
    var posY = screen.height / 2 - (_height / 2);
    
    window.open(_url, _title, "left=" + posX + ",top=" + posY + ",height=" + _height + ",width=" + _width + ",scrollbars="+ _scrollbars + ",toolbar="+ _toolbar + ",menubar=" + _menubar + ",location=" + _location);
}

function window_open(_url, _width, _height)
{
    var posX = screen.width / 2 - (_width / 2);
    var posY = screen.height / 2 - (_height / 2);

    window.open(_url, "xms" , "left=" + posX + ",top=" + posY + ",height=" + _height + ",width=" + _width + ",scrollbars=yes,toolbar=no,menubar=no,location=no");
}

function checkEmailFormat(obj) 
{
    if (obj.value.length > 0)
    {    
        at = obj.value.indexOf("@");
        dot = obj.value.indexOf(".",at);
        len = obj.value.length;
        comma = obj.value.indexOf(",");
        space = obj.value.indexOf(" ");
        lastToken = obj.value.lastIndexOf(".") + 1;
        if ((at <= 0) || (dot <= (1+1)) || (comma != -1) || (space != -1) || (len-lastToken < 2) || (len-lastToken > 3)) 
        {
            obj.focus();
            return false; // format error
        }    
    } 
    else 
    {
        obj.focus();
        return false; // no data
    }
    return true;
}

function returnSelection(theRadio) 
{ 
	var selection=1; 
	for(var i=0; i < theRadio.length; i++) 
	{ 
        if(theRadio[i].checked) 
        { 
                selection=theRadio[i].value; 
                return selection; 
        } 
	} 
		return selection;		
} 

function swapImgSrc(el,which)
{
	el.src=el.getAttribute(which||"origsrc");
} 

function isHtmlFile(filename)
{
    if (filename == "") return false;
    
    var rep = /.*(htm|html|mht)$/;
    
    if (filename.match(rep))   
    {	            
        return true;
    } 
    else
    {
        return false;
    }    
}

function insertLogoSWF(url, w, h) { document.write(insertSWF(url, w, h)); }
function insertSWF(url, w, h, id)
{
    var _id = (id) ? "id=" + id : "";
    var _name = (id) ? "name=" + id : "";
    return '<div>\
                <object ' + _id + ' classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,18,0" width="' + w + '" height="' + h + '">\
                    <param name="allowScriptAccess" value="always"/>\
                    <param name="allowFullScreen" value="true" />\
                    <param name="movie" value="' + url + '"/>\
                    <param name="quality" value="high"/>\
                    <param name="bgcolor" value="#fff"/>\
                    <param name="WMode" value="transparent"/>\
                    <embed ' + _id + ' src="' + url + '" swliveconnect="true" quality="high" allowFullScreen="true" bgcolor="#fff" width="' + w + '" height="' + h + '" allowScriptAccess="always" WMode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"/>\
                </object>\
             </div>';
}
function stringByteLength(str)
{   
    var arr = str.match(/[^\x00-\xff]/ig);   
    return  arr == null ? str.length : str.length + arr.length;   
}

function stringCharLength(str)
{   
    var arr;
    if (gBrowser.isNS) arr = str.match(/\n/ig);   
    return  arr == null ? str.length : str.length + arr.length;
}