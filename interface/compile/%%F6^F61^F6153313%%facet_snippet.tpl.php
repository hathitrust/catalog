<?php /* Smarty version 2.6.21, created on 2012-03-21 11:54:51
         compiled from Search/facet_snippet.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'translate', 'Search/facet_snippet.tpl', 10, false),)), $this); ?>
<?php $_from = $this->_tpl_vars['indexes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['cluster']):
?>
<dl class="narrowList navmenu narrow_begin" id="facet_begin_<?php echo $this->_tpl_vars['clusterName']; ?>
">
    <dt><?php echo $this->_tpl_vars['facetConfig'][$this->_tpl_vars['cluster']]; ?>
</dt>
    <?php $_from = $this->_tpl_vars['counts'][$this->_tpl_vars['cluster']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }$this->_foreach['facetLoop'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['facetLoop']['total'] > 0):
    foreach ($_from as $this->_tpl_vars['facet']):
        $this->_foreach['facetLoop']['iteration']++;
?>
      <?php if ($this->_foreach['facetLoop']['iteration'] == 6): ?>
        <dd id="more_<?php echo $this->_tpl_vars['cluster']; ?>
"><a href="" onclick="showThese('<?php echo $this->_tpl_vars['cluster']; ?>
'); return false;"><i>more...</i></a></dd>
        </dl>
        <dl class="narrowList navmenu narrow_end" id="facet_end_<?php echo $this->_tpl_vars['cluster']; ?>
">
      <?php endif; ?>
      <dd><a href="<?php echo $this->_tpl_vars['url']; ?>
/Search/Home?<?php echo $this->_tpl_vars['facet']['url']; ?>
"><?php echo translate(array('text' => $this->_tpl_vars['facet']['value']), $this);?>
</a> <span dir="ltr">(<?php echo $this->_tpl_vars['facet']['count']; ?>
)</span></dd>
      <?php if (( $this->_foreach['facetLoop']['iteration'] > 5 ) && ($this->_foreach['facetLoop']['iteration'] == $this->_foreach['facetLoop']['total'])): ?>
          <dd><a href="#" onclick="hideThese('<?php echo $this->_tpl_vars['cluster']; ?>
'); return false;"><i>less...</i></a></dd>
      <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
</dl>
<?php endforeach; endif; unset($_from); ?>