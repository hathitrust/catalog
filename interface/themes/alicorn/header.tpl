<header class="site-navigation" role="banner">
  <nav aria-label="about the site">
  <ul id="nav" class="nav">
    {if $logo}
    <li>
      <a class="home-link" aria-hidden="true" href="https://www.hathitrust.org" tabindex="-1"><span class="offscreen">Home</span></a>
    </li>
    {/if}
  <li>
    <a href="https://www.hathitrust.org">Home</a>
  </li>
  <li class="nav-menu">
    <a href="#" aria-haspopup="true" id="about-menu">About <i class="icomoon icomoon-triangle" aria-hidden="true" style="position: absolute; top: 35%"></i></a>
    <ul class="navbar-menu-children" role="menu" aria-labelledby="about-menu" aria-hidden="true">
    <li aria-role="presentation">
      <a href="https://www.hathitrust.org/about" aria-role="menuitem">Welcome to HathiTrust</a>
    </li>
    <li aria-role="presentation">
      <a href="https://www.hathitrust.org/partnership" aria-role="menuitem">Our Partnership</a>
    </li>
    <li aria-role="presentation">
      <a href="https://www.hathitrust.org/digital_library" aria-role="menuitem">Our Digital Library</a>
    </li>
    <li aria-role="presentation">
      <a href="https://www.hathitrust.org/collaborative-programs" aria-role="menuitem">Our Collaborative Programs</a>
    </li>
    <li aria-role="presentation">
      <a href="https://www.hathitrust.org/htrc" aria-role="menuitem">Our Research Center</a>
    </li>
    <li aria-role="presentation">
      <a href="https://www.hathitrust.org/news_publications" aria-role="menuitem">News &amp; Publications</a>
    </li>
    </ul>
  </li>
  <li>
    <a href="{$unicorn_root}/cgi/mb">Collections</a>
  </li>
  <li class="help">
    <a href="https://www.hathitrust.org/help">Help</a>
  </li>
  <li>
    <a href="{$unicorn_root}/cgi/feedback?page=form" data-m="pt" data-toggle="feedback tracking-action" data-id="" data-tracking-action="Show Feedback">Feedback</a>
  </li>
  </ul>
  <ul id="person-nav" class="nav pull-right">
    {if $ht_status.affiliation}
    <li><span>{$ht_status.affiliation}</span></li>
    <li><a href="{$unicorn_root}/cgi/mb?colltype=my-collections;a=listcs">My Collections</a></li>
    <li>
      <a id="logout-link" href="{$unicorn_root}/cgi/logout?{$fullPath_esc}">Log out</a>
    </li>
    {else}
    <li>
      <a id="login-link" class="trigger-login action-login" data-close-target=".modal.login" href="#">Log in</a>
    </li>
    {/if}
  </ul>
  </nav>
</header>
