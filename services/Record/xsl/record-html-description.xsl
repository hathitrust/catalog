<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:php="http://php.net/xsl"
                xsl:extension-element-prefixes="php">

  <xsl:import href="record-html.xsl"/>

  <xsl:output method="html" indent="yes" />

  <xsl:template match="/">
    <table cellpadding="2" cellspacing="0" border="0" class="citation">
      <xsl:if test="//datafield[@tag=500]">
        <tr valign="top">
          <th width="150">Item Description:</th>
          <td>
            <xsl:for-each select="//datafield[@tag=500]">
            <p>
              <xsl:value-of select="subfield[@code='a']"/>
            </p>
            </xsl:for-each>

            <p>
              <xsl:value-of select="//datafield[@tag=520]/subfield[@code='a']"/>
            </p>
          </td>
        </tr>
      </xsl:if>
    
      <xsl:if test="//datafield[@tag=300]">
        <tr valign="top">
          <th width="150">Physical Description:</th>
          <td>
            <xsl:value-of select="//datafield[@tag=300]/subfield[@code='a']"/>
		    <xsl:text> </xsl:text>
	        <xsl:value-of select="//datafield[@tag=300]/subfield[@code='b']"/>
		    <xsl:text> </xsl:text>
	        <xsl:value-of select="//datafield[@tag=300]/subfield[@code='e']"/>
	        <xsl:for-each select="//datafield[@tag=530]/subfield[@code='a']">
              <br/>
              <xsl:value-of select="."/>
            </xsl:for-each>
          </td>
        </tr>
      </xsl:if>

      <xsl:if test="//datafield[@tag=306]">
        <tr valign="top">
          <th width="150">Playing Time: </th>
          <td><xsl:value-of select="//datafield[@tag=306]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=538]">
        <tr valign="top">
          <th width="150">Format: </th>
          <td><xsl:value-of select="//datafield[@tag=538]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=521]">
        <tr valign="top">
          <th width="150">Audience: </th>
          <td><xsl:value-of select="//datafield[@tag=521]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=586]">
        <tr valign="top">
          <th width="150">Awards: </th>
          <td><xsl:value-of select="//datafield[@tag=586]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=508]">
        <tr valign="top">
          <th width="150">Production Credits: </th>
          <td><xsl:value-of select="//datafield[@tag=508]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=504]">
        <tr valign="top">
          <th width="150">Bibliography:</th>
          <td>
            <xsl:value-of select="//datafield[@tag=504]/subfield[@code='a']"/>
          </td>
        </tr>
      </xsl:if>

      <xsl:if test="//datafield[@tag=020]">
        <tr valign="top">
          <th width="150">ISBN: </th>
          <td><xsl:value-of select="//datafield[@tag=020]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>
  	  
      <xsl:if test="//datafield[@tag=022]">
        <tr valign="top">
          <th width="150">ISSN: </th>
          <td><xsl:value-of select="//datafield[@tag=022]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=580]">
        <tr valign="top">
          <th width="150">Related Items: </th>
          <td><xsl:value-of select="//datafield[@tag=580]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=506]">
        <tr valign="top">
          <th width="150">Access: </th>
          <td><xsl:value-of select="//datafield[@tag=506]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>

      <xsl:if test="//datafield[@tag=555]">
        <tr valign="top">
          <th width="150">Finding Aid: </th>
          <td><xsl:value-of select="//datafield[@tag=555]/subfield[@code='a']"/></td>
        </tr>
  	  </xsl:if>
    </table>
  </xsl:template>
  
</xsl:stylesheet>
