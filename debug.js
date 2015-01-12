/****************  lib  *****************/
function getScreenInfo()
{
    var s = "";
    s += " 網頁可見區域寬："+document.body.clientWidth + "\n" ;
    s += " 網頁可見區域高："+document.body.clientHeight + "\n" ;
    s += " 網頁可見區域寬："+ document.body.offsetWidth+ " (包括邊線和捲軸的寬)" + "\n" ;
    s += " 網頁可見區域高："+document.body.offsetHeight + " (包括邊線的寬)" + "\n" ;
    s += " 網頁正文全文寬："+document.body.scrollWidth + "\n" ;
    s += " 網頁正文全文高："+document.body.scrollHeight + "\n" ;
    s += " 網頁被捲去的高(ff)："+ document.body.scrollTop + "\n" ;
    s += " 網頁被捲去的高(ie)："+ document.documentElement.scrollTop + "\n" ;
    s += " 網頁被捲去的左："+document.body.scrollLeft + "\n" ;
    s += " 網頁正文部分上："+window.screenTop + "\n" ;
    s += " 網頁正文部分左："+window.screenLeft + "\n" ;
    s += " 螢幕解析度的高："+window.screen.height + "\n" ;
    s += " 螢幕解析度的寬："+window.screen.width + "\n" ;
    s += " 螢幕可用工作區高度："+window.screen.availHeight + "\n" ;
    s += " 螢幕可用工作區寬度："+window.screen.availWidth + "\n" ;
    s += " 你的螢幕設置是 "+window.screen.colorDepth +" 位元彩色" + "\n" ;
    s += " 你的螢幕設置 "+window.screen.deviceXDPI +" 圖元/英寸" + "\n" ;
    alert (s);
}
// getInfo();
function $(id)    { return (document.getElementById(id)) ? document.getElementById(id) : document.getElementsByTagName(id)[0]; }
function $V(id)   { return $(id).value.replace(/^\s+/g, '').replace(/\s+$/g, ''); }
function $S(id)   { return $(id).style; }
function $E(tag)  { return document.createElement(tag); }
function $A(tag)   { var el = $E(tag); $append(el); return el; }
function $append(el) { $("body").appendChild(el); }
function $remove(el) { $("body").removeChild(el); }
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

/*********************  define  **********************/

var SEARCH_LEVEL = 9;

/****************  functions  *****************/
function attribute_expand(ctrl, arr, parent, level)
{
    parent = (parent) ? parent : "";
    level = (level) ? level : 1;
    if (level > SEARCH_LEVEL) return;    
    
    var parent_path = parent.replace(/./g, " "); 
    arr = (arr) ? arr : "";
    var h = "", pathStr = "", _show;
    for (obj in ctrl)
    {
        _show = (arr) ? false : true;
        if (typeof ctrl[obj] == "function") continue;
        
        //搜尋特定關鍵字.
        if (arr)
        {
            var att = obj.toUpperCase();
            for (var i=0; i<arr.length; i++)
            {
                if (att.indexOf(arr[i]) != -1) _show = true;
            }
        }
  
        //顯示屬性.
        var objVal = "";
        if (_show)
        {   
            objVal = (typeof ctrl[obj] == "string") ? $makehtml(ctrl[obj]) : ctrl[obj];
            if ($isInt(obj))
                h += parent_path + '[' + obj + "] = " + objVal + "\n";            
            else
                h += parent_path + '.' + obj + " = " + objVal + "\n";
        }

        //向下層搜尋.
        if (typeof ctrl[obj] == 'object')
        {
            pathStr = $isInt(obj) ? parent + "[" + obj + "]" : parent + "." + obj;
            h += attribute_expand(ctrl[obj], arr, pathStr, ++level);
            continue;
        }
    }
    return h;
}

function alertAtt(ctrl, arr)
{
    if (!arr)
        arr = [];
    else
        for(var i=0; i<arr.length; i++) arr[i] = arr[i].toUpperCase();

    alert(attribute_expand(ctrl, arr));
}

function popupAtt(ctrl, arr)
{
    var h = att(ctrl);
    $showPopup("顯示屬性", h.replace(/\n/g, "&nbsp;<br>"), 0, 0, 600,500);
}

function debugAtt(ctrl, arr)
{
    if (!arr)
        arr = "";
    else
        for(var i=0; i<arr.length; i++) arr[i] = arr[i].toUpperCase();
    
    var h = attribute_expand(ctrl, arr);

    var el = $("_debug");
    if (el) $remove(el);
    
    el = $E("DIV");
    el.id = "_debug";
    el.style.border = "1px solid #ccc";
    $append(el);
    $("_debug").innerHTML = "<PRE style='text-align:left'>" + $makehtml(h) + "</PRE>";
}

function debugClear()
{
    if ($("_debug")) $("_debug").innerHTML = "";
} 