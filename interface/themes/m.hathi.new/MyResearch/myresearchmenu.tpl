<div class="headerlight2darkgrad"><!----></div>

{assign var=button2 value='newsearch'}
{include file='buttonbar.tpl'}

<div class="contentbox">{$username}'s Account</div>

<ul id="myResearchMenu" class="list">
	<li class="linkeditemrightarrow">
		<a href="{$base_url}/MyResearch/Favorites"><span class="favorites" >&nbsp;Favorites</span></a>
	</li>
	<li class="linkeditemrightarrow">
		<a href="{$base_url}/MyResearch/CheckedOut" >{translate text='Checked Out Items'}</a>
	</li>	
	<li class="linkeditemrightarrow">
	    <a href="/MyResearch/Logout">{translate text="Log Out"}</a>
	</li>
</ul>

<div class="footergrad"><!----></div>	