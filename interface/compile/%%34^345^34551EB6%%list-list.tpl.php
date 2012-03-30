<?php /* Smarty version 2.6.21, created on 2012-03-21 11:54:50
         compiled from Search/list-list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'truncate', 'Search/list-list.tpl', 27, false),array('modifier', 'highlight', 'Search/list-list.tpl', 27, false),array('modifier', 'default', 'Search/list-list.tpl', 27, false),array('modifier', 'escape', 'Search/list-list.tpl', 47, false),array('modifier', 'regex_replace', 'Search/list-list.tpl', 85, false),array('modifier', 'getvalue', 'Search/list-list.tpl', 93, false),array('function', 'translate', 'Search/list-list.tpl', 44, false),)), $this); ?>
<form name="addForm">
<?php $_from = $this->_tpl_vars['recordSet']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['recordLoop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['recordLoop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['record']):
        $this->_foreach['recordLoop']['iteration']++;
?>
  <?php if (( $this->_foreach['recordLoop']['iteration'] % 2 ) == 0): ?>
  <div class="result alt record<?php echo $this->_foreach['recordLoop']['iteration']; ?>
">
  <?php else: ?>
  <div class="result record<?php echo $this->_foreach['recordLoop']['iteration']; ?>
">
  <?php endif; ?>
  
<!--
  <script type="text/javascript">
     getStatuses('<?php echo $this->_tpl_vars['record']['id']; ?>
');
  </script>
-->
    <div class="yui-ge">
      <div class="yui-u first">
      <div id=GoogleCover_<?php echo $this->_tpl_vars['record']['id']; ?>
 style="display:none;position: relative; float: left; border: 2px solid #ccc">
      </div>

        <div class="resultitem">
          <div id="resultItemLine1" class="results_title">
            <?php if ($this->_tpl_vars['showscores']): ?>
            (<span class="score"><?php echo $this->_tpl_vars['record']['score']; ?>
</span>)
            <?php endif; ?>
          <?php if (is_array ( $this->_tpl_vars['record']['title'] )): ?>
<!-- title array -->
            <?php $_from = $this->_tpl_vars['record']['title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['title']):
?>
            <!-- <a href="/Record/<?php echo $this->_tpl_vars['record']['id']; ?>
" class="title"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 180, "...") : smarty_modifier_truncate($_tmp, 180, "...")))) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])))) ? $this->_run_mod_handler('default', true, $_tmp, 'Title not available') : smarty_modifier_default($_tmp, 'Title not available')); ?>
</a><br> -->
            <span class="title"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 180, "...") : smarty_modifier_truncate($_tmp, 180, "...")))) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])))) ? $this->_run_mod_handler('default', true, $_tmp, 'Title not available') : smarty_modifier_default($_tmp, 'Title not available')); ?>
</span><br>            <?php endforeach; endif; unset($_from); ?>
          <?php else: ?>
<!-- title non-array -->
          <!-- <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['record']['id']; ?>
" class="title"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['record']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 180, "...") : smarty_modifier_truncate($_tmp, 180, "...")))) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])))) ? $this->_run_mod_handler('default', true, $_tmp, 'Title not aavailable') : smarty_modifier_default($_tmp, 'Title not aavailable')); ?>
</a> -->
          <span class="title"><?php echo ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['record']['title'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 180, "...") : smarty_modifier_truncate($_tmp, 180, "...")))) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])))) ? $this->_run_mod_handler('default', true, $_tmp, 'Title not aavailable') : smarty_modifier_default($_tmp, 'Title not aavailable')); ?>
</span>
          
          <?php endif; ?>
          <?php if ($this->_tpl_vars['record']['title2']): ?>
          <br>
          <span class="results_title2"><?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['record']['title2'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 180, "...") : smarty_modifier_truncate($_tmp, 180, "...")))) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])); ?>
</span>
          <?php endif; ?>
          
          </div>
  
          <div id="resultItemLine2" class="results_author">
          <?php if ($this->_tpl_vars['record']['author']): ?>
          <?php echo translate(array('text' => 'by'), $this);?>

          <?php if (is_array ( $this->_tpl_vars['record']['author'] )): ?>
            <?php $_from = $this->_tpl_vars['record']['author']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['author']):
?>
           <!-- <a href="/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=$this->_tpl_vars['author'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'uri') : smarty_modifier_escape($_tmp, 'uri')); ?>
%22&amp;type=author&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['author'])) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])); ?>
</a> -->
           <?php echo ((is_array($_tmp=$this->_tpl_vars['author'])) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])); ?>

            <?php endforeach; endif; unset($_from); ?>
          <?php else: ?>
          <!-- <a href="/Search/Home?lookfor=%22<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['author'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'uri') : smarty_modifier_escape($_tmp, 'uri')); ?>
%22&amp;type=author&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['record']['author'])) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])); ?>
</a> -->
          <?php echo ((is_array($_tmp=$this->_tpl_vars['record']['author'])) ? $this->_run_mod_handler('highlight', true, $_tmp, $this->_tpl_vars['lookfor']) : smarty_modifier_highlight($_tmp, $this->_tpl_vars['lookfor'])); ?>

          
          <?php endif; ?>
          <?php endif; ?>
          </div>
    
          <div id="resultItemLine3" class="results_published">
          <?php if ($this->_tpl_vars['record']['publishDate']): ?><?php echo translate(array('text' => 'Published'), $this);?>
 <?php echo $this->_tpl_vars['record']['publishDate']['0']; ?>
<?php endif; ?>
          </div>
          
          
          <div>
            
          </div>
          
          <!-- Viewability Link -->
          
          <div class="AccessLink">
            <ul>
              <li>
                <a href="/Record/<?php echo $this->_tpl_vars['record']['id']; ?>
" class="cataloglinkhref">Catalog Record</a>
              </li>
              
              <li>
               <?php $this->assign('marcField', $this->_tpl_vars['record']['marc']->getFields('974')); ?>
                <?php if ($this->_tpl_vars['marcField']): ?>

                    <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['myLoop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['myLoop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['myLoop']['iteration']++;
?>
                    <?php endforeach; endif; unset($_from); ?>
                    <?php $this->assign('count', 0); ?>
                    <?php $_from = $this->_tpl_vars['marcField']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['loop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['loop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['field']):
        $this->_foreach['loop']['iteration']++;
?>
                        <?php $this->assign('url', $this->_tpl_vars['field']->getSubfield('u')); ?>
                        <?php $this->assign('url', $this->_tpl_vars['url']->getData()); ?>
                        <?php $this->assign('nmspace', ((is_array($_tmp=$this->_tpl_vars['url'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/\.\d+/", "") : smarty_modifier_regex_replace($_tmp, "/\.\d+/", ""))); ?>

                    <?php if (($this->_foreach['myLoop']['iteration']-1) > 0): ?>
                        <!-- <a href="/Record/<?php echo $this->_tpl_vars['record']['id']; ?>
" class="multivolLink">Multiple volumes</a> -->
                        <span class="multivolLink">(view record to see multiple volumes)</span>
                    
                    <?php break; ?> 
                    <?php else: ?>
<a href="http://hdl.handle.net/2027/<?php echo $this->_tpl_vars['url']; ?>
" class="rights-<?php echo ((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')); ?>
 
  <?php if ($this->_tpl_vars['session']->get('inUSA')): ?>
    <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'pd'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'pdus'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nd'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc-nd'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc-sa'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-sa'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'world'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'ic-world'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'und-world'): ?>fulltext">Full view
      <?php else: ?>searchonly">Limited (search-only)
    <?php endif; ?>
  <?php else: ?>
    <?php if (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'pd'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'pdus'): ?>searchonly">Limited (search-only)
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nd'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc-nd'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-nc-sa'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'cc-by-sa'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'world'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'ic-world'): ?>fulltext">Full view
      <?php elseif (((is_array($_tmp=$this->_tpl_vars['field'])) ? $this->_run_mod_handler('getvalue', true, $_tmp, 'r') : smarty_modifier_getvalue($_tmp, 'r')) == 'und-world'): ?>fulltext">Full view

      <?php else: ?>searchonly">Limited (search-only)
    <?php endif; ?>
  <?php endif; ?>
  
</a>

                    <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?>
                <?php endif; ?>
              </li>
            </ul>
            
            
  
          </div>              


       </div>
      </div>
  
  
      <!--<div class="yui-u">
        <div id="saveLink<?php echo $this->_tpl_vars['record']['id']; ?>
"> -->
<!--          <a href="<?php echo $this->_tpl_vars['url']; ?>
/Record/<?php echo $this->_tpl_vars['record']['id']; ?>
/Save" onClick="getLightbox('Record', 'Save', '<?php echo $this->_tpl_vars['record']['id']; ?>
', null, '<?php echo translate(array('text' => 'Add to Favorites'), $this);?>
'); return false;" class="fav tool"><?php echo translate(array('text' => 'Add to favorites'), $this);?>
</a> -->
       <!--  <a href="#" onClick="fillLightbox('favorite_help'); return false;;return false;" class="fav tool"><?php echo translate(array('text' => 'Add to favorites'), $this);?>
</a>

        </div>
        <?php if ($this->_tpl_vars['user']): ?>
        <script language="JavaScript" type="text/javascript">
          getSaveStatuses('<?php echo $this->_tpl_vars['record']['id']; ?>
');
        </script>
        <?php endif; ?>
      </div>
    -->
    
    </div>

<!-- 
          <?php if ($this->_tpl_vars['record']['format'] == 'Book'): ?>
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Abook&amp;rfr_id=info%3Asid%2F<?php echo $this->_tpl_vars['coinsID']; ?>
%3Agenerator&amp;rft.genre=book&amp;rft.btitle=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.title=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.series=<?php echo $this->_tpl_vars['record']['series']; ?>
&amp;rft.au=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['author'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.date=<?php echo $this->_tpl_vars['record']['publishDate']; ?>
&amp;rft.pub=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['publisher'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.edition=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['edition'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.isbn=<?php echo $this->_tpl_vars['record']['isbn']; ?>
">
          <?php elseif ($this->_tpl_vars['record']['format'] == 'Journal'): ?>
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal&amp;rfr_id=info%3Asid%2F<?php echo $this->_tpl_vars['coinsID']; ?>
%3Agenerator&amp;rft.genre=article&amp;rft.title=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.date=<?php echo $this->_tpl_vars['record']['publishDate']; ?>
&amp;rft.issn=<?php echo $this->_tpl_vars['record']['issn']; ?>
">
          <?php else: ?>
    <span class="Z3988"
          title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc&amp;rfr_id=info%3Asid%2F<?php echo $this->_tpl_vars['coinsID']; ?>
%3Agenerator&amp;rft.title=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['title'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.creator=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['author'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.date=<?php echo $this->_tpl_vars['record']['publishDate']; ?>
&amp;rft.pub=<?php echo ((is_array($_tmp=$this->_tpl_vars['record']['publisher'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;rft.format=<?php echo $this->_tpl_vars['record']['format']; ?>
">
          <?php endif; ?>
-->

  </div>

<!--   <?php if (! $this->_tpl_vars['record']['url']): ?> 
  <script type="text/javascript">
     getStatuses('<?php echo $this->_tpl_vars['record']['id']; ?>
');
  </script>
  <?php endif; ?> -->
  <script type="text/javascript">
   <?php if ($this->_tpl_vars['record']['googleLinks']): ?>
<?php echo '      jq(document).ready(function() { '; ?>

        getGoogleBookInfo('<?php echo $this->_tpl_vars['record']['googleLinks']; ?>
', '<?php echo $this->_tpl_vars['record']['id']; ?>
')
<?php echo '        }); '; ?>

    <?php endif; ?>
  </script>


<?php endforeach; endif; unset($_from); ?>
</form>
