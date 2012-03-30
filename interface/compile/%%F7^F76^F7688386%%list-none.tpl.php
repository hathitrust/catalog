<?php /* Smarty version 2.6.21, created on 2012-03-29 15:22:41
         compiled from Search/list-none.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'translate', 'Search/list-none.tpl', 7, false),array('modifier', 'escape', 'Search/list-none.tpl', 7, false),)), $this); ?>
<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first contentbox">
      <div class="record">
        <!-- Suggestions? -->
        <?php if ($this->_tpl_vars['newPhrase']): ?>
        <p class="correction"><?php echo translate(array('text' => 'Did you mean'), $this);?>
 <a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/<?php echo $this->_tpl_vars['action']; ?>
?lookfor=<?php echo ((is_array($_tmp=$this->_tpl_vars['newPhrase'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'url') : smarty_modifier_escape($_tmp, 'url')); ?>
&amp;type=<?php echo $this->_tpl_vars['type']; ?>
<?php echo $this->_tpl_vars['filterListStr']; ?>
"><?php echo $this->_tpl_vars['newPhrase']; ?>
</a>?</p>
        <?php endif; ?>

        <p class="error">Your search - <b><?php echo $this->_tpl_vars['lookfor']; ?>
</b> - did not match any resources.</p>
    
        <p>You may want to try to revise your search phrase by removing some words.</p>
      </div>
    </div>
  </div>
</div>