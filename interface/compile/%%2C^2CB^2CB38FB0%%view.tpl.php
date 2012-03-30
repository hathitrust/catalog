<?php /* Smarty version 2.6.21, created on 2012-03-29 15:44:32
         compiled from Record/view.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'translate', 'Record/view.tpl', 18, false),array('modifier', 'lower', 'Record/view.tpl', 26, false),array('modifier', 'replace', 'Record/view.tpl', 26, false),array('modifier', 'getvalue', 'Record/view.tpl', 61, false),array('modifier', 'escape', 'Record/view.tpl', 116, false),array('modifier', 'regex_replace', 'Record/view.tpl', 483, false),)), $this); ?>
<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['path']; ?>
/services/Record/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['path']; ?>
/js/googleLinks.js"></script>

<!--<div  id="login_to_text" style="display:none;" >
  <h3>Send record via text message</h3>
  <p>Texting is only available to logged-in users. Please <a id="login_link" href="">log in</a>.
  </p>
<script language="JavaScript" type="text/javascript">
  jq('#login_link').attr('href', loginLink());
</script>  
</div>
-->


<div id="bd" style="width: 100%">
   <div id="start of left column container" class='yui-b' style="margin: 0px; padding: 0px; float: left; width: 17em;">
       <div class="box submenu">
          <h3><?php echo translate(array('text' => 'Similar Items'), $this);?>
</h3>
     <!-- <?php echo $this->_tpl_vars['similarRecords']; ?>
 -->
           <?php if (is_array ( $this->_tpl_vars['similarRecords'] )): ?>
           <ul class="similar">
             <?php $_from = $this->_tpl_vars['similarRecords']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['similar']):
?>
             <?php if (is_array ( $this->_tpl_vars['similar']['title'] )): ?><?php $this->assign('similarTitle', $this->_tpl_vars['similar']['title']['0']); ?>
             <?php else: ?><?php $this->assign('similarTitle', $this->_tpl_vars['similar']['title']); ?><?php endif; ?>  
             <li>
               <span class="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['similar']['format'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, ' ', "") : smarty_modifier_replace($_tmp, ' ', "")); ?>
">
               <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['similar']['id']; ?>
"><?php echo $this->_tpl_vars['similarTitle']; ?>
</a>
               </span>
               <span style="font-size: .8em">

               <?php if ($this->_tpl_vars['similar']['author']): ?><br>By: <?php echo $this->_tpl_vars['similar']['author']['0']; ?>
<?php endif; ?>
               <?php if ($this->_tpl_vars['similar']['publishDate']): ?><br>Published: (<?php echo $this->_tpl_vars['similar']['publishDate']['0']; ?>
)<?php endif; ?>
               </span>
             </li>
             <?php endforeach; endif; unset($_from); ?>
           </ul>
           <?php else: ?>
           Cannot find similar records
           <?php endif; ?>
         </div>
          
         <?php if (is_array ( $this->_tpl_vars['editions'] )): ?>
           <div class="box submenu">
             <h4><?php echo translate(array('text' => 'Other Editions'), $this);?>
</h4>
             <ul class="similar">
               <?php $_from = $this->_tpl_vars['editions']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['edition']):
?>
               <li>
                 <span class="<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['similar']['format'])) ? $this->_run_mod_handler('lower', true, $_tmp) : smarty_modifier_lower($_tmp)))) ? $this->_run_mod_handler('replace', true, $_tmp, ' ', "") : smarty_modifier_replace($_tmp, ' ', "")); ?>
">
                 <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['edition']['id']; ?>
"><?php echo $this->_tpl_vars['edition']['title']; ?>
</a>
                 </span>
                 <?php echo $this->_tpl_vars['edition']['edition']; ?>

                 <?php if ($this->_tpl_vars['edition']['publishDate']): ?>(<?php echo $this->_tpl_vars['edition']['publishDate']; ?>
)<?php endif; ?>
               </li>
               <?php endforeach; endif; unset($_from); ?>
             </ul>
          </div>

         <?php endif; ?>

         <?php $this->assign('marcField', $this->_tpl_vars['marc']->getField('245')); ?>
         <?php $this->assign('title', ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a'))); ?>
         <?php if (((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b'))): ?>
           <?php $this->assign('title_b', ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b'))); ?>
           <?php $this->assign('title', ($this->_tpl_vars['title'])." ".($this->_tpl_vars['title_b'])); ?>
         <?php endif; ?>
      
   </div> <!-- end of left column -->
   
   <div id="content" style="margin: 0px; padding: 0px; margin-left: 19em;">
     <div class="record">
       <?php if ($this->_tpl_vars['lastsearch']): ?>
        <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?<?php echo $this->_tpl_vars['lastsearch']; ?>
" class="backtosearch"><img style="vertical-align: middle;" alt="" src="/static/umichwebsite/images/return.png"><?php echo translate(array('text' => 'Back to Search Results'), $this);?>
</a><br>
       <?php endif; ?>
       
       <h3 class="SkipLink">Tools</h3>
       <ul class="ToolLinks">
              <li><a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Cite" class="cite" onClick="getLightbox('Record', 'Cite', '<?php echo $this->_tpl_vars['id']; ?>
', null, '<?php echo translate(array('text' => 'Cite this'), $this);?>
'); return false;"><?php echo translate(array('text' => 'Cite this'), $this);?>
</a></li>
         <li><a class="endnotelink" href="/Search/SearchExport?handpicked=<?php echo $this->_tpl_vars['id']; ?>
&amp;method=ris" onClick="pageTracker._trackEvent('recordActions', 'click', 'Endnote');">Export to Endnote</a></li>
       </ul>
       
       <div class="recordnav">
         <?php if ($this->_tpl_vars['previous']): ?>
         <a href="<?php echo $this->_tpl_vars['url']; ?>
<?php echo $this->_tpl_vars['previous']; ?>
" class="backtosearch"><img style="vertical-align: middle;" alt="" src="/static/umichwebsite/images/arrow_left.png"> <?php echo translate(array('text' => 'Previous record'), $this);?>
</a>
         <?php endif; ?>
         <?php if ($this->_tpl_vars['current']): ?>
         <?php echo translate(array('text' => ($this->_tpl_vars['current'])), $this);?>

         <?php endif; ?>
         <?php if ($this->_tpl_vars['next']): ?>
         <a href="<?php echo $this->_tpl_vars['url']; ?>
<?php echo $this->_tpl_vars['next']; ?>
" class="backtosearch"><?php echo translate(array('text' => 'Next record'), $this);?>
 <img style="vertical-align: middle;" alt="" src="/static/umichwebsite/images/arrow_right.png"></a>
         <?php endif; ?>
       </div> 
             

         
       <!--
       <div>
       <ul class="tools">
        <li>
          <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Cite" class="cite" onClick="getLightbox('Record', 'Cite', '<?php echo $this->_tpl_vars['id']; ?>
', null, '<?php echo translate(array('text' => 'Cite this'), $this);?>
'); return false;"><?php echo translate(array('text' => 'Cite this'), $this);?>
</a>
        </li>

         <li>
           <?php if ($this->_tpl_vars['username']): ?>
           <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/SMS" class="sms" onClick="getLightbox('Record', 'SMS', '<?php echo $this->_tpl_vars['id']; ?>
', null, '<?php echo translate(array('text' => 'Text this'), $this);?>
'); return false;"><?php echo translate(array('text' => 'Text this'), $this);?>
</a></li>
           <?php else: ?>
           <a href="#" class="sms" onClick="fillLightbox('login_to_text'); return false;"><?php echo translate(array('text' => 'Text this'), $this);?>
</a>
           <?php endif; ?>
         </li>
                   
        -->

          <!--<li><a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Email" class="mail" onClick="getLightbox('Record', 'Email', '<?php echo $this->_tpl_vars['id']; ?>
', null, '<?php echo translate(array('text' => 'Email this'), $this);?>
'); return false;"><?php echo translate(array('text' => 'Email this'), $this);?>
</a></li>-->
          <!-- <li><a href="#" class="mail" onClick="fillLightbox('email_help'); return false;;return false;"><?php echo translate(array('text' => 'Email this'), $this);?>
</a></li>              -->

          <!--<li><a target="RefWorksMain" href="http://www.refworks.com.proxy.lib.umich.edu/express/expressimport.asp?vendor=Univeristy+of+Michigan+Mirlyn2+Beta&amp;filter=MARC+Format&amp;database=All+MARC+Formats&amp;encoding=65001&amp;url=<?php echo ((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Export%3Fstyle%3DREF">Export to Refworks</a></li>-->
          <!--<li><a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Export?style=endnote" class="export" onClick="showMenu('exportMenu'); return false;"><?php echo translate(array('text' => 'Import Record'), $this);?>
</a>
           </li>-->
          <!-- <li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('export_help');return false;">Export Record</a></li>                      -->
          <!--<ul class="menu" id="exportMenu">-->
          <!-- <li><a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Export?style=refworks"><?php echo translate(array('text' => 'Import to'), $this);?>
 RefWorks</a></li> -->
          <!-- <li><a onclick="hideMenu('exportMenu');return false;" href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Export?style=endnote"><?php echo translate(array('text' => 'Import to'), $this);?>
 EndNote</a></li> -->
          <!-- <li><a onclick="hideMenu('exportMenu');return false;" href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Export?style=zotero"><?php echo translate(array('text' => 'Import to'), $this);?>
 Zotero</a></li> -->
          <!--<li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('refworks_help');return false;"><?php echo translate(array('text' => 'Import to'), $this);?>
 RefWorks</a></li>
          <li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('endnote_help');return false;"><?php echo translate(array('text' => 'Import to'), $this);?>
 Endnote</a></li>-->
          <!-- <li><a href="#" onclick="hideMenu('exportMenu'); fillLightbox('zotero_help');return false;"><?php echo translate(array('text' => 'Import to'), $this);?>
 Zotero</a></li> -->
          <!--</ul>-->
          <!--</li>-->
          <!--<li id="saveLink"><a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Save" class="fav" onClick="getLightbox('Record', 'Save', '<?php echo $this->_tpl_vars['id']; ?>
', null, '<?php echo translate(array('text' => 'Add to Favorites'), $this);?>
'); return false;"><?php echo translate(array('text' => 'Add to favorites'), $this);?>
</a></li> 
           <li id="savelink"><a href="#" onClick="fillLightbox('favorite_help'); return false;;return false;" class="fav"><?php echo translate(array('text' => 'Add to favorites'), $this);?>
</a></li>
            <script language="JavaScript" type="text/javascript">
            getSaveStatus('<?php echo $this->_tpl_vars['id']; ?>
', 'saveLink');
            </script>
        -->
       <!--</ul>
      </div>-->

     <br>

     <?php if ($this->_tpl_vars['error']): ?><p class="error"><?php echo $this->_tpl_vars['error']; ?>
</p><?php endif; ?>

     <!-- Display Title -->
       <div id="title_collection">
         <!-- Display Book Cover -->
         <div id=GoogleCover_<?php echo $this->_tpl_vars['id']; ?>
 style="display:none; margin: 10px; position: relative; float: left; border: 2px solid #ccc">
         </div>
         
         <!-- End Book Cover -->
         <div style="margin-left: 70px">
         <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('245')); ?>
         <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
           <h2>
           <?php $_from = $this->_tpl_vars['field']->getSubfields(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['subloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['subloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['subcode'] => $this->_tpl_vars['subfield']):
        $this->_foreach['subloop']['iteration']++;
?>
             <?php if ($this->_tpl_vars['subcode'] >= 'a' && $this->_tpl_vars['subcode'] <= 'z'): ?>
             <?php echo $this->_tpl_vars['subfield']->getData(); ?>

             <?php endif; ?>
           <?php endforeach; endif; unset($_from); ?>
           </h2> 
         <?php endforeach; endif; unset($_from); ?>
         </div>
       </div>
     <!-- End Title -->
                           
<table summary="This table displays bibliographic information about this specific book or series" class="citation" style="margin: 0px; margin-top: 2em; padding: 0px; *width=auto">
  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('785')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'New Title'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 's') : smarty_modifier_getvalue($_tmp, 's')); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 't') : smarty_modifier_getvalue($_tmp, 't')); ?>
%22&type=title&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 's') : smarty_modifier_getvalue($_tmp, 's')); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 't') : smarty_modifier_getvalue($_tmp, 't')); ?>
</a><br>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('780')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Previous Title'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 's') : smarty_modifier_getvalue($_tmp, 's')); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 't') : smarty_modifier_getvalue($_tmp, 't')); ?>
%22&type=title&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 's') : smarty_modifier_getvalue($_tmp, 's')); ?>
<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 't') : smarty_modifier_getvalue($_tmp, 't')); ?>
</a><br>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>



  
  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getField('100')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Main Author'), $this);?>
: </th>
    <td><a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
<?php if (((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>
<?php endif; ?><?php if (((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c')); ?>
<?php endif; ?><?php if (((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'd') : smarty_modifier_getvalue($_tmp, 'd'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'd') : smarty_modifier_getvalue($_tmp, 'd')); ?>
<?php endif; ?>%22&amp;type=author&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
<?php if (((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>
<?php endif; ?><?php if (((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c')); ?>
<?php endif; ?><?php if (((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'd') : smarty_modifier_getvalue($_tmp, 'd'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'd') : smarty_modifier_getvalue($_tmp, 'd')); ?>
<?php endif; ?></a></td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getField('110')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Corporate Author'), $this);?>
: </th>
    <td><a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')))) ? $this->_run_mod_handler('escape', true, $_tmp, 'uri') : smarty_modifier_escape($_tmp, 'uri')); ?>
%22&amp;type=author&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['marcField'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
</a></td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('700')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Other Authors'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
<?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>
<?php endif; ?><?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c')); ?>
<?php endif; ?><?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'd') : smarty_modifier_getvalue($_tmp, 'd'))): ?> <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'd') : smarty_modifier_getvalue($_tmp, 'd')); ?>
<?php endif; ?>%22&amp;type=author&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'd') : smarty_modifier_getvalue($_tmp, 'd')); ?>
</a><?php if (! ($this->_foreach['loop']['iteration'] == $this->_foreach['loop']['total'])): ?>, <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <!-- <tr valign="top">
    <th><?php echo translate(array('text' => 'Format'), $this);?>
: </th>
    <td><span class="<?php echo $this->_tpl_vars['recordFormat']; ?>
"><?php echo $this->_tpl_vars['recordFormat']; ?>
</span></td>
  </tr> -->
  <?php $this->assign('lang', $this->_tpl_vars['recordLanguage']); ?>
  <?php if ($this->_tpl_vars['recordLanguage']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Language(s)'), $this);?>
: </th>
    <td>
    <?php $_from = $this->_tpl_vars['lang']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
    <?php if (($this->_foreach['loop']['iteration'] <= 1)): ?><?php echo $this->_tpl_vars['field']; ?>
<?php else: ?>; <?php echo $this->_tpl_vars['field']; ?>
<?php endif; ?> 
    <?php endforeach; endif; unset($_from); ?>
   </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('260')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Published'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'c') : smarty_modifier_getvalue($_tmp, 'c')); ?>
<br>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('250')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Edition'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
<br>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('440')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Series'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')))) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
%22&amp;type=series&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
</a><br>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>



  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('600|610|630|650|651|655',1)); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Subjects'), $this);?>
: </th>
    <td>
        <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
          <?php $this->assign('subject', ""); ?>
          <?php $_from = $this->_tpl_vars['field']->getSubfields(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['subloop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['subloop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['subcode'] => $this->_tpl_vars['subfield']):
        $this->_foreach['subloop']['iteration']++;
?>
           <?php if ($this->_tpl_vars['subcode'] >= 'a'): ?>
            <?php if ($this->_tpl_vars['subject']): ?> &gt; <?php endif; ?>
            <?php $this->assign('subfield', $this->_tpl_vars['subfield']->getData()); ?>
            <?php $this->assign('subject', ($this->_tpl_vars['subject'])." ".($this->_tpl_vars['subfield'])); ?>
            <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=$this->_tpl_vars['subject'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
%22&amp;type=subject&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo $this->_tpl_vars['subfield']; ?>
</a>
           <?php endif; ?>
          <?php endforeach; endif; unset($_from); ?>
          <br>
        <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('500|501|521|525|526|530|547|550|552|561|565|584|585',true)); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Note'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
<br>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('300')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Physical Description'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
        <?php $_from = $this->_tpl_vars['field']->getSubfields(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['subcode'] => $this->_tpl_vars['subfield']):
?>
          <?php if ($this->_tpl_vars['subcode'] != '6'): ?>
            <?php echo $this->_tpl_vars['subfield']->getData(); ?>

          <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
        <br>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('970')); ?>
  <?php if ($this->_tpl_vars['marcField']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'Original Format'), $this);?>
: </th>
    <td>
      <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
       <?php if ($this->_foreach['loop']['iteration'] == 1): ?>
       <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')) != 'Electronic Resource'): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>
<?php endif; ?>
       <?php elseif ($this->_foreach['loop']['iteration'] > 1): ?>
       <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')) != 'Electronic Resource'): ?><br><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>
<?php endif; ?>
       <?php endif; ?>
      <?php endforeach; endif; unset($_from); ?>
    </td>
  </tr>
  <?php endif; ?>

  <?php $this->assign('852Field', $this->_tpl_vars['marc']->getFields('852')); ?>
  <?php if (isset ( $this->_tpl_vars['852Field'] )): ?>
    <?php $_from = $this->_tpl_vars['852Field']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
      <?php if ($this->_foreach['loop']['iteration'] < 2): ?>
        <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')) == 'MiU'): ?>
          <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'h') : smarty_modifier_getvalue($_tmp, 'h'))): ?>
            <tr valign="top">
            <th><?php echo translate(array('text' => 'Original Classification Number'), $this);?>
: </th>
            <td>
            <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'h') : smarty_modifier_getvalue($_tmp, 'h')); ?>

            </td>
            </tr>
          <?php endif; ?>
      <?php else: ?>
        <?php $this->assign('050Field', $this->_tpl_vars['marc']->getFields('050')); ?>
        <?php $_from = $this->_tpl_vars['050Field']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
          <?php if ($this->_foreach['loop']['iteration'] < 2): ?>
            <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a'))): ?>
              <tr valign="top">
              <th><?php echo translate(array('text' => 'Original Classification Number'), $this);?>
: </th>
              <td>
              <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
 <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'b') : smarty_modifier_getvalue($_tmp, 'b')); ?>

              </td>
              </tr>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
      <?php endif; ?>
      <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
  <?php endif; ?>

  <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('020')); ?>
   <?php if ($this->_tpl_vars['marcField']): ?>
   <tr valign="top">
     <th><?php echo translate(array('text' => 'ISBN'), $this);?>
: </th>
     <td>
       <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
         <?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'a') : smarty_modifier_getvalue($_tmp, 'a')); ?>
<br>
       <?php endforeach; endif; unset($_from); ?>
     </td>
   </tr>
   <?php endif; ?>

  <tr valign="top">
    <th><?php echo translate(array('text' => 'Locate a Print Version'), $this);?>
: </th>
    <td>
          <?php if (is_array ( $this->_tpl_vars['record']['oclc'] )): ?>
<!-- title array -->
            <?php $_from = $this->_tpl_vars['record']['oclc']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['title']):
        $this->_foreach['loop']['iteration']++;
?>
              <?php if ($this->_foreach['loop']['iteration'] < 3): ?>
              <a href="http://www.worldcat.org/oclc/<?php echo $this->_tpl_vars['title']; ?>
" onClick="pageTracker._trackEvent('outLinks', 'click', 'Find in a Library');">Find in a library</a><br>
              <?php endif; ?>
            <?php endforeach; endif; unset($_from); ?>
          <?php else: ?>
<!-- title non-array -->
            <?php if ($this->_tpl_vars['record']['oclc']): ?>
             <a href="http://www.worldcat.org/oclc/<?php echo $this->_tpl_vars['record']['oclc']; ?>
" onClick="pageTracker._trackEvent('outLinks', 'click', 'Find in a Library');">Find in a library</a>
            <?php else: ?> Find in a library service is not available from this catalog. <a href="http://www.worldcat.org/" onClick="pageTracker._trackEvent('outLinks', 'click', 'Search Worldcat');" target="_blank">Search Worldcat</a>
            <?php endif; ?>
          <?php endif; ?>
    </td>
  </tr>


  <!-- url to record in legacy system -->
<!-- commented by jjyork 3/31/09  <?php if ($this->_tpl_vars['recordURL']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'URL'), $this);?>
: </th>
    <td>
      <a href="<?php echo $this->_tpl_vars['recordURL']; ?>
<?php echo $this->_tpl_vars['id']; ?>
" target="Mirlyn">Display record in Mirlyn</a> 
    </td>
  </tr>
  <?php endif; ?> 
-->
  <!-- url to xserver holdings -->
<!-- commented by jjyork 3/31/09  <?php if ($this->_tpl_vars['holdingsURL']): ?>
  <tr valign="top">
    <th><?php echo translate(array('text' => 'xserver holdings'), $this);?>
: </th>
    <td>
      <a href="<?php echo $this->_tpl_vars['holdingsURL']; ?>
<?php echo $this->_tpl_vars['id']; ?>
" target="Mirlyn">Display holdings from Mirlyn xserver</a>       
    </td>
  </tr>
  <?php endif; ?>
--> 
</table> 

<!-- Availability set apart from table-->

<div id="accessLinks">
  <h3><?php echo translate(array('text' => 'Viewability'), $this);?>
: </h3>
  <ul>
    
  <?php if ($this->_tpl_vars['mergedItems']): ?>
    <?php $_from = $this->_tpl_vars['mergedItems']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
      <li><a href="<?php echo $this->_tpl_vars['item']['itemURL']; ?>
"><?php echo $this->_tpl_vars['item']['usRightsString']; ?>
 <?php if ($this->_tpl_vars['item']['enumcron']): ?><span class="IndItem"><?php echo $this->_tpl_vars['item']['enumcron']; ?>
</span><?php endif; ?></a>
        <em>(original from <?php echo $this->_tpl_vars['item']['orig']; ?>
)</em>
    <?php endforeach; endif; unset($_from); ?>
  <?php else: ?>
    <?php $this->assign('marcField', $this->_tpl_vars['marc']->getFields('974')); ?>
    <?php if ($this->_tpl_vars['marcField']): ?>

        <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
            <?php $this->assign('url', $this->_tpl_vars['field']->getSubfield('u')); ?>
            <?php $this->assign('url', $this->_tpl_vars['url']->getData()); ?>
           <!-- <?php $this->assign('nmspace', ((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/\.\d+/", "") : smarty_modifier_regex_replace($_tmp, "/\.\d+/", ""))); ?> -->
            <?php $this->assign('nmspace', ((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/\..*/", "") : smarty_modifier_regex_replace($_tmp, "/\..*/", ""))); ?>
            <li><a href="http://hdl.handle.net/2027/<?php echo $this->_tpl_vars['url']; ?>
" 
              <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'pd'): ?>
                class="fulltext">Full view
              <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'pdus' && $this->_tpl_vars['session']->get('inUSA')): ?>
                class="fulltext">Full view
              <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'world'): ?>class="fulltext">Full view
              <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'ic-world'): ?>class="fulltext">Full view
              <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'und-world'): ?>class="fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by'): ?>class="fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nd'): ?>class="fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc-nd'): ?>class="fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc'): ?>class="fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc-sa'): ?>class="fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-sa'): ?>class="fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-zero'): ?>class="fulltext">Full view
              <?php else: ?>class="searchonly">Limited (search-only)
          <?php endif; ?>
        
         <span class="IndItem"><?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'z') : smarty_modifier_getvalue($_tmp, 'z'))): ?><?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'z') : smarty_modifier_getvalue($_tmp, 'z')); ?>
<?php else: ?><?php endif; ?></span></a> 
         <em>
         <?php if ($this->_tpl_vars['nmspace'] == 'mdp'): ?> (original from University of Michigan) 
           <?php elseif ($this->_tpl_vars['nmspace'] == 'miua'): ?> (original from University of Michigan)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'miun'): ?> (original from University of Michigan)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'wu'): ?> (original from University of Wisconsin)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'inu'): ?> (original from Indiana University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'uc1'): ?> (original from University of California)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'uc2'): ?> (original from University of California)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'pst'): ?> (original from Penn State University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'umn'): ?> (original from University of Minnesota)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'nnc1'): ?> (original from Columbia University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'nnc2'): ?> (original from Columbia University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'nyp'): ?> (original from New York Public Library)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'uiuo'): ?> (original from University of Illinois)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'njp'): ?> (original from Princeton University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'yale'): ?> (original from Yale University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'chi'): ?> (original from University of Chicago)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'coo'): ?> (original from Cornell University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'ucm'): ?> (original from Universidad Complutense de Madrid)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'loc'): ?> (original from Library of Congress)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'ien'): ?> (original from Northwestern University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'hvd'): ?> (original from Harvard University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'uva'): ?> (original from University of Virginia)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'dul1'): ?> (original from Duke University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'ncs1'): ?> (original from North Carolina State University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'nc01'): ?> (original from University of North Carolina)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'pur1'): ?> (original from Purdue University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'pur2'): ?> (original from Purdue University)
           <?php elseif ($this->_tpl_vars['nmspace'] == 'usu'): ?> (original from Utah State University)
	   <?php elseif ($this->_tpl_vars['nmspace'] == 'mdl'): ?> (original from Minnesota Digital Library)
	   <?php elseif ($this->_tpl_vars['nmspace'] == 'gri'): ?> (original from Getty Research Institute)
	   <?php elseif ($this->_tpl_vars['nmspace'] == 'uiug'): ?> (original from University of Illinois)
           <?php else: ?>
         <?php endif; ?></li>
         </em>
        <?php endforeach; endif; unset($_from); ?>
    <?php endif; ?>   <?php endif; ?>   </ul>
</div>



          <!-- Display Tab Navigation -->
<!--          <div id="tabnav">
            <ul>
              <li<?php if ($this->_tpl_vars['tab'] == 'Holdings' || $this->_tpl_vars['tab'] == 'Home'): ?> class="active"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Holdings" class="first"><span></span><?php echo translate(array('text' => 'Holdings'), $this);?>
</a>
              </li>
              <li<?php if ($this->_tpl_vars['tab'] == 'Description'): ?> class="active"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Description" class="first"><span></span><?php echo translate(array('text' => 'Description'), $this);?>
</a>
              </li>
              <?php if ($this->_tpl_vars['marc']->getFields('505')): ?>
              <li<?php if ($this->_tpl_vars['tab'] == 'TOC'): ?> class="active"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/TOC" class="first"><span></span><?php echo translate(array('text' => 'Table of Contents'), $this);?>
</a>
              </li>
              <?php endif; ?>
-->
<!--              <li<?php if ($this->_tpl_vars['tab'] == 'UserComments'): ?> class="active"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/UserComments" class="first"><span></span><?php echo translate(array('text' => 'Comments'), $this);?>
</a>
              </li>
-->
              <!-- <?php if ($this->_tpl_vars['hasReviews']): ?>
              <li<?php if ($this->_tpl_vars['tab'] == 'Reviews'): ?> class="active"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Reviews" class="first"><span></span><?php echo translate(array('text' => 'Reviews'), $this);?>
</a>
              </li>
              <?php endif; ?> -->
<!--              <?php if ($this->_tpl_vars['hasExcerpt']): ?>
              <li<?php if ($this->_tpl_vars['tab'] == 'Excerpt'): ?> class="active"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Excerpt" class="first"><span></span><?php echo translate(array('text' => 'Excerpt'), $this);?>
</a>
              </li>
              <?php endif; ?>
              <li<?php if ($this->_tpl_vars['tab'] == 'Details'): ?> class="active"<?php endif; ?>>
                <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['id']; ?>
/Details" class="first"><span></span><?php echo translate(array('text' => 'MARC View'), $this);?>
</a>
              </li>
            </ul>
          </div>
-->
 <!-- End id=tabnav -->
          

 
             </div> <!-- end record -->
   </div> <!-- end of content -->

</div>
<script>
 <?php if ($this->_tpl_vars['googleLinks']): ?>
    getGoogleBookInfo('<?php echo $this->_tpl_vars['googleLinks']; ?>
', '<?php echo $this->_tpl_vars['id']; ?>
');
  <?php endif; ?>
</script>

