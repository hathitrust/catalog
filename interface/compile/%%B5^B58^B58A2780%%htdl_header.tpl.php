<?php /* Smarty version 2.6.21, created on 2012-03-21 11:54:45
         compiled from htdl_header.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'htdl_header.tpl', 6, false),)), $this); ?>
<div id="mbHeader">
  <div id="masthead">
    <div class="branding">
      <div class="brandingLogo">
        <a class="SkipLink" accesskey="2" href="#skipNav">Skip navigation</a>
        <h1 class="SkipLink">Hathi Trust Digital Library - <?php echo ((is_array($_tmp=$this->_tpl_vars['pageTitle'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 64, "...") : smarty_modifier_truncate($_tmp, 64, "...")); ?>
</h1>
        <a href="http://catalog.hathitrust.org/">
          <img src="/images/hathi/HathiTrust.gif" alt="Hathi Trust Logo" /><span>Digital Library</span></a>
      </div>
    </div>  
    <h2 class="SkipLink">Navigation links for help, feedback, FAQ, etc.</h2>
    <div class="MBooksNav">
      <ul>
        <!--<li><a href="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub;sort=cn_a">Login</a></li>-->
        <li><a href="http://www.hathitrust.org/help" title="Help page and faq">Help</a></li>
	      <li><a href="#" id="feedback" title="Feedback form for problems or comments"><span>Feedback</span></a></li>
      </ul>
    </div>

  </div>  

  
  <div id="CBNavContainer">
    <div id="CBNav">
      <h2 class="SkipLink">Navigation links for search and collections</h2>
      <ul>
        <li><a href="http://www.hathitrust.org/"><span title="HathiTrust Home">Home</span></a></li>
        <li><a href="http://www.hathitrust.org/about"><span title="About">About</span></a></li>
        <li><a href="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub"><span title="Collections">Collections</span></a></li>
        <li><a href="http://babel.hathitrust.org/cgi/mb?a=listcs;colltype=priv"><span title="My Collections">My Collections</span></a></li>
      </ul>
    </div>
  </div>

  <?php if ($this->_tpl_vars['pageTitle'] == 'Search Home'): ?>
  <div id="SubNavHeader">
    <div id="SubNavHeaderCont">
      <div class="CollPage">
        <span></span>
      </div>
    </div>
  </div>
 
  <?php else: ?>

  <div id="SubNavHeader">
    <div id="SubNavHeaderCont">
      <div class="CollPage">
        <span>Catalog Search</span>
      </div>
    </div>
  </div>
  <?php endif; ?>
  
</div>  
  
<a name="skipNav"></a>  







