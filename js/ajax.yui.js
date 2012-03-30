/* AJAX Functions using YUI Connection Manager Functionality
 *
 * @todo: Please rewrite me as a class!!!
 */

function getLightbox(module, action, id, lookfor, message)
{
    if ((module == '') || (action == '')) {
        hideLightbox();
        return 0;
    }

    // Popup Lightbox
    lightbox();

    // Load Popup Box Content from AJAX Server
    var url = path + "/AJAX/Home";
    var params = 'method=GetLightbox' +
                 '&lightbox=true'+
                 '&submodule=' + module +
                 '&subaction=' + action +
                 '&id=' +id +
                 '&lookfor=' +lookfor +
                 '&message='+message;
    var callback = 
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                document.getElementById('popupbox').innerHTML =
                    response.getElementsByTagName('result').item(0).firstChild.nodeValue;
            } else {
                document.getElementById('popupbox').innerHTML = 'Error: Popup Box Contains No Data';
            }
        },
        failure: function(transaction) {
            document.getElementById('popupbox').innerHTML = 'Error: Cannot Load Popup Box';
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);

    // Make Popup Box Draggable
    var dd = new YAHOO.util.DD("popupbox");
    dd.setHandleElId("popupboxHeader");
}

function SaltedLogin(elems, module, action, id, lookfor, message)
{
    // Load Popup Box Content from AJAX Server
    var url = path + "/AJAX/Home";
    var params = 'method=GetSalt';
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                Login(elems,
                      response.getElementsByTagName('result').item(0).firstChild.nodeValue,
                      module, action, id, lookfor, message);
                
            }
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}

function Login(elems, salt, module, action, id, lookfor, message)
{
    var username = elems['username'].value;
    var password = elems['password'].value;

    // Encrypt Password
    //var cipher = new Blowfish(salt);
    //password = cipher.encrypt(password);
    //password = TEAencrypt(password, salt);
    password = rc4Encrypt(salt, password);

    // Process Login via AJAX
    var url = path + "/AJAX/Home";
    var params = 'method=Login' +
                 '&username=' + username +
                 '&password=' + hexEncode(password);
    var callback =
    {
        success: function(transaction) {
            var response = transaction.responseXML.documentElement;
            if (response.getElementsByTagName('result')) {
                getLightbox(module, action, id, lookfor, message);
            }
        }
    }
    var transaction = YAHOO.util.Connect.asyncRequest('GET', url+'?'+params, callback, null);
}
