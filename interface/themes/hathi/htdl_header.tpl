  <!-- NAVBAR -->
  <div class="navbar navbar-static-top navbar-inverse">
    <div class="navbar-inner">
      <ul id="nav" class="nav">
        <li><a href="http://www.hathitrust.org">Home</a></li>
        <li><a href="http://www.hathitrust.org/about">About</a>
          <ul>
          <li><a href="http://www.hathitrust.org/partnership">Our Partnership</a></li>
          <li><a href="http://www.hathitrust.org/digital_library">Our Digital Library</a></li>
          <li><a href="http://www.hathitrust.org/htrc">Our Research Center</a></li>
          <li><a href="http://www.hathitrust.org/news_publications">News &amp; Publications</a></li>
          </ul></li>
        <li><a href="http://babel.hathitrust.org/cgi/mb">Collections</a></li>
        <li class="divider-vertical"></li>
        <li class="help"><a href="http://www.hathitrust.org/help">Help</a></li>
      </ul>
      <!-- IF LOGGED IN
      <ul id="person-nav" class="nav pull-right">
        <li><span>Hi bjensen!</span></li>
        <li><a href="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=priv">My Collections</a></li>
        <li><a href="http://babel.hathitrust.org/cgi/logout">Logout</a></li>
      </ul>
      -->
    </div>
  </div>

  <!-- HEADER -->
  <div class="container centered header clearfix">
    <div class="logo">
      <a href="http://www.hathitrust.org"><span class="offscreen">HathiTrust Digital Library</span></a>
    </div>
    <div class="search-form">

      <form action="/cgi/ls/one" method="GET">
        <div class="search-tabs">
          <input name="target" type="radio" id="option-full-text-search" value="ls" />
          <label for="option-full-text-search" class="search-label-full-text">Full-text</label>
          <input name="target" type="radio" id="option-catalog-search" value="catalog" checked="checked"/>
          <label for="option-catalog-search" class="search-label-catalog">Catalog</label>
        </div>
        <fieldset>
          <input name="q1" type="text" class="search-input-text" placeholder="Search words about or within the items" value="{$lookfor}" />
          <div class="search-input-options">
            <select size="1" class="search-input-select" name="searchtype">
              <option value="all">Everything</option>
              <option value="title">Title</option>
              <option value="author">Author</option>
              <option value="subject">Subject</option>
              <option value="isbn">ISBN/ISSN</option>
              <option value="publisher">Publisher</option>
              <option value="seriestitle">Series Title</option>
              <option value="pubyear">Publication Year</option>
            </select>
          </div>
          <button class="button"><span class="offscreen">Search</span></button>
        </fieldset>
        <div class="search-extra-options">
          <ul class="search-links">
            <li class="search-advanced-link"><a href="#">Advanced full-text search</a></li>
            <li class="search-catalog-link"><a href="#">Advanced catalog search</a></li>
            <li><a href="#">Search tips</a></li>
          </ul>
          <label>
            <input type="checkbox" value="ft" />
            Full view only
          </label>
        </div>
      </form>

    </div>
    <div class="login">
      <!-- NEEDS TO REFLECT LOGIN STATUS -->
      <a href="#" id="login-button" class="button log-in">LOG IN</a>
    </div>
  </div>

