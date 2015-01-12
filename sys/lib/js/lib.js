function trim(str)
{
    if (str == undefined || str == null) return "";
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

function checkPriv(courseID, privs)
{
    privs = (privs) ? privs : "a,as,s";

    var obj = $syncload("/sys/http_check_priv.php", {courseID:courseID, privs:privs});
    if (!obj) return true;
    
    if (obj.ret.status == "false")
    {
        alert(obj.ret.msg);
        return false;
    }
    return true;
}

function checkSeccode(seccode, sname)
{
    sname = (sname) ? sname : "";
    var obj = $syncload("/sys/http_check_seccode.php", {seccode:seccode, sname:sname});
    if (!obj) return true;
    
    if (obj.ret.status == "false")
    {
        alert(obj.ret.msg);
        return false;
    }
    return true;
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
function insertSWF(url, w, h, id, WMode)
{
	var mode = WMode || "transparent";
    var _id = (id) ? "id=" + id : "";
    var _name = (id) ? "name=" + id : "";
    var _html  = "<object " + _id + " classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,18,0' width='" + w + "' height='" + h + "'>";
        _html +=    "<param name='allowScriptAccess' value='always' />";
        _html +=    "<param name='allowFullScreen' value='true' />";
        _html +=    "<param name='movie' value='" + url + "' />";
        _html +=    "<param name='quality' value='high' />";
        _html +=    "<param name='bgcolor' value='#fff' />";
        _html +=    "<param name='WMode' value='" + mode + "' />";
        _html +=    "<embed " + _name + " src='" + url + "' swliveconnect='true' quality='high' allowFullScreen='true' bgcolor='#fff' width='" + w + "' height='" + h + "' allowScriptAccess='always' WMode='" + mode + "' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />";
        _html += "</object>";
    return _html;
}
function stringByteLength(str)
{   
    var arr = str.match(/[^\x00-\xff]/ig);   
    return  arr == null ? str.length : str.length + arr.length;   
}

function stringCharLength(str)
{   
    var arr;
    if (!gBrowser.isIE) arr = str.match(/\n/ig);   
    return  arr == null ? str.length : str.length + arr.length;
}
function $serialize (mixed_value) 
{
    // Returns a string representation of variable (which can later be unserialized)  
    // 
    // version: 1103.1210
    // discuss at: http://phpjs.org/functions/serialize    // +   original by: Arpad Ray (mailto:arpad@php.net)
    // +   improved by: Dino
    // +   bugfixed by: Andrej Pavlovic
    // +   bugfixed by: Garagoth
    // +      input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)    // +   bugfixed by: Russell Walker (http://www.nbill.co.uk/)
    // +   bugfixed by: Jamie Beck (http://www.terabit.ca/)
    // +      input by: Martin (http://www.erlenwiese.de/)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // +   improved by: Le Torbi (http://www.letorbi.de/)    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
    // +   bugfixed by: Ben (http://benblume.co.uk/)
    // -    depends on: utf8_encode
    // %          note: We feel the main purpose of this function should be to ease the transport of data between php & js
    // %          note: Aiming for PHP-compatibility, we have to translate objects to arrays    // *     example 1: serialize(['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
    // *     example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
    // *     returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'
    var _utf8Size = function (str) {        var size = 0,
            i = 0,
            l = str.length,
            code = '';
        for (i = 0; i < l; i++) {            code = str.charCodeAt(i);
            if (code < 0x0080) {
                size += 1;
            } else if (code < 0x0800) {
                size += 2;            } else {
                size += 3;
            }
        }
        return size;    };
    var _getType = function (inp) {
        var type = typeof inp,
            match;
        var key; 
        if (type === 'object' && !inp) {
            return 'null';
        }
        if (type === "object") {            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            match = cons.match(/(\w+)\(/);            if (match) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = ''; 
    switch (type) {
    case "function":
        val = "";
        break;    case "boolean":
        val = "b:" + (mixed_value ? "1" : "0");
        break;
    case "number":
        val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;        break;
    case "string":
        val = "s:" + _utf8Size(mixed_value) + ":\"" + mixed_value + "\"";
        break;
    case "array":    case "object":
        val = "a";
/*
            if (type == "object") {
                var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);                if (objname == undefined) {
                    return;
                }
                objname[1] = this.$serialize(objname[1]);
                val = "O" + objname[1].substring(1, objname[1].length - 1);            }
            */
        var count = 0;
        var vals = "";
        var okey;        var key;
        for (key in mixed_value) {
            if (mixed_value.hasOwnProperty(key)) {
                ktype = _getType(mixed_value[key]);
                if (ktype === "function") {                    continue;
                }
 
                okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
                vals += this.$serialize(okey) + this.$serialize(mixed_value[key]);                count++;
            }
        }
        val += ":" + count + ":{" + vals + "}";
        break;    case "undefined":
        // Fall-through
    default:
        // if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
        val = "N";        break;
    }
    if (type !== "object" && type !== "array") {
        val += ";";
    }    return val;
}