<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:php="http://php.net/xsl"
                xsl:extension-element-prefixes="php">

  <xsl:output method="text" indent="no"/>

  <xsl:template match="/">
    <xsl:if test="not(//datafield[@tag=700])">
        <xsl:value-of select="substring(//datafield[@tag=100]/subfield[@code='a'], 1, string-length(//datafield[@tag=100]/subfield[@code='a']) - 1)" />.
    </xsl:if>
    <xsl:if test="//datafield[@tag=700]">
        <xsl:value-of select="//datafield[@tag=100]/subfield[@code='a']"/>
    </xsl:if>
    <xsl:apply-templates select="//datafield[@tag=700]" />
    (<xsl:choose> 
        <xsl:when test="substring(//datafield[@tag=260]/subfield[@code='c'], 1, 1) = 'c'">
            <xsl:value-of select="substring(//datafield[@tag=260]/subfield[@code='c'], 2, string-length(//datafield[@tag=260]/subfield[@code='c']) - 2)" />
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="substring(//datafield[@tag=260]/subfield[@code='c'], 1, string-length(//datafield[@tag=260]/subfield[@code='c']) - 1)" />
        </xsl:otherwise>
    </xsl:choose>)
    <i>
      <xsl:value-of select="//datafield[@tag=245]/subfield[@code='a']" />
      <xsl:value-of select="substring(//datafield[@tag=245]/subfield[@code='b'], 1, string-length(//datafield[@tag=245]/subfield[@code='b']) - 1)" />
    </i> 
    <xsl:value-of select="//datafield[@tag=260]/subfield[@code='a']" />
    <xsl:text> </xsl:text>
    <xsl:value-of select="//datafield[@tag=260]/subfield[@code='b']" />
  </xsl:template>
    
    <xsl:template match="//datafield[@tag=700]">
        <xsl:if test="position() = last() and not(count(.) = 1)">
            <xsl:text> and </xsl:text>
            <xsl:value-of select="substring(./subfield[@code='a'], 1, string-length(./subfield[@code='a'])-1)"/>.
            <xsl:if test="not(//datafield[@tag=100])">
                <xsl:text> (Eds.) </xsl:text>
            </xsl:if>
        </xsl:if>
        <xsl:if test="position() != last()">
            <xsl:if test="not(not(//datafield[@tag=100]) and position() = 1)">
                <xsl:text>, </xsl:text>
            </xsl:if>
            <xsl:value-of select="./subfield[@code='a']"/>
        </xsl:if>
        <xsl:if test="position() = last() and (count(.) = 1)">
            <xsl:value-of select="substring(./subfield[@code='a'], 1, string-length(./subfield[@code='a'])-1)"/>.
            <xsl:if test="not(//datafield[@tag=100])">
                <xsl:text> (Eds.) </xsl:text>
            </xsl:if>
        </xsl:if>
        
    </xsl:template>
    
</xsl:stylesheet>
