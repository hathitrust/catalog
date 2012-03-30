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
<script language="JavaScript" type="text/javascript">
{literal}

suffixes = ['holdings', 'on', 'off'];

function holdings_set_initial_state(prefix) 
{
  table = jq('#' + prefix + '_holdings');
  if (document.location.hash == '#' + prefix && jq('tr', table).size() > 3) {
    holdings_toggle(prefix);
    return;
  }
  if (jq('tr', table).size() <= 3 ) {
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

{* dummy elec, for possible google link *}
<div id="dummyElec" style="display:none">
<h3 class="holdings_header">Electronic Resources</h3>
<table style="width: auto; display:none" id="ELEC_holdings" cellpadding="2" cellspacing="0" border="0" class="citation">
</table>
</div>

<div class="toggle">
<a href="#" onclick="jq('#ELEC_holdings, #ELEC_on, #ELEC_off').toggle(); return false" title="Toggle display of individual items">
  <span id="ELEC_on"><i>more...</i></span>
  <span id="ELEC_off" style="display:none"><i>less...</i></span>
</a>
</div>
    
<script language="JavaScript" type="text/javascript">
  holdings_set_initial_state('ELEC');
</script>

{assign var=prev_location value=''}
{assign var=copy_table_open value=0}
{foreach from=$holdings item=copy key=copy_key name=copyloop}
  {assign var=num_items value=$copy.item_info|@count}
  {assign var='loc' value=$copy_key|replace:" ":"_"}
  <a name="{$loc}"></a>

  {assign var=summary_info value=''}
  {if $copy.public_note}{assign var=summary_info value=$summary_info|cat:"<h4>Note: "|cat:$copy.public_note|cat:"</h4>"}{/if}
  {if $copy.summary_holdings}{assign var=summary_info value=$summary_info|cat:"<h4>Library has: "|cat:$copy.summary_holdings|cat:"</h4>"}{/if}
  {if $copy.supplementary_material}{assign var=summary_info value=$summary_info|cat:"<h4>Supplementary material: "|cat:$copy.supplementary_material|cat:"</h4>"}{/if}
  {if $copy.index}{assign var=summary_info value=$summary_info|cat:"<h4>Index: "|cat:$copy.index|cat:"</h4>"}{/if}
  {if $num_items eq 0}
     {if $copy.callnumber}{assign var=summary_info value=$summary_info|cat:"<h4>Call number: "|cat:$copy.callnumber|cat:"</h4>"}{/if}
     {if $copy.status}{assign var=summary_info value=$summary_info|cat:"<h4>Status: "|cat:$copy.status|cat:"</h4>"}{/if}
  {/if}

  {if $copy.location != $prev_location}
    {assign var=copy_num value=1}
    <h3 class="holdings_header">{$copy.location} 
    {if $copy.info_link}
      <a href={$copy.info_link} target="new"><img src="{$path}/images/info.gif" alt="Library information"></a>
    {/if}
    </h3>
  {/if}
  {if $summary_info}{$summary_info}{/if}
  <table id="{$loc}_holdings" style="display:none; *width: auto; margin-top: 0; margin-bottom: 0;" class="citation" cellpadding="2" cellspacing="0" border="0">
  {foreach from=$copy.item_info item=item}
    <tr style="border-bottom: 1px solid #cccccc;">
    <td style="width: 20%;">
    {if $item.description}{$item.description}
{*
    {else}
      copy {$copy_num}
      {assign var=copy_num value=$copy_num+1}
 *}
    {/if}
    </td>
    {if $copy.sub_library == 'HATHI'}
      {if $item.rights eq 'opb'}
        <td style="width: 30%;"><a class="clickpostlog" 
                                   ref="rechathi|{$item.id}|hathi|{$item.status}" 
                                   target="link" href="http://hdl.handle.net/2027/{$item.id}">Limited Access</a><br/>
                                   [Full view available to authenticated UM users and in some UM Libraries ...  
                                   <i><a class="dolightbox" href="#section108">more</a></i> ]
                                   </td>
      {else}
        <td style="width: 30%;"><a class="clickpostlog" ref="rechathi|{$item.id}|hathi|{$item.status}" target="link" href="http://hdl.handle.net/2027/{$item.id}">{$item.status}</a></td>
      {/if}
      <td style="width: 30%;">(original from {$item.source})</td>
      <td style="width: 20%;"></td>
    {elseif $copy.sub_library == 'ELEC'}
      {assign var=item_link value = $item.link}
      {assign var=pitem_link value=$item_link}
      <td style="width: 30%;">{$item.note}</td>
      <td style="width: 30%;"><a class="clickpostlog" ref="recelink|{$record.id}|{$item_link}|{$item.status}" target="link" href="{$pitem_link}">{$item.status}</a></td>
      <td style="width: 20%;"></td>
    {else}
      <td style="width: 30%;">
      {if $item.callnumber}{$item.callnumber}{/if}
      {if $item.temp_location and $item.location != $copy.location}
        <br>Shelved at {$item.location}
      {/if}
      </td>
      <td style="width: 30%;">{$item.status}</td>
      <td style="width: 20%;">
      {if $item.can_request}
        <form method="get" action="{$url}/Record/{$id}/Hold">
        <input type="hidden" name="barcode" value="{$item.barcode}">
        <input type="submit" name="getthis" value="Get this">
        </form>
      {/if}
      {if $item.can_book and $showBooking}
        <button onClick="window.open('/Record/{$id}/Booking?full_item_key={$item.full_item_key}')">Advance booking</button>
      {/if}
      </td>
    {/if}
    </tr>
  {/foreach}
  </table>
  <div class="toggle">
  <a href="#" onclick="jq('#{$loc}_holdings, #{$loc}_on, #{$loc}_off').toggle(); return false" title="Toggle display of individual items">
    <span id="{$loc}_on"><i>more...</i><hr></span>
    <span id="{$loc}_off" style="display:none"><i>less...</i><hr></span>
  </a>
  </div>
  <script language="JavaScript" type="text/javascript">
    holdings_set_initial_state('{$loc}');
  </script>
  {* <hr> *}
  {assign var=prev_location value=$copy.location}
{/foreach}

</div> <!-- all_holdings -->

{if $history}
<h3>Most Recent Received Issues</h3>
<ul>
  {foreach from=$history item=row}
  <li>{$row.issue}</li>
  {/foreach}
</ul>
{/if}

