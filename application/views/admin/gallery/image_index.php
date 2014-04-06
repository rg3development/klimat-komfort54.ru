	<div class="admin_module_title"><h4><?=!empty($module_title) ? $module_title : '';?></h4></div>
	<div id="content">
		<div class="photo_module">
			<div class="admin_module_form right_buttons">
				<a href="<?=base_url();?>admin/gallery/add_image/<?=$parent_id;?>"><button class="g-button">создать</button></a>
			</div>
			<div class="admin_module_list">
				<ul class="admin_list photo_image_list">
				<?php if (!empty($list)) :?>
				<?php foreach ($list as $key => $item) :?>
					<li id="text<?=$item->id;?>" onmouseover="$(this).children().children().css('color', '#0099cc')" onmouseout="$(this).children().children().css('color', '#666666')">
						<a href="<?=base_url();?>admin/gallery/edit_image/<?=$item->id;?>/<?=$parent_id;?>" title="редактировать"><em><?=$item->title;?></em></a>
						<a href="<?=base_url();?>admin/gallery/delete_image/<?=$item->id;?>/<?=$parent_id;?>" class="admin_form_action_page" onclick="if (confirm('Вы уверены?')) return true; else return false;" title="удалить"><img title="удалить" alt="удалить" src="<?=base_url();?>/img/admin/icon_delete_1.5.png"/></a>
						<a href="<?=base_url();?>admin/gallery/edit_image/<?=$item->id;?>/<?=$parent_id;?>" class="admin_form_action_page" title="редактировать"><img title="редактировать" alt="редактировать" src="<?=base_url();?>/img/admin/icon_edit_1.5.png"/></a>
						<a href="<?=base_url();?>admin/gallery/prioritydown/<?=$item->id;?>/<?=$parent_id;?>" class="admin_form_action_page" title="опустить" ><img title="вниз" alt="вниз" src="<?=base_url();?>/img/admin/icon_arrow_down_1.5.png"/></a>
						<a href="<?=base_url();?>admin/gallery/priorityup/<?=$item->id;?>/<?=$parent_id;?>" class="admin_form_action_page" title="поднять" ><img title="вниз" alt="вниз" src="<?=base_url();?>/img/admin/icon_arrow_up_1.5.png"/></a>
						<?php if (isset($images[$key])) : ?>
							<a href="<?=base_url();?>admin/gallery/edit_image/<?=$item->id;?>/<?=$parent_id;?>" title="редактировать"><img src="<?=$path_to_image.'/'.$images[$key];?>" class="admin_form_action_page" width="100" /></a>
						<?php endif; ?>
						<div class="clear"></div>
					</li>
				<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
