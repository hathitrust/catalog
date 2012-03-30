<div class="headerlight2darkgrad"><!----></div>

{assign var=button1 value='record'}
{assign var=button2 value='newsearch'}
{assign var=button3 value='favorites'}
{include file='buttonbar.tpl'}

<div class="headergrad"><!----></div>    
    
<div class="contentbox">
	<div class="record">
    	<h1>{$record.245.a.0} {$record.245.b.0}</h1>
		{include file="Record/$subTemplate"}
	</div>
</div>

<div class="footergrad"><!----></div>
    