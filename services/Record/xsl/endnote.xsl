<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:php="http://php.net/xsl"
                xsl:extension-element-prefixes="php">

  <xsl:output method="text" indent="no"/>

  <xsl:template match="/">
        <xsl:if test="//datafield[@tag=100] or //datafield[@tag=700]">
        <xsl:text>Author:</xsl:text>
        
        <xsl:apply-templates select="//datafield[@tag=100]" />
        <xsl:apply-templates select="//datafield[@tag=700]" />
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=245]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>Title:&#9;&#9;&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=245]/subfield[@code='a']" />
        <xsl:value-of select="substring(//datafield[@tag=245]/subfield[@code='b'], 1, string-length(//datafield[@tag=245]/subfield[@code='b']) - 1)" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=260]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>City:&#9;&#9;&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=260]/subfield[@code='a']" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=260]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>Publisher:&#9;&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=260]/subfield[@code='b']" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=300]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>Number of pages:&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=300]/subfield[@code='a']" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=250]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>Edition:&#9;&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=250]/subfield[@code='a']" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=020]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>ISBN:&#9;&#9;&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=020]/subfield[@code='a']" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=035]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>Accession Number:&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=035]/subfield[@code='a']" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
        <xsl:if test="//datafield[@tag=505]">
        <xsl:text>&#xa;</xsl:text>
        <xsl:text>Notes:&#9;&#9;&#9;</xsl:text>
        <xsl:value-of select="//datafield[@tag=505]" />
        <xsl:text>&#xa;</xsl:text>
        </xsl:if>
        
    </xsl:template>
    <xsl:template match="//datafield[@tag=100]">
        <xsl:text>&#9;&#9;&#9;</xsl:text>
        <xsl:value-of select="./subfield[@code='a']"/>
        <xsl:text>&#xa;</xsl:text>
    </xsl:template>
    <xsl:template match="//datafield[@tag=700]">
        <xsl:text>&#9;&#9;&#9;</xsl:text>
        <xsl:value-of select="./subfield[@code='a']"/>
        <xsl:text>&#xa;</xsl:text>
    </xsl:template>


</xsl:stylesheet>
