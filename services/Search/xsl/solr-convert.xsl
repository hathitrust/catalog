<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="xml" indent="yes"/>
  <xsl:template match="//doc">
    <record>
      <id><xsl:value-of select="./field[@name='id']"/></id>

      <xsl:if test="./field[@name='format']">
      <format><xsl:value-of select="./field[@name='format']"/></format>
      </xsl:if>

      <xsl:if test="./field[@name='language']">
      <language><xsl:value-of select="./field[@name='language']"/></language>
      </xsl:if>
            
      <xsl:if test="./field[@name='isbn']">
      <isbn><xsl:value-of select="./field[@name='isbn']"/></isbn>
      </xsl:if>
      
      <xsl:if test="./field[@name='issn']">
      <issn><xsl:value-of select="./field[@name='issn']"/></issn>
      </xsl:if>
      
      <xsl:if test="./field[@name='callnumber']">
      <callnumber><xsl:value-of select="./field[@name='callnumber']"/></callnumber>
      </xsl:if>
      
      <xsl:for-each select="./field[@name='author']">
        <author><xsl:value-of select="."/></author>
      </xsl:for-each>
      
      <title><xsl:value-of select="./field[@name='title']"/></title>
      
      <xsl:if test="./field[@name='title2']">
      <title2><xsl:value-of select="./field[@name='title2']"/></title2>
      </xsl:if>

      <xsl:if test="./field[@name='publisher']">
      <publisher><xsl:value-of select="./field[@name='publisher']"/></publisher>
      </xsl:if>
            
      <xsl:if test="./field[@name='publishDate']">
      <publishDate><xsl:value-of select="./field[@name='publishDate']"/></publishDate>
      </xsl:if>
      
      <xsl:if test="./field[@name='physical']">
      <physical><xsl:value-of select="./field[@name='physical']"/></physical>
      </xsl:if>
      
      <xsl:if test="./field[@name='dateSpan']">
      <dateSpan><xsl:value-of select="./field[@name='dateSpan']"/></dateSpan>
      </xsl:if>
      
      <xsl:if test="./field[@name='series']">
      <series><xsl:value-of select="./field[@name='series']"/></series>
      </xsl:if>

      <xsl:if test="./field[@name='contents']">
      <contents><xsl:value-of select="./field[@name='contents']"/></contents>
      </xsl:if>
      
      <xsl:call-template name="subjects"/>
      
      <xsl:if test="./field[@name='oldTitle']">
      <oldTitle><xsl:value-of select="./field[@name='oldTitle']"/></oldTitle>
      </xsl:if>
      
      <xsl:if test="./field[@name='newTitle']">
      <newTitle><xsl:value-of select="./field[@name='newTitle']"/></newTitle>
      </xsl:if>
      
      <xsl:if test="./field[@name='url']">
      <url><xsl:value-of select="./field[@name='url']"/></url>
      </xsl:if>
    </record>
  </xsl:template>
  
  <xsl:template name="subjects">
    <xsl:if test="./field[@name='subject1']">
      <xsl:for-each select="./field[@name='subject1']">
        <subject1><xsl:value-of select="."/></subject1>
      </xsl:for-each>
    </xsl:if>
    <xsl:if test="./field[@name='subject2']">
      <xsl:for-each select="./field[@name='subject2']">
        <subject2><xsl:value-of select="."/></subject2>
      </xsl:for-each>
    </xsl:if>
    <xsl:if test="./field[@name='subject3']">
      <xsl:for-each select="./field[@name='subject3']">
        <subject3><xsl:value-of select="."/></subject3>
      </xsl:for-each>
    </xsl:if>
    <xsl:if test="./field[@name='subject4a']">
      <xsl:for-each select="./field[@name='subject4a']">
        <subject4a><xsl:value-of select="."/></subject4a>
      </xsl:for-each>
    </xsl:if>
    <xsl:if test="./field[@name='subject4x']">
      <xsl:for-each select="./field[@name='subject4x']">
        <subject4x><xsl:value-of select="."/></subject4x>
      </xsl:for-each>
    </xsl:if>
    <xsl:if test="./field[@name='subject5']">
      <xsl:for-each select="./field[@name='subject5']">
        <subject5><xsl:value-of select="."/></subject5>
      </xsl:for-each>
    </xsl:if>
    <xsl:if test="./field[@name='subject6']">
      <xsl:for-each select="./field[@name='subject6']">
        <subject6><xsl:value-of select="."/></subject6>
      </xsl:for-each>
    </xsl:if>
  </xsl:template>  
</xsl:stylesheet>
