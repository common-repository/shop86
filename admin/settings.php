<?php 
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'main_settings';
?>
<div class="wrap">
  <div class="icon32 icon32_shop86logo"><br></div>
  <h2 class="nav-tab-wrapper">
  <?php foreach($data['setting']->getSettingsTabs() as $tab_key => $tab_caption): ?>
    <?php $active = $tab == $tab_key ? 'nav-tab-active' : ''; ?>
    <a class="nav-tab <?php echo $active; ?>" href="?page=shop86-settings&tab=<?php echo $tab_key; ?>"><?php echo $tab_caption['tab']; ?></a>
  <?php endforeach; ?>
    <span class="settings-version-number"><?php echo shop86_VERSION_NUMBER; ?></span>
  </h2>
  <?php do_settings_sections($tab); ?>
</div>