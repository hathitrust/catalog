<?php /* Smarty version 2.6.21, created on 2012-03-21 11:54:50
         compiled from searchbox.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'translate', 'searchbox.tpl', 15, false),array('modifier', 'escape', 'searchbox.tpl', 39, false),)), $this); ?>
<div class="searchbox">
  <h3 class="SkipLink">Search Catalog</h3>
  <div class="yui-b" style="margin-left: 0em; *margin-left: 0em;">
  
    <?php if ($this->_tpl_vars['suppress_searchbox']): ?>
      <!-- 
        <div style="margin: none; padding: none;">            
          <div style="margin-left: 5em; padding-bottom: 1em; padding-top: 15px">
            <a href="/?&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
">&lt; Back to basic search</a>
          </div>
          <div>
            <span style="position: absolute; right: 2em;">             
                <?php if ($this->_tpl_vars['username']): ?>
                    <a href="<?php echo $this->_tpl_vars['path']; ?>
/MyResearch/Home" title="Account information for <?php echo $this->_tpl_vars['username']; ?>
">My Account</a> |
                    <a href="<?php echo $this->_tpl_vars['path']; ?>
/MyResearch/Logout"><?php echo translate(array('text' => 'Log Out'), $this);?>
</a></span>
                <?php else: ?>
                  <a href="<?php echo $this->_tpl_vars['path']; ?>
/MyResearch/Home" title="Log in and view your  account information">My Account</a> |
                  <a href="<?php echo $this->_tpl_vars['loginURL']; ?>
"><?php echo translate(array('text' => 'Login'), $this);?>
</a> 
                <?php endif; ?>
            </span>
          </div> 
        </div>-->

    <?php else: ?>  
    
    
      <form method="GET" id="searchForm" 
            action="<?php echo $this->_tpl_vars['path']; ?>
/Search/Home" name="searchForm" class="search" 
            onsubmit="trimForm(this.lookfor); return true;">
        <div id="searchGraphic">
          <img src="/images/hathi/SearchArrow_Cat.png" alt="Catalog Search">
        </div>
        <input type="hidden" name="checkspelling" value="true" />
       
        <div id="searchboxCont">

           <!-- Index selection -->
               <label for="lookfor" class="skipLink">Search Catalog</label>
               <input type="text" name="lookfor" id="lookfor" size="30" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['lookfor'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
">
               <label for="searchtype" class="skipLink">Select type of search</label>
               <select name="type" id="searchtype">
                 <option value="all"><?php echo translate(array('text' => 'All Fields'), $this);?>
</option>
                 <option value="title"<?php if ($this->_tpl_vars['type'] == 'title'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Title'), $this);?>
</option>
                 <option value="author"<?php if ($this->_tpl_vars['type'] == 'author'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Author'), $this);?>
</option>
                 <option value="subject"<?php if ($this->_tpl_vars['type'] == 'subject'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Subject'), $this);?>
</option>
                 <!--<option value="hlb"<?php if ($this->_tpl_vars['type'] == 'hlb'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Categories'), $this);?>
</option>-->
                 <!--<option value="callnumber"<?php if ($this->_tpl_vars['type'] == 'callnumber'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Call Number'), $this);?>
 / in progress</option>-->
                 <option value="isn"<?php if ($this->_tpl_vars['type'] == 'isn'): ?> selected<?php endif; ?>>ISBN/ISSN</option>
                 <option value="publisher" <?php if ($this->_tpl_vars['type4'] == 'publisher'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Publisher'), $this);?>
</option>
                 <option value="series" <?php if ($this->_tpl_vars['type4'] == 'series'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Series Title'), $this);?>
</option>
                 <option value="year" <?php if ($this->_tpl_vars['type4'] == 'year'): ?> selected<?php endif; ?>><?php echo translate(array('text' => 'Year of Publication'), $this);?>
</option>
                 <!-- <option value="tag"<?php if ($this->_tpl_vars['type'] == 'tag'): ?> selected<?php endif; ?>>Tag</option> -->
               </select>
               <input type="hidden" name="sethtftonly" value="true">
               <input type="checkbox" name="htftonly" value="true" id="fullonly" <?php if ($this->_tpl_vars['ht_fulltextonly']): ?>checked="checked"<?php endif; ?>/>&nbsp;<label for="fullonly">Full view only</label>
               <input  type="submit" name="submit" value="<?php echo translate(array('text' => 'Find'), $this);?>
">
                
        
          <!-- fixme:suz hidden until advanced search can work better --> 
            <!-- <a style="padding-right: 2.5em; position: relative; " href="<?php echo $this->_tpl_vars['path']; ?>
/Search/Advanced/<?php echo $this->_tpl_vars['inst']; ?>
" class="small"><?php echo translate(array('text' => 'Advanced'), $this);?>
</a>           -->
          
            <span id="searchLinks">
              <a href="<?php echo $this->_tpl_vars['path']; ?>
/Search/Advanced"><?php echo translate(array('text' => 'Advanced Catalog Search'), $this);?>
</a>
              <a href="#" id="searchTips">Search Tips</a>
            </span>
        
        <!-- 
            <span style="position: absolute; right: 2em;">             
            <?php if ($this->_tpl_vars['username']): ?>
                <a href="<?php echo $this->_tpl_vars['path']; ?>
/MyResearch/Home&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
" title="Account information for <?php echo $this->_tpl_vars['username']; ?>
">Your Account</a> |
                <a href="<?php echo $this->_tpl_vars['path']; ?>
/MyResearch/Logout"><?php echo translate(array('text' => 'Log Out'), $this);?>
</a></span>
             <?php else: ?>
                <a href="<?php echo $this->_tpl_vars['path']; ?>
/MyResearch/Home" title="Log in and view your  account information">My Account</a> |
                 <a href="<?php echo $this->_tpl_vars['loginURL']; ?>
&amp;inst=<?php echo $this->_tpl_vars['inst']; ?>
"><?php echo translate(array('text' => 'Login'), $this);?>
</a> 
             <?php endif; ?>
          </span>
      -->

        
        <!-- Login 
-->
       
       </div> <!-- End of the margin:none padding:none -->
      </form>
    <?php endif; ?>
  </div>
</div>