<div class="admin_module_title">
  <h4><?= $module['title']; ?></h4>
</div>
<div id="content">
  <div class="text_module">
  	<h6>
  		Список категорий каталога: <em><?= $section->title; ?></em>
  	</h6>
    <div class="admin_module_form">
      <a class="g-button" style="float:right" href="<?= $section->link('cat_add'); ?>">создать категорию</a>
    </div>
    <div class="clear"></div>
    <div class="admin_module_list">
      <ul class="admin_list">
        <? foreach ( $category_list as $category ): ?>
          <li>
            <a href="<?= $category->link('items'); ?>">
              <?= $category->title; ?>
            </a>
            <a href="<?= $category->link('del'); ?>" class="admin_form_action_page" onclick="if (confirm('Вы уверены? Будут удалены все категории в данном каталоге!')) return true; else return false;" title="удалить">
              <img title="удалить" alt="удалить" src="/img/admin/icon_delete_1.5.png" />
            </a>
            <a href="<?= $category->link('edit'); ?>" class="admin_form_action_page" title="редактировать">
              <img title="редактировать" alt="редактировать" src="/img/admin/icon_edit_1.5.png"/>
            </a>
          </li>
        <? endforeach; ?>
      </ul>

    </div>
  </div>
  <div class="text_module">
    <h6>
      Список товаров в каталоге: <em><?= $section->title; ?></em>
    </h6>
    <div class="admin_module_form">
      <a class="g-button" style="float:right" href="<?= $section->link('item_add'); ?>">создать товар</a>
    </div>
    <div class="clear"></div>
    <div class="admin_module_list">
      <ul class="admin_list">
        <? foreach ( $item_list as $item ): ?>
          <li>
            <a href="<?= $item->link('item_list'); ?>">
              <?= $item->title; ?>
            </a>
            <a href="<?= $item->link('del'); ?>" class="admin_form_action_page" onclick="if (confirm('Вы уверены? Будут удалены все категории в данном каталоге!')) return true; else return false;" title="удалить">
              <img title="удалить" alt="удалить" src="/img/admin/icon_delete_1.5.png" />
            </a>
            <a href="<?= $item->link('edit'); ?>" class="admin_form_action_page" title="редактировать">
              <img title="редактировать" alt="редактировать" src="/img/admin/icon_edit_1.5.png"/>
            </a>
          </li>
        <? endforeach; ?>
      </ul>

    </div>
  </div>
</div>