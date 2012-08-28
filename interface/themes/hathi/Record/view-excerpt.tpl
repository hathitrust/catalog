{foreach from=$reviews item=review}
  <p class="summary">{$review.Content}</p>
  {$review.Copyright}
  <hr>
{foreachelse}
No excerpts were found for this record.
{/foreach}

{if $reviewProvider == "Amazon"}
<div>
  <a target="new" style="font-size: 6pt; color: #999999;"
     href="http://amazon.com/dp/{$record.020.a}">Supplied by Amazon</a>
</div>
{/if}
