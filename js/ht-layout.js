 function SubmitFeedback(frm) {
    jq.post('/static/feedback.php',
            {'comment': jq("[name='comment']", frm).val(),
             'uniqname': jq("[name='uniqname']", frm).val(),
             'subject':  jq("[name='subject']", frm).val(),
             'url': jq("[name='url']:checked", frm).val()
            },
            function(data) {
              document.getElementById('popupbox').innerHTML = '<div style="padding: 4em;"><h3>Message Sent</h3><p>Thank you for your feedback!</p></div>';
              setTimeout("hideLightbox();", 3000);
              return false;
            }

          );
    return false;
  }

String.prototype.trim = function() {
  return this.replace(/^\s+|\s+$/g,"");
}

function trimForm(infrm) {
  infrm.value = infrm.value.trim()
  return true;
 }

function loginLink() {
  login = 'https://beta.lib.umich.edu/vf/vflogin?returnto=';
  loc = window.location.href;
  return login + escape(loc);
}

closeButton = ' <div style="float: right"><a href="#" onclick="hideLightbox(); return false">Close [X]</a></div>'

function fillLightbox(id) {
  lightbox();
  document.getElementById('popupbox').innerHTML = closeButton + document.getElementById(id).innerHTML;
  return false;
}

jq.fn.clearForm = function(exceptions) {
  return this.each(function() {
    if (jq(this).hasClass('stickyform')) {
      return;
    }
    var type = this.type, tag = this.tagName.toLowerCase();
    if (tag == 'form')
      return jq(':input',this).clearForm();
    if (type == 'text' || type == 'password' || tag == 'textarea')
      this.value = '';
    else if (type == 'checkbox' || type == 'radio')
      this.checked = false;
    else if (tag == 'select')
      this.selectedIndex = 0;
  });
};

function pausecomp(millis)
{
  var date = new Date();
  var curDate = null;

  do { curDate = new Date(); }
  while(curDate-date < millis);
}

function submitFeedback()
{
  document.getElementById('popupbox').innerHTML +=  document.getElementById('feedback_thanks').innerHTML;
  setTimeout("hideLightbox()", 1500);
  return true;
}

function hideMenu(elemId)
{
    document.getElementById(elemId).style.display='none';
}

  function changetop(A){
    // var B=document.getElementById(A);
    // var C=BrowserDetect.browser;
   // if(C=="Safari"){
    //      } else {
    B.className="specialstyle";
    // }
    return false;
  }

  function resumetop(A) {
    var B=document.getElementById(A);
    B.className="";
    return false;
  }


  this.randomtip = function(){
    var length = $("#featured .itemList").length;
    var ran = Math.floor(Math.random()*length) + 1;
    $("#featured .itemList:nth-child(" + ran + ")").show();
  };

  $(document).ready(function(){
    randomtip();
  });

