function displaySFXOptions(id)
{
    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=GetSFXData";
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                // Do something here
            }
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function getSaveStatus(id, elemId)
{
    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=GetSaveStatus";
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                if (value == 'Saved') {
                    document.getElementById(elemId).style.backgroundColor = '#FFFFCC';
                }
            }
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
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
                    document.getElementById('saveLink').style.backgroundColor = '#FFFFCC';
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

function addList(title, desc, public, id, text)
{
    var url = path + "/MyResearch/AJAX";
    var params = "method=AddList&" +
                 "title=" + title + "&" +
                 "public=" + public + "&" +
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

function SendEmail(id, to, from, message)
{
    document.getElementById('popupbox').innerHTML = '<h3>Sending Message ...</h3>';

    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=SendEmail&" +
                 "from=" + from + "&" +
                 "to=" + to + "&" +
                 "message=" + encodeURI(message);
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

function SendSMS(id, to, provider)
{
    document.getElementById('popupbox').innerHTML = '<h3>Sending Message ...</h3>';

    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=SendSMS&" +
                 "to=" + to + "&" +
                 "provider=" + provider;
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                if (value == "Done") {
                    document.getElementById('popupbox').innerHTML = '<h3>Message Sent</h3>';
                    setTimeout("hideLightbox();", 3000);
                }
            } else {
                document.getElementById('popupbox').innerHTML = 'Error: Popup Box Contains No Data';
            }
        },
        failure: function(transaction) {
            document.getElementById('popupbox').innerHTML = 'Error: Cannot Send Text Message';
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function SaveTag(id, formElem)
{
    var tags = formElem.elements['tags'].value;

    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=SaveTag&tag=" + tags;
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                if (value == "Unauthorized") {
                    document.forms['loginForm'].elements['followup'].value='SaveRecord';
                    popupMenu('loginBox');
                } else {
                    GetTags(id, 'tagList');
                    document.getElementById('popupbox').innerHTML = '<h3>Tag Saved</h3>';
                    setTimeout("hideLightbox();", 3000);
                }
            } else {
                document.getElementById('popupbox').innerHTML = '<i>Error: Could Not Save Tags</i>';
            }
        },
        failure: function(transaction) {
            document.getElementById('popupbox').innerHTML = 'Error: Could Not Save Tags';
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function GetTags(id, elemId)
{
    var tags;
    var output = "";
    
    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=GetTags";
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                tags = response.getElementsByTagName("Tag");
                if(tags.length > 0) {
                    for(i = 0; i < tags.length; i++) {
                        if (i > 0) {
                            output = output + ", ";
                        }
                        output = output + '<a href="' + path + '/Search/Home?tag=' +
                                 tags.item(i).childNodes[0].nodeValue + '">' +
                                 tags.item(i).childNodes[0].nodeValue + '</a>';
                    }
                }
                document.getElementById(elemId).innerHTML = output;
            } else {
                document.getElementById(elemId).innerHTML = '<i>Error: Could Not Load Tags</i>';
            }
        },
        failure: function(transaction) {
            document.getElementById(elemId).innerHTML = 'Error: Could Not Load Tags';
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function SaveComment(id)
{
    comment = document.forms['commentForm'].elements['comment'].value;

    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=SaveComment&comment=" + comment;
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var value = response.getElementsByTagName('result').item(0).firstChild.nodeValue;
                if (value == "Done") {
                    document.forms['commentForm'].elements['comment'].value = '';
                    LoadComments(id);
                } else {
                    getLightbox('AJAX', 'Login', id, null, 'Save Comment');
                }
            } else {
                document.getElementById(elemId).innerHTML = '<i>Error: Could Not Save Comment</i>';
            }
        },
        failure: function(transaction) {
            document.getElementById(elemId).innerHTML = 'Error: Could Not Save Comment';
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function LoadComments(id)
{
    var output = '';
    
    var url = path + "/Record/" + id + "/AJAX";
    var params = "method=GetComments";
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                var commentList = response.getElementsByTagName('Comment');
                if (commentList.length > 0) {
                    for(i = 0; i < commentList.length; i++) {
                        output += '<li>';
                        output += commentList.item(i).childNodes[0].nodeValue;
                        output += '<div class="posted">Posted by <strong>';
                        output += commentList.item(i).getAttribute('by') + '</strong>';
                        output += ' on ' + commentList.item(i).getAttribute('on');
                        output += '</li>';
                    }
                }
                document.getElementById('commentList').innerHTML = output;
            } else {
                document.getElementById('commentList').innerHTML = '<i>Error: Could Not Save Comment</i>';
            }
        },
        failure: function(transaction) {
            document.getElementById('commentList').innerHTML = 'Error: Could Not Save Comment';
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function tagBox(link, title)  {
// displayOptions: ('all', 'none', 'mtagthis', 'all[|nomtagthis|nocollections|nohelp])
// Note: not setting displayOptions analogous to the 'none' option
//     'mtagthis':"Mtag this Page" box is displayed by itself
//     'none'    :no tagging elements are displayed
//     'all'     :all tagging elements are displayed
//                  example -> 'all|nomtagthis|nocollections|nohelp' (tags are presented by themselves)
//     'none'    :no tagging elements are displayed
  if(typeof(tagDisplay)=='undefined'){tagDisplay='all'}
  url = 'http://www.lib.umich.edu/mtagger/tags/getTagCloud?URL=' +
    encodeURIComponent(link) +
    '&title=' + encodeURIComponent(title) +
    '&tagDisplay=' + tagDisplay;
  document.write('<iframe name="tagging_iframe" id="tagging_iframe" src ="' + url
    + '" height="180" width="410" frameborder="0" scrolling="auto" allowTransparency="true"></iframe>');
}

