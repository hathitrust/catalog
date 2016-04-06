<div class="headerlight2darkgrad"><!----></div>

{assign var=button1 value='record'}
{assign var=button2 value='newsearch'}
{assign var=button3 value='favorites'}
{include file='buttonbar.tpl'}

<div class="headergrad"><!----></div>    

<div class="contentbox">
	{if $message}
	  <div style="color: red">{$message}<br><br></div>
	{/if}
</div>

<div class="footergrad"><!----></div>

{*
{if $message}
  <div style="color: red">{$message}<br><br></div>
{/if}

<a href="/Record/{$id}" class="backtosearch"><img style="vertical-align: middle;" alt="Back to full record display" src="/static/umichwebsite/images/return.png">{translate text="Back to full record display"}</a><br><br>
*}

