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

function LoadOptions(query, field, elem, nextElem, nextField)
{
    var responseHTML = '';
    var facetText = '';

    var loadingHTML = '<div id="narrowLoading">' +
                      '<img src="' + path + '/images/loading.gif" alt="Loading"><br>' +
                      'Loading Options ...' +
                      '</div>';

    document.getElementById(elem).innerHTML = loadingHTML;

    var http = createRequestObject();
    http.open("GET", path + "/Browse/AJAX?method=GetOptions&query=" + query + "&field=" + field, true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var response = http.responseXML.documentElement;
            if (options = response.getElementsByTagName('item')) {
                for (i=0; i<options.length; i++) {
                    facetText = options.item(i).firstChild.data;

                    if (nextElem) {
                        responseHTML += '<li style="float: none;">' +
                                        '<a href="" onClick="this.style.background=\'URL('+path+'/images/tab_active_bg.jpg)\'; LoadOptions(\'' + nextField + ':%22' + facetText + '%22\', \'' + nextField + '\', \'' + nextElem + '\'); return false;">' +
                                        facetText + ' (' + options.item(i).getAttribute('count') + ')</a>' +
                                        '</li>';
                    } else {
                        // Final Column
                        type = getType(field);
                        responseHTML += '<li style="float: none;">' +
                                        '<a style="float: right; font-size:70%;" href="' + path + '/Search/Home?lookfor[]=%22' + facetText + '%22&type[]=' + type + '&filter[]=' + query + '">View Records</a>' +
                                        '<a href="' + path + '/Search/Home?lookfor[]=%22' + facetText + '%22&type[]=' + type + '&filter[]=' + query + '">' +
                                        facetText + ' (' + options.item(i).getAttribute('count') + ')</a>' +
                                        '</li>';
                    }
                }
                document.getElementById(elem).innerHTML = responseHTML;
            }
        }
    }
    
    http.send(null);
}

function LoadAlphabet(field, column, lookfor)
{
    var responseHTML = '';
    var facetText = '';
    var reg = /[A-Z]/;

    var loadingHTML = '<div id="narrowLoading">' +
                      '<img src="' + path + '/images/loading.gif" alt="Loading"><br>' +
                      'Loading Options ...' +
                      '</div>';

    document.getElementById(column).innerHTML = loadingHTML;

    var http = createRequestObject();
    http.open("GET", path + "/Browse/AJAX?method=GetAlphabet&field=" + field, true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var response = http.responseXML.documentElement;
            if (options = response.getElementsByTagName('item')) {
                for (i=0; i<options.length; i++) {
                    facetText = options.item(i).firstChild.data;
                    
                    // Only include Alphabetical Responses
                    if (reg.exec(facetText)) {
                        responseHTML += '<li style="float: none;">' +
                                        '<a href="" onClick="this.style.background=\'URL('+path+'/images/tab_active_bg.jpg)\'; LoadOptions(\'' + lookfor + ':' + facetText + '*\', \'' + lookfor + '\', \'list4\'); return false;">' +
                                        facetText + ' (' + options.item(i).getAttribute('count') + ')</a>' +
                                        '</li>';
                    }

                }
                document.getElementById(column).innerHTML = responseHTML;
            }
        }
    }

    http.send(null);
}

function LoadSubject(field, column, lookfor)
{
    var responseHTML = '';
    var facetText = '';

    var loadingHTML = '<div id="narrowLoading">' +
                      '<img src="' + path + '/images/loading.gif" alt="Loading"><br>' +
                      'Loading Options ...' +
                      '</div>';

    document.getElementById(column).innerHTML = loadingHTML;

    var http = createRequestObject();
    http.open("GET", path + "/Browse/AJAX?method=GetSubjects&field=" + field, true);
    http.onreadystatechange = function()
    {
        if ((http.readyState == 4) && (http.status == 200)) {
            var response = http.responseXML.documentElement;
            if (options = response.getElementsByTagName('item')) {
                for (i=0; i<options.length; i++) {
                    facetText = options.item(i).firstChild.data;
                    responseHTML += '<li style="float: none;">' +
                                    '<a style="float: right; font-size:70%;" href="' + path + '/Search/Home?lookfor[]=%22' + facetText + '%22&type[]=' + field + '">View Records</a>' +
                                    '<a href="" onClick="this.style.background=\'URL('+path+'/images/tab_active_bg.jpg)\'; LoadOptions(\'' + field + ':%22' + facetText + '%22\', \'' + lookfor + '\', \'list4\'); return false;">' +
                                    facetText + ' (' + options.item(i).getAttribute('count') + ')</a>' +
                                    '</li>';
                }
                document.getElementById(column).innerHTML = responseHTML;
            }
        }
    }

    http.send(null);
}

function getType(field)
{
    switch(field) {
        case 'authorStr':
            field = 'author';
            break;
        case 'topicStr':
            field = 'topic';
            break;
        case 'genreStr':
            field = 'genre';
            break;
        case 'eraStr':
            field = 'era';
            break;
        case 'geographicStr':
            field = 'geo';
            break;
    }
    return field;
}