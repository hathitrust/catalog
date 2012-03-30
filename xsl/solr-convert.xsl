<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>
  <xsl:template match="/">
    <ResultSet>
      <RecordCount><xsl:value-of select="//result/@numFound"/></RecordCount>
      <SpellcheckSuggestion><xsl:value-of select="//lst[@name='spellcheck']/lst[@name='suggestions']/str[@name='collation']"/></SpellcheckSuggestion>
      <xsl:call-template name="facet"/>
      <xsl:call-template name="doc"/>
      <xsl:call-template name="similar"/>
    </ResultSet>
  </xsl:template>
    
  <xsl:template name="doc">
    <xsl:for-each select="//result[@name='response']/doc">
    <record>
      <score><xsl:value-of select="./float[@name='score']"/></score>

      <id><xsl:value-of select="./str[@name='id']"/></id>

      <xsl:if test="./str[@name='fullrecord']">
        <fullrecord><xsl:value-of select="./str[@name='fullrecord']"/></fullrecord>
      </xsl:if>

      <xsl:if test="./arr[@name='format']">
        <xsl:for-each select="./arr[@name='format']/str">
          <format><xsl:value-of select="."/></format>
        </xsl:for-each>
      </xsl:if>

      <xsl:if test="./arr[@name='oclc']">
        <xsl:for-each select="./arr[@name='oclc']/str">
          <oclc><xsl:value-of select="."/></oclc>
        </xsl:for-each>
      </xsl:if>

      <xsl:if test="./arr[@name='availability']">
        <xsl:for-each select="./arr[@name='availability']/str">
          <availability><xsl:value-of select="."/></availability>
        </xsl:for-each>
      </xsl:if>

      <xsl:if test="./arr[@name='language']">
        <xsl:for-each select="./arr[@name='language']/str">
          <language><xsl:value-of select="."/></language>
        </xsl:for-each>
      </xsl:if>
            
      <xsl:if test="./arr[@name='isbn']">
        <isbn><xsl:value-of select="./arr[@name='isbn']/str"/></isbn>
      </xsl:if>
      
      <xsl:if test="./arr[@name='issn']">
        <issn><xsl:value-of select="./arr[@name='issn']/str"/></issn>
      </xsl:if>
      
      <xsl:if test="./str[@name='callnumber']">
        <callnumber><xsl:value-of select="./str[@name='callnumber']"/></callnumber>
      </xsl:if>

      <xsl:if test="./str[@name='callnumber-a']">
        <callnumber-a><xsl:value-of select="./str[@name='callnumber-a']"/></callnumber-a>
      </xsl:if>

      <xsl:if test="./str[@name='callnumber-first']">
        <callnumber-first><xsl:value-of select="./str[@name='callnumber-first']"/></callnumber-first>
      </xsl:if>

      <xsl:if test="./str[@name='callnumber-first-code']">
        <callnumber-first-code><xsl:value-of select="./str[@name='callnumber-first-code']"/></callnumber-first-code>
      </xsl:if>

      <xsl:if test="./str[@name='callnumber-subject']">
        <callnumber-subject><xsl:value-of select="./str[@name='callnumber-subject']"/></callnumber-subject>
      </xsl:if>

      <xsl:if test="./str[@name='callnumber-subject-code']">
        <callnumber-subject-code><xsl:value-of select="./str[@name='callnumber-subject-code']"/></callnumber-subject-code>
      </xsl:if>

      <xsl:if test="./str[@name='callnumber-label']">
        <callnumber-label><xsl:value-of select="./str[@name='callnumber-label']"/></callnumber-label>
      </xsl:if>

      <xsl:for-each select="./arr[@name='author']/str">
        <author><xsl:value-of select="."/></author>
      </xsl:for-each>

      <xsl:if test="./str[@name='author-letter']">
        <author-letter><xsl:value-of select="./str[@name='author-letter']"/></author-letter>
      </xsl:if>

      <xsl:for-each select="./arr[@name='author2']/str">
        <author2><xsl:value-of select="."/></author2>
      </xsl:for-each>
      
      <xsl:if test="./str[@name='titleSort']">
        <titleSort><xsl:value-of select="./str[@name='titleSort']"/></titleSort>
      </xsl:if>

      <xsl:for-each select="./arr[@name='title']/str">
        <title><xsl:value-of select="."/></title>
      </xsl:for-each>

      <xsl:if test="./arr[@name='title_ab']">
        <shorttitle><xsl:value-of select="./arr[@name='title_ab']/str"/></shorttitle>
      </xsl:if>

      <xsl:if test="./arr[@name='title_full']">
        <fulltitle><xsl:value-of select="./arr[@name='title_full']/str"/></fulltitle>
      </xsl:if>

      <xsl:if test="./arr[@name='title_alt']">
        <title2><xsl:value-of select="./arr[@name='title_alt']/str"/></title2>
      </xsl:if>

      <xsl:if test="./str[@name='title_old']">
        <oldTitle><xsl:value-of select="./str[@name='title_old']"/></oldTitle>
      </xsl:if>

      <xsl:if test="./str[@name='title_new']">
        <newTitle><xsl:value-of select="./str[@name='title_new']"/></newTitle>
      </xsl:if>

      <xsl:if test="./str[@name='publisher']">
        <publisher><xsl:value-of select="./str[@name='publisher']"/></publisher>
      </xsl:if>
            
      <xsl:if test="./arr[@name='publishDate']">
        <publishDate><xsl:value-of select="./arr[@name='publishDate']"/></publishDate>
      </xsl:if>

      <xsl:if test="./str[@name='edition']">
        <edition><xsl:value-of select="./str[@name='edition']"/></edition>
      </xsl:if>

      <xsl:if test="./str[@name='dateSpan']">
        <dateSpan><xsl:value-of select="./str[@name='dateSpan']"/></dateSpan>
      </xsl:if>
      
      <xsl:if test="./str[@name='series']">
        <series><xsl:value-of select="./str[@name='series']"/></series>
      </xsl:if>

      <xsl:if test="./str[@name='topicStr']">
        <topic><xsl:value-of select="./str[@name='topicStr']"/></topic>
      </xsl:if>

      <xsl:if test="./str[@name='fulltopic']">
        <fulltopic><xsl:value-of select="./str[@name='fulltopic']"/></fulltopic>
      </xsl:if>

      <xsl:for-each select="./arr[@name='url']/str">
        <url><xsl:value-of select="."/></url>
      </xsl:for-each>

    </record>
    </xsl:for-each>
  </xsl:template>
  
  <xsl:template name="facet">
    <Facets>
      <xsl:for-each select="//lst[@name='facet_fields']/lst">
        <Cluster>
          <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
          <xsl:for-each select="./int">
            <xsl:variable name="elem" select="../@name"/>
            <item>
              <xsl:attribute name="count"><xsl:value-of select="."/></xsl:attribute>
              <xsl:value-of select="@name"/>
            </item>
          </xsl:for-each>
        </Cluster>
      </xsl:for-each>
    </Facets>
  </xsl:template>

  <xsl:template name="similar">
    <MoreLikeThis>
      <xsl:for-each select="//lst[@name='moreLikeThis']/result/doc">
        <record>
          <id><xsl:value-of select="./str[@name='id']"/></id>
          <format><xsl:value-of select="./str[@name='format']"/></format>
          <title><xsl:value-of select="./str[@name='title']"/></title>
        </record>
      </xsl:for-each>
    </MoreLikeThis>
  </xsl:template>

  <xsl:template name="spelling">
    <SpellCheck>
      <xsl:for-each select="//lst[@name='spellcheck']/lst">
        <word>
          <original><xsl:value-of select="."/></original>
          <suggestion>
            <xsl:for-each select="./arr[@name='suggestion']/str">
              <word><xsl:value-of select="."/></word>
            </xsl:for-each>
          </suggestion>
        </word>
      </xsl:for-each>
    </SpellCheck>
  </xsl:template>

</xsl:stylesheet>
