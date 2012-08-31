<div id="mbHeader">
  <div id="masthead">
    <div class="branding">
      <div class="brandingLogo">
        <a class="SkipLink" accesskey="2" href="#skipNav">Skip navigation</a>
        <h1 class="SkipLink">{$pageTitle|truncate:64:"..."} | Hathi Trust Digital Library</h1>
        <a href="http://catalog.hathitrust.org/">
          <img src="/images/hathi/HathiTrust.gif" alt="Hathi Trust Logo" ><span>Digital Library</span></a>
      </div>
    </div>
    <h2 class="SkipLink">Navigation links for help, feedback, FAQ, etc.</h2>
    <div class="MBooksNav">
      <ul>
        <!--<li><a href="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub;sort=cn_a">Login</a></li>-->
        <li><a href="http://www.hathitrust.org/help" title="Help page and faq">Help</a></li>
	      <li><a href="#" id="feedback" title="Feedback form for problems or comments"><span>Feedback</span></a></li>
      </ul>
    </div>

  </div>


  <div id="CBNavContainer">
    <div id="CBNav">
      <h2 class="SkipLink">Navigation links for search and collections</h2>
      <ul>
        <li><a href="http://www.hathitrust.org/"><span title="HathiTrust Home">Home</span></a></li>
        <li><a href="http://www.hathitrust.org/about"><span title="About">About</span></a></li>
        <li><a href="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub"><span title="Collections">Collections</span></a></li>
        <li><a href="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=priv"><span title="My Collections">My Collections</span></a></li>
      </ul>
    </div>
  </div>

  {if $pageTitle eq 'Search Home'}
  <div id="SubNavHeader">
    <div id="SubNavHeaderCont">
      <div class="CollPage">
        <span></span>
      </div>
    </div>
  </div>

  {else}

  <div id="SubNavHeader">
    <div id="SubNavHeaderCont">
      <div class="CollPage">
        <span>Catalog Search</span>
      </div>
    </div>
  </div>
  {/if}

</div>

<a name="skipNav"></a>








