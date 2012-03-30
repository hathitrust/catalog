<div class="headerlight2darkgrad"><!----></div>

{assign var=button2 value='newsearch'}
{include file='buttonbar.tpl'}

{if $subpage}
	{include file=$subpage}
{else}
	{$pageContent}
{/if}

<ul id="recordTools" class="list">
	{* todo -- remove hardcoded URL *}
	<li class="recordToolLink linkeditemrightarrow">
		{*<a class="linkeditemlink" href="{$regular_url}{$smarty.server.REQUEST_URI}&mdetect=no" target="Mirlyn">{translate text='View Favorites in Regular Catalog'}</a>*}
		<a href="{$regular_url}{$smarty.server.REQUEST_URI}&mdetect=no" target="Mirlyn">{translate text='View Favorites in Regular Catalog'}</a>
	</li>
</ul>

<div class="footergrad"><!----></div>	