<?xml version="1.0" encoding="utf-8"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                              xmlns:aws="http://webservices.amazon.com/AWSECommerceService/2005-10-05">

  <xsl:output method="html" omit-xml-declaration="yes" indent="yes"/>

  <xsl:template match="/">
    <xsl:for-each select="//aws:Review"><p>
       <img>
          <xsl:attribute name="src">
            ../../images/<xsl:value-of select="./aws:Rating"/>.gif</xsl:attribute>
          <xsl:attribute name="alt">
            <xsl:value-of select="./aws:Rating"/>
          </xsl:attribute>
        </img><b>
        <xsl:value-of select="./aws:Summary"/>
        </b>,
       
        
        <xsl:value-of select="./aws:Date"/>
      </p>
      <div class="summary" id="summary">
        <xsl:value-of select="./aws:Content"/>
      </div>
      <hr/>
    </xsl:for-each>
    
    <div>
      <a target="new" style="font-size: 6pt; color: #999999;">
        <xsl:attribute name="href">http://amazon.com/dp/<xsl:value-of select="$isbn"/></xsl:attribute>
        Supplied by Amazon
      </a>
    </div>

  </xsl:template>

</xsl:stylesheet>
