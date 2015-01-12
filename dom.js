var $mnu = null;
var $modalarg;
var $ie = (navigator.appName.indexOf("Explore") > 0) ? true : false;

document.onclick = hideall;
function hideall() { try { if ($mnu) {$mnu.$hide(); $mnu = null;} } catch (e) {}; }

function $(id)    { return (document.getElementById(id)) ? document.getElementById(id) : document.getElementsByTagName(id)[0]; }
function $V(id)   { return $(id).value.replace(/^\s+/g, '').replace(/\s+$/g, ''); }
function $S(id)   { return $(id).style; }
function $E(tag)  { return document.createElement(tag); }
function $A(tag)   { var el = $E(tag); $append(el); return el; }

function $append(el) { $("body").appendChild(el); }
function $remove(el) { $("body").removeChild(el); }
function $int(val)   { return (val == "" || val == "undefined") ? 0 : parseInt(val, 10); }
function $rnd()      { return "id" + $int(Math.random() * 65536) + "." + $int(Math.random() * 65536); }
function $size(val)
{
    var k = $int(val / 102.4) / 10;
    if (k > 1024 * 1024) return $int(k / (1024 * 102.4)) / 10 + "GB";
    if (k > 1024) return ($int(k / 102.4) / 10) + "MB";
    return k + "KB";
    // return (k > 1000) ? ($int(k / 102.4) / 10) + "M" : k + "K";
}

function $isNum(str)
{
    isNumber=/^\d+(\.\d+)?$/;
    return (isNumber.test(str));
}
function $isInt(str)
{
    isInteger=/^\d+$/;
    return (isInteger.test(str));
}

function $addEvt(el, evt, cb, e)
{
	if(typeof(e)=="undefined") e=false;
	(el.addEventListener) ? el.addEventListener(evt,cb,e) : el.attachEvent("on"+evt,cb)
}


function $ieTableTricky()
{
    var s = "";
    if ($ie && $getCss("content", "margin", "margin").indexOf("px") >= 0) return "style='height:1%'";
    else return "";
}
function $getCss(id, cssproperty, csspropertyNS)
{
    var el = $(id);
    if (el.currentStyle) return el.currentStyle[cssproperty];

    if (window.getComputedStyle)
    {
        var elstyle=window.getComputedStyle(el, "")
        return elstyle.getPropertyValue(csspropertyNS);
    }
}


function $addslashes(_str) { return _str.replace('/(["\'\])/g', "\\$1").replace('/\0/g', "\\0"); }
function $nl2br(_str) { return _str.replace(/\n/g, "&nbsp;<br>"); }
function $br2nl(_str) { return _str.replace(/<br>/g, "\n").replace(/<BR>/g, "\n"); }

function $imgLink(_src, _url, _w, _h, _css, _target)
{
    _w = (_w) ? " width=" + _w: "";
    _h = (_h) ? " height=" + _h: "";
    _css = (_css) ? _css : "image";
    var _mode = (_target) ? " target=" + _target : " ";
    return " <a href='" + _url + "'" + _mode + "><img " + _w + " " + _h + " onmouseover='this.className=\"" + _css + "Over\"' onmouseout='this.className=\"" + _css + "\"' class=" + _css + " src='" + _src + "'></a>";
}

function $setContent(_html)
{
    $("content").innerHTML = _html;
    document.body.scrollTop = 0;
}
function $filter(_txt)
{
    return _txt.replace(/\$sload/g, "$s<span></span>load").replace(/getkey.php/g, "getkey<span></span>.php");
}
function $makehtml(_text)
{
    var h = _text.replace(/&/g,"&amp;");
    h = h.replace(/</g,"&lt;");
    h = h.replace(/>/g,"&gt;");
    h = h.replace(/\"/g, "&quot;");
    h = h.replace(/'/g, "&#039;");
    h = h.replace(/\r\n/g," &nbsp;<br>");
    h = h.replace(/\n/g," &nbsp;<br>");
    h = h.replace(/\r/g," &nbsp;<br>");
    h = h.replace(/<br> /g,"<br>&nbsp;");
    h = h.replace(/  /g," &nbsp;");

    return(h);
}

function $getDays(_year, _month)
{
    var _daysInMonth = new Array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (_month == 2)
	{
		if ( (_year%4==0) && (_year%100!=0) ) return 29;
		if (_year%400 == 0) return 29;
    }
	return _daysInMonth[_month];
}

function $reload() { window.location.reload(); }
function $getArg2(_name) { return $getArg($getHash(), _name); }
function $getArg(_page, _name)
{
    var _params = _page.split("__");
    for (var i=0; i<_params.length; i++)
    {
        var _pos =_params[i].indexOf("_");
        var t = _params[i].substr(0, _pos);
        if (_name == t) return _params[i].substr(_pos+1);
    }
    return "";
}


function $setCookie(_name, _value, _days)
{
    var ex = "";
    if (_days > 0)
    {
        var _today = new Date();
        var _exdate = new Date(_today.getTime() + 3 * 1000 * 60 * 60 * 24);
        ex = ";expires=" + _exdate.toGMTString() + ";"
    }
    document.cookie = _name + "=" + _value + ex + ";path=/;";
}
function $delCookie(_name) { document.cookie = _name + "=" + ";expires=Thu, 01-Jan-1970 00:00:01 GMT;path=/;"; }
function $getCookie(_name)
{
    var _aCookie = document.cookie.split("; ");
    for (var i=0; i < _aCookie.length; i++)
    {
        var _aCrumb = _aCookie[i].split("=");
        if (_name == _aCrumb[0]) return unescape(_aCrumb[1]);
    }
    return "";
}

function m_scrollTop()     { return Math.max(document.documentElement.scrollTop, document.body.scrollTop); }
function m_scrollLeft()    { return Math.max(document.documentElement.scrollLeft, document.body.scrollLeft); }
function m_scrollWidth()   { return Math.max(document.documentElement.scrollWidth, document.body.scrollWidth); }
function m_scrollHeight()  { return Math.max(document.documentElement.scrollHeight, document.body.scrollHeight); }

// modal dialog
function $getDoctype()
{
    if (gBrowser.isIE)
    {
        var doctype = document.all[0].text;
        var chk = /^<!DOCTYPE/i;
        if (chk.test(doctype)) 
            return doctype;
        else
            return false;
    }
    else
        if (document.doctype)
            return document.doctype.publicId;  
        else
            return false;
}
function $getBrowserSize()
{
    var width, height;
    if (gBrowser.isIE) 
    {
        if ($getDoctype())
        {
            width = document.documentElement.clientWidth;
            height = document.documentElement.clientHeight;
        }
        else
        {
            width = document.getElementsByTagName('body')[0].clientWidth;
            height = document.getElementsByTagName('body')[0].clientHeight;
        }
    }
    else
    {
        width = window.innerWidth;
        height = window.innerHeight;
    }
    return {width:width, height:height};
}
function $closeModal()
{
    var scroll = (document.body.scrollHeight > document.body.clientHeight) ? "scroll" : "auto";
    if ($getDoctype())
    {
        document.body.parentNode.style.overflowY = scroll;
    }
    else
    {
        $("body").style.scroll = "yes";
        $("body").style.overflowY = scroll;
    }
    $remove($("_modal"));
}
function $showModal(_title, _url, w, h, cb, hint)
{
    $("body").style.height = "100%";
    if ($getDoctype())
    {	
        document.body.parentNode.style.overflowY = "hidden";
    }
    else
    {
        $("body").style.scroll = "no";
        $("body").style.overflowY = "hidden";
    }
    
    // get browser size
    var browserScreen = $getBrowserSize();
    var maxWidth  = browserScreen.width;
    var maxHeight = browserScreen.height;

    var el = $E("div");
    el.id = "_modal";
    el.className = "modal";
    $append(el);
    
    el.style.width = maxWidth + "px";
    el.style.height = maxHeight + "px";
    el.style.top = m_scrollTop() + "px";

    maxWidth  -= 30;
    maxHeight -= 50;
    if (w > maxWidth)
    {
        var iframeWidth = w;
        w = maxWidth;
    }
    h = (h > maxHeight) ? maxHeight : h;

    var a = $area(el);
    if (!_title) var _title = "PowerCam.cc";
    if (!w) var w = 400;
    if (!h) var h = 300;
    var x = (a.width - w) / 2;
    var y  = (a.height - h) / 2 + m_scrollTop(); if (y < 0) y = 0;

    var _body = "<div style='height:" + (h-30) + "px'><iframe id=if1 name=if frameborder=0 width=100% height=100%></iframe></div>";
    $showPopup(_title, _body, x, y, w, h, cb, hint);
    $("if1").src = _url;
}


function $hidePopup()
{
    var el = $("_popup");
    if (el)
    {
        $hide(el);
        if (el.$cb) el.$cb.call(el);
        $remove(el);
    }
}
function $showPopup(_title, _body, x, y, w, h, cb, hint)
{
    if (!hint) hint = "";
    $hidePopup();
    var el = $E("DIV");
    el.id = "_popup";
    el.className = "popup";
    if (cb) el.$cb = cb;


    el.style.width = w + "px";
    if (h) el.style.height = h + "px";
    $append(el);

    var h = "";
    h += "<div style='width:100%; cursor:move' onmousedown='$dragStart(event, \"_popup\")'>";
    h +=     "<div id=popuptitle class=popuptitle style='float:left'>" + _title + " <span class=hint>" + hint + "</span></div>";
    h +=     "<div style='float:right; width:16px'>";
    h +=         "<img title='close' onclick='$hidePopup()' style='cursor:pointer' src='/sys/res/icon/close.gif'>";
    h +=     "</div>";
    h +=     "<div style='clear:both'></div>";
    h += "</div>";
    h += "<div class=popupbody>" + _body + "</div>";
    el.innerHTML = h;

    $show(el, x, y);
}

function $showPopup2( _body, x, y, w, h, cb)
{
	var _el = $E("DIV");
	_el.id = "_popup";
	_el.className = "popup2";
	if (cb) _el.$cb = cb;
	$append(_el);

	var _h = _body;
	_el.innerHTML = _h;

	$show(_el, x, y);
}

function $showPopupMask(el, x, y)
{
    var a = $area(el);
    var _el = $E("DIV");

	_el.id = "_popupMask";
	_el.className = "popupMask";
	$append(_el);
    $EvtListener(el, "resize", $resizePopupMask);

    _el.style.width  = a.width  + "px";
    _el.style.height = a.height + "px";
    _el.style.left = x + "px";
    _el.style.top  = y + "px";
    _el.innerHTML ="<iframe id=if_popupMask style='width:" + a.width + "px; height:" + a.height + "px'></iframe>";
    
    _el.filters[0].Apply();
    _el.filters[0].transition=1;
    _el.style.visibility = "visible";
    _el.filters[0].Play();

}

function $resizePopupMask(event)
{
    var a = $area(window.event.srcElement);
    var _el     = $("_popupMask");
    var _elMask = $("if_popupMask");

    _el.style.width  = _elMask.style.width  = a.width  + "px";
    _el.style.height = _elMask.style.height = a.height + "px";
}

function $hidePopupMask()
{
    var el = $("_popupMask");
    if (el) $remove(el);
}

function $chkDate(date)
{
    var temp = date.split(' ');
    var ary = temp[0].split('-');

    for (var i=0; i<ary.length; i++)
    {
        if (isNaN(ary[i])) return false;
        ary[i] = $int(ary[i]);
    }


    if (ary[0]<=0) return false;
    if (ary[1]<=0 || ary[1]>12) return false;
    if (ary[2]<=0 || ary[2]>m_getDays(ary[0], ary[1])) return false;

    if (temp[1])
    {
        ary = temp[1].split(':');
        for (var i=0; i<ary.length; i++)
        {
            if (isNaN(ary[i])) return false;
            ary[i] = $int(ary[i]);
        }
        if (ary[0]<0 || ary[0]>23) return false;
        if (ary[1]<0 || ary[1]>59) return false;
    }

    return true;
}
function m_getDays(y, m)
{
    var ds = new Array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    if (m > 12) return 0;
    if (m == 2)
    {
     if ( (y%4==0) && (y%100!=0) ) return 29;
     if (y%400 == 0) return 29;
    }
    return ds[m];
}





function $load(_url, _arg, _onload, _onerror) {gAjax.$load(_url, _arg, _onload, _onerror);}
function $syncload(_url, _arg) { return gAjax.$syncload(_url, _arg); }
function $sload(_url, _arg, _onload, _onerror)
{
    var ret = $syncload("/sys/getkey.php", {});
    var enc = base64.encode(gAjax.rc4(str2ba(ret.key), str2ba(utf16to8(gAjax.m_getParam(_arg)))));
    _arg = {encrypt: encodeURIComponent(enc)};
    $load(_url, _arg, _onload, _onerror);
}
var gAjax = {
    m_getReq: function()
    {
        try { return new XMLHttpRequest(); }
        catch(e)
        {
            var _XmlHttpVersions = new Array("MSXML2.XMLHTTP.6.0", "MSXML2.XMLHTTP.5.0", "MSXML2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP");
            for (var i=0; i<_XmlHttpVersions.length; i++)
            {
                try { return new ActiveXObject(_XmlHttpVersions[i]); }
                catch (e) {}
            }
        }
	},
    $syncload: function(_url, _arg)
    {
        var _req = gAjax.m_getReq();

        gAjax.m_setLoading(true);
        _req.open('POST', _url, false);
        _req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        _req.send(gAjax.m_getParam(_arg));
        gAjax.m_setLoading(false);
        var _response = _req.responseText.replace(/^\s+/g, '').replace(/\s+$/g, '');
        return (_response.indexOf("{") == 0) ? eval("(" + _response + ")") : "";
    },
    $load: function(_url, _arg, _onload, _onerror) // asynchronous load
    {
        var _req = gAjax.m_getReq();

        gAjax.m_setLoading(true);
        _req.open('POST', _url, true);
        _req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
        _req.send(gAjax.m_getParam(_arg));

        _req.onreadystatechange = function()
        {
            if (_req.readyState == 4)
            {
                gAjax.m_setLoading(false);
                if (_req.status == 200)
                {
                    var _err = 1
                    // try {
                        // alert(_req.responseText);
                        var obj = eval("(" + _req.responseText + ")");
                        _err = 2
                        _onload.call(this, obj);
                    // } catch (e) { (_err == 1) ? gAjax.m_dataError(_req.responseText) : alert("ajax.load() callback '" + _onload + "' internal error!"); }
                }
                else
                {
                    (_onerror) ? _onerror.call(this) : gAjax.m_defaultError.call(this, _url, _req.status);
                }
            }
        }
    },

    m_getParam: function(_arg)
    {
        var _str = "";
        for (p in _arg)
        {
            if (_str != "") _str = _str + "&";
            _str = _str + p + "=" + _arg[p];
        }
        return _str;
    },

    m_dataError: function(_str)
    {
        alert("data error: " + _str);
    },
    m_defaultError: function(_url, _status)
    {
        if (_status != 0)
            alert("error in http request: " + _status + ", " + _url);
    },

    rc4: function(pwd, str) {
        var pwd_length = pwd.length;
        var data_length = str.length;
        var key = []; var box = [];
        var cipher = [];
        var k;
        for (var i=0; i < 256; i++){
          key[i] = pwd[i % pwd_length];
          box[i] = i;
        }
        for (var j = i = 0; i < 256; i++){
          j = (j + box[i] + key[i]) % 256;
          tmp = box[i];
          box[i] = box[j];
          box[j] = tmp;
        }
        var len = 0;
        for (var a = j = i = 0; i < data_length; i++){
          a = (a + 1) % 256;
          j = (j + box[a]) % 256;
          tmp = box[a];
          box[a] = box[j];
          box[j] = tmp;
          k = box[((box[a] + box[j]) % 256)];
          cipher[len++] = (str[i] ^ k);
        }
        return cipher;
    },

    m_setLoading: function(_flag)
    {
        var _ctrl = $("ajaxs");
        if (!_ctrl)
        {
            _ctrl = $E("DIV");
            _ctrl.id = "ajaxs";
            _ctrl.innerHTML = "Loading";
            _ctrl.className = "loading";
            _ctrl.style.left = $("body").clientWidth - 90 + "px";
            $append(_ctrl);
        }

        _ctrl.style.top = 2 + document.body.scrollTop + "px";
        _ctrl.style.display = (_flag) ? "block" : "none";
    }
}
var base64 = function(){};
base64.classID = function() { return 'system.utility.base64'; };
base64.isFinal = function() { return true; };
base64.encString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
base64.encStringS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';

base64.encode = function(inp, uc, safe) {
  if (arguments.length < 1) return null;
  var readBuf = new Array();
  if (arguments.length >= 3 && safe != true && safe != false) return null;
  var enc = (arguments.length >= 3 && safe) ? this.encStringS : this.encString;
  var b = (typeof inp == "string");
  if (!b && (typeof inp != "object") && !(inp instanceof Array)) return null;
  if (arguments.length < 2)  uc = true;
  if (uc != true && uc != false) return null;
  var n = (!b || !uc) ? 1 : 2;
  var out = '';
  var c = 0;
  var j = 1;
  var l = 0;
  var s = 0;

  for (var i = 0; i < inp.length; i++) {
    c = (b) ? inp.charCodeAt(i) : inp[i];
    for (var k = n - 1; k >= 0; k--) {
      readBuf[k] = c & 0xff;
      c >>= 8;
    }
    for (var m = 0; m < n; m++) {
      l = ((l<<8)&0xff00) | readBuf[m];
      s = (0x3f<<(2*j)) & l;
      l -=s;
      out += enc.charAt(s>>(2*j));
      j++;
      if (j==4) {
        out += enc.charAt(l&0x3f);
        j = 1;
      }
    }
  }
  switch (j) {
    case 2:
      s = 0x3f & (16 * l);
      out += enc.charAt(s);
      out += '==';
      break;
    case 3:
      s = 0x3f & (4 * l);
      out += enc.charAt(s);
      out += '=';
      break;
    default:
      break;
  }

  return out;

}

base64.decode = function(inp, outType, safe, lax) {
  if (arguments.length < 1) return null;
  if (arguments.length < 2) outType = 0 ;
  if (outType != 0 && outType != 1 && outType != 2) return null;
  if (arguments.length >= 3 && safe != true && safe != false) return null;
  var sEnc = (arguments.length >= 3 && safe) ? this.encStringS : this.encString;
  if (arguments.length >= 4 && lax != true && lax != false) return null;
  var aDec = new Object();
  for (var p = 0; p < sEnc.length; p++) vaDec[sEnc.charAt(p)] = p;
  var out = (outType == 0) ? new Array() : '';
  lax = (arguments.length == 4 && lax);
  var l = 0;
  var i = 0;
  var j = 0;
  var c = 0;
  var k = 0;
  var end = inp.length;
  var C = '';
  if (lax) {
    var inpS = '';
    var ignore = false;
    var cnt = 0;
    for (var p = 1; p <= inp.length; p++) {
      c = inp.charAt(end - p);
      if (c == '=') {
        if (!ignore) {
          if (++cnt > 1) ignore = true;
        } else {
          continue;
        }
      } else if (undefined != aDec[c]) {
        if (!ignore) ignore = true;
        inpS = c + inpS;
      }
    }
    for (var p = 0; p <= cnt; p++) {
      if (p == 2) return null;
      if ((inpS.length + cnt)%4 == 0) break;
    }
    if (inpS.length%4==1) return null;
    inp = inpS;
    end = inp.length;
  } else {
    if (inp.length%4 > 0) return null;
    for (var p = 0; p < 2; p++) {
      if (inp.charAt(end - 1) == '=') {
        end--;
      } else {
        break;
      }
    }
  }
  for (i = 0; i < end; i++) {
    l <<= 6;
    if (undefined == (c = aDec[inp.charAt(i)])) return null;
    l |= (c&0x3f);    // append it
    if (j == 0) {
      j++;
      continue;
    }
    if (outType == 2) {
      if (k == 1) {
        out += String.fromCharCode(l>>(2*(3-j)));
        l &= ~(0xffff<<(2*(3-j)));
      }
      k = ++k%2;
    } else {
      if (outType == 0) {
        out.push(l>>(2*(3-j)));
      } else {
        out += String.fromCharCode(l>>(2*(3-j)));
      }
      l &= ~(0xff<<(2*(3-j)));
    }
    j = ++j%4;
  }
  if (outType == 2 && k == 1) return null;

  return out;
}

function utf16to8(str) {
    var out, i, len, c;

    out = "";
    len = str.length;
    for(i = 0; i < len; i++) {
    c = str.charCodeAt(i);
    if ((c >= 0x0001) && (c <= 0x007F)) {
        out += str.charAt(i);
    } else if (c > 0x07FF) {
        out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F));
        out += String.fromCharCode(0x80 | ((c >>  6) & 0x3F));
        out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
    } else {
        out += String.fromCharCode(0xC0 | ((c >>  6) & 0x1F));
        out += String.fromCharCode(0x80 | ((c >>  0) & 0x3F));
    }
    }
    return out;
}

function utf8to16(str) {
    var out, i, len, c;
    var char2, char3;

    out = "";
    len = str.length;
    i = 0;
    while(i < len) {
    c = str.charCodeAt(i++);
    switch(c >> 4)
    {
      case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7:
        // 0xxxxxxx
        out += str.charAt(i-1);
        break;
      case 12: case 13:
        // 110x xxxx   10xx xxxx
        char2 = str.charCodeAt(i++);
        out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F));
        break;
      case 14:
        // 1110 xxxx  10xx xxxx  10xx xxxx
        char2 = str.charCodeAt(i++);
        char3 = str.charCodeAt(i++);
        out += String.fromCharCode(((c & 0x0F) << 12) |
                       ((char2 & 0x3F) << 6) |
                       ((char3 & 0x3F) << 0));
        break;
    }
    }

    return out;
}
function str2ba(str)
{
	var ba = [];
	var len = 0;
  for (var i=0; i<str.length; i++)
  {
  	var ch = str.charCodeAt(i);
    if ((ch < 0) || (ch > 255))
    {
      ba[len++] = ch & 0xff;
      ba[len++] = ch >>> 8;
    }
    else
    	ba[len++] = ch;
  }
  return ba;
}











function $show(el,x,y)
{
    var _bodyw = document.body.offsetWidth - 20; //scrollbar
    var _elw = el.offsetWidth;
    if ((_elw + x) > _bodyw) x = _bodyw - _elw;

    el.style.left = x + "px";
    el.style.top = y + "px";

    if ($ie)
    {
        el.filters[0].Apply();
        el.filters[0].transition=1;
        el.style.visibility = "visible";
        el.filters[0].Play();

        $showPopupMask(el, x, y);
    }
    else
        el.style.visibility = "visible";
}
function $hide(el)
{
    if ($ie)
    {
        el.filters[0].Apply();
        el.filters[0].transition=0;
        el.style.visibility = "hidden";
        el.filters[0].Play();

        $hidePopupMask()
    }
    else
        el.style.visibility = "hidden";
}

function $area(el)
{
    var _ua = navigator.userAgent.toLowerCase();
    var _parent = null;
    var _pos = [];
    var _box;

    if (gBrowser.isIE)    //IE
    {
        _box = el.getBoundingClientRect();
        var _scrollTop = Math.max(document.documentElement.scrollTop, document.body.scrollTop);
        var _scrollLeft = Math.max(document.documentElement.scrollLeft, document.body.scrollLeft);

        var if_ctrl = "";
        if (parent != self)
        {
            for (var i=0; i < parent.frames.length; i++)
            {
                if (parent.frames[i].location == self.location)
                {
                    if_ctrl = parent.document.frames[i].frameElement;
                    break;
                }
            }
        }
        if (if_ctrl && (if_ctrl.frameBorder == "0" || if_ctrl.frameBorder.toLowerCase() == "no"))
            return {left:_box.left + _scrollLeft, top:_box.top + _scrollTop, width:el.offsetWidth, height:el.offsetHeight};
        else
            return {left:_box.left + _scrollLeft -2, top:_box.top + _scrollTop -2, width:el.offsetWidth, height:el.offsetHeight};
    }


    if(document.getBoxObjectFor)    // gecko
    {
        _box = document.getBoxObjectFor(el);
        var _borderLeft = (el.style.borderLeftWidth) ? $int(el.style.borderLeftWidth) : 0;
        var _borderTop = (el.style.borderTopWidth) ? $int(el.style.borderTopWidth) : 0;
        _pos = [_box.x - _borderLeft, _box.y - _borderTop];
    }
    else    // safari & opera
    {
        _pos = [el.offsetLeft, el.offsetTop];
        _parent = el.offsetParent;
        if (_parent != el)
        {
            while (_parent)
            {
                _pos[0] += _parent.offsetLeft;
                _pos[1] += _parent.offsetTop;
                _parent = _parent.offsetParent;
            }
        }
        if (_ua.indexOf('opera') != -1 || ( _ua.indexOf('safari') != -1 && el.style.position == 'absolute' ))
        {
            _pos[0] -= document.body.offsetLeft;
            _pos[1] -= document.body.offsetTop;
        }
    }

    _parent = (el.parentNode) ? el.parentNode : null;
    while (_parent && _parent.tagName != 'BODY' && _parent.tagName != 'HTML')
    {
        _pos[0] -= _parent.scrollLeft;
        _pos[1] -= _parent.scrollTop;
        _parent = (_parent.parentNode) ? _parent.parentNode : null;
    }
    return {left:_pos[0], top:_pos[1], width:el.offsetWidth, height: el.offsetHeight};
}














function uploadClose(bReload)
{
    //$("body").removeChild($("swfuo"));
   if (bReload == false) $("body").removeChild($("swfu_xxx"));
   else window.location.reload();
}


function frow(id, name, size, status)
{
    var css = "style='font-size:11px'";
    if (status == "limit" || status == "quota") css = " style='font-size:11px; color:#ccc'";
    var h = "<table width=100% " + css + " border=0><tr><td>" + name + "</td><td width=50px align=right>" + $size(size) + "</td><td width=50px align=right><div id=status" + id + ">" + status + "</div></td></tr></table>";
    return "<div id=file" + id + " style='border-bottom:1px solid #ccc;'>" + h + "</div>";
}


var SWFUpload;

if (SWFUpload == undefined) {
	SWFUpload = function (init_settings) {
		this.initSWFUpload(init_settings);
	};
}

/*
var SWFUpload = function (init_settings) {
	this.initSWFUpload(init_settings);
};
*/
SWFUpload.prototype = {
    initSWFUpload: function (init_settings) {
    	try {
    		this.settings = {};
    		this.eventQueue = [];
    		this.movieName = "SWFUpload_" + SWFUpload.movieCount++;
    		this.swf = null;
    		SWFUpload.instances[this.movieName] = this;

    		this.initSettings(init_settings);
    		this.loadFlash();
    	} catch (ex2) {
    		alert(1 +  ex2);
    	}
    },

    initSettings: function (init_settings) {
    	// Upload backend settings
    	this.addSetting("upload_url",		 	init_settings.upload_url,		  		"");
    	this.addSetting("file_post_name",	 	init_settings.file_post_name,	  		"Filedata");
    	this.addSetting("post_params",		 	init_settings.post_params,		  		{});

    	this.addSetting("quota",              init_settings.quota,                    "1024000");
        this.addSetting("left",               init_settings.left  ,                   "0");
        this.addSetting("top",               init_settings.top  ,                    "0");
        this.addSetting("type",              init_settings.type  ,                   "attach");
    	// File Settings
    	this.addSetting("file_types",		  	init_settings.file_types,				"*.*");
    	this.addSetting("file_types_description", 	init_settings.file_types_description, 	"All Files");
    	this.addSetting("file_size_limit",		  	init_settings.file_size_limit,			"1024");
    	this.addSetting("flash_url",		    init_settings.flash_url,			    "swfupload.swf");

		// Button Settings
		this.addSetting("button_image_url", 		init_settings.button_image_url	, 			"");
		this.addSetting("button_width", 			init_settings.button_width	, 				42);
		this.addSetting("button_height", 		init_settings.button_height	, 				20);
		this.addSetting("button_text", 			init_settings.button_text, 					"");
		this.addSetting("button_text_style", 		init_settings.button_text_style, 			"color: #000000; font-size: 16pt;");
		this.addSetting("button_text_top_padding", 	init_settings.button_text_top_padding, 		0);
		this.addSetting("button_text_left_padding", 	init_settings.button_text_left_padding, 	0);
		this.addSetting("button_action", 		init_settings.button_action, 				null);
		this.addSetting("button_disabled",		init_settings.button_disabled, 				false);
		this.addSetting("button_placeholder_id",	init_settings.button_placeholder_id, 		null);

		this.addSetting("button_cursor", 		init_settings.button_cursor, 				-2);
		//this.addSetting("button_window_mode", 	init_settings.button_window_mode, "window");
		
		//new added
		/*this.addSetting("button_class", 			init_settings.button_class, 				"");
		this.addSetting("button_style", 			init_settings.button_style, 				"");
		this.addSetting("button_onmouseover", 	init_settings.button_onmouseover,			"");
		this.addSetting("button_onmouseout", 		init_settings.button_onmouseout, 			"");
		this.addSetting("button_onmousedown", 	init_settings.button_onmousedown, 			"");
		*/
    	// Event Handlers
    	this.fileDialogComplete_handler	= SWFUpload.fileDialogComplete;
    	this.uploadStart_handler		= SWFUpload.uploadStart;
    	this.uploadProgress_handler		= SWFUpload.uploadProgress;
    	this.uploadComplete_handler		= SWFUpload.uploadComplete;


//returnUploadStart
    	this.flashReady_handler         = SWFUpload.flashReady;	// This is a non-overrideable event handler
    	this.swfUploadLoaded_handler    = SWFUpload.swfUploadLoaded;
    	this.fileDialogStart_handler	= SWFUpload.fileDialogStart;
    	this.fileQueued_handler			= SWFUpload.fileQueued;
    	this.fileQueueError_handler		= SWFUpload.fileQueueError;

    	this.uploadError_handler		= SWFUpload.uploadError;
    	this.uploadSuccess_handler		= SWFUpload.uploadSuccess;
    	this.debug_handler				= SWFUpload.debug;    	
		
    },

    loadFlash: function () {
    	var html, target_element, container;
		// Make sure an element with the ID we are going to use doesn't already exist
		if (document.getElementById(this.movieName) !== null) {
			throw "ID " + this.movieName + " is already in use. The Flash Object could not be added";
		}

		// Get the element where we will be placing the flash movie
		targetElement = document.getElementById(this.getSetting("button_placeholder_id"));

		if (targetElement == undefined) {
			throw "Could not find the placeholder element.";
		}

		// Append the container and load the flash
		//tempParent = document.createElement("div");
		//tempParent.innerHTML = this.getFlashHTML();	// Using innerHTML is non-standard but the only sensible way to dynamically add Flash in IE (and maybe other browsers)
		targetElement.innerHTML = this.getFlashHTML();
		if(this.swf == undefined){
			this.swf = $(this.movieName);
		}
    },

    getFlashHTML: function () {
    	var html = "";
		var transparent = this.getSetting("button_image_url")=== "" ? true : false;
		
    	if (navigator.plugins && navigator.mimeTypes && navigator.mimeTypes.length) {
    		html = '<embed type="application/x-shockwave-flash" src="' + this.getSetting("flash_url") + '" width="'+ this.getSetting("button_width") + '" height="' + this.getSetting("button_height") + '" ';
			html += ' wmode="' + (transparent ? "transparent" : "Opaque") + '"'; 
    		html += ' id="' + this.movieName + '" name="' + this.movieName + '" ';
    		html += ' quality="high" menu="false" flashvars="';
    		html += this.getFlashVars();
    		html += '" />';
    	} else {
    		// Build the basic Object tag
    		html = '<object id="' + this.movieName + '" type="application/x-shockwave-flash" data="'+ this.getSetting("flash_url") + '" width="'+ this.getSetting("button_width") + '" height="' + this.getSetting("button_height") + '" class="swfupload" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0">';
			html += '<param name="wmode" value="'+ (transparent ? "transparent" : "Opaque") + '" />';
    		html += '<param name="movie" value="' + this.getSetting("flash_url") + '">';
    		html += '<param name="quality" value="high" />';
    		html += '<param name="menu" value="false" />';
			html += '<param name="allowScriptAccess" value="always" />',
    		html += '<param name="flashvars" value="' + this.getFlashVars() + '" />';
    		html += '</object>';
		}
		//html = '<div id="fupload" class="' + this.getSetting("button_class") + '" style="' + this.getSetting("button_style") + '" onmouseover="' + this.getSetting("button_onmouseover")+ '" onmouseout="' + this.getSetting("button_onmouseout") + '" onmousedown="' + this.getSetting("button_onmousedowns")+ '">' + html + '</div>';
    	return html;
    },

    getFlashVars: function () {
    	var param_string = this.buildParamString();
    	var html = "";
    	html += "movieName=" + encodeURIComponent(this.movieName);
    	html += "&uploadURL=" + encodeURIComponent(this.getSetting("upload_url"));
    	html += "&params=" + encodeURIComponent(param_string);
    	html += "&filePostName=" + encodeURIComponent(this.getSetting("file_post_name"));
    	html += "&fileTypes=" + encodeURIComponent(this.getSetting("file_types"));
    	html += "&fileTypesDescription=" + encodeURIComponent(this.getSetting("file_types_description"));
    	html += "&fileSizeLimit=" + encodeURIComponent(this.getSetting("file_size_limit"));
    	html += "&fileUploadLimit=0";
    	html += "&fileQueueLimit=0";
    	html += "&debugEnabled=false";
		html += "&buttonImageURL=" + encodeURIComponent(this.getSetting("button_image_url"));
		html +=	"&buttonWidth=" + encodeURIComponent(this.getSetting("button_width"));
		html +=	"&buttonHeight=" + encodeURIComponent(this.getSetting("button_height"));
		html +=	"&buttonText=" + encodeURIComponent(this.getSetting("button_text"));
		html +=	"&buttonTextTopPadding=" + encodeURIComponent(this.getSetting("button_text_top_padding"));
		html +=	"&buttonTextLeftPadding=" + encodeURIComponent(this.getSetting("button_text_left_padding"));
		html +=	"&buttonTextStyle=" + encodeURIComponent(this.getSetting("button_text_style"));
		html +=	"&buttonAction=" + encodeURIComponent(this.getSetting("button_action"));
		html +=	"&buttonDisabled=" + encodeURIComponent(this.getSetting("button_disabled"));
		html += "&buttonCursor=" + encodeURIComponent(this.getSetting("button_cursor"));
    	return html;
    },

    buildParamString: function () {
    	var post_params = this.getSetting("post_params");
    	var param_string_pairs = [];
    	var i, value, name;

    	if (typeof(post_params) === "object") {
    		for (name in post_params) {
    			if (post_params.hasOwnProperty(name)) {
    				if (typeof(post_params[name]) === "string") {
    					param_string_pairs.push(encodeURIComponent(name) + "=" + encodeURIComponent(post_params[name]));
    				}
    			}
    		}
    	}

    	return param_string_pairs.join("&");
    },

    flashReady: function () {
        var swf = $(this.movieName);
    	if (swf === null || typeof(swf.StartUpload) !== "function") {
    		alert("ExternalInterface methods failed to initialize.");
    		return;
    	}

    	var self = this;
    	if (typeof(self.flashReady_handler) === "function") {
    		this.eventQueue[this.eventQueue.length] = function() { self.flashReady_handler(); };
    		setTimeout(function () { self.execNextEvent();}, 0);
    	} else {
    		alert("flashReady_handler event not defined");
    	}
    },



    addSetting:     function(name, value, dft) { this.settings[name] = (value) ? value : dft; },
    getSetting:     function(name) { return (this.settings[name]) ? this.settings[name] : ""; },

    execNextEvent:  function() { var  f = this.eventQueue.shift(); if (typeof(f) === "function") f(); },



    fileDialogStart: function() {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.fileDialogStart_handler(); };
    	setTimeout(function() { self.execNextEvent();}, 0);
    },

    fileQueued: function(file)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.fileQueued_handler(file); };
    	setTimeout(function() { self.execNextEvent();}, 0);
    },

    fileQueueError: function(file, error_code, message)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() {  self.fileQueueError_handler(file, error_code, message); };
    	setTimeout(function() { self.execNextEvent();}, 0);
    },

    fileDialogComplete: function(num_files_selected)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.fileDialogComplete_handler(num_files_selected); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    uploadStart: function(file)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.returnUploadStart(self.uploadStart_handler(file)); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    returnUploadStart: function(return_value) { this.swf.ReturnUploadStart(return_value); },
    uploadProgress: function (file, bytes_complete, bytes_total)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadProgress_handler(file, bytes_complete, bytes_total); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    uploadError: function(file, error_code, message)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadError_handler(file, error_code, message); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    uploadSuccess: function(file, server_data)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadSuccess_handler(file, server_data); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    uploadComplete: function(file)
    {
    	var self = this;
    	this.eventQueue[this.eventQueue.length] = function() { self.uploadComplete_handler(file); };
    	setTimeout(function () { self.execNextEvent();}, 0);
    },

    debug: function (message)
    {
        var self = this;
    	setTimeout(function () { self.execNextEvent();}, 0);
    },



    selectFile:     function () { this.swf.SelectFile(); },
    selectFiles:    function () { this.swf.SelectFiles(); },    

    startUpload:    function(file_id) { var swf = this.swf; setTimeout( function () { swf.StartUpload(file_id); }, 0 ); },
    cancelUpload:   function(file_id) { this.swf.CancelUpload(file_id); },
    stopUpload:     function() { this.swf.StopUpload(); },
    getStats:       function() { return this.swf.GetStats(); },
    getFile:        function(file_id) { return (typeof(file_id) === "number") ? this.swf.GetFileByIndex(file_id) : this.swf.GetFile(file_id);}
}



SWFUpload.instances = {};
SWFUpload.movieCount = 0;
SWFUpload.type = "attach";


SWFUpload.flashReady = function () {
	try {
		if (typeof(this.swfUploadLoaded_handler) === "function") {
			this.swfUploadLoaded_handler();
		}
	} catch (ex) {
		alert(4 + ex);
	}
};




SWFUpload.fileDialogComplete = function (num_files_selected, numFilesQueued)
{
    if (num_files_selected == 0) return;
    
    if (this.getSetting("type") == "zip")
    {
        if (num_files_selected > 1) 
        {
            alert("Can not select multiple files. Only single ZIP file is permit!!");
            return; 
        }   
    }

    var swfFilesSize = 0;
    var maxFileSize = parseInt(this.getSetting("file_size_limit"), 10) * 1024;
    var swfMaxFilesSize = parseInt(this.getSetting("quota"), 10) * 1024;


    var el = $E("div");
    $("body").appendChild(el);

    el.id = "swfu_xxx";
    var st = el.style;
    el.zIndex = "100";
    st.background = "#efe";
    st.padding = "5px";
    st.border = "1px solid #333";
    st.position = "absolute";
    st.fontSize = "11px";
    st.width = "360px";
    st.overflowY = "auto";
    st.left = this.getSetting("left");
    st.top = this.getSetting("top");
    el.innerHTML  = "<table width=100% style='font-size:11px' border=0><tr><td>file</td><td width=50px>size</td><td width=50px>status</td></tr></table>";
    el.innerHTML += "<div id=swfu_area style='height:190px; overflow-y:auto; background:#fff; border:1px solid #aaa;'></div>";


    var h = "";
    var cnt = 0;
    var fzero = false;
    var fquota = false;
    var flimit = false;
    for (i=0; ; i++)
    {
        var f = this.getFile(i);
        if (!f) break;

        var fsize = parseInt(f.size, 10);
        if (fsize == 0)
        {
            h += frow(f.id, f.name, f.size, "empty");
            this.cancelUpload(f.id);
            fzero = true;
            continue;
        }
        
        if (swfFilesSize+fsize > swfMaxFilesSize)
        {
            h += frow(f.id, f.name, f.size, "quota");
            this.cancelUpload(f.id);
            fquota = true;
            continue;
        }

        if (fsize > maxFileSize)
        {
            h += frow(f.id, f.name, f.size, "limit");
            flimit = true;
            continue;
        }

        swfFilesSize += fsize;
        cnt ++;
        h += frow(f.id, f.name, f.size, "");
    }
    $("swfu_area").innerHTML = h;


    var info = "<div style='padding:2px; font-size:11px; color:#000'>" + cnt + " files (" + $size(swfFilesSize) + ")";
    if (fzero) info += ", file size=0";
    if (fquota) info += ", quota=" + $size(swfMaxFilesSize);
    if (flimit) info += ", size limit=" + $size(maxFileSize);
    info += "</div>";
    $("swfu_xxx").innerHTML += info;

    if (cnt == 0)
    {
        $("swfu_xxx").innerHTML += "<div style='text-align:center'><input type=button onclick='uploadClose(false)' value=close></div>";
        return;
    }
	this.startUpload();
}

SWFUpload.uploadStart = function(file)
{
    $("swfu_area").scrollTop = $area($("file" + file.id)).top - 68;
    return true;
}
						
SWFUpload.uploadProgress = function(fileObj, bytesLoaded, bytesTotal)
{
	var percent = Math.ceil((bytesLoaded / fileObj.size) * 100);
    $("status" + fileObj.id).innerHTML = percent + "%";

    var w = $("swfu_area").offsetWidth;
    $("file" + fileObj.id).style.background = "#ffa url(/sys/res/icon/progbar.png) no-repeat -" + (w - percent) + "px 0";

    if (percent == 100)
    {
    	$("file" + fileObj.id).style.background = "#ffc url(/sys/res/icon/progbar.png) no-repeat -" + (w - percent) + "px 0";
	    $("status" + fileObj.id).innerHTML = "<img src='/sys/res/icon/checkmark.gif'>";
	}
}

SWFUpload.uploadComplete = function(fileObj) {
	if (this.getStats().files_queued > 0) {
		this.startUpload();
	} else {	    
	    if (this.getSetting("type") == "zip")
	    {
	        alert("upload complete!");
	        $("body").removeChild($("swfu_xxx"));
	        //$("body").removeChild($("swfuo"));
	        return;
	    }

	    $("swfu_xxx").innerHTML += "<div style='text-align:center'><input type=button onclick='uploadClose(true)' value=close></div>";
	}
}





SWFUpload.swfUploadLoaded = function () { };
SWFUpload.fileDialogStart = function () { };
SWFUpload.fileQueued = function (file) { };
SWFUpload.fileQueueError = function (file, error_code, message) { };
SWFUpload.uploadSuccess = function (file, server_data) { };
SWFUpload.uploadError = function (file, error_code, message) {};












// http://www.brainjar.com/dhtml/drag/
function m_Browser() {

  var ua, s, i;

  this.isIE    = false;
  this.isNS    = false;
  this.version = null;

  ua = navigator.userAgent;

  s = "MSIE";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isIE = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  s = "Netscape6/";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = parseFloat(ua.substr(i + s.length));
    return;
  }

  // Treat any other "Gecko" browser as NS 6.1.

  s = "Gecko";
  if ((i = ua.indexOf(s)) >= 0) {
    this.isNS = true;
    this.version = 6.1;
    return;
  }
}

var gBrowser = new m_Browser();



var gDragObj = new Object();
gDragObj.zIndex = 0;

function $dragStart(event, id) {
    var el;
    var x, y;

    // If an element id was given, find it. Otherwise use the element being clicked on.
    if (id)
        gDragObj.elNode = $(id);
    else {
        if (gBrowser.isIE)
            gDragObj.elNode = window.event.srcElement;
        if (gBrowser.isNS)
            gDragObj.elNode = event.target;

        // If this is a text node, use its parent element.
        if (gDragObj.elNode.nodeType == 3)
            gDragObj.elNode = gDragObj.elNode.parentNode;
    }

    // Get cursor position with respect to the page.
    if (gBrowser.isIE) {
        x = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
        y = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
    }
    if (gBrowser.isNS) {
        x = event.clientX + window.scrollX;
        y = event.clientY + window.scrollY;
    }

    // Save starting positions of cursor and element.
    gDragObj.cursorStartX = x;
    gDragObj.cursorStartY = y;
    gDragObj.elStartLeft  = $int(gDragObj.elNode.style.left);
    gDragObj.elStartTop   = $int(gDragObj.elNode.style.top);

    if (isNaN(gDragObj.elStartLeft)) gDragObj.elStartLeft = 0;
    if (isNaN(gDragObj.elStartTop))  gDragObj.elStartTop  = 0;

    // Update element's z-index.
    gDragObj.elNode.style.zIndex = 100;

    // Capture mousemove and mouseup events on the page.

    if (gBrowser.isIE) {
        document.attachEvent("onmousemove", dragGo);
        document.attachEvent("onmouseup",   dragStop);
        window.event.cancelBubble = true;
        window.event.returnValue = false;
    }
    if (gBrowser.isNS) {
        document.addEventListener("mousemove", dragGo,   true);
        document.addEventListener("mouseup",   dragStop, true);
        event.preventDefault();
    }
}

function dragGo(event) {

    var x, y;

    // Get cursor position with respect to the page.
    if (gBrowser.isIE) {
        x = window.event.clientX + document.documentElement.scrollLeft + document.body.scrollLeft;
        y = window.event.clientY + document.documentElement.scrollTop + document.body.scrollTop;
    }
    if (gBrowser.isNS) {
        x = event.clientX + window.scrollX;
        y = event.clientY + window.scrollY;
    }

    // Move drag element by the same amount the cursor has moved.
    gDragObj.elNode.style.left = (gDragObj.elStartLeft + x - gDragObj.cursorStartX) + "px";
    gDragObj.elNode.style.top  = (gDragObj.elStartTop  + y - gDragObj.cursorStartY) + "px";

    var objMask = $("_popupMask");
    if (objMask)
    {
        objMask.style.left = (gDragObj.elStartLeft + x - gDragObj.cursorStartX) + "px";
        objMask.style.top  = (gDragObj.elStartTop  + y - gDragObj.cursorStartY) + "px";
    }

    if (gBrowser.isIE) {
        window.event.cancelBubble = true;
        window.event.returnValue = false;
    }
    if (gBrowser.isNS)
        event.preventDefault();
}

function dragStop(event)
{
    if (gBrowser.isIE) {
        document.detachEvent("onmousemove", dragGo);
        document.detachEvent("onmouseup",   dragStop);
    }
    if (gBrowser.isNS) {
        document.removeEventListener("mousemove", dragGo,   true);
        document.removeEventListener("mouseup",   dragStop, true);
    }
}






























var gWeekRes;
function m_CalendarEventHide(event)
{
    var _hide=false;
	if (gBrowser.isIE) { var _key=window.event.keyCode; var _button=window.event.button; if(_button==1 || _key == 27) _hide=true; }
	if (gBrowser.isNS) { var _key=event.keyCode;		var _button=event.button; 		 if(_button==0 || _key == 27) _hide=true; }

	if (_hide)
	{
		if (gBrowser.isIE) { document.detachEvent("onkeyup", m_CalendarEventHide); document.detachEvent("onmouseup", m_CalendarEventHide);}
		if (gBrowser.isNS) { document.removeEventListener("keyup", m_CalendarEventHide, false); document.removeEventListener("mouseup", m_CalendarEventHide, false);}
		$hidePopup();
	}
}

function $getWeekDay(year, month ,day)
{
	var _week = new Date(year, month-1 ,day);
	switch(_week.getDay())
	{
		case 0:	return 'Sun';
		case 1:	return 'Mon';
		case 2:	return 'Tue';
		case 3:	return 'Wed';
		case 4:	return 'Thu';
		case 5:	return 'Fri';
		case 6:	return 'Sat';
	}
}

function $ShowCalendar(event, divcal, _type, date, _weekRes)
{
    gWeekRes = (!_weekRes) ? new Array("Su", "Mo", "Tu", "We", "Th", "Fr", "Sa") : _weekRes;
	_type = _type || 0;

	$hidePopup();
    $(divcal).setAttribute("autocomplete","off");


	var a, h, w = 0;
	a = $area($(divcal));
	h = a.top + a.height;
	a.left -= 0;

	$EvtListener(document, "mouseup", m_CalendarEventHide);
	$EvtListener(document, "keyup"  , m_CalendarEventHide);

	_body = "<div style='width:170px' onmouseup=$EvtCancelBubble(event) id='smale_calender' name='smale_calender'></div>";
	$showPopup2(_body, a.left, h, w);

	if(!date)
	{
		var _today = new Date();		
		m_CalendarLoad(event, divcal, _today.getFullYear(), _today.getMonth()+1, _type);
	}
	else
	{
		var arr = date.split("-");		
		m_CalendarLoad(event, divcal, arr[0], arr[1], _type);
	}
}

function $EvtListener(e, method, cb)
{
	if (gBrowser.isIE) e.attachEvent("on" + method, cb);
    if (gBrowser.isNS) e.addEventListener(method, cb, false);
}

function $EvtCancelBubble(event)
{
	if (gBrowser.isNS) { event.preventDefault(); event.stopPropagation(); }
   	if (gBrowser.isIE) { window.event.cancelBubble = true; }
}


function m_CalendarLoad(event, divcal, year, month, _type)
{
   	$EvtCancelBubble(event);
    if (month < 1)  { month = 12; year--;}
    if (month > 12) { month = 1,  year++;}
    
    m_ShowCalendarHTML(event, divcal, year, month, _type);
}

function m_CalendarReturn(divcal, date, _type)		//if type = 1 . return to func  ScalGetDate(yyyy,mm,dd)
{    
	var arr = date.split("-");
    if(arr[1].length<2) arr[1] = '0' + arr[1];
    if(arr[2].length<2) arr[2] = '0' + arr[2];
	if( _type==1 ) 
        ScalGetDate(arr[0], arr[1], arr[2], divcal);
	else
		$(divcal).value = arr[0] + '-' + arr[1] + '-' + arr[2];

    $hidePopup();
}

function m_ShowCalendarHTML(event, divcal, year, month, type)
{    
    var _date = new Date(year, month-1, 1);
    var _html = "";
	month = $int(month);

    _html += "<table class='calTable'  cellSpacing=0px; cellPadding=0px;><tr valign=middle class='calTitleTr'>";
    _html +=     "<td class='calTitlePrevTd'><img src='/sys/res/icon/prev.gif' style='cursor:pointer' onmouseup='m_CalendarLoad(event, \"" + divcal + "\", \"" + year + "\",\"" + (month-1) + "\",\"" + type + "\")'></td>";
	_html +=     "<td class='calTitleMonthTd' colspan='5'><b>" + year + " / " + month + "</b></td>";
	_html +=     "<td class='calTitleNextTd'><img src='/sys/res/icon/next.gif' style='cursor:pointer' onmouseup='m_CalendarLoad(event, \"" + divcal + "\", \"" + year + "\",\"" + (month+1) + "\",\"" + type + "\")'></td>";
    _html += "</tr><tr class='calDayTr'>";

    for (var i=0; i<7; i++) _html += "<td class='calDayTd'>" + gWeekRes[i] + "</td>";
	_html += "</tr><tr class='calTr'>";
    var _MonStartDay = _date.getDay();
    var _TotalDay    = $getDays(year, month);
	var _PreviousMonStartDay = $getDays(year, month-1);
	if(month==1)  _PreviousMonStartDay = $getDays(year, 12);

	var x = _PreviousMonStartDay;
	var _line = 0;

	month--;
	if (month == 0) { month = 12; year--; }

	if (_MonStartDay==5 || _MonStartDay==6)
	{
		_PreviousMonStartDay -= _MonStartDay-1;
		for (i=0; i<_MonStartDay; i++, _PreviousMonStartDay++)
		{
			_date = year + "-" + month + "-" + _PreviousMonStartDay;
			_html += "<td class='calOddTd' onMouseOver='this.className=\"calTdOver\"' onMouseOut='this.className=\"calOddTd\"' onClick='m_CalendarReturn( \"" + divcal + "\",\"" + _date + "\",\"" + type + "\")'>" + _PreviousMonStartDay + "</td>";
		}
	}
	else
	{
		_PreviousMonStartDay -= ( 6 + _MonStartDay );
		for (l=1; _PreviousMonStartDay<=x; _PreviousMonStartDay++, l++)
		{
			_date = year + "-" + month + "-" + _PreviousMonStartDay;
			_html += "<td class='calOddTd' onMouseOver='this.className=\"calTdOver\"' onMouseOut='this.className=\"calOddTd\"' onClick='m_CalendarReturn(\"" + divcal + "\",\"" + _date + "\",\"" + type + "\")'>" + _PreviousMonStartDay + "</td>";
			if ( l%7==0 ) { _html += "</tr><tr class='calTr'>"; _line++; }
		}
	}

	var _today = new Date();
	month++;

	if (month == 13) { month = 1; year++; }	

    for (i=1; i<=_TotalDay; i++)
	{
		var _date = year + "-" + month + "-" + i;

		if ( _today.getFullYear() == year && _today.getMonth() == month-1 && _today.getDate() == i )
		{
			_html += "<td class='calTodayTd' onMouseOver='this.className=\"calTdOver\"' onMouseOut='this.className=\"calTodayTd\"' onClick='m_CalendarReturn( \"" + divcal + "\",\"" + _date + "\",\"" + type + "\")'>";
			_html += i + "</td>";
		}
		else
		{
			_html += "<td class='calTd' onMouseOver='this.className=\"calTdOver\"' onMouseOut='this.className=\"calTd\"' onClick='m_CalendarReturn( \"" + divcal + "\",\"" + _date + "\",\"" + type + "\")'>";
			_html += i + "</td>";
		}

		if ((i+_MonStartDay)%7==0) { _html += "</tr>"; if( i!=_TotalDay){ _line++; _html += "<tr class='calTr'>"; } }
	}

	_date = new Date(year, month, 1);
	var _leftday = _date.getDay();
	_leftday = 7-_leftday;
	month++;

	if (month == 13) { month = 1; year++; }	

	i = 1;
	if (_leftday!=7)
	{
		for ( ; i <= _leftday; i++)
		{
			_date = year + "-" + month + "-" + i;
			_html += "<td class='calOddTd' onMouseOver='this.className=\"calTdOver\"' onMouseOut='this.className=\"calOddTd\"' onClick='m_CalendarReturn( \"" + divcal + "\",\"" + _date + "\",\"" + type + "\")'>" + i + "</td>";
		}
	}
	_html += "</tr>";
	_line++;
	while( _line<7 )
	{
		_html += "<tr class='calTr'>";
		for (t=1; t<=7; i++,t++)
		{
			_date = year + "-" + month+ "-" + i;
			_html += "<td class='calOddTd' onMouseOver='this.className=\"calTdOver\"' onMouseOut='this.className=\"calOddTd\"' onClick='m_CalendarReturn( \"" + divcal + "\",\"" + _date + "\",\"" + type + "\")'>" + i + "</td>";
		}
		_html += "</tr>";
		_line++;
	}
	_html += "</table>";

   $("smale_calender").innerHTML = _html;
}

function $ShowTime(name, inputID)
{
    $hidePopup();
    $EvtListener(document, "mouseup", m_TimeEventHide);
    $EvtListener(document, "keyup", m_TimeEventHide);
    var _body = "";
    var timeres = ["00:00", "00:30", "01:00", "01:30", "02:00", "02:30", "03:00", "03:30", "04:00", "04:30", "05:00",
                   "05:30", "06:00", "06:30", "07:00", "07:30", "08:00", "08:30", "09:00", "09:30", "10:00", "10:30",
                   "11:00", "11:30", "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", "15:00", "15:30", "16:00",
                   "16:30", "17:00", "17:30", "18:00", "18:30", "19:00", "19:30", "20:00", "20:30", "21:00", "21:30",
                   "22:00", "22:30", "23:00", "23:30"];

    _body += "<select onmouseup='$EvtCancelBubble(event)' style='margin:-3px; border:1px solid #000; background:#ffc; width:90px; font-size:12px' size='10' id= " + name + " name=" + name + " onClick=m_Timeinner(\"" + name + "\",\""+ inputID + "\")>";
    for (i=0; i<48; i++) { _body += "<option value=" + i + ">" + timeres[i] + "</option>"; }
    _body += "</select>";

    var a, h, w = 150;
    a = $area($(inputID));
    h = a.top + a.height;
    $showPopup2( _body, a.left, h, w);
}
function m_Timeinner(name, inputID)
{
    var index = $(name).selectedIndex;
    var value = $(name).options[index].text;

    $(inputID).value = value;

    index = ( index<44 ) ? index+4 : index-44;

    if ( name == "st" ) $("eTime").value = $(name).options[index].text;

    $hidePopup();
    $(inputID).focus();
}
function m_TimeEventHide(event)
{
    var hide = false;
    if (gBrowser.isIE) { var key=window.event.keyCode; var button=window.event.button; if (button==1 || key == 27) hide=true; }
    if (gBrowser.isNS) { var key=event.keyCode; var button=event.button; if (button==0 || key == 27) hide=true; }
    if (hide)
    {
        if (gBrowser.isIE) { document.detachEvent("onkeyup", m_TimeEventHide); document.detachEvent("onmouseup", m_TimeEventHide);}
        if (gBrowser.isNS) { document.removeEventListener("keyup", m_TimeEventHide, false); document.removeEventListener("mouseup", m_TimeEventHide, false);}
        var el = $("_popup");
        if (el) $hide(el);
    }
}