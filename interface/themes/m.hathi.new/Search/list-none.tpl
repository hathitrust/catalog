{*
<div class="headerlight2darkgrad"><!----></div>


<div class="headergrad"><!----></div>

<div class="contentbox">
	*}{* <!-- Suggestions? --> *}
	{* todo -- new phrase??? *}{*
	{if $newPhrase}
		<p class="correction">{translate text='Did you mean'} <a href="{$url}/Search/{$action}?lookfor={$newPhrase|escape:"url"}&amp;type={$type}{$filterListStr}">{$newPhrase}</a>?</p>
	{/if}
	<p>Your search - <b>{$lookfor}</b> - did not match any resources.</p>
	<p>You may want to try to revise your search phrase by removing some words.</p>
</div>
*}
{assign var=noresult value="true"}
{include file="searchbox.tpl"}

{*
<div class="footergrad"><!----></div>
*}

   