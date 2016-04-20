  <!-- NAVBAR -->
  <h2 class="offscreen">Navigation</h2>
  <div class="navbar navbar-static-top navbar-inverse" role="navigation">
    <div class="navbar-inner">
      <ul id="nav" class="nav">
        <li><a href="//www.hathitrust.org">Home</a></li>
        <li><a href="//www.hathitrust.org/about">About</a>
          <ul>
          <li><a href="//www.hathitrust.org/partnership">Our Partnership</a></li>
          <li><a href="//www.hathitrust.org/digital_library">Our Digital Library</a></li>
          <li><a href="//www.hathitrust.org/htrc">Our Research Center</a></li>
          <li><a href="//www.hathitrust.org/news_publications">News &amp; Publications</a></li>
          </ul></li>
        <li><a href="//babel.hathitrust.org/cgi/mb">Collections</a></li>
        <li class="divider-vertical"></li>
        <li class="help"><a href="//www.hathitrust.org/help">Help</a></li>
        <li><a href="{$unicorn_root}/cgi/feedback?page=form" data-m="ht" data-id="HathiTrust Catalog Feedback" data-toggle="feedback">Feedback</a></li>      
      </ul>
      <!-- IF LOGGED IN
      <ul id="person-nav" class="nav pull-right">
        <li><span>Hi bjensen!</span></li>
        <li><a href="//babel.hathitrust.org/cgi/mb?a=listcs;colltype=priv">My Collections</a></li>
        <li><a href="//babel.hathitrust.org/cgi/logout">Logout</a></li>
      </ul>
      -->
    </div>
  </div>

  <!-- HEADER -->
  <div class="container centered header clearfix">
    <div class="logo">
      <a href="//www.hathitrust.org"><span class="offscreen">HathiTrust Digital Library</span></a>
    </div>
    <div class="search-form" role="search">

{if !$suppress_searchbox}
      <form action="{$unicorn_root}/cgi/ls/one" method="GET">
        <div class="search-tabs">
          <input name="target" type="radio" id="option-full-text-search" value="ls" />
          <label for="option-full-text-search" class="search-label-full-text">Full-text</label>
          <input name="target" type="radio" id="option-catalog-search" value="catalog" checked="checked"/>
          <label for="option-catalog-search" class="search-label-catalog">Catalog</label>
        </div>
        <fieldset>
          <label for="q1-input" class="offscreen">Search</label>
          <input id="q1-input" name="q1" type="text" class="search-input-text" placeholder="Search words about or within the items" value="{$lookfor|escape:'html'}" />
          <div class="search-input-options">
            <label for="search-input-select" class="offscreen">Search Field List</label>
            <select size="1" id="search-input-select" class="search-input-select" name="searchtype">
              <option value="all" {if $searchtype == 'all'}selected="selected"{/if}>All Fields</option>
              <option value="title" {if $searchtype == 'title'}selected="selected"{/if}>Title</option>
              <option value="author" {if $searchtype == 'author'}selected="selected"{/if}>Author</option>
              <option value="subject" {if $searchtype == 'subject'}selected="selected"{/if}>Subject</option>
              <option value="isbn" {if $searchtype == 'isbn'}selected="selected"{/if}>ISBN/ISSN</option>
              <option value="publisher" {if $searchtype == 'publisher'}selected="selected"{/if}>Publisher</option>
              <option value="seriestitle" {if $searchtype == 'seriestitle'}selected="selected"{/if}>Series Title</option>
            </select>
          </div>
          <button class="button"><span class="offscreen">Search</span></button>
        </fieldset>
        <div class="search-extra-options">
          <ul class="search-links">
            <li class="search-advanced-link"><a href="//babel.hathitrust.org/cgi/ls?a=page;page=advanced">Advanced full-text search</a></li>
            <li class="search-catalog-link"><a href="/Search/Advanced">Advanced catalog search</a></li>
            <li><a href="//www.hathitrust.org/help_digital_library#SearchTips">Search tips</a></li>
          </ul>
          <label>
            <input type="checkbox" name="ft" value="ft" {if $check_ft_checkbox}checked="checked"{/if}/>
            Full view only
          </label>
        </div>
      </form>
{/if}
    </div>
    <div class="login">
      <!-- NEEDS TO REFLECT LOGIN STATUS -->
      <a href="#" id="login-button" class="button log-in">LOG IN</a>
    </div>
  </div>

