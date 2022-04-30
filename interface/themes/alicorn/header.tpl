<header class="site-navigation" role="banner">
  <nav aria-label="about the site">
    <ul id="nav" class="nav">
      <li class="nav-item">
        <a class="nav-link home-link" href="https://www.hathitrust.org">
          <span class="offscreen-for-narrowest">Home</span>
        </a>
      </li>
      <li class="nav-item dropdown" id="burger-menu-container">
        <a id="burger-menu-trigger" href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false"><i class="icomoon icomoon-reorder" aria-hidden="true"></i> Menu</a>
        <ul id="burger-menu" class="dropdown-menu">
          <li class="fixed">
            <span class="dropdown-header">About</span>
          </li>
          <li class="nested">
            <a class="dropdown-item" href="https://www.hathitrust.org/about">Welcome to HathiTrust</a>
          </li>
          <li class="nested">
            <a class="dropdown-item" href="https://www.hathitrust.org/partnership">Our Partnership</a>
          </li>
          <li class="nested">
            <a class="dropdown-item" href="https://www.hathitrust.org/digital_library">Our Digital Library</a>
          </li>
          <li class="nested">
            <a class="dropdown-item" href="https://www.hathitrust.org/collaborative-programs">Our Collaborative Programs</a>
          </li>
          <li class="nested">
            <a class="dropdown-item" href="https://www.hathitrust.org/htrc">Our Research Center</a>
          </li>
          <li class="nested">
            <a class="dropdown-item" href="https://www.hathitrust.org/news_publications">News &amp; Publications</a>
          </li>
          <li><hr class="dropdown-divider" /></li>
          {if FALSE && $ht_status.affiliation}
          <li><a class="dropdown-item" href="{$unicorn_root}/cgi/mb?colltype=my-collections;a=listcs">My Collections</a></li>
          {/if}
          <li class="help">
            <a class="dropdown-item" href="https://www.hathitrust.org/help">Help</a>
          </li>
          <li>
            <a href="//babel.hathitrust.org/cgi/feedback?page=form" data-m="pt" data-toggle="feedback tracking-action" data-id="" data-tracking-action="Show Feedback">Feedback</a>
          </li>
        </ul>
      </li>
      <li class="nav-item" id="about-menu-container">
        <a id="about-menu" href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button">About</a>
        <ul class="dropdown-menu">
          <li>
            <a class="dropdown-item" href="https://www.hathitrust.org/about">Welcome to HathiTrust</a>
          </li>
          <li>
            <a class="dropdown-item" href="https://www.hathitrust.org/partnership">Our Partnership</a>
          </li>
          <li>
            <a class="dropdown-item" href="https://www.hathitrust.org/digital_library">Our Digital Library</a>
          </li>
          <li>
            <a class="dropdown-item" href="https://www.hathitrust.org/collaborative-programs">Our Collaborative Programs</a>
          </li>
          <li>
            <a class="dropdown-item" href="https://www.hathitrust.org/htrc">Our Research Center</a>
          </li>
          <li>
            <a class="dropdown-item" href="https://www.hathitrust.org/news_publications">News &amp; Publications</a>
          </li>
        </ul>
      </li>
      {if FALSE && $ht_status.affiliation}
      <li class="nav-item wide"><a class="nav-link" href="{$unicorn_root}/cgi/mb?colltype=my-collections;a=listcs">My Collections</a></li>
      {/if}
      <li class="nav-item wide">
        <a class="nav-link" href="{$unicorn_root}/cgi/mb">Collections</a>
      </li>
      <li class="nav-item help wide">
        <a class="nav-link" href="https://www.hathitrust.org/help">Help</a>
      </li>
      <li class="nav-item wide">
        <a href="//babel.hathitrust.org/cgi/feedback?page=form" data-m="pt" data-toggle="feedback tracking-action" data-id="" data-tracking-action="Show Feedback">Feedback</a>
      </li>
    </ul> 
  <ul id="person-nav" class="nav pull-right">
    <li class="on-for-pt on-for-narrow">
      <button class="btn action-search-hathitrust control-search">
        <i class="icomoon icomoon-search"></i><span class="off-for-narrowest"> Search</span> HathiTrust</button>
    </li>
    <li>
      <button disabled="disabled" class="btn action-toggle-notifications" aria-label="Toggle Notifications">
        <i class="icomoon icomoon-bell" aria-hidden="true"></i>
      </button>
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
