<?php /* Smarty version 2.6.21, created on 2012-03-21 11:54:45
         compiled from layout.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'layout.tpl', 5, false),)), $this); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="<?php echo $this->_tpl_vars['userLang']; ?>
">
<!-- Machine is <?php echo $this->_tpl_vars['machine']; ?>
 -->
  <head>
    <title>Hathi Trust Digital Library - <?php echo ((is_array($_tmp=$this->_tpl_vars['pageTitle'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 64, "...") : smarty_modifier_truncate($_tmp, 64, "...")); ?>
</title>
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="<?php echo $this->_tpl_vars['url']; ?>
/Search/OpenSearch?method=describe">  
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->_tpl_vars['path']; ?>
/interface/themes/hathi/css/htdl_vf.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->_tpl_vars['path']; ?>
/interface/themes/hathi/css/styles.css">
    <link rel="stylesheet" type="text/css" media="print" href="<?php echo $this->_tpl_vars['path']; ?>
/interface/themes/hathi/css/print.css">

<?php if ($this->_tpl_vars['id']): ?>
  <link rel="canonical" href="/Record/<?php echo $this->_tpl_vars['id']; ?>
">
  <link rel="alternate" type="application/marc" href="/Record/<?php echo $this->_tpl_vars['id']; ?>
.mrc" >
  <link rel="alternate" type="application/marc+xml" href="/Record/<?php echo $this->_tpl_vars['id']; ?>
.xml" >
  <link rel="alternate" type="application/x-Research-Info-Systems" href="/Record/<?php echo $this->_tpl_vars['id']; ?>
.ris" >
  <link rel="alternate" type="application/x-endnote-refer" href="/Record/<?php echo $this->_tpl_vars['id']; ?>
.endnote" >
  <link rel="alternate" href="/Record/<?php echo $this->_tpl_vars['id']; ?>
.rdf" type="application/rdf+xml" />    
<?php endif; ?>

<!-- Jeremy's additions 3/27/09 -->

    <link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.7.0/build/container/assets/container-core.css"> 
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->_tpl_vars['path']; ?>
/interface/themes/hathi/css/yui.css"> 

    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/yuiloader/yuiloader-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dom/dom-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/event/event-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/connection/connection-min.js"></script> 
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/dragdrop/dragdrop-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/container/container-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/element/element-min.js"></script>
    <script type="text/javascript" src="http://yui.yahooapis.com/2.7.0/build/button/button-min.js"></script>
    <script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['path']; ?>
/js/yui/feedback.js"></script>
    <script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['path']; ?>
/js/ajax.yui.js"></script>
    <script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['path']; ?>
/js/scripts.js"></script>

<!-- End Jeremy's additions -->
    
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">


    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js" type="text/javascript"></script>
    <script language="JavaScript" type="text/javascript">
    path = '<?php echo $this->_tpl_vars['url']; ?>
';
    <?php echo '

    var jq = jQuery.noConflict();
    

  function SubmitFeedback(frm) {
    jq.post(\'/static/feedback.php\', 
            {\'comment\': jq("[name=\'comment\']", frm).val(),
             \'uniqname\': jq("[name=\'uniqname\']", frm).val(),
             \'subject\':  jq("[name=\'subject\']", frm).val(),
             \'url\': jq("[name=\'url\']:checked", frm).val()
            },
            function(data) {
              document.getElementById(\'popupbox\').innerHTML = \'<div style="padding: 4em;"><h3>Message Sent</h3><p>Thank you for your feedback!</p></div>\';
              setTimeout("hideLightbox();", 3000);
              return false;
            }

          );
    return false;        
  }

String.prototype.trim = function() {
	return this.replace(/^\\s+|\\s+$/g,"");
}

function trimForm(infrm) {
  infrm.value = infrm.value.trim()
  return true;
 }

function loginLink() {
  login = \'https://beta.lib.umich.edu/vf/vflogin?returnto=\';
  loc = window.location.href;
  return login + escape(loc);
}

closeButton = \' <div style="float: right"><a href="#" onclick="hideLightbox(); return false">Close [X]</a></div>\'

function fillLightbox(id) {
  lightbox();
  document.getElementById(\'popupbox\').innerHTML = closeButton + document.getElementById(id).innerHTML;
  return false;
}

jq.fn.clearForm = function(exceptions) {
  return this.each(function() {
    if (jq(this).hasClass(\'stickyform\')) {
      return;
    }
    var type = this.type, tag = this.tagName.toLowerCase();
    if (tag == \'form\')
      return jq(\':input\',this).clearForm();
    if (type == \'text\' || type == \'password\' || tag == \'textarea\')
      this.value = \'\';
    else if (type == \'checkbox\' || type == \'radio\')
      this.checked = false;
    else if (tag == \'select\')
      this.selectedIndex = 0;
  });
};

function pausecomp(millis)
{
  var date = new Date();
  var curDate = null;

  do { curDate = new Date(); }
  while(curDate-date < millis);
}

function submitFeedback() 
{
  document.getElementById(\'popupbox\').innerHTML +=  document.getElementById(\'feedback_thanks\').innerHTML;
  setTimeout("hideLightbox()", 1500); 
  return true;
}

function hideMenu(elemId)
{
    document.getElementById(elemId).style.display=\'none\';
}


    </script>

  <script language="JavaScript" type="text/javascript">
  function changetop(A){
    // var B=document.getElementById(A);
    // var C=BrowserDetect.browser;
   // if(C=="Safari"){
    //      } else {
   	B.className="specialstyle";
    // }
  	return false;
  }
  	
  function resumetop(A) {
  	var B=document.getElementById(A);
  	B.className="";
  	return false;
  }
  
  </script>

  <!-- Suz, testing random featured collection -->
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript">

  this.randomtip = function(){
  	var length = $("#featured .itemList").length;
  	var ran = Math.floor(Math.random()*length) + 1;
  	$("#featured .itemList:nth-child(" + ran + ")").show();
  };

  $(document).ready(function(){	
  	randomtip();
  });

  </script>

'; ?>


  </head>

  <body id="yahoo-com" class="yui-skin-sam">

    <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "htdl_header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

    <div id="doc3" class="yui-t8">

      <h2 class="SkipLink">Main Content</h2>
<!-- Searchbox -->
      <?php if (! $this->_tpl_vars['isTheHomePage']): ?>
      <div id="bd">
        <div id="yui-main">
            <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "searchbox.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
        </div>
      </div>        
      <?php endif; ?>

<!-- Content -->
      <div id="contentContainer">
          <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => ($this->_tpl_vars['module'])."/".($this->_tpl_vars['pageTemplate']), 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>

    </div>

<!-- HTDL Footer     -->
    <div id="mbFooter">
      <div id="FooterCont">
        <div class="MBooksNav">
          <h2 class="SkipLink">Footer Links</h2>
          <ul>
              <!-- <li><a href="https://babel.hathitrust.org/cgi/mb?a=listcs;colltype=pub;sort=cn_a">Login</a></li> -->
              <li><a href="http://www.hathitrust.org" title="About Hathi Trust">About</a></li>
              <li><a href="http://www.hathitrust.org/faq" title="Help page and faq">Help</a></li>
              <li><a href=" " id="feedback_footer" title="Feedback form for problems or comments"><span>Feedback</span></a></li>
              <!-- <li><a href="http://mirlyn.lib.umich.edu/" title="New Search in Mirlyn">Mirlyn Library Catalog</a></li>-->
              <li> | <a href="http://www.hathitrust.org/take_down_policy" title="item removal policy">Take-Down Policy</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div id="popupbox"> 
    </div>
      <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "popout_help.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      
      <script type="text/javascript">
      <?php echo '
      var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
      document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
      </script>
      <script type="text/javascript">
      try {
      var pageTracker = _gat._getTracker("UA-954893-23");
      pageTracker._setDomainName(".hathitrust.org");
      pageTracker._trackPageview();
      } catch(err) {} 
      '; ?>

      </script>
  </body>
</html>