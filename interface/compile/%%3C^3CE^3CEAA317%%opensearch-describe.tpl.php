<?php /* Smarty version 2.6.21, created on 2012-03-21 11:54:46
         compiled from Search/opensearch-describe.tpl */ ?>
<?php echo '<?xml'; ?>
 version="1.0"<?php echo '?>'; ?>

<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
  <ShortName><?php echo $this->_tpl_vars['site']['title']; ?>
</ShortName>
  <Description>Library Catalog Search</Description>
  <Image height="16" width="16" type="image/x-icon"><?php echo $this->_tpl_vars['site']['url']; ?>
/favicon.ico</Image>
  <Contact><?php echo $this->_tpl_vars['site']['email']; ?>
</Contact>
  <Url type="text/html" method="get" template="<?php echo $this->_tpl_vars['site']['url']; ?>
/Search/Home?lookfor=<?php echo '{searchTerms}&amp;page={startPage?}'; ?>
"/>
  <Url type="application/rss+xml" method="get" template="<?php echo $this->_tpl_vars['site']['url']; ?>
/Search/Home?lookfor=<?php echo '{searchTerms}'; ?>
&amp;view=rss"/>
  <Url type="application/x-suggestions+json" method="get" template="<?php echo $this->_tpl_vars['site']['url']; ?>
/Search/Suggest?lookfor=<?php echo '{searchTerms}'; ?>
&amp;format=JSON"/>
</OpenSearchDescription>