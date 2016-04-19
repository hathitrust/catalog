<div class="homeContent">
  <div class="colMain">


    <div id="CatalogBox" class="BoxCont">
      <div class="tab">
       <span class="tr"></span>
       <h3>Catalog Search</h3>
      </div>

      <div class="HomeBox Box1">
        <span class="bl"></span>
        <div class="SearchForm">
          <form method="get" action="/Search/Home" name="searchForm" onsubmit="trimForm(this.lookfor); return true;" class="search" id="searchForm">
          <input type="hidden" name="checkspelling" value="true">
          <input type="hidden" name="type" value="all">
          <label for="lookfor" class="skipLink">Search Catalog</label>
          <input type="text" name="lookfor" value="" id="lookfor">
          <select id="searchtype" name="type">
             <option value="all">All Fields</option>
             <option value="title">Title</option>
             <option value="author">Author</option>
             <option value="subject">Subject</option>
             <!--<option value="hlb">Academic Discipline</option>-->
             <!--<option value="callnumber">Call Number / in progress</option>-->
             <option value="isn">ISBN/ISSN</option>
             <option value="publisher">Publisher</option>
             <option value="series">Series Title</option>
             <option value="year">Year of Publication</option>
             <!-- <option value="tag">Tag</option> -->
           </select>
           <button type="submit" name="submit" id="searchSubmit">Find</button>
          </form>
        </div>
        <p class="FindDesc">Search bibliographic data such as title, author, ISBN, etc., for all items in the library.</p>
        <h4>Search Tips:</h4>
        <h5>Phrase Searching</h5>
        <p>Use quotes to search an exact phrase: e.g. "occult fiction"</p>
        <h5>Wildcards</h5>
        <p>Use * or ? to search for alternate forms of a word. Use * to stand for several characters, and ? for a single character: e.g. optim* will find optimal, optimize or optimum; wom?n will find woman and women. </p>
        <h5>Boolean Searching</h5>
        <p>Use AND and OR between words to combine them with Boolean logic: e.g. (heart OR cardiac) AND surgery will find items about heart surgery or cardiac surgery. Boolean terms must be in uppercase. The boolean NOT is not allowed. By default, it's set to AND so searching heart cardiac will find items with both words.</p>

      </div>
    </div>

    <div id="LSBox" class="BoxCont">
      <div class="HomeBox Box2">
        <img id="betaburst" alt="" src="/images/hathi/beta_orange.jpg">
        <h3>Full-text Search</h3>

        <div class="SearchForm">
          <form name="searchcoll" action="https://babel.hathitrust.org/cgi/ls" id="itemlist_searchform" onsubmit="trimForm(this.lookfor); return true;">
          <label for="srch" class="skipLink">Content Search </label>
          <input type="text" value="" name="q1">
          <input type="hidden" value="srchls" name="a">
          <button value="srchls" id="srch" name="a" type="submit">Find</button>
          </form>
        </div>

        <p class="FindDesc">Search for words that occur within the text of the items in the library.</p>

        <h4>Search Tips:</h4>
        <h5>Phrase Searching</h5>
        <p>Use quotes to search an exact phrase: e.g. "occult fiction." When your search terms are not quoted phrases, avoid common words (such as: 'a', 'and', 'of', 'the', etc.) to speed up your search. </p>

        <!--<h5>Wildcards</h5>-->

        <h5>Boolean Searching</h5>
        <p>Use AND and OR between words to combine them with Boolean logic: e.g. heart OR cardiac will find items with heart or cardiac. Boolean terms must be in uppercase. By default, it's set to AND so searching heart cardiac will find items with both words. Use a minus (-) to remove words from the result e.g. heart -cardiac will find items about the heart that do not include the word cardiac. </p>

        <h5>Note</h5>
        <p>Full-text search does not currently include functionality in use elsewhere in HathiTrust (e.g., no sorting or collection-related functionality), and does not have features like clustering of results which are likely to be in a fuller implementation.</p>
        <p>Read more about our <a href="https://www.hathitrust.org/large_scale_search">large-scale search</a>.
      </div>
    </div>

    <div id="BrowseBox" class="BoxCont">
      <div class="HomeBox Box3">
        <h3>Browse Collections</h3>
        <p class="FindDesc">Collections are a way to group items for private or public use. Once grouped, you can search the full-text of all items within a collection.</p>

        <form method="link" action="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub">
          <input type="submit" value="View Public Collections">
        </form>

        <h4>Featured Collection:</h4>

        <div id="featured">
          <div class="itemList">
            <a href="https://babel.hathitrust.org/cgi/mb?a=listis;c=622231186"><img src="https://babel.hathitrust.org/u/umpress/graphics/hathiTrust_ump.jpg" alt="" class="imgLeft"></a>
            <h5><a href="https://babel.hathitrust.org/cgi/mb?a=listis;c=622231186">UM Press</a></h5>
            <p><a href="http://www.lib.umich.edu/spec-coll/"><a href="http://press.umich.edu/">Univ. of Michigan Press</a>'s full-text titles available in HathiTrust </p>
          </div>

          <div class="itemList">
            <a href="https://babel.hathitrust.org/cgi/mb?a=listis;c=464226859"><img src="/images/hathi/featuredCols/Henty1.jpg" alt="" class="imgLeft"></a>
            <h5><a href="https://babel.hathitrust.org/cgi/mb?a=listis;c=464226859">Adventure Novels: G.A. Henty</a></h5>
            <p>Novels from the <a href="http://www.lib.umich.edu/spec-coll/">U-M Special Collections'</a> Henty Collection and books about George A. Henty</p>
          </div>

          <div class="itemList">
            <a href="https://babel.hathitrust.org/cgi/mb?a=listis;c=503653486"><img src="/images/hathi/featuredCols/Patent.jpg" alt="" class="imgLeft"></a>
            <h5><a href="https://babel.hathitrust.org/cgi/mb?a=listis;c=503653486">Patent Indexes</a></h5>
            <p>Collection of currently available volumes of the Annual report of the Commissioner of Patents</p>
          </div>

        </div>

      </div>
    </div>

  </div>
</div>

<!--<div id="home_footer"></div>-->



<!--

  <h3>Welcome to the Hathi Trust Digital Library Catalog Search</h3>

	<p>This interface allows you to search bibliographic data such as title, author, ISBN, etc, for all items in the Library. Full-text search of all items in the Library is not yet available, but full-text searching options are available via the <a href="https://babel.hathitrust.org/cgi/ls">Experimental Search</a> page or from within a <a href="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub">public</a> or <a href="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=priv">private</a> collection.</p>
	<p>The current discovery catalog is temporary, pending the release of a permanent catalog that is being developed by OCLC in conjunction with the HathiTrust partners. Information about this and other HathiTrust development efforts is available on the <a href="https://www.hathitrust.org/objectives">HathiTrust Functional Objectives</a> page of the <a href="https://www.hathitrust.org/about">About</a> section of this site. Instructions on how to <a href="https://www.hathitrust.org/bibliographic_data_distribution">load bibliographic records</a> for volumes in this catalog into local library catalogs is also available in the <a href="https://www.hathitrust.org/about">About</a> section, as well as information on HathiTrust <a href="https://www.hathitrust.rg/partnership">Partnership</a>, <a href="https://www.hathitrust.org/rights_management">Rights Management</a> policies, <a href="https://www.hathitrust.org/preservation">Preservation</a> practices and more.</p>
	<p>Feedback on all aspects of the catalog are welcome and requested. Please use the feedback link in the upper right corner of the page to send your thoughts and suggestions.</p>
	<p> Enjoy!</p>

</div>
<div class="colSide">
   {include file="searchtips.tpl"}
</div>

-->