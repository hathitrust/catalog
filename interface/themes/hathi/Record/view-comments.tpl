<ul class="commentList" id="commentList">
{foreach from=$commentList item=comment}
  <li>
    {$comment->comment}
    <div class="posted">Posted by <strong>{$comment->fullname}</strong> on {$comment->created}</div>
    {if $comment->user_id == $user->id}
    <a href="{$url|escape:"url"}/Record/{$id}/UserComments?delete={$comment->id}" class="delete tool">Delete</a>
    {/if}
  </li>
{/foreach}
</ul>
{if !$commentList}
<p>Be the first to leave a comment!</p>
{/if}

<form name="commentForm" id="commentForm">
  <p><textarea name="comment" rows="4" cols="50"></textarea></p>
  <a href="{$url|escape:"url"}/Record/{$id}/UserComments" class="tool add"
     onClick="SaveComment('{$id}'); return false;">{translate text="Add your comment"}</a>
</form>