//Script: feedbackHTForm.js

YAHOO.namespace("hathitrust");

        YAHOO.util.Event.addListener(window, "load", init);

        function init() {
        // Instantiate a Panel from markup
        YAHOO.hathitrust.panel1 = new YAHOO.widget.Panel("searchtips_help", { width:"400px", visible:false, constraintoviewport:true, draggable:true, fixedcenter:true, modal:true, close:true } );
        YAHOO.hathitrust.panel1.setHeader("Search Tips");
        YAHOO.hathitrust.panel1.render();

        // Instantiate a Panel from script

        YAHOO.hathitrust.panel2 = new YAHOO.widget.Panel("gettingStarted", { width:"650px", xy:[250,200], visible:false, constraintoviewport:true, draggable:true, fixedcenter:true, modal:true, close:true } );
        YAHOO.hathitrust.panel2.setHeader("Getting Started With HathiTrust");
        YAHOO.hathitrust.panel2.render();

        // Instantiate a Panel from script

        YAHOO.hathitrust.panel3 = new YAHOO.widget.Panel("emailThisRecord", { width:"400px", xy:[250,200], visible:false, constraintoviewport:true, draggable:true, fixedcenter:true, modal:true, close:true } );
        YAHOO.hathitrust.panel3.setHeader("Email this record");
        YAHOO.hathitrust.panel3.render();

        // Instantiate a Panel from script

        YAHOO.hathitrust.panel4 = new YAHOO.widget.Panel("emailThisSearch", { width:"400px", xy:[250,200], visible:false, constraintoviewport:true, draggable:true, fixedcenter:true, modal:true, close:true } );
        YAHOO.hathitrust.panel4.setHeader("Email this search");
        YAHOO.hathitrust.panel4.render();

        YAHOO.util.Event.addListener("searchTips", "click", YAHOO.hathitrust.panel1.show, YAHOO.hathitrust.panel1, true);
        YAHOO.util.Event.addListener("searchTips_close", "click", YAHOO.hathitrust.panel1.hide, YAHOO.hathitrust.panel1, true);

        YAHOO.util.Event.addListener("getStarted", "click", YAHOO.hathitrust.panel2.show, YAHOO.hathitrust.panel2, true);
        YAHOO.util.Event.addListener("getStarted_close", "click", YAHOO.hathitrust.panel2.hide, YAHOO.hathitrust.panel2, true);

        YAHOO.util.Event.addListener("emailRecord", "click", YAHOO.hathitrust.panel3.show, YAHOO.hathitrust.panel3, true);
        YAHOO.util.Event.addListener("emailRecord_close", "click", YAHOO.hathitrust.panel3.hide, YAHOO.hathitrust.panel3, true);

        YAHOO.util.Event.addListener("emailSearch", "click", YAHOO.hathitrust.panel4.show, YAHOO.hathitrust.panel4, true);
        YAHOO.util.Event.addListener("emailSearch_close", "click", YAHOO.hathitrust.panel4.hide, YAHOO.hathitrust.panel4, true);
        YAHOO.util.Event.addListener("emailSearch_lower", "click", YAHOO.hathitrust.panel4.show, YAHOO.hathitrust.panel4, true);
                }


var emailDefault = "Email address (optional)";
var commentsDefault = "Add your feedback here";
var emailLen = 96;
var commentsLen = 4096;
var width = 375;
var feedbackUrl;
var recaptchaArgs;
var captchaCgi;
var captchaValidation;
var protocol = window.location.protocol;

var PUBLIC_KEY;
var htfbhost = window.location.href;

/*window.onload=function() {
var aObj=document.getElementsByName('url')[0];
aObj.setAttribute('value', location);
};
*/
function clickclear(thisfield, defaulttext) {
if (thisfield.value == defaulttext) {
thisfield.value = "";
}
}
function clickrecall(thisfield, defaulttext) {
if (thisfield.value == "") {
thisfield.value = defaulttext;
}
}

function getPublicKey()
{
        if (htfbhost.indexOf("sdr.lib.umich.edu") >= 0)
        {
                return '6Lc84QIAAAAAAA2H-tsq08Cxn9fTOfg8hYGWN7M0';
        }
        else if (htfbhost.indexOf("mdp.lib.umich.edu") >= 0)
        {
                return '6Lc94QIAAAAAAHiFjwlWLhQCnaZjoypEaogs0tcZ';
        }
        else if (htfbhost.indexOf("babel.hathitrust.org") >= 0)
        {
                return '6Lc-4QIAAAAAAIlliZEO4MNxyBlY1utFvR7q0Suz';
        }
        else if (htfbhost.indexOf("umdl.umich.edu") >= 0)
        {
                return '6Lc_4QIAAAAAADemEBAW2CxlZwIOF90T0j99hMvK';
        }
        else
        {
                return '';
        }
}

function getHTFBFormHTML() {
        //Post to local feedback cgi if on clamato, post to quod if in production
        if (htfbhost.indexOf("umdl.umich.edu") >= 0)
        {
                feedbackUrl = protocol + "//" + location.hostname + "/cgi/f/feedback/feedback";
                captchaCgi = protocol + "//" + location.hostname + "/cgi/f/feedback/validatecaptcha";
        }
        else
        {
                feedbackUrl = "https://babel.hathitrust.org/cgi/feedback";
                captchaCgi = protocol + "//quod.lib.umich.edu/cgi/f/feedback/validatecaptcha";
        }

        var currentUrl = window.location;
        var HTform = "<div class=\"feedback_form\"><form method='post' id='HTfeedback' name='HTfeedback' action='" + feedbackUrl + "'>" +
                "<input type='hidden' value='ht' name='m'/>" +
                "<input type='hidden' value=" + currentUrl + " name='return'/>" +
                "<input type='hidden' value='HathiTrust Catalog' name='id'/>" +
                "<input name='email' id='email' maxlength='"+ emailLen + "' value='" + emailDefault +
                "' class='overlay' onclick=\"clickclear(this, '" + emailDefault + "')\" onfocus=\"clickclear(this, '" +
                emailDefault + "') this.focus();return false;\" onblur=\"clickrecall(this,'" + emailDefault + "')\" style='width:" +  width +"px'/><br />" +
                "<textarea name='comments' id='comment' class='overlay' rows='7' maxlength='" + commentsLen + "' onclick=\"clickclear(this, '" + commentsDefault + "')\" onfocus=\"clickclear(this, '" +
                commentsDefault + "')\" onblur=\"clickrecall(this,'" + commentsDefault + "')\" style='width:" +  width +"px'/>" + commentsDefault + "</textarea>" +
                "<div id='reCAPTCHA'></div>" +
                "<div id='HTFBError' style='color:red'><div class='bd'></div></div>" +
                "<div id='HTFBLoading'><div class='bd'><p><b>Loading...</b></p></div></div>" +
                "<table><tbody><tr valign='bottom'><td width='40'>" +
                "<button type='button' alt='submit' value='htFBSubmit' name='htFBSubmit' id='htFBSubmit'>Submit</button></td>" +
                "<td width='100' align='left'><a href='' id='htFBCancel'><b>Cancel</b></a></td>" +
                "</tr></tbody></table></form></div>";
        return HTform;
}

function initHTFeedback() {

        var browser = navigator.appName;

        if (browser =="Microsoft Internet Explorer") //Make non-modal for IE - made modal for IE by jjy 4/23/09
        {
                YAHOO.hathitrust.HTformWidget = new YAHOO.widget.Panel("HTformWidget", { width:'400px', visible:false, draggable:true, constraintoviewport:true, fixedcenter:true, close:true, modal:true, iframe: true} );
        }
        else
        {
                YAHOO.hathitrust.HTformWidget = new YAHOO.widget.Panel("HTformWidget", { width:'400px', visible:false, draggable:true, constraintoviewport:true, fixedcenter:true, close:true, modal:true, iframe: true} );
        }

        YAHOO.hathitrust.HTformWidget.setHeader("Feedback");
        YAHOO.hathitrust.HTformWidget.setBody("");
        YAHOO.hathitrust.HTformWidget.render(document.body);

}

var interceptHTFBSubmit = function(e)
{
        YAHOO.hathitrust.HTFBError.hide();
        YAHOO.hathitrust.HTFBLoading.hide();

        if (this.id=="htFBCancel")
        {
                YAHOO.hathitrust.HTformWidget.hide();
                YAHOO.util.Event.preventDefault(e);
        }
        else
        {
                var frm = document.getElementById("HTfeedback");

                //If no comments are entered, prevent submit
                if(frm.comments.value == commentsDefault)
                {
                        YAHOO.util.Event.preventDefault(e);
                        YAHOO.hathitrust.HTFBError.setBody("<p><b>You must enter feedback before submitting or click cancel.</b></p>");
                        YAHOO.hathitrust.HTFBError.show();
                        YAHOO.hathitrust.HTformWidget.render();
                }
                /* Temporarily comment-out recaptcha until validatecaptcha cgi is moved to server with https
                else if (frm.recaptcha_response_field.value == "")
                {
                        YAHOO.util.Event.preventDefault(e);
                        YAHOO.hathitrust.HTFBError.setBody("<p><b>You must respond to the captcha.</b></p>");
                        YAHOO.hathitrust.HTFBError.show();
                        YAHOO.hathitrust.HTformWidget.render();
                }*/

                else
                {
                        /* Temporarily comment-out recaptcha until validatecaptcha cgi is moved to server with https

                        YAHOO.hathitrust.HTFBLoading.show();
                        processHTFBRequest();
                        YAHOO.hathitrust.HTFBLoading.hide();

                        The code below is temporary and should be removed when the recaptcha is reactivated
                        */

                        YAHOO.hathitrust.HTFBError.hide();
                        frm.submit();
                }
        }

};


var displayHTFeedback = function(e) {
        YAHOO.util.Event.preventDefault(e);

        if (PUBLIC_KEY === undefined)
        {
                PUBLIC_KEY = getPublicKey();
        }

        //Temporarily comment-out recaptcha until validatecaptcha cgi is moved to server with https
        //setTimeout('Recaptcha.create("' + PUBLIC_KEY + '","reCAPTCHA", {theme: "white"})',5);

        YAHOO.hathitrust.HTformWidget.setBody(getHTFBFormHTML());

        YAHOO.hathitrust.HTFBError = new YAHOO.widget.Module("HTFBError", { visible: false });
        YAHOO.hathitrust.HTFBError.render();

        YAHOO.hathitrust.HTFBLoading = new YAHOO.widget.Module("HTFBLoading", { visible: false });
        YAHOO.hathitrust.HTFBLoading.render();

        YAHOO.hathitrust.reCAPTCHA =  new YAHOO.widget.Module("reCAPTCHA", { visible: true });
        YAHOO.hathitrust.reCAPTCHA.render();

        //Add listener to form submit and cancel button
        //YAHOO.util.Event.addListener("HTfeedback", "submit", interceptHTFBSubmit);
        YAHOO.util.Event.addListener("htFBSubmit", "click", interceptHTFBSubmit);

        YAHOO.util.Event.addListener("htFBCancel", "click", interceptHTFBSubmit);

        YAHOO.hathitrust.HTformWidget.show();
};

var callbackHTFB = {
        success: function(o){
                captchaValidation = o.responseText;

                if(captchaValidation.indexOf("SUCCESS") >= 0)
                {
                        //Remove the captcha from the feedback form DOM before emailing
                        var frm = document.getElementById("HTfeedback");
                        var olddiv = document.getElementById('reCAPTCHA');
                        frm.removeChild(olddiv);
                        YAHOO.hathitrust.HTFBError.hide();
                        frm.submit();
                }
                else
                {
                        YAHOO.hathitrust.HTFBError.setBody("Incorrect response to captcha. Captcha has been reloaded. If you cannot decipher the captcha, please click the reload or sound button in the captcha box.");
                        YAHOO.hathitrust.HTFBError.show();
                        YAHOO.hathitrust.HTformWidget.render();
                        Recaptcha.reload();
                }
        },
        failure: function(o) {
                captchaValidation = "COMMUNICATION FAILURE";
                YAHOO.hathitrust.HTFBError.setBody("Communication failure. Please try again.");
                YAHOO.hathitrust.HTFBError.show();
                YAHOO.hathitrust.HTformWidget.render();
        }
};

function processHTFBRequest()
{
        var htfbfrm = document.getElementById("HTfeedback");
        var recaptcha_challenge = htfbfrm.recaptcha_challenge_field.value;
        var recaptcha_response = encodeURIComponent(htfbfrm.recaptcha_response_field.value);
        recaptchaArgs = "recaptcha_challenge_field=" +
                recaptcha_challenge + ";recaptcha_response_field=" +
                recaptcha_response;
        var request = YAHOO.util.Connect.asyncRequest('POST', captchaCgi, callbackHTFB, recaptchaArgs);
        return false;
}




YAHOO.util.Event.addListener(window, "load", initHTFeedback);

YAHOO.util.Event.addListener("feedback", "click", displayHTFeedback);
YAHOO.util.Event.addListener("feedback_footer", "click", displayHTFeedback);

//End feedbackHTForm.js

