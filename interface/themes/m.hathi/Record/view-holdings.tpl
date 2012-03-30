<!--
{assign var=marcField value=$marc->getFields('856')}
{if $marcField}
<h3>Internet</h3>
{foreach from=$marcField item=field name=loop}
{if $proxy}
<a href="{$proxy}/login?url={$field|getvalue:'u'}">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{$field|getvalue:'u'}{/if}</a><br/>
{else}
<a href="{$field|getvalue:'u'}">{if $field|getvalue:'z'}{$field|getvalue:'z'}{else}{$field|getvalue:'u'}{/if}</a><br/>
{/if}
{/foreach}
{/if}
<br>
-->
<!--
<script language="JavaScript" type="text/javascript">
{literal}

suffixes = ['holdings', 'on', 'off'];

function holdings_set_initial_state(prefix) 
{
  table = jq('#' + prefix + '_holdings');
  if (jq('tr', table).size() <= 3) {
    holdings_toggle(prefix);
    jq('#' + prefix + '_off').hide();
  }
  return false;
}

function holdings_toggle(prefix)
{
  idstring = jq.map(suffixes, function(n, i) { return '#' + prefix + '_' + n}).join(', ');
  jq(idstring).toggle();
  return false;
}
{/literal}
</script>


<a name="holdings"></a>
<div id="all_holdings">
-->
<!--
{if $elec_holdings}
<h3 class="holdings_header">{$elec_holdings.location}</h3>
<table style="width: auto; display: none" id="elec_holdings" cellpadding="2" cellspacing="0" border="0" class="citation">
{foreach from=$elec_holdings.item_info item=item}
  {assign var=item_link value = $item.link}
  {if $proxy}
    {assign var=item_link value = "$proxy/login?url=$item_link"}
  {/if}
<tr>
  {if $item.description}
     <th>{$item.description}</th>
  {else}
     <th></th>
  {/if}
  <td style="width: 50%;">{$item.note}</td>
  
-->  
  <!-- <td><a target=link href="{$item.link}">{$item.status}</a></td>  -->
<!--
    <td><a target=link href="{$item_link}">{$item.status}</a></td> 
</tr>
{/foreach}
</table>
{/if} -->

<!--<div class="toggle">
<a href="#" onclick="jq('#elec_holdings, #elec_on, #elec_off').toggle(); return false" title="Toggle display of individual items">
  <span id="elec_on"><i>more...</i></span>
  <span id="elec_off" style="display:none"><i>less...</i></span>
</a>
</div>
-->
  <!--
<script language="JavaScript" type="text/javascript">
  holdings_set_initial_state('elec');
</script>
-->
 <!--
{if $hathi_holdings}
<h3 class="holdings_header">{$hathi_holdings.location}</h3>
<table style="width: auto; display: none" id="hathi_holdings" cellpadding="2" cellspacing="0" border="0" class="citation">
{foreach from=$hathi_holdings.item_info item=item}
<tr>
  {if $item.description}
     <th>{$item.description}</th>
  {else}
     <th></th>
  {/if}
  <td><a target=link href=http://hdl.handle.net/2027/{$item.id}>{$item.status}</a></td>
  <td>(original from {$item.source})</td>
</tr>
{/foreach}
</table>
{/if}
-->
<!--
<div class="toggle">
<a href="#" onclick="jq('#hathi_holdings, #hathi_on, #hathi_off').toggle(); return false" title="Toggle display of individual items">
  <span id="hathi_on"><i>more...</i></span>
  <span id="hathi_off" style="display:none"><i>less...</i></span>
</a>
</div>
-->
 <!--
<script language="JavaScript" type="text/javascript">
  holdings_set_initial_state('hathi');
</script>
-->
<!-- Start NonHTDL holdings

{foreach from=$holdings item=copy}
{assign var='sublib' value=$copy.sublib_code}
{assign var='collection' value=$copy.collection_code}
{assign var='loc' value="$sublib$collection"}
<h3 class="holdings_header">{$copy.location}</a></h3>
{if $copy.callnumber}<h4>Call number:  {$copy.callnumber}</h4>{/if}
{if $copy.public_note} <h4>Note:  {$copy.public_note}</h4>{/if}
{if $copy.summary_holdings} <h4>Library has:  {$copy.summary_holdings}</h4>{/if}
{if $copy.index} <h4>Index:  {$copy.index}</h4>{/if}
<table id="{$loc}_holdings" style="display:none; *width: auto;" cellpadding="2" cellspacing="0" border="0" class="citation">
{foreach from=$copy.item_info item=item}
<tr>
  {if $item.description}
     <th>{$item.description}</th>
  {else}
     <th></th>
  {/if}
  <td>{$item.status}</td>
   {if $item.can_request}
   <td>-->
     <!-- <a href="{$url}/Record/{$id}/Hold/{$item.barcode}">Get this</a> -->
     <!--<a href="#" onclick="fillLightbox('{$id}-getthis');return false;">Get this</a>
     <div id="{$id}-getthis" style="display: none">
       <div style="text-align: left; padding: 1em;">
       <h2>Get this</h2>
       <p>We are still working on Get This.  For now, you can request this title via the current <a  href="http://mirlyn.lib.umich.edu/">Mirlyn Catalog</a>.</p>
       </div>
     </div></td>
   {/if}
</tr>
{/foreach}-->
 <!--
</table>
-->
<!-- End NonHTDL holdings-->


<!--<div class="toggle">
<a href="#" onclick="jq('#{$loc}_holdings, #{$loc}_on, #{$loc}_off').toggle(); return false" title="Toggle display of individual items">
  <span id="{$loc}_on"><i>more...</i></span>
  <span id="{$loc}_off" style="display:none"><i>less...</i></span>
</a>
</div>
-->
 <!--
<script language="JavaScript" type="text/javascript">
  holdings_set_initial_state('{$loc}');
</script>
{/foreach}

</div>--> <!-- all_holdings -->



 <!--
{if $history}
<h3>Most Recent Received Issues</h3>
<ul>
  {foreach from=$history item=row}
  <li>{$row.issue}</li>
  {/foreach}
</ul>
{/if}
-->
