<div id="bd">
  <div class="yui-main content">
    <div class="yui-b first contentbox">
      <p>
        <h3>Browse By Top Language</h3>
        {foreach from=$subjectList.3.item item=tag}
        <a href="/Search/Home?lookfor=%22{$tag._content}%22&type=language">{$tag._content}</a> ({$tag.count})
        {/foreach}
      </p>
      <p>
        <h3>Browse By Top Authors</h3>
        {foreach from=$subjectList.4.item item=tag}
        <a href="/Search/Home?lookfor=%22{$tag._content}%22&type=author">{$tag._content}</a> ({$tag.count})
        {/foreach}
      </p>
      <p>
        <h3>Browse By Top Topics</h3>
        {foreach from=$subjectList.0.item item=tag}
        <a href="/Search/Home?lookfor=%22{$tag._content}%22&type=topic">{$tag._content}</a> ({$tag.count})
        {/foreach}
      </p>
      <p>
        <h3>Browse By Top Genres</h3>
        {foreach from=$subjectList.1.item item=tag}
        <a href="/Search/Home?lookfor=%22{$tag._content}%22&type=genre">{$tag._content}</a> ({$tag.count})
        {/foreach}
      </p>
      <p>
        <h3>Browse By Top Locations</h3>
        {foreach from=$subjectList.2.item item=tag}
        <a href="/Search/Home?lookfor=%22{$tag._content}%22&type=geographic">{$tag._content}</a> ({$tag.count})
        {/foreach}
      </p>

      <!-- display tag cloud -->
      <h3>Browse By Tag</h3>
      {foreach from=$tagCloud item=font_sz key=tag}
        <span class="cloud{$font_sz}">
        <a href="/Search/Home?tag={$tag}">{$tag}</a>
        </span>
      {/foreach}
    </div>
  </div>
</div>