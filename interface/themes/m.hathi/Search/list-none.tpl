<div>

  <!-- Suggestions? -->
  {if $newPhrase}
  <p class="correction">{translate text='Did you mean'} <a href="{$url}/Search/{$action}?lookfor={$newPhrase|escape:"url"}&amp;type={$type}{$filterListStr}">{$newPhrase}</a>?</p>
  {/if}

  <p class="error">Your search - <b>{$lookfor}</b> - did not match any resources.</p>

  <p>You may want to try to revise your search phrase by removing some words.</p>
  
  {include file="searchbox.tpl"}
        
</div>