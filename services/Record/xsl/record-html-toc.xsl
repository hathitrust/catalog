<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:php="http://php.net/xsl"
                xsl:extension-element-prefixes="php">

  <xsl:import href="record-html.xsl"/>

  <xsl:output method="html" indent="yes" />

  <xsl:template match="/">
    <b>Table of Contents: </b>
    <xsl:choose>
      <xsl:when test="//datafield[@tag=505]/subfield[@code='t']">
        <ul class="toc">
          <xsl:for-each select="//datafield[@tag=505]/subfield[@code='t']">
            <xsl:variable name="cnt" select="position()"/>
            <li>
              <xsl:value-of select="../subfield[@code='g'][$cnt]"/>
              <xsl:choose>
                <xsl:when test="../subfield[@code='u'][$cnt]">
                  <a>
                    <xsl:attribute name="href"><xsl:value-of select="subfield[@code='u'][$cnt]"/></xsl:attribute>
                    <xsl:value-of select="text()"/>
                  </a>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="../subfield[@code='t'][$cnt]"/></xsl:otherwise>
              </xsl:choose>
              <xsl:value-of select="../subfield[@code='r'][$cnt]"/>
            </li>

		    <!--
		    <br/>
		    <i><xsl:value-of select="//datafield[@tag=505]/subfield[@code='r']"/></i>
		    </td>
		    <td><xsl:value-of select="//datafield[@tag=505]/subfield[@code='g']"/></td>
		    -->
          </xsl:for-each>
        </ul>
      </xsl:when>
      <xsl:when test="//datafield[@tag=505]/subfield[@code='a']">
        <xsl:value-of select="//datafield[@tag=505]/subfield[@code='a']"/>
      </xsl:when>
    </xsl:choose>
  </xsl:template>
  
</xsl:stylesheet>
