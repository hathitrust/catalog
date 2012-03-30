{literal}
<style type="text/css" media="screen">

  h2 {
    font-weight: bold;
  }
	/* column container */
	.colmask {
		position:relative;	/* This fixes the IE7 overflow hidden bug */
		clear:both;
		float:left;
		width:100%;			/* width of whole page */
		overflow:hidden;		/* This chops off any overhanging divs */
		padding-top: 2em;
	}
	/* common column settings */
	.colright,
	.colmid,
	.colleft {
		float:left;
		width:100%;			/* width of page */
		position:relative;
	}
	.col1,
	.col2,
	.col3 {
		float:left;
		position:relative;
		padding:0 0 1em 0;	/* no left and right padding on columns, we just make them narrower instead 
						only padding top and bottom is included here, make it whatever value you need */
		overflow:hidden;
	}
	/* 3 Column settings */
	.threecol {
		background:#fff;		/* right column background colour */
	}
	.threecol .colmid {
		right:20%;			/* width of the right column */
		background:#fff;		/* center column background colour */
	}
	.threecol .colleft {
		right:60%;			/* width of the middle column */
		background:#fff;	/* left column background colour */
	}
	.threecol .col1 {
		width:72%;			/* width of center column content (column width minus padding on either side) */
		left:86%;			/* 100% plus left padding of center column */
	}
	.threecol .col2 {
		width:0%;			/* Width of left column content (column width minus padding on either side) */
		left:24%;			/* width of (right column) plus (center column left and right padding) plus (left column left padding) */
		display: none;
	}
	.threecol .col3 {
		width:16%;			/* Width of right column content (column width minus padding on either side) */
		left:90%;			/* Please make note of the brackets here:
						(100% - left column width) plus (center column left and right padding) plus (left column left and right padding) plus (right column left padding) */
	}
	/* Footer styles */
	#footer {
		clear:both;
		float:left;
		width:100%;
		border-top:1px solid #000;
	}

#m2bnotes {
  margin-top: 2em;
}

#m2bnotes p {
  margin-top: 0.25em;
  margin-bottom: 1em;
  margin-left: 1.5em;  
}
.inner h3 {
  padding-top: 0.5em;
  padding-bottom: 0px;
  margin-bottom: 0px;
  font-size: 90%;
  font-weight: bold;
}

#m2bnotes p:first-child {
  padding-top: 0px;
  margin-top: 0px;
}

h1 {
  padding-top: 0px;
  margin-top: 0px;
}

</style>
{/literal}

{include file="tempbox.tpl"}      

<div class="colmask threecol">
	<div class="colmid">
    
		<div class="colleft">
			<div class="col1">
        <div style="padding-right: 4em;">
          <h1 style="font-size: 175%; font-weight: bold; padding-bottom: 0.5em;">Welcome to Mirlyn!</h1> 
          <p>The Mirlyn catalog enables you to search and browse the University of Michigan Library’s 
            collection of books, journals, audio/video materials, electronic resources and more. 
            If you would prefer to use the <a href="http://mirlyn-classic.lib.umich.edu/">classic</a> catalog interface, please click on the
            Mirlyn Classic link at the top of any Mirlyn screen.

         <h2>New! Mirlyn Mobile</h2>
         <p><img align="left" hspace="5" height="250" width="132" src="http://www.lib.umich.edu/files/services/usability/MM_for-website-TEMP.jpg" alt="Mirlyn Mobile graphic" />Have you ever wanted to look up a book when you were deep in the heart of the library stacks, riding the bus, or between sets at the gym? We have! Mirlyn Mobile is designed to bring the powers of the library catalog to your handheld device. Take your research needs in stride with the smartphone-optimized mobile catalog.<span><br /> 
         </span></p> 
         <p>To access Mirlyn Mobile, point your mobile device's web browser to <a tooltip="linkalert-tip" href="http://m.mirlyn.lib.umich.edu">m.mirlyn.lib.umich.edu</a> (works with desktop browsers, too).</p> 
         <p>Feedback and suggestions about the current version of Mirlyn Mobile and its features are welcome. Contact us: <a tooltip="linkalert-tip" href="mailto:MirlynMobile@umich.edu?subject=MirlynMobile%20Feedback%20via%20MLibrary%20Mobile%20webpage">MirlynMobile@umich.edu</a></p>         
 
        </div>
        
        
				<!-- Column 1 end -->
  		</div>


  		<div class="col2">
  		  <div class="inner">
 {*                   <h2>What's New?</h2>
                    
                    <h3><span class="favorites">Favorites</span>!</h3>
                    Add items to your permanent Favorites collection from your Selected
                    Items list or from an individual record screen. A list of your favorites
                    helps you keep track of catalog records, similar to the 'My Shelf' area
                    of Mirlyn Classic. You can optionally add tags to organize and keep
                    track of your collection.

                    <h3>View Media Reservations</h3>
                    <p>From the My Account area you can now view your Askwith Media Library
                    "bookings" (reservations).</p>

                    <h3>Limit by Location</h3>
                    <p>On the <a href="/Search/Advanced">Advanced Search screen</a>, you can limit your search to specific
                    library locations, such as Askwith Media Library or Special Collections.
                    Select UM Ann Arbor Libraries and then select a single library building
                    to limit your search terms to a particular location.</p>
                    
                    <h3>Select Records for Batch Email/Export/Favorites</h3>
                    <p>You can now select and unselect records as you search, putting them into a temporary "Selected items" 
                      set. The whole set can then be viewed/printed, emailed, sent to Refworks, or saved as a file for later import into a program like Endnote.</p>

                    <h3>New Search Options</h3>
                    <p>Search for a specific journal/serial title from the Basic
                    Search screen. Search for a range of publication dates on the Advanced
                    Search screen.</p>   
        *}                             
        </div>
     </div>


		<div class="col3">    
		  <div class="inner">
        {include file="searchtips.tpl"}
      </div>
    </div>
  </div>
</div>

<div id="home_footer"></div>      
