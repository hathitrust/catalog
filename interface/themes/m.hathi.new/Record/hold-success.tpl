<div class="headerlight2darkgrad"><!----></div>

{assign var=button1 value='record'}
{assign var=button2 value='newsearch'}
{assign var=button3 value='favorites'}
{include file='buttonbar.tpl'}

<div id="detailHeader" >

	<h1>{"<br/>"|implode:$record.titles}</h1>
	{if $record.author}
		<label>{translate text='Author'}:</label>
		<span class="value">{"<br/>"|implode:$record.author}</span>
		<br />
	{/if}
	<label>{translate text='Call Number'}:</label>
	<span class="value">{$record.item.callnumber}</span>
	{*
	{if $record.item.description}
		<br />
		<label>{translate text='Volume or Issue'}:</label>
		<span class="value">{$record.item.description}</span>	
	{/if}
	*}
	<br />
	<label>{translate text='Owning library'}:</label>
	<span class="value">{$record.item.location}</span>
	{*
	<br />
	<label>{translate text='Item status'}:</label>
	<span class="value">{$record.item.status}</span>
	*}	
</div>

<div class="headergrad"><!----></div>    

<div class="contentbox">

	{if $message}
	  <div style="color: green">{$message}<br><br></div>
	{/if}

	{*<a href="/Record/{$id}" class="backtosearch"><img style="vertical-align: middle;" alt="Back to full record display" src="/static/umichwebsite/images/return.png">{translate text="Back to full record display"}</a><br><br>*}
	{*
	<table>
	  <tr><th>Title:</th><td>{"<br>"|implode:$record.title}</th></tr>
	  <tr><th>Call number:</th><td>{$callnumber}</th></tr>
	  <tr><th>Owning library:</th><td>{$location}</th></tr>
	  <tr><th>Name:</th><td>{$patron_name}</th></tr>
	</table>
	*}
</div>

<div class="footergrad"><!----></div>
