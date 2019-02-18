<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
      <div class="record">
        <!-- Suggestions? -->
        {if $newPhrase}
        <p class="correction">{translate text='Did you mean'} <a href="{$url}/Search/{$action|escape:"url"}?lookfor={$newPhrase|escape:"url"}&amp;type={$type}{$filterListStr}">{$newPhrase}</a>?</p>
        {/if}

        <p class="error">Your
          <strong>{if $check_ft_checkbox}Full view only{/if}</strong>
           search &mdash; 
           <strong>{$searchterms|escape}</strong>
           &mdash; did not match any resources.</p>
         

        <p>You may want to try to revise your search phrase by removing some words.</p>
      </div>
    </div>
  </div>
</div>