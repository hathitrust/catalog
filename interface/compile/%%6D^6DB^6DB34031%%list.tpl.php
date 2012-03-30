<?php /* Smarty version 2.6.21, created on 2012-03-21 11:54:50
         compiled from Search/list.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'translate', 'Search/list.tpl', 18, false),array('modifier', 'escape', 'Search/list.tpl', 34, false),)), $this); ?>
<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['path']; ?>
/services/Search/ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="<?php echo $this->_tpl_vars['path']; ?>
/js/googleLinks.js"></script>

<!-- Main Listing -->
<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">

      <!-- Narrow Options -->
      <?php if ($this->_tpl_vars['narrow']): ?>
      <div class="yui-g resulthead" style="border: solid 1px #999999; background-color: #FFFFEE;">
        <div class="yui-u first">
        <?php $_from = $this->_tpl_vars['narrow']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['narrowLoop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['narrowLoop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['narrowItem']):
        $this->_foreach['narrowLoop']['iteration']++;
?>
          <?php if ($this->_foreach['narrowLoop']['iteration'] == 6): ?>
            </div>
            <div class="yui-u">
          <?php endif; ?>
          <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?<?php echo $this->_tpl_vars['narrowItem']['authurl']; ?>
"><?php echo translate(array('text' => $this->_tpl_vars['narrowItem']['name']), $this);?>
</a> (<?php echo $this->_tpl_vars['narrowItem']['num']; ?>
)<br>
        <?php endforeach; endif; unset($_from); ?>
        </div>
	      <?php if ($this->_tpl_vars['narrowcount'] > $this->_foreach['narrowLoop']['iteration']): ?>
        <div style="clear:both; text-align: right;"> <a href="<?php echo $this->_tpl_vars['url']; ?>
/Author/Search?<?php echo $this->_tpl_vars['searchcomps']; ?>
">see all (<?php echo $this->_tpl_vars['narrowcount']; ?>
)</a></div>
      <?php endif; ?>
      </div>
      <?php endif; ?>
      <!-- End Narrow Options -->

      <!-- Spelling suggestion -->
      <?php if ($this->_tpl_vars['newPhrase']): ?>
      <p class="correction"><?php echo translate(array('text' => 'Did you mean'), $this);?>
 <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/<?php echo $this->_tpl_vars['action']; ?>
?lookfor=<?php echo $this->_tpl_vars['newPhrase']; ?>
&amp;type=<?php echo $this->_tpl_vars['type']; ?>
"><?php echo $this->_tpl_vars['newPhrase']; ?>
</a>?</p>
      <?php endif; ?>

      <div class="searchtools">
        <!-- <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/<?php echo $this->_tpl_vars['action']; ?>
?lookfor=<?php echo ((is_array($_tmp=$this->_tpl_vars['lookfor'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&amp;type=<?php echo $this->_tpl_vars['type']; ?>
&amp;view=rss" class="feed"><?php echo translate(array('text' => 'Get RSS Feed'), $this);?>
</a> -->

        <!-- fixme:suz RSS doesn't work so well 
        <a href="" id="RSSFeed"><?php echo translate(array('text' => 'Get RSS Feed'), $this);?>
</a>
        <script language="JavaScript" type="text/javascript">
          loc = window.location.href;
          loc.replace(/checkspelling=true/, '');
          loc = loc + '&view=rss';
          jq('#RSSFeed').attr('href', loc)
        </script>
        -->
        <!-- <a class="feed" href="/Search/SearchExport?<?php echo ((is_array($_tmp=$this->_tpl_vars['searchcomps'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
&amp;method=atom" id="Feed"><?php echo translate(array('text' => 'Get Feed'), $this);?>
</a> -->

        <a href="#" id="emailSearch" class="mail" onClick="pageTracker._trackEvent('resultsActions', 'click', 'Email this Search top');"><?php echo translate(array('text' => 'Email this Search'), $this);?>
</a>
      </div>

      <!-- Listing Options -->
      <div class="yui-ge resulthead">
        <div class="yui-u first">
        <?php if ($this->_tpl_vars['recordCount']): ?>
          <?php echo translate(array('text' => 'Showing'), $this);?>

          <span class="strong"><?php echo $this->_tpl_vars['recordStart']; ?>
 - <?php echo $this->_tpl_vars['recordEnd']; ?>
</span>
          <?php echo translate(array('text' => 'of'), $this);?>
 <span class="strong"><?php echo $this->_tpl_vars['recordCount']; ?>
</span>
          <?php echo translate(array('text' => 'Results for'), $this);?>
 <span class="strong"><?php echo $this->_tpl_vars['searchterms']; ?>
</span>
        <?php endif; ?>
        </div>

        <div class="yui-u toggle" style="width: auto">
          <label for="sortOption"><?php echo translate(array('text' => 'Sort'), $this);?>
</label>
          <select id="sortOption" name="sort" onChange="document.location.href='<?php echo $this->_tpl_vars['fullPath']; ?>
&amp;sort=' + this.options[this.selectedIndex].value;">
            <option value="">Relevance</option>
            <option value="year"<?php if ($this->_tpl_vars['sort'] == 'year'): ?> selected<?php endif; ?>>Date (newest first)</option>
            <option value="yearup"<?php if ($this->_tpl_vars['sort'] == 'yearup'): ?> selected<?php endif; ?>>Date (oldest first)</option>            
     <!--       <option value="author"<?php if ($this->_tpl_vars['sort'] == 'author'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Author'), $this);?>
</option>-->
            <option value="title"<?php if ($this->_tpl_vars['sort'] == 'title'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Title'), $this);?>
</option>
          </select>
        </div>
      

      </div>
      <!-- End Listing Options -->
      <?php $this->assign('pageLinks', $this->_tpl_vars['pager']->getLinks()); ?>
      <div class="pagination"><?php echo $this->_tpl_vars['pageLinks']['all']; ?>
</div>

      <?php if ($this->_tpl_vars['subpage']): ?>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['subpage'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      <?php else: ?>
        <?php echo $this->_tpl_vars['pageContent']; ?>

      <?php endif; ?>

      <!-- <?php $this->assign('pageLinks', $this->_tpl_vars['pager']->getLinks()); ?> -->
      <div class="pagination"><?php echo $this->_tpl_vars['pageLinks']['all']; ?>
</div>
      <div class="searchtools">
        <!-- <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/<?php echo $this->_tpl_vars['action']; ?>
?lookfor=<?php echo ((is_array($_tmp=$this->_tpl_vars['lookfor'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
&amp;type=<?php echo $this->_tpl_vars['type']; ?>
&amp;view=rss" class="feed"><?php echo translate(array('text' => 'Get RSS Feed'), $this);?>
</a> -->

        <!-- fixme:suz RSS doesn't work so well <a href="" id="RSSFeed"><?php echo translate(array('text' => 'Get RSS Feed'), $this);?>
</a>
        <script language="JavaScript" type="text/javascript">
          loc = window.location.href;
          loc.replace(/checkspelling=true/, '');
          loc = loc + '&view=rss';
          jq('#RSSFeed').attr('href', loc)
        </script>
      -->
        <a href="#" class="mail" id="emailSearch_lower" onClick="pageTracker._trackEvent('resultsActions', 'click', 'Email this Search bottom');"><?php echo translate(array('text' => 'Email this Search'), $this);?>
</a>
      </div>
    </div>
    <!-- End Main Listing -->
  </div>

  <!-- Narrow Search Options -->
  <div id="listleftcol" class="yui-b"><div class="box submenu narrow">
  <?php if ($this->_tpl_vars['currentFacets']): ?>
    <div id="applied_filters">
      <h3><?php echo translate(array('text' => 'Results refined by:'), $this);?>
</h3>
        <ul class="filters">
          <?php $_from = $this->_tpl_vars['currentFacets']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['facet']):
?>
            <li>
              <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/<?php echo $this->_tpl_vars['action']; ?>
?<?php echo $this->_tpl_vars['facet']['removalURL']; ?>
"><img  class="facetbutton" src="<?php echo $this->_tpl_vars['path']; ?>
/images/silk/cancel.png" alt="Delete"></a><?php echo $this->_tpl_vars['facet']['indexDisplay']; ?>
 : <?php echo translate(array('text' => $this->_tpl_vars['facet']['valueDisplay']), $this);?>
</li>
          <?php endforeach; endif; unset($_from); ?>
        </ul>        
    </div>
  <?php endif; ?>
      
      <div class="narrowList navmenu" id="narrowList">
      <h3><?php echo translate(array('text' => 'Refine Search'), $this);?>
</h3>
        <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Search/facet_snippet.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
      </div>

    </div>
  </div>
  <!-- End Narrow Search Options -->
</div> <!-- ??? -->
</div>