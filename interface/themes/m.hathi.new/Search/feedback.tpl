<div class="header">
  <a href="{$site.home_url}" class="htlogobutton" ></a>
  <a class="backlink"  href="{$site.url}{$smarty.get.currenturl}">&lt;&lt;&nbsp;Back</a>
</div>

<div class="contentbox">
{if $errormessage}<div id="errormessage">{$errormessage}</div><br />{/if}


<div class="bd">
  <div class="feedback_form">
    <form method="post" id="HTfeedback" name="HTfeedback" action="http://babel.hathitrust.org/cgi/feedback">
      <label>HathiTrust Mobile Feedback Form</label>
      <input type="hidden" value="ht" name="m">
      <input type="hidden" value="{$return}" name="return">
      <input type="hidden" value="HathiTrust Catalog" name="id">
      <p><label for="email">Your email</label></p>
      <input name="email" class="feedbackfield" id="email" maxlength="96" value=""><br>
      
      <p><label for="comment">Comments / Questions *</label></p>
      <textarea name="comments" id="comment" class="feedbackfield overlay" rows="4" maxlength="256"></textarea>
       <input type="submit" value="Submit">
    </form>
  </div>
</div>


{*	<form method="post" action="{$url}/Search/Feedback">
<input type="hidden" name="useragent" value="{$smarty.server.HTTP_USER_AGENT}">
<input type="hidden" name="currenturl" value="{$url}{$smarty.get.currenturl}">
<label>HathiTrust Mobile Feedback Form</label>

<p>
<label>Your email</label><br />
<input class="feedbackfield" name="from" type="text" maxlength="100" {if $from}value="{$from}"{/if}/>
</p>
<p>
<label>Comments/Questions *</label>
<textarea  class="feedbackfield" name="message" {if $message}value="{$message}"{/if}></textarea>
</p>
<input type="submit" value="Submit"/>
</form>

*}
</div>

<div class="footergrad"><!----></div>

