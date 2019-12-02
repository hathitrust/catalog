<header class="site-navigation" role="banner">
  <nav aria-label="about the site">
  <ul id="nav" class="nav">
    <li>
      <a class="home-link" href="https://www.hathitrust.org"><span class="offscreen-for-narrowest">Home</span></a>
    </li>
    <li class="menu nav-links">
      <a aria-expanded="false" class="menu" href="#" id="burger-menu"><i class="icomoon icomoon-reorder" aria-hidden="true"></i> Menu</a>
      <ul>
        <li class="menu">
          <a href="#" class="menu" aria-expanded="false" aria-haspopup="true" id="about-menu">About <span class="caret" aria-hidden="true"></span></a>
          <ul role="menu" aria-labelledby="about-menu" aria-hidden="true">
            <li>
              <a href="https://www.hathitrust.org/about" aria-role="menuitem">Welcome to HathiTrust</a>
            </li>
            <li>
              <a href="https://www.hathitrust.org/partnership" aria-role="menuitem">Our Partnership</a>
            </li>
            <li>
              <a href="https://www.hathitrust.org/digital_library" aria-role="menuitem">Our Digital Library</a>
            </li>
            <li>
              <a href="https://www.hathitrust.org/collaborative-programs" aria-role="menuitem">Our Collaborative Programs</a>
            </li>
            <li>
              <a href="https://www.hathitrust.org/htrc" aria-role="menuitem">Our Research Center</a>
            </li>
            <li>
              <a href="https://www.hathitrust.org/news_publications" aria-role="menuitem">News &amp; Publications</a>
            </li>
          </ul>
        </li>
        {if $ht_status.affiliation}
        <li><a href="{$unicorn_root}/cgi/mb?colltype=my-collections;a=listcs">My Collections</a></li>
        {/if}
        <li>
          <a href="{$unicorn_root}/cgi/mb">Collections</a>
        </li>
        <li class="help">
          <a href="https://www.hathitrust.org/help">Help</a>
        </li>
        <li>
          <a href="{$unicorn_root}/cgi/feedback?page=form" data-m="pt" data-toggle="feedback tracking-action" data-id="" data-tracking-action="Show Feedback">Feedback</a>
        </li>
        {if false && $ht_status.affiliation}
        <li class="on-for-narrowest"><a class="logout-link" href="{$unicorn_root}/cgi/logout?{$fullPath_esc}">Log out</a></li>
        {/if}
      </ul>
    </li>
  </ul>
  <ul id="person-nav" class="nav pull-right">
    <li class="on-for-pt on-for-narrow">
      <button class="btn action-search-hathitrust control-search">
        <i class="icomoon icomoon-search"></i><span class="off-for-narrowest"> Search</span> HathiTrust</button>
    </li>
    {if $ht_status.affiliation}
    <li class="item-vanishing"><span>{$ht_status.affiliation}</span></li>
    <li class="x--off-for-narrowest">
      <a class="logout-link" href="{$unicorn_root}/cgi/logout?{$fullPath_esc}">Log out</a>
    </li>
    {else}
    <li>
      <a id="login-link" class="trigger-login action-login" data-close-target=".modal.login" href="#">Log in</a>
    </li>
    {/if}
  </ul>
  </nav>
</header>
