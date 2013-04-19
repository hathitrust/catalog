{literal}
  <script type="text/javascript" charset="utf-8">
    function changeRange(id) {
      sel = jq('#' + id);
      name = sel.attr('name');
      val = sel.val();
      jq(".yop").val('').hide();
      if (val == 'before') {
        jq('#' + name + '-end').val('').show().val("");
      }
      if (val == 'after') {
        jq('#' + name + '-start').show().val("");
      }

      if (val == 'between') {
        jq('#' + name + '-start').show().val("");
        jq('#' + name + '-between').show();
        jq('#' + name + '-end').show().val("");
      }

      if (val == 'in') {
       jq('#' + name + '-in').show().val('');
      }

    }

    function changeSublibs(inst) {
      var sublibSelect = jq("#sublib");
      sublibSelect.empty();
      var instSublibs =
{/literal}
{$instLocsJSON};
{literal}
      // sublibSelect.addOption(instSublibs[inst]["sublibs"], false);
      for (var i in instSublibs[inst]["sublibs"]) {
        var val = instSublibs[inst]["sublibs"][i];
        sublibSelect.append('<option value="' + i + '">' + val + '</option>');
      }


      // var firstSublib = jq("#sublib option")[0].value;
      var firstSublib = jq('#sublib option').attr('value');

      changeCollections(firstSublib);
    }



    function changeCollections(sublib) {
      jq("#sublibColl").empty();
      var sublibCollections =
{/literal}
{$locCollJSON};
{literal}

      // jq("#sublibColl").addOption(sublibCollections[sublib]["collections"], false);
      for (var i in sublibCollections[sublib]["collections"]) {
        var val = sublibCollections[sublib]["collections"][i];
        jq('#sublibColl').append('<option value="' + i + '">' + val + '</option>');
      }

    }

    jq(document).ready(function() {
      changeRange('yop');
    });
  </script>

  <style type="text/css" media="screen">
    h2 {margin-top: 1.5em; margin-bottom: .75em;}
  </style>
{/literal}



<div id="bd">
  <div id="yui-main" class="content" style="*margin-left: 0em;">

    <div class="first contentbox"  style="margin-left: 1em;">
      <div class="record" style="text-align: left;" >

        <form method="GET" action="{$url}/Search/Home" name="searchForm" class="search" onSubmit="clickpostlog(document, ['advsearch'])">
          <input type='hidden' name='adv' value='1'>

          <div id="advanced_titles">
            <div class="title">
              <h2>{translate text='Advanced Catalog Search'}:</h2>
              <p>Search information <em>about</em> the item.</p>
            </div>
            
            <div class="link_to_ft">
              Prefer to search <em>within</em> the item in an <a href="http://babel.hathitrust.org/cgi/ls?a=page&page=advanced">Advanced Full-text search</a>?
            </div>
            
          </div>

          <div style="clear:both"></div>
          <h3 class="hidden">Search by field</h3>
          <table style="width: auto">


            <tr>
              <td></td>
              <td >
                <label class="hidden" for="sf1">search field 1</label>
                <select id="sf1" name="type[]">
                  <option value="all" {if $type1 == 'all'} selected{/if}>{translate text="All Fields"}</option>
                  <option value="title" {if $type1 == 'title'} selected{/if}>{translate text="Title"}</option>
                  <option value="author" {if $type1 == 'author'} selected{/if}>{translate text="Author"}</option>
                  <option value="subject" {if $type1 == 'subject'} selected{/if}>{translate text="Subject"}</option>
                  <!--<option value="hlb" {if $type1 == 'hlb'} selected{/if}>{translate text="Categories"}</option>
                  <option value="toc" {if $type1 == 'toc'} selected{/if}>{translate text="Table of Contents"}</option>
                  <option value="callnumber" {if $type1 == 'callnumber'} selected{/if}>{translate text="Call Number"}</option>-->
                  <option value="publisher" {if $type1 == 'publisher'} selected{/if}>{translate text="Publisher"}</option>
                  <option value="series" {if $type1 == 'series'} selected{/if}>{translate text="Series Title"}</option>
                  <option value="year" {if $type1 == 'year'} selected{/if}>{translate text="Year of Publication"}</option>
                  <option value="isn" {if $type1 == 'isn'} selected{/if}>ISBN/ISSN</option>
                </select>
              </td>
              <td>
                <label class="hidden" for="field1">search box 1</label>
                <input id="field1" type="text" name="lookfor[]" size="50" value="{$lookfor1}"></td>

            </tr>

            <tr>
              <td>
                <label class="hidden" for="op1">Operator 1</label>
                <select id="op1" name="bool[]">
                  <option value="AND" {if $bool1 == 'AND'} selected{/if}>{translate text="AND"}</option>
                  <option value="OR" {if $bool1 == 'OR'} selected{/if}>{translate text="OR"}</option>
                  <!-- <option value="NOT" {if $bool1 == 'NOT'} selected{/if}>{translate text="NOT"}</option> -->
                </select>
              </td>
              <td >
                <label class="hidden" for="sf2">search field 2</label>

                <select id="sf2" name="type[]">
                  <option value="all" {if $type2 == 'all'} selected{/if}>{translate text="All Fields"}</option>
                  <option value="title" {if $type2 == 'title'} selected{/if}>{translate text="Title"}</option>
                  <option value="author" {if $type2 == 'author'} selected{/if}>{translate text="Author"}</option>
                  <option value="subject" {if $type2 == 'subject'} selected{/if}>{translate text="Subject"}</option>
                  <!--<option value="hlb" {if $type2 == 'hlb'} selected{/if}>{translate text="Categories"}</option>
                  <option value="toc" {if $type2 == 'toc'} selected{/if}>{translate text="Table of Contents"}</option>
                  <option value="callnumber" {if $type2 == 'callnumber'} selected{/if}>{translate text="Call Number"}</option>-->
                  <option value="publisher" {if $type2 == 'publisher'} selected{/if}>{translate text="Publisher"}</option>
                  <option value="series" {if $type2 == 'series'} selected{/if}>{translate text="Series Title"}</option>
                  <option value="year" {if $type2 == 'year'} selected{/if}>{translate text="Year of Publication"}</option>
                  <option value="isn" {if $type2 == 'isn'} selected{/if}>ISBN/ISSN</option>
                </select>
              </td>
              <td>
                <label class="hidden" for="field2">search box 2</label>
                <input id="field2" type="text" name="lookfor[]" size="50" value="{$lookfor2}">
              </td>
            </tr>

            <tr>
              <td>
                <label class="hidden" for="op2">Operator 2</label>
                <select id="op2" name="bool[]">
                  <option value="AND" {if $bool2 == 'AND'} selected{/if}>{translate text="AND"}</option>
                  <option value="OR" {if $bool2 == 'OR'} selected{/if}>{translate text="OR"}</option>
                  <!-- <option value="NOT" {if $bool2 == 'NOT'} selected{/if}>{translate text="NOT"}</option> -->
                </select>
              </td>
              <td >
                <label class="hidden" for="sf3">search field 3</label>

                <select id="sf3" name="type[]">
                  <option value="all" {if $type3 == 'all'} selected{/if}>{translate text="All Fields"}</option>
                  <option value="title" {if $type3 == 'title'} selected{/if}>{translate text="Title"}</option>
                  <option value="author" {if $type3 == 'author'} selected{/if}>{translate text="Author"}</option>
                  <option value="subject" {if $type3 == 'subject'} selected{/if}>{translate text="Subject"}</option>
                  <!--<option value="hlb" {if $type3 == 'hlb'} selected{/if}>{translate text="Categories"}</option>
                  <option value="toc" {if $type3 == 'toc'} selected{/if}>{translate text="Table of Contents"}</option>
                  <option value="callnumber" {if $type3 == 'callnumber'} selected{/if}>{translate text="Call Number"}</option>-->
                  <option value="publisher" {if $type3 == 'publisher'} selected{/if}>{translate text="Publisher"}</option>
                  <option value="series" {if $type3 == 'series'} selected{/if}>{translate text="Series Title"}</option>
                  <option value="year" {if $type3 == 'year'} selected{/if}>{translate text="Year of Publication"}</option>
                  <option value="isn" {if $type3 == 'isn'} selected{/if}>ISBN/ISSN</option>
                </select>
              </td>
              <td><label class="hidden" for="field3">search box 3</label><input id="field3" type="text" name="lookfor[]" size="50" value="{$lookfor3}"></td>
            </tr>

            <tr>
              <td>
                <label class="hidden" for="op3">Operator 3</label>
                <select id="op3" name="bool[]">
                  <option value="AND" {if $bool3 == 'AND'} selected{/if}>{translate text="AND"}</option>
                  <option value="OR" {if $bool3== 'OR'} selected{/if}>{translate text="OR"}</option>
                  <!-- <option value="NOT" {if $bool3 == 'NOT'} selected{/if}>{translate text="NOT"}</option> -->
                </select>
              </td>
              <td >
                <label class="hidden" for="sf4">search field 4</label>
                <select id="sf4" name="type[]">
                  <option value="all" {if $type4 == 'all'} selected{/if}>{translate text="All Fields"}</option>
                  <option value="title" {if $type4 == 'title'} selected{/if}>{translate text="Title"}</option>
                  <option value="author" {if $type4 == 'author'} selected{/if}>{translate text="Author"}</option>
                  <option value="subject" {if $type4 == 'subject'} selected{/if}>{translate text="Subject"}</option>
                  <!--<option value="hlb" {if $type4 == 'hlb'} selected{/if}>{translate text="Categories"}</option>
                  <option value="toc" {if $type4 == 'toc'} selected{/if}>{translate text="Table of Contents"}</option>
                  <option value="callnumber" {if $type4 == 'callnumber'} selected{/if}>{translate text="Call Number"}</option>-->
                  <option value="publisher" {if $type4 == 'publisher'} selected{/if}>{translate text="Publisher"}</option>
                  <option value="series" {if $type4 == 'series'} selected{/if}>{translate text="Series Title"}</option>
                  <option value="year" {if $type4 == 'year'} selected{/if}>{translate text="Year of Publication"}</option>
                  <option value="isn" {if $type4 == 'isn'} selected{/if}>ISBN/ISSN</option>
                </select>
              </td>
              <td><label class="hidden" for="field4">search box 4</label><input id="field4" type="text" name="lookfor[]" size="50" value="{$lookfor4}"></td>
              <!-- <td><input  type="submit" name="submit" value="{translate text="Find"}"></td> -->
            </tr>

          </table>

          <br>
          <h3>{translate text='Limit To'}:</h3>

              <input type="hidden" name="setft" value="true">
              <label class="checkbox inline" for="fullonly"><input type="checkbox" name="ft" value="ft" id="fullonly" > Full view only</label>

              <label for="yop" class="hidden">Publication year restrictions</label>
              <span style="margin-left: 4em">
                <span style="margin-right: 1em;">Year of publication:</span>
                <select id="yop" name="yop" onchange="changeRange('yop')">
                <option value="before">Before or during</option>
                <option value="after" selected="selected">During or after</option>
                <option value="between">Between</option>
                <option value="in">Only during</option>
              </select>

              <label for="yop-start" class="hidden">Earliest allowable publication year</label>
              <input class="yop" id="yop-start" type="text" size="4" name="fqrange-start-publishDateTrie-1">
              <span class="yop" id="yop-between" > and </span>

              <label for="yop-end" class="hidden">Latest allowable publication year</label>
              <input class="yop"  id="yop-end" type="text" size="4" name="fqrange-end-publishDateTrie-1">

              <label for="yop-in" class="hidden">Publication year must be</label>
              <input class="yop" id="yop-in" type="text" size="4" name="fqor-publishDateTrie[]">
            </span>


          <table style="width: auto">
            <tr>
              <th><label for="fqor-language">{translate text="Language"}</label>: </th>
              <th><label for="fqor-format">{translate text="Original Format"}</label>: </th>
            </tr>
            <tr>
              <td>
                <select multiple  id="fqor-language" name="fqor-language[]" size="10">
                  <option value="">All</option>
                  {foreach from=$languageList item="language"}
                  <option value="{$language}">{$language|escape:"html"}</option>
                  {/foreach}
                </select>
              </td>
              <td>
                <select multiple id="fqor-format" name="fqor-format[]" size="10">
                  <option value="">All</option>
                  {foreach from=$formatList item="format"}
                  <option value="{$format}">{$format|escape:"html"}</option>
                  {/foreach}
                </select>
              </td>
            </tr>
          </table>
<input  type="submit" name="submit" value="{translate text="Search"}">
          <!-- div style="margin-left: 1em"><input style="width: 7em; font-size: 1.5em; padding: 0.5em 2em;" type="submit" name="submit" value="{translate text="Search"}"></div><br> -->
        </form>
      </div>
    </div>
  </div>


</div>
