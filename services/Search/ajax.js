var GetStatusList = new Array();
var GetSaveStatusList = new Array();

function createRequestObject() {  
    // find the correct xmlHTTP, works with IE, FF and Opera
    var xmlhttp;
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch(e) {
        try {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(e) {
            xmlhttp = null;
        }
    }
    
    if (!xmlhttp && typeof XMLHttpRequest!="undefined") {
        xmlhttp = new XMLHttpRequest();
    }
    
    return xmlhttp;
}

function getElem(id)
{
    if (document.getElementById) {
        return document.getElementById(id);
    } else if (document.all) {
        return document.all[id];
    }
}

function getThumbnail(id, imgname)
{
    var http = createRequestObject();
    http.open("GET", path + "/Search/AJAX?method=GetThumbnail&isn="+id+"&size=small", true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var response = http.responseXML.documentElement;
            if (response.getElementsByTagName('image').item(0)) {
                var url = response.getElementsByTagName('image').item(0).firstChild.data;
                alert(url);
                // write out response
                if (url) {
                    document[imgname].src = url;
                } else {
                    document[imgname].src = path + '/images/noCover2.gif';
                }
            } else {
                document[imgname].src = path + '/images/noCover2.gif';
            }
        }
    }
    http.send(null);
}

function getStatuses(id)
{
    GetStatusList[GetStatusList.length] = id;
}

function doGetStatuses()
{
    var now = new Date();
    var ts = Date.UTC(now.getFullYear(),now.getMonth(),now.getDay(),now.getHours(),now.getMinutes(),now.getSeconds(),now.getMilliseconds());
    var http = createRequestObject();

    var url = path + "/Search/AJAX?method=GetItemStatuses";
    for (var i=0; i<GetStatusList.length; i++) {
       url += "&id[]=" + GetStatusList[i];
    }
    url += "&time="+ts;

    http.open("GET", url, true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var response = http.responseXML.documentElement;
            var items = response.getElementsByTagName('item');
            var elemId;
            var statusDiv;
            var status;
            var reserves;

            for (i=0; i<items.length; i++) {
                elemId = items[i].getAttribute('id');
                statusDiv = getElem('status' + elemId);
               
                if (statusDiv) {
                    if (items[i].getElementsByTagName('reserve')) {
                        reserves = items[i].getElementsByTagName('reserve').item(0).firstChild.data;
                    }

                    if (reserves == 'Y') {
                        statusDiv.innerHTML = '';
                    } else if (items[i].getElementsByTagName('availability')) {
                        if (items[i].getElementsByTagName('availability').item(0).firstChild) {
                            status = items[i].getElementsByTagName('availability').item(0).firstChild.data;
                            // write out response
                            if (status == "true") {
                                statusDiv.innerHTML = '<span class="available">Available</span>';
                            } else {
                                statusDiv.innerHTML = '<span class="checkedout">Checked Out</span>';
                            }
                        } else {
                            statusDiv.innerHTML = '<span class="unknown">Unknown</span>';
                        }
                    } else {
                        statusDiv.innerHTML = '<span class="unknown">Unknown</span>';
                    }

                    if (items[i].getElementsByTagName('location')) {
                        var callnumber
                        var location = items[i].getElementsByTagName('location').item(0).firstChild.data;
                        var reserves = items[i].getElementsByTagName('reserve').item(0).firstChild.data;

                        if (reserves == 'Y') {
                            getElem('location' + elemId).innerHTML = 'Reserves - Ask at Circulation';
                        } else {
                            getElem('location' + elemId).innerHTML = location;
                        }

                        if (items[i].getElementsByTagName('callnumber').item(0).firstChild) {
                            callnumber = items[i].getElementsByTagName('callnumber').item(0).firstChild.data
                            getElem('callnumber' + elemId).innerHTML = callnumber;
                        } else {
                            getElem('callnumber' + elemId).innerHTML = '';
                        }
                    }
                }
            }
        }
    }
    http.send(null);
}

function getStatus(id, elemId)
{
    var now = new Date();
    var ts = Date.UTC(now.getFullYear(),now.getMonth(),now.getDay(),now.getHours(),now.getMinutes());
    // ts is used as a cache preventer for IE

    var http = createRequestObject();
    http.open("GET", path + "/Search/AJAX?method=GetItemStatus&id="+id+"&time="+ts,true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var response = http.responseXML.documentElement;
            if (response.getElementsByTagName('availability')) {
                if (response.getElementsByTagName('availability').item(0).firstChild) {
                    var status = response.getElementsByTagName('availability').item(0).firstChild.data;
                    // write out response
                    if (status == "true") {
                        getElem(elemId).innerHTML = '<span class="available">Available</span>';
                    } else {
                        getElem(elemId).innerHTML = '<span class="checkedout">Checked Out</span>';
                    }
                } else {
                    getElem(elemId).innerHTML = '<span class="unknown">Unknown</span>';
                }
            } else {
                getElem(elemId).innerHTML = '<span class="unknown">Unknown</span>';
            }
            
            if (response.getElementsByTagName('location')) {
                var callnumber = response.getElementsByTagName('callnumber').item(0).firstChild.data;
                var location = response.getElementsByTagName('location').item(0).firstChild.data;
                var reserves = response.getElementsByTagName('reserve').item(0).firstChild.data;
                
                if (reserves == 'Y') {
                    getElem('location' + id).innerHTML = 'Reserves - Ask at Circulation';
                } else {
                    getElem('location' + id).innerHTML = location;
                }
                
                getElem('callnumber' + id).innerHTML = callnumber;
            }
        }
    }
    http.send(null);
}

function showSaveForm()
{
    var id = document.forms['listForm'].elements['recordId'].value;

    var http = createRequestObject();
    http.open("GET", path + "/Search/AJAX?method=IsLoggedIn", true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var result = http.responseXML.documentElement.getElementsByTagName('result').item(0).firstChild.data;
            if (result == "True") {
                var http2 = createRequestObject();
                http2.open("GET", path + "/Search/AJAX?method=GetSavedData&id=" + id, true);
                http2.onreadystatechange = function()
                {
                    if ((http2.readyState == 4) && (http2.status == 200)) {
                        var response = http2.responseXML.documentElement;
                        if (response.getElementsByTagName('result')) {
                            var notes = response.getElementsByTagName('Notes');
                            if (notes.length) {
                                notes = notes.item(0).firstChild.data;
                                document.forms['listForm'].elements['notes'].value=notes;
                            } else {
                                document.forms['listForm'].elements['notes'].value='';
                            }

                            tags = response.getElementsByTagName("Tag");
                            if (tags.length > 0) {
                                var output = '';
                                for(i = 0; i < tags.length; i++) {
                                    if (i > 0) {
                                        output = output + " ";
                                    }
                                    output = output + tags.item(i).firstChild.data;
                                }
                                document.forms['listForm'].elements['tags'].value=output;
                            } else {
                                document.forms['listForm'].elements['tags'].value='';
                            }
                        }
                    }
                }
                http2.send(null);
                popupMenu('listForm');
            } else {
                popupMenu('loginBox');
            }
        }
    }
    http.send(null);
}

function saveRecord(id, formElem)
{
    var tags = formElem.elements['tags'].value;
    var notes = formElem.elements['notes'].value;
    var list = formElem.elements['list'].options[formElem.elements['list'].selectedIndex].value;

    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=SaveRecord&" +
                 "tags=" + tags + "&" +
                 "list=" + list + "&" +
                 "notes=" + notes;
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                if (value == "Done") {
                    document.getElementById('saveLink'+id).style.backgroundColor = '#FFFFCC';
                    hideLightbox();
                } else {
                    getLightbox('Record', 'Save', id, null, 'Add to Favorites');
                }
            } else {
                document.getElementById('popupbox').innerHTML = 'Error: Record not saved';
                setTimeout("hideLightbox();", 3000);
            }
        },
        failure: function(transaction) {
            document.getElementById('popupbox').innerHTML = 'Error: Record not saved';
            setTimeout("hideLightbox();", 3000);
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function addList(title, desc, pub, id, text)
{
    var url = path + "/MyResearch/AJAX";
    var params = "method=AddList&" +
                 "title=" + title + "&" +
                 "public=" + pub + "&" +
                 "desc=" + desc;

    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                if (value == "Done") {
                    getLightbox('Record', 'Save', id, null, text);
                } else {
                    document.getElementById('popupbox').innerHTML = 'Error: List not created';
                    setTimeout("hideLightbox();", 3000);
                }
            } else {
                document.getElementById('popupbox').innerHTML = 'Error: List not created';
                setTimeout("hideLightbox();", 3000);
            }
        },
        failure: function(transaction) {
            document.getElementById('popupbox').innerHTML = 'Error: List not created';
            setTimeout("hideLightbox();", 3000);
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function getSaveStatuses(id)
{
    GetSaveStatusList[GetSaveStatusList.length] = id;
}

function doGetSaveStatuses()
{
    if (GetSaveStatusList.length < 1) return;

    var http = createRequestObject();
    var now = new Date();
    var ts = Date.UTC(now.getFullYear(),now.getMonth(),now.getDay(),now.getHours(),now.getMinutes(),now.getSeconds(),now.getMilliseconds());

    var url = path + "/Search/AJAX?method=GetSaveStatuses";
    for (var i=0; i<GetSaveStatusList.length; i++) {
        url += "&id" + i + "=" + GetSaveStatusList[i];
    }
    url += "&time="+ts;

    http.open("GET", url, true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {

            var response = http.responseXML.documentElement;
            var items = response.getElementsByTagName('item');

            for (var i=0; i<items.length; i++) {
                var elemId = items[i].getAttribute('id');

                var result = items[i].getElementsByTagName('result').item(0).firstChild.data;
                if (result != 'False') {
                    getElem('saveLink' + elemId).style.backgroundColor = '#FFFFCC';
                    var lists = eval('(' + result + ')');
                    getElem('lists' + elemId).innerHTML = '<li>' + lists.title + '</li>';
                }
            }
        }
    }
    http.send(null);
}

function showSuggestions(elem)
{
    if ((elem.value != '') && (document.searchForm.suggest.checked)) {
        var http = createRequestObject();
        http.open("GET", path + "/Search/Search/AJAX?method=GetSuggestion&phrase=" + elem.value, true);
        http.onreadystatechange = function()
        {
            if ((http.readyState == 4) && (http.status == 200)) {
                document.getElementById('SuggestionList').style.visibility = 'visible';
                document.getElementById('SuggestionList').innerHTML = '';

                var result = http.responseXML.documentElement.getElementsByTagName('result').item(0).firstChild.data;
                var resultList = result.split("|");

                for (i=0; i<10; i++) {
                    if (i==0) {
                        document.getElementById('SuggestionList').innerHTML = document.getElementById('SuggestionList').innerHTML + '<li class="top"><a href="">' + resultList[i] + '</a></li>';
                    } else {
                        document.getElementById('SuggestionList').innerHTML = document.getElementById('SuggestionList').innerHTML + '<li><a href="">' + resultList[i] + '</a></li>';
                    }
                }
            }
        }
        http.send(null);
    } else {
        document.getElementById('SuggestionList').style.visibility = 'hidden';
        document.getElementById('SuggestionList').innerHTML = '';
    }
}

function SendEmail(elems)
{
    document.getElementById('popupbox').innerHTML = '<h3>Sending Message ...</h3>';

    var from    = elems['from'].value;
    var to      = elems['to'].value;
    var message = elems['message'].value;

    var url = path + "/Search/AJAX";
    var params = "method=SendEmail&" +
                 "url=" + URLEncode(window.location.href) + "&" +
                 "from=" + from + "&" +
                 "to=" + to + "&" +
                 "message=" + message;
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                if (value == "Done") {
                    document.getElementById('popupbox').innerHTML = '<h3>Message Sent</h3>';
                    setTimeout("hideLightbox();", 3000);
                } else {
                    document.getElementById('popupbox').innerHTML = '<h3>Error - Message Cannot Be Sent</h3>';
                }
            } else {
                document.getElementById('popupbox').innerHTML = 'Error: Popup Box Contains No Data';
            }
        },
        failure: function(transaction) {
            document.getElementById('popupbox').innerHTML = 'Error: Cannot Send Email';
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function getSubjects(phrase)
{
    var liList = '';
    var http = createRequestObject();
    http.open("GET", path + "/Search/AJAX?method=GetSubjects&lookfor=" + phrase, true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var response = http.responseXML.documentElement;
            if (subjects = response.getElementsByTagName('Subject')) {
                for (i = 0; i < subjects.length; i++) {
                    if (subjects.item(i).firstChild) {
                        liList = liList + '<li><a href="">' + subjects.item(i).firstChild.data + '</a></li>';
                    }
                }
                document.getElementById('subjectList').innerHTML = liList;
            }
        }
    }
    http.send(null);
}

function getNarrowOptions(query, fquery, limit, optionLimit, link)
{
    var narrowList = getElem('narrowList');
    var loading = getElem('narrowLoading');
    // Remove page from URL
    //link = link.replace(/&page=[0-9]+/, '');
    // "link" has been escaped in the calling template
    link = link.replace(/%26page%3D[0-9]+/, '');
    link = link.replace(/checkspelling=true/, '');

    // Load Popup Box Content from AJAX Server
    var url = path + "/Search/AJAX";
    var params = 'method=GetNarrowOptions' +
                 '&filter=' + fquery +
                 '&limit=' + limit +
                 '&link=' + link +
                 '&optionLimit=' + optionLimit +
                 '&query=' + query
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                loading.style.display = 'none';
                narrowList.innerHTML = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
            } else {
                narrowList.innerHTML = 'Error: Cannot Load Facets';
            }
        },
        failure: function(transaction) {
            narrowList.innerHTML = 'Error: Cannot Load Facets';
        }
    }
    // var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
    var transaction = YAHOO.util.Connect.asyncRequest('POST', url, callback, params);
}
            
function setCookie(c_name,value,expiredays)
{
    var exdate = new Date();
    exdate.setDate(exdate.getDate()+ expiredays);
    document.cookie = c_name + "=" + escape(value) +
        ((expiredays==null) ? "" : ";expires=" + exdate.toGMTString())
}

function getCookie(c_name)
{
    if (document.cookie.length > 0)
    {
        c_start = document.cookie.indexOf(c_name + "=")
        if (c_start != -1)
        { 
            c_start = c_start + c_name.length + 1;
            c_end = document.cookie.indexOf(";",c_start);
            if (c_end == -1) 
                c_end = document.cookie.length;
            return unescape(document.cookie.substring(c_start,c_end));
        } 
    }
    return "";
}

function parseQueryString(qs, term)
{
    qs = qs + "";
    var list = new Array();
    var elems = qs.split("&");
    for (var i=0; i<elems.length; i++) {
        var pair = elems[i].split("=");
        if (pair[0].substring(0, term.length) != term) {
            list.push(elems[i]);
        }
    }
    return list.join('&');
}

function showThese(elemId)
{
   getElem('facet_end_' + elemId).style.display='block';
   getElem('more_' + elemId).style.display='none';
}

function hideThese(elemId)
{
   getElem('facet_end_' + elemId).style.display='none';
   getElem('more_' + elemId).style.display='block';
}

function URLEncode (clearString) {
  var output = '';
  var x = 0;
  clearString = clearString.toString();
  var regex = /(^[a-zA-Z0-9_.]*)/;
  while (x < clearString.length) {
    var match = regex.exec(clearString.substr(x));
    if (match != null && match.length > 1 && match[1] != '') {
    	output += match[1];
      x += match[1].length;
    } else {
      if (clearString[x] == ' ')
        output += '+';
      else {
        var charCode = clearString.charCodeAt(x);
        var hexVal = charCode.toString(16);
        output += '%' + ( hexVal.length < 2 ? '0' : '' ) + hexVal.toUpperCase();
      }
      x++;
    }
  }
  return output;
}
