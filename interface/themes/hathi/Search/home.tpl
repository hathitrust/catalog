<div class="homeContent">
  <div class="colMain">


    <div id="CatalogBox" class="BoxCont">
      <div class="tab">
       <span class="tl"></span>
       <span class="tr"></span>
       <h3>Catalog Search</h3>
      </div>

      <div class="HomeBox Box1">
        <span class="bl"></span>
        <p class="FindDesc">Search information <strong>about</strong> the items.</p>
        <div class="SearchForm">
          <form method="get" action="/Search/Home" name="searchForm" onsubmit="trimForm(this.lookfor); return true;" class="search" id="searchForm">
          <div>
            <input type="hidden" name="checkspelling" value="true">
            <input type="hidden" name="type" value="all">
            <label for="lookfor" class="skipLink">Search Catalog</label>
            <input class="searchterms" type="text" name="lookfor" value="" id="lookfor">
            <button type="submit" name="submit" id="searchSubmit">Find</button>
          </div>

          <div>
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
            <input type="hidden" name="sethtftonly" value="true">
            <input type="checkbox" name="htftonly" value="true" id="fullonly" {if $ht_fulltextonly}checked="checked"{/if}>&nbsp;<label for="fullonly">Full view only</label>
           </div>
          </form>

          <div class="advSearch"><a href="{$path}/Search/Advanced">{translate text="Advanced Catalog Search"}</a></div>

        </div>
        <h4>Search Tips:</h4>
        <p><strong>Phrase Searching:</strong> Use quotes to search an exact phrase: e.g., "occult fiction"</p>
        <p><strong>Wildcards:</strong> Use * or ? to search for alternate forms of a word. Use * to stand for several characters, and ? for a single character: e.g., optim* will find optimal, optimize or optimum; wom?n will find woman and women. </p>
        <p><strong>Boolean Searching:</strong> Use AND and OR between words to combine them with Boolean logic: e.g., (heart OR cardiac) AND surgery will find items about heart surgery or cardiac surgery.</p>

      </div>
    </div>

    <div id="LSBox" class="BoxCont">
      <div class="tab">
       <span class="tl"></span>
       <span class="tr"></span>
       <h3>Full-text Search</h3>
      </div>
      <div class="HomeBox Box2">
        <span class="bl"></span>

        <p class="FindDesc">Search words that occur <strong>within</strong> the items.</p>

            <div id="ls_errormsg">
              <div class="bd">
              </div>
            </div>

        <div class="SearchForm">
          <form name="searchcoll" action="http://babel.hathitrust.org/cgi/ls" id="itemlist_searchform">
          <div>
            <label for="srch" class="skipLink">Content Search </label>
            <input class="searchterms" type="text" value="" name="q1">
            <button id="srch" type="submit">Find</button>
          </div>
          <div>
            <input type="checkbox" value="ft" name="lmt" id="ls_fullonly">
            <label for="ls_fullonly">Full view only</label>
            <input type="hidden" value="srchls" name="a">
          </div>
        </div>

          </form>

        <h4>Search Tips:</h4>
        <p><strong>Phrase Searching:</strong> Use quotes to search an exact phrase: e.g., "occult fiction"</p>
        <p><strong>Multiple Term Searching:</strong> When your search terms are not quoted phrases, avoid common words (such as: 'a', 'and', 'of', 'the', etc.) to speed up your search. </p>
        <p><strong>Boolean Searching:</strong> Use AND and OR between words to combine them with Boolean logic: e.g., heart OR cardiac  will find items containing the word heart or the word cardiac;  heart AND cardiac will find items containing both words. Use a minus (-) to remove words from the result e.g., heart &nbsp;-cardiac will find items containing the word  heart that do not include the word cardiac. </p>

        <h5>Note</h5>
        <p>Full-text search does not currently include functionality in use elsewhere in HathiTrust (e.g., there is no sorting or collection-related functionality). Read more about our <a href="http://www.hathitrust.org/large_scale_search">"large-scale search"</a>.
      </div>
    </div>

    <div id="BrowseBox" class="BoxCont">
      <div class="tab">
       <span class="tl"></span>
       <span class="tr"></span>
       <h3>Collections</h3>
      </div>

      <div class="HomeBox Box3">
        <span class="bl"></span>

        <p class="FindDesc">Browse, search, or make HathiTrust collections.</p>

        <form method="link" action="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub">
          <input type="submit" value="View Public Collections">
        </form>

        <p>Collections are a way to group items for private or public use. Once grouped, you can search the full-text of all items within a collection. More information about <a href="http://www.hathitrust.org/faq#Build">creating collections</a>.</p>


        <h4>Featured Collection:</h4>

        <div id="featured">
          <!-- <div class="itemList">
            <a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=622231186"><img src="http://babel.hathitrust.org/u/umpress/graphics/hathiTrust_ump.jpg" alt="" class="imgLeft"></a>
            <h5><a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=622231186">UM Press</a></h5>
            <p><a href="http://www.lib.umich.edu/spec-coll/"><a href="http://press.umich.edu/">Univ. of Michigan Press</a>'s full-text titles available in HathiTrust </p>
          </div> -->


          <!-- Removed 4/28/10 -->
          <!-- <div class="itemList">
            <a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=503653486"><img src="/images/hathi/featuredCols/Patent.jpg" alt="" class="imgLeft"></a>
            <h5><a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=503653486">Patent Indexes</a></h5>
            <p>Collection of currently available volumes of the Annual report of the Commissioner of Patents</p>
          </div>

          <div class="itemList">
            <a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=1874608773"><img src="http://babel.hathitrust.org/m/moaa-cb1/graphics/MOAA.jpg" alt="" class="imgLeft"></a>
            <h5><a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=1874608773">Ann Arbor History</a></h5>
            <p>History and development of the Ann Arbor Community.</p>
          </div>

          <div class="itemList">
            <a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=464226859"><img src="/images/hathi/featuredCols/Henty1.jpg" alt="" class="imgLeft"></a>
            <h5><a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=464226859">Adventure Novels: G.A. Henty</a></h5>
            <p>Novels from the <a href="http://www.lib.umich.edu/spec-coll/">U-M Special Collections'</a> Henty Collection and books about George A. Henty</p>
          </div>
          -->

          <div class="itemList">
            <a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=332123463"><img src="/images/hathi/featuredCols/familytree.jpg" alt="" class="imgLeft"></a>
            <h5><a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=332123463">Ancestry and Genealogy</a></h5>
            <p>Online content related to genealogy, family history, ancestry, etc.</p>
          </div>

          <!-- Added 4/28/10 -->
          <div class="itemList">
            <a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=1169728529"><img src="/images/hathi/featuredCols/Eyjafjallajokull.png" alt="" class="imgLeft"></a>
            <h5><a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=1169728529">Eyjafjallaj&#246;kull</a></h5>
            <p>Selected full view: The Icelandic volcano, a.k.a. Eyjafjalla Jokull, Eyjafjalla Skul, "Island Mountain Glacier", "Mountain of Islands"</p>
          </div>

          <!-- Added 4/28/10 -->
          <div class="itemList">
            <a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=1961411403"><img src="/images/hathi/featuredCols/IslamicMSS.jpg" alt="" class="imgLeft"></a>
            <h5><a href="http://babel.hathitrust.org/cgi/mb?a=listis;c=1961411403">Islamic Manuscripts (Michigan)</a></h5>
            <p>Newly digitized manuscripts from the Islamic Manuscripts Collection at the University of Michigan, mainly in Arabic, Persian and Ottoman Turkish and dating from the 8th to 20th century CE.</p>
          </div>

        </div>
        <p class="clear">&nbsp;</p>

      </div>
    </div>

  </div>
</div>

<!--<div id="home_footer"></div>-->



<!--

  <h3>Welcome to the Hathi Trust Digital Library Catalog Search</h3>

	<p>This interface allows you to search bibliographic data such as title, author, ISBN, etc, for all items in the Library. Full-text search of all items in the Library is not yet available, but full-text searching options are available via the <a href="http://babel.hathitrust.org/cgi/ls">Experimental Search</a> page or from within a <a href="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub">public</a> or <a href="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=priv">private</a> collection.</p>
	<p>The current discovery catalog is temporary, pending the release of a permanent catalog that is being developed by OCLC in conjunction with the HathiTrust partners. Information about this and other HathiTrust development efforts is available on the <a href="http://www.hathitrust.org/objectives">HathiTrust Functional Objectives</a> page of the <a href="http://www.hathitrust.org/about">About</a> section of this site. Instructions on how to <a href="http://www.hathitrust.org/bibliographic_data_distribution">load bibliographic records</a> for volumes in this catalog into local library catalogs is also available in the <a href="http://www.hathitrust.org/about">About</a> section, as well as information on HathiTrust <a href="http://www.hathitrust.rg/partnership">Partnership</a>, <a href="http://www.hathitrust.org/rights_management">Rights Management</a> policies, <a href="http://www.hathitrust.org/preservation">Preservation</a> practices and more.</p>
	<p>Feedback on all aspects of the catalog are welcome and requested. Please use the feedback link in the upper right corner of the page to send your thoughts and suggestions.</p>
	<p> Enjoy!</p>

</div>
<div class="colSide">
   {include file="searchtips.tpl"}
</div>

-->
