<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:php="http://php.net/xsl"
                xsl:extension-element-prefixes="php">

  <xsl:output method="html" indent="yes"/>

  <xsl:template match="/">
    <xsl:if test="//datafield[@tag=022]">
      <xsl:call-template name="sfx"/>
    </xsl:if>
    
    <!--
    <xsl:if test="//datafield[@tag=022]">
      <xsl:attribute name="style">margin-right: 260px</xsl:attribute>
    </xsl:if>
    -->

    <div class="alignleft">
      <xsl:call-template name="bookcover"/>
    </div>
      
    <xsl:call-template name="thetitle"/>
    <xsl:call-template name="citation"/>

    <xsl:call-template name="tabs"/>

    <xsl:call-template name="zotero"/>

  </xsl:template>

  <xsl:template name="sfx">
    <!--
    <div class="findit">
      <h4>Access This Journal</h4>
      <script language="JavaScript" type="text/javascript">
        <xsl:attribute name="src">
          <xsl:value-of select="$path"/>
          <xsl:text>/services/Record/ajax.js</xsl:text>
        </xsl:attribute>
      </script>
      <script language="JavaScript" type="text/javascript">
        <xsl:text>displaySFXOptions(</xsl:text>
        <xsl:value-of select="//datafield[@tag=022]/subfield[@code='a']"/>
        <xsl:text>);</xsl:text>
      </script>
    </div>
    -->
  </xsl:template>

  <xsl:template name="thetitle">
    <h1><xsl:value-of select="//datafield[@tag=245]/subfield[@code='a']"/></h1>
    <xsl:if test="//datafield[@tag=245]/subfield[@code='b']">
	  <h2><xsl:value-of select="//datafield[@tag=245]/subfield[@code='b']"/></h2>
	</xsl:if>
    <xsl:if test="//datafield[@tag=245]/subfield[@code='c']">
	  <h3><xsl:value-of select="//datafield[@tag=245]/subfield[@code='c']"/></h3>
    </xsl:if>
  </xsl:template>
  
  <xsl:template name="bookcover">
    <a>
      <xsl:attribute name="href"><xsl:value-of select="$path"/>/bookcover.php?isn=<xsl:value-of select="substring(//datafield[@tag=020]/subfield[@code='a'], 0, 11)" />&amp;size=large</xsl:attribute>     
      <img alt="Book Cover">
        <xsl:attribute name="src"><xsl:value-of select="$path"/>/bookcover.php?isn=<xsl:value-of select="substring(//datafield[@tag=020]/subfield[@code='a'], 0, 11)" />&amp;size=medium</xsl:attribute>
      </img>
    </a>
    <xsl:if test="//datafield[@tag=020]">
      <br/>
      <a target="new" style="font-size: 6pt; color: #999999;">
        <xsl:attribute name="href">http://amazon.com/dp/<xsl:value-of select="//datafield[@tag=020]/subfield[@code='a']"/></xsl:attribute>
        Amazon
      </a>
    </xsl:if>
    
  </xsl:template>

  <xsl:template name="citation">
    <table cellpadding="2" cellspacing="0" border="0" class="citation">
      <xsl:if test="//datafield[@tag=100]">
        <tr valign="top">
          <th><xsl:value-of select="php:function('translate', 'Main Author')"/>: </th>
          <td>
     	    <a>
              <xsl:attribute name="href"><xsl:value-of select="$path"/>/Author/Home?author=<xsl:value-of select="//datafield[@tag=100]/subfield[@code='a']"/></xsl:attribute>
      		  <xsl:value-of select="//datafield[@tag=100]/subfield[@code='a']"/>
     		</a>
      	  </td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=700]">
        <tr valign="top">
          <th><xsl:value-of select="php:function('translate', 'Other Authors')"/>: </th>
          <td>
    	    <xsl:for-each select="//datafield[@tag=700]/subfield[@code='a']">
    	      <a>
                <xsl:attribute name="href"><xsl:value-of select="$path"/>/Author/Home?author=<xsl:value-of select="text()"/></xsl:attribute>
    		    <xsl:value-of select="text()"/>
      		  </a>
			  <xsl:if test="position() &lt; last()">
                <xsl:text> | </xsl:text>
              </xsl:if>
      	    </xsl:for-each>
      	  </td>
        </tr>
      </xsl:if>
      <xsl:if test="//datafield[@tag=260]">
	    <tr valign="top">
          <th><xsl:value-of select="php:function('translate', 'Published')"/>: </th>
          <td>
		    <xsl:value-of select="//datafield[@tag=260]/subfield[@code='a']"/>
		    <xsl:text> </xsl:text>
            <xsl:value-of select="//datafield[@tag=260]/subfield[@code='b']"/>
		    <xsl:text> </xsl:text>
            <xsl:value-of select="//datafield[@tag=260]/subfield[@code='c']"/>
          </td>
	    </tr>
  	  </xsl:if>
      <xsl:if test="//datafield[@tag=250]">
	    <tr valign="top">
          <th><xsl:value-of select="php:function('translate', 'Edition')"/>: </th>
          <td>
		    <xsl:value-of select="//datafield[@tag=250]/subfield[@code='a']"/>
          </td>
	    </tr>
  	  </xsl:if>
      <xsl:if test="//datafield[@tag=362]">
        <tr valign="top">
          <th><xsl:value-of select="php:function('translate', 'Published')"/>: </th>
          <td><xsl:value-of select="//datafield[@tag=362]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>
      <xsl:if test="//datafield[@tag=440]">
        <tr valign="top">
          <th><xsl:value-of select="php:function('translate', 'Series')"/>: </th>
          <td>
            <xsl:for-each select="//datafield[@tag=440]/subfield[@code='a']">
    	      <a>
                <xsl:attribute name="href"><xsl:value-of select="$path"/>/Search/Home?lookfor="<xsl:value-of select="text()"/>"&amp;type=series</xsl:attribute>
    	        <xsl:value-of select="text()"/>
    		  </a>
    		</xsl:for-each>
          </td>
    	</tr>
  	  </xsl:if>
      <xsl:if test="//datafield[@tag=650]/subfield[@code='a']">
        <tr valign="top">
	      <th><xsl:value-of select="php:function('translate', 'Topics')"/>: </th>
	      <td>
			<xsl:for-each select="//datafield[@tag=650]">
			  <xsl:variable name="DFIELD" select="." />
			  <xsl:for-each select="subfield">
			    <xsl:variable name="POS" select="position() + 1" />
    		    <a>
                  <xsl:attribute name="href"><xsl:value-of select="$path"/>/Search/Home?lookfor=<xsl:for-each select="$DFIELD/subfield"><xsl:if test="position() &lt; $POS"><xsl:value-of select="text()"/><xsl:text> </xsl:text></xsl:if></xsl:for-each>&amp;type=subject</xsl:attribute>
                  <xsl:value-of select="text()"/>  		      
    		    </a>
			    <xsl:if test="position() &lt; last()">
                  <xsl:text> - </xsl:text>
                </xsl:if>
              </xsl:for-each>
              <xsl:if test="position() &lt; last()">
                <xsl:text> | </xsl:text>
              </xsl:if>
			</xsl:for-each>
          </td>
        </tr>
      </xsl:if>
      <xsl:if test="//datafield[@tag=651]/subfield[@code='a']">
        <tr valign="top">
		  <th><xsl:value-of select="php:function('translate', 'Regions')"/>: </th>
		  <td>
			<xsl:for-each select="//datafield[@tag=651]">
			  <xsl:variable name="DFIELD" select="." />
			  <xsl:for-each select="subfield">
			    <xsl:variable name="POS" select="position() + 1" />
                <a>
                  <xsl:attribute name="href"><xsl:value-of select="$path"/>/Search/Home?lookfor=<xsl:for-each select="$DFIELD/subfield"><xsl:if test="position() &lt; $POS"><xsl:value-of select="text()"/><xsl:text> </xsl:text></xsl:if></xsl:for-each>&amp;type=subject</xsl:attribute>
                  <xsl:value-of select="text()"/>
			    </a>
			    <xsl:if test="position() &lt; last()">
                  <xsl:text> - </xsl:text>
                </xsl:if>
              </xsl:for-each>
              <xsl:if test="position() &lt; last()">
                <xsl:text> | </xsl:text>
              </xsl:if>
			</xsl:for-each>
          </td>
        </tr>
      </xsl:if>
      <xsl:if test="//datafield[@tag=655]/subfield[@code='a']">
        <tr valign="top">
		  <th><xsl:value-of select="php:function('translate', 'Genres')"/>: </th>
		  <td>
			<xsl:for-each select="//datafield[@tag=655]">
			  <xsl:variable name="DFIELD" select="." />
			  <xsl:for-each select="/subfield">
			    <xsl:variable name="POS" select="position() + 1" />
                <a>
                  <xsl:attribute name="href"><xsl:value-of select="$path"/>/Search/Home?lookfor=<xsl:for-each select="$DFIELD/subfield"><xsl:if test="position() &lt; $POS"><xsl:value-of select="text()"/><xsl:text> </xsl:text></xsl:if></xsl:for-each>&amp;type=subject</xsl:attribute>
                  <xsl:value-of select="text()"/>
                </a>
                <xsl:if test="position() &lt; last()">
                  <xsl:text> - </xsl:text>
                </xsl:if>
              </xsl:for-each>
			  <xsl:if test="position() &lt; last()">
                <xsl:text> | </xsl:text>
              </xsl:if>
			</xsl:for-each>
          </td>
        </tr>
      </xsl:if>

      <xsl:if test="//datafield[@tag=856]">
        <tr valign="top">
          <th><xsl:value-of select="php:function('translate', 'Online Access')"/>: </th>
          <td>
            <xsl:for-each select="//datafield[@tag=856]">
              <a>
                <xsl:attribute name="href">
                  <xsl:value-of select="./subfield[@code='u']"/>
                </xsl:attribute>
                <xsl:value-of select="./subfield[@code='z']"/>
              </a>
              <br/>
            </xsl:for-each>
          </td>
        </tr>
      </xsl:if>
      <tr valign="top">
        <th><xsl:value-of select="php:function('translate', 'Tags')"/>: </th>
        <td>
          <span style="float:right;">
            <a href="" class="addtag" onClick="showTagForm(); return false;">Add</a>
          </span>
          <div id="tagList">
          <xsl:choose>
            <xsl:when test="$tagList">
              <xsl:value-of select="$tagList" disable-output-escaping="yes"/>
            </xsl:when>
            <xsl:otherwise>
               No Tags, Be the first to tag this record!
            </xsl:otherwise>
          </xsl:choose>
          </div>
          <form name="tagForm" id="tagForm">
            <xsl:attribute name="onSubmit" disable-output-escaping="yes">SaveTag('<xsl:value-of select="$id"/>', '<xsl:value-of select="$userId"/>'); return false;</xsl:attribute>
            <input type="text" name="tag" style="float: right;"/>
          </form>
        </td>
      </tr>
    </table>
  </xsl:template>
	
  <xsl:template name="tabs">
      <div id="tabnav">
      <ul>
        <li>
          <xsl:if test="$tab = 'Description'">
            <xsl:attribute name="class">active</xsl:attribute>
          </xsl:if>
          <a href="{$localPath}/{$id}/Description" class="first"><span></span><xsl:value-of select="php:function('translate', 'Description')"/></a>
        </li>
        <li>
          <xsl:if test="($tab = 'Home') or ($tab = 'Holdings')">
            <xsl:attribute name="class">active</xsl:attribute>
          </xsl:if>
          <a href="{$localPath}/{$id}/Holdings"><span></span><xsl:value-of select="php:function('translate', 'Holdings')"/></a>
        </li>
        <xsl:if test="//datafield[@tag=505]">
          <li>
            <xsl:if test="$tab = 'TOC'">
              <xsl:attribute name="class">active</xsl:attribute>
            </xsl:if>
            <span></span><a href="{$localPath}/{$id}/TOC"><span></span><xsl:value-of select="php:function('translate', 'Table of Contents')"/></a>
          </li>
        </xsl:if>
        <li>
          <xsl:if test="$tab = 'UserComments'">
            <xsl:attribute name="class">active</xsl:attribute>
          </xsl:if>
          <a href="{$localPath}/{$id}/UserComments"><span></span><xsl:value-of select="php:function('translate', 'Comments')"/></a>
        </li>
        <xsl:if test="//datafield[@tag=020]">
          <li>
            <xsl:if test="$tab = 'Reviews'">
              <xsl:attribute name="class">active</xsl:attribute>
            </xsl:if>
            <a href="{$localPath}/{$id}/Reviews"><span></span><xsl:value-of select="php:function('translate', 'Reviews')"/></a>
          </li>
        </xsl:if>
        <li>
          <xsl:if test="$tab = 'Details'">
            <xsl:attribute name="class">active</xsl:attribute>
          </xsl:if>
          <a href="{$localPath}/{$id}/Details"><span></span><xsl:value-of select="php:function('translate', 'Staff View')"/></a>
        </li>
      </ul></div><br clear="left"/>
  </xsl:template>
  
  <xsl:template name="zotero">
    <xsl:choose>
      <xsl:when test="$format = 'Book'">
        <span class="Z3988">
          <xsl:attribute name="title">
            <xsl:text>ctx_ver=Z39.88-2004&amp;</xsl:text>
            <xsl:text>rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&amp;</xsl:text>
            <xsl:text>rfr_id=info%3Asid%2Focoins.info%3Agenerator&amp;</xsl:text>
            <xsl:text>rft.genre=book&amp;</xsl:text>
            <xsl:text>rft.btitle=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='a']))"/>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='b']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.title=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='a']))"/>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='b']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.au=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=100]/subfield[@code='a']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.date=</xsl:text>
            <xsl:value-of select="//datafield[@tag=260]/subfield[@code='c']"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.pub=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=260]/subfield[@code='a']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.edition=</xsl:text>
            <xsl:value-of select="//datafield[@tag=250]/subfield[@code='a']"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.isbn=</xsl:text>
            <xsl:value-of select="substring(//datafield[@tag=020]/subfield[@code='a'], 0, 10)"/>
          </xsl:attribute>
        </span>
      </xsl:when>
      <xsl:when test="$format = 'Journal'">
        <span class="Z3988">
          <xsl:attribute name="title">
            <xsl:text>ctx_ver=Z39.88-2004&amp;</xsl:text>
            <xsl:text>rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;</xsl:text>
            <xsl:text>rfr_id=info%3Asid%2Focoins.info%3Agenerator&amp;</xsl:text>
            <xsl:text>rft.genre=article&amp;</xsl:text>
            <xsl:text>rft.title=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='a']))"/>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='b']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.date=</xsl:text>
            <xsl:value-of select="//datafield[@tag=260]/subfield[@code='c']"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.issn=</xsl:text>
            <xsl:value-of select="//datafield[@tag=022]/subfield[@code='a']"/>
          </xsl:attribute>
        </span>
      </xsl:when>
      <xsl:otherwise>
        <span class="Z3988">
          <xsl:attribute name="title">
            <xsl:text>ctx_ver=Z39.88-2004&amp;</xsl:text>
            <xsl:text>rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc&amp;</xsl:text>
            <xsl:text>rfr_id=info%3Asid%2Focoins.info%3Agenerator&amp;</xsl:text>
            <xsl:text>rft.title=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='a']))"/>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=245]/subfield[@code='b']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:choose>
              <xsl:when test="//datafield[@tag=100]">
                <xsl:text>rft.creator=</xsl:text>
                <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=100]/subfield[@code='a']))"/>
                <xsl:text>&amp;</xsl:text>
              </xsl:when>
              <xsl:when test="//datafield[@tag=700]">
                <xsl:for-each select="//datafield[@tag=700]">
                  <xsl:text>rft.creator=</xsl:text>
                  <xsl:value-of select="php:function('urlencode', string(./subfield[@code='a']))"/>
                  <xsl:text>&amp;</xsl:text>
                </xsl:for-each>
              </xsl:when>
            </xsl:choose>
            <xsl:for-each select="//datafield[@tag=650]">
              <xsl:text>rft.subject=</xsl:text>
              <xsl:value-of select="php:function('urlencode', string(./subfield[@code='a']))"/>
              <xsl:text>&amp;</xsl:text>
            </xsl:for-each>
            <xsl:text>rft.description=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=500]/subfield[@code='a']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.publisher=</xsl:text>
            <xsl:value-of select="php:function('urlencode', string(//datafield[@tag=260]/subfield[@code='b']))"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.date=</xsl:text>
            <xsl:value-of select="//datafield[@tag=260]/subfield[@code='c']"/>
            <xsl:text>&amp;</xsl:text>
            <xsl:text>rft.format=</xsl:text>
            <xsl:value-of select="$format"/>
          </xsl:attribute>
        </span>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:stylesheet>