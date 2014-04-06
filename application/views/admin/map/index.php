	<div class="admin_module_title"><h4><?=!empty($module_title) ? $module_title : '';?></h4></div>
	<div id="content">
		<div class="admin_module_menu"><a href="<?=base_url();?>admin/map/add"><button class="g-button">создать</button></a></div>
		<ul class="admin_list admin_mainlist">
			<?=$map;?>
		</ul>
	</div>