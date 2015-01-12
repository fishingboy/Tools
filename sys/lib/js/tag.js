var httpRequestKeyword = "";
var userKeyword = "";
var suggestions = 0;
var suggestionMaxLength = 30;
var isKeyUpDownPressed = false;
var autocompletedKeyword = "";
var hasResults = false;
var timeoutId = -1;
var position = -1;
var oCache = new Object();

function checkForChanges()
{
    var keywords = document.getElementById(tagFieldID).value;  // x, x
    var tokens = keywords.split(","); // tokens[0]=x, tokens[0]= " x" 
    var keyword = tokens[tokens.length-1]; // " x"
    keyword = keyword.replace(/^\s+|\s+$/g, ""); // "x"

    if(keyword == "")
    {
        hideSuggestions();
        userKeyword = "";
        httpRequestKeyword = "";
    }
    
    //setTimeout("checkForChanges()", 500);

    if((userKeyword != keyword) && (!isKeyUpDownPressed))
    {        
        getSuggestions(keyword);    
    }
}

function getSuggestions(keyword)
{
    if(keyword == "" && isKeyUpDownPressed)
        return;
    userKeyword = keyword;    
    $load("/blog/lib/http_get_tag.php", {keyword:keyword}, onGetSuggestionsLoad);
}

function onGetSuggestionsLoad(jsonArray)
{
    if(jsonArray == null)   return;
    
    httpRequestKeyword = userKeyword;    
    displayResults(httpRequestKeyword, jsonArray);
}

function addToCache(keyword, values)
{
    oCache[keyword] = new Array();
    for(var i = 0; i < values.length; i++)
        oCache[keyword][i] = values[i];
}

function displayResults(keyword, results_array)
{    
    if(results_array.length == 0) return;
	var area = $area($(tagFieldID));
    var div = "<table width=" + (area.width - 6) + " cellspacing=0 >";
    position = -1;
    isKeyUpDownPressed = false;
    hasResults = true;
    suggestions = results_array.length;
    for(var i = 0; i < results_array.length; i++)
    {
        var crtFunction = results_array[i];
        var tokens = crtFunction.split(",");
        var crtFunctionLink = tokens[0];
        
        
        while(crtFunctionLink.indexOf("_") != -1)
            crtFunctionLink = crtFunctionLink.replace("_", "-");  
            
        div += "<tr id='tr" + i + "'  onclick='updateKeywordValue(this);' onmouseover='handleOnMouseOver(this);' onmouseout='handleOnMouseOut(this);' >" +
               "<td align='left' id='td" + i + "' >" + crtFunctionLink + "</td><td align='right'>" + tokens[1] + "</td></tr>";
    }
    div += "<tr><td></td><td align=right><span onclick='hideSuggestions();' style='cursor: pointer;'><img src='/sys/res/icon/cancel.gif' align=absmiddle></span></td></tr></table>";
    var oSuggest = $("tagSuggest");    
    oSuggest.innerHTML = div;    
    var oScrollFrame = document.getElementById("tagScrollFrame");
    var oScroll = document.getElementById("tagScroll");
    oScroll.scrollTop = 0;
    oScrollFrame.style.height = oScroll.offsetHeight + 8; 
    oScrollFrame.style.width = document.getElementById(tagFieldID).offsetWidth;
    oScrollFrame.style.visibility = "visible";
    oScroll.style.visibility = "visible";    
     
    oScrollFrame.style.left = area.left;
    oScrollFrame.style.top = area.top + 20;  
    oScroll.style.left = area.left + 3;
    oScroll.style.top = area.top + 23; 
    
    if (is_smart_device() && parent.frames["if1"]) modifyHeight("if1", parent);
    
    if(results_array.length > 0)
        autocompleteKeyword();
}

function handleKeyUp(e)
{
    var e = (!e) ? window.event : e;    
    var target = (!e.target) ? e.srcElement: e.target;
    if(target.nodeType == 3)
        target = target.parentNode;
    var code = (e.charCode) ? e.charCode : ((e.keyCode) ? e.keyCode : ((e.which) ? e.which : 0));
    if(e.type == "keyup")
    {
        isKeyUpDownPressed = false;
        
        if((code < 13 && code != 8) || (code >= 14 && code < 32) || (code >= 33 && code <= 46 && code != 38 && code != 40) || (code >= 112 && code <= 123))
        {            
        }
        else if(code == 13)      // enter;
        {            
            if(position >= 0)
            {
                 var newTR = document.getElementById("tr" + position);
                 updateKeywordValue(newTR);                                
            }
            hideSuggestions();
        }
        else if(code == 40)      // down                                                                             
        {
            var newTR = document.getElementById("tr" + (++position));
            var oldTR = document.getElementById("tr" + (--position));
            if(position >= 0 && position < suggestions-1)
                oldTR.className = "";
            if(position < suggestions-1)
            {
                newTR.className = "highlightrow";
                updateKeywordValue(newTR);
                position++;
            }
            e.cancelBubble = true;
            e.returnValue = false;
            isKeyUpDownPressed = true;
        }
        else if(code == 38)      // up
        {
            var newTR = document.getElementById("tr" + (--position));
            var oldTR = document.getElementById("tr" + (++position));
            if(position >= 0 && position <= suggestions-1)
                oldTR.className = "";
            if(position > 0)
            {
                newTR.className = "highlightrow";
                updateKeywordValue(newTR);
                position--;
            }
            if(position == 0)
                position--;
            e.cancelBubble = true;
            e.returnValue = false;
            isKeyUpDownPressed = true;
        }
        else
        {
            position = -1;
            checkForChanges();
        }        
    }  
}

function updateKeywordValue(oTr)
{
    var oKeywords = document.getElementById(tagFieldID);
    var tokens = oKeywords.value.split(",");
    var prior_keywords = "";
    for(var i = 0; i < tokens.length-1; i++)
    {
        if(prior_keywords == "")
            prior_keywords = tokens[i];
        else
            prior_keywords += "," + tokens[i];
    }

    var this_keyword = document.getElementById("td" + oTr.id.substring(2,oTr.id.length)).innerHTML;    
    
    if(prior_keywords == "")
        oKeywords.value = this_keyword;
    else
        oKeywords.value = prior_keywords + ", " + this_keyword;
}

function deselectAll()
{
    for(var i = 0; i < suggestions; i++)
    {
        var oCrtTr = document.getElementById("tr" + i);
        oCrtTr.className = "";
    }
}

function handleOnMouseOver(oTr)
{
    deselectAll();
    oTr.className = "highlightrow";
	oTr.style.backgroundColor = "#ccc";
    position = oTr.id.substring(2, oTr.id.length);
}

function handleOnMouseOut(oTr)
{
    oTr.className = "";
	oTr.style.backgroundColor = "#fff";
    position = -1;
}

function encode(uri)
{
    if(encodeURIComponent)
        return encodeURIComponent(uri);
    if(escape)
        return excape(uri);
}

function hideSuggestions()
{
    var oScrollFrame = document.getElementById("tagScrollFrame");
    oScrollFrame.style.visibility = "hidden";
    oScrollFrame.style.top = 0;
    oScrollFrame.style.left = 0;
    var oScroll = document.getElementById("tagScroll");
    oScroll.style.visibility = "hidden";
    var oSuggest = document.getElementById("tagSuggest");
    oSuggest.innerHTML = "";    
       
    if (is_smart_device() && parent.frames["if1"]) reduceModalHeight("if1", parent);
}

function autocompleteKeyword()
{
    var oKeywords = document.getElementById(tagFieldID).value;
    var tokens = oKeywords.split(",");
    position = -1;
    deselectAll();    
   // document.getElementById("tr0").className = "highlightrow";
   // updateKeywordValue(document.getElementById("tr0"));
   // selectRange(oKeyword, httpRequestKeyword.length, oKeyword.value.length);
    autocompletedKeyword = tokens[tokens.length-1].replace(/^\s+|\s+$/g, "");
}

function displayError(message)
{
    alert("error accessing the server!!");
}