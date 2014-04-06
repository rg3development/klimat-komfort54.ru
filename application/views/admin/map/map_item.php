			<li onmouseover="$(this).children().children().css('color', '#0099cc')" onmouseout="$(this).children().children().css('color', '#666666')">
				<em>
					<a id="page<?=$page->id;?>" href="<?=base_url();?>admin/map/edit/<?=$page->id;?>"><?=$page->title;?><?= ( $page->alias ) ? ' (' . $page->alias . ')' : ''; ?></a>
				</em>
				<a href="<?=base_url();?>admin/map/delete/<?=$page->id;?>" class="admin_form_action_page" onclick="if (confirm('Вы уверены?')) return true; else return false;" title="удалить"><img title="удалить" alt="удалить" src="<?=base_url();?>/img/admin/icon_delete_1.5.png"/></a>
				<a href="<?=base_url();?>admin/map/edit/<?=$page->id;?>" class="admin_form_action_page" title="редактировать"><img title="редактировать" alt="редактировать" src="<?=base_url();?>/img/admin/icon_edit_1.5.png"/></a>
				<a href="<?=base_url();?>admin/map/prioritydown/<?=$page->id;?>" class="admin_form_action_page" title="вниз" ><img title="вниз" alt="вниз" src="<?=base_url();?>/img/admin/icon_arrow_down_1.5.png"/></a>
				<a href="<?=base_url();?>admin/map/priorityup/<?=$page->id;?>" class="admin_form_action_page" title="вверх" ><img title="вверх" alt="вверх" src="<?=base_url();?>/img/admin/icon_arrow_up_1.5.png"/></a>
				<!--
				<span class="admin_form_action_page"><?=$page->level;?>&nbsp;</span>
				<span class="admin_form_action_page"><?=$page->priority;?>&nbsp;</span>
				-->
				<div class="clear"></div>
				<div>
					<?=$submenu;?>
				</div>
			</li>
