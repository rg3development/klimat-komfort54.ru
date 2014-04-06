<div class="admin_module_title">
  <h4><?= $module['title']; ?></h4>
</div>
<div id="content">
  <div class="text_module">
    <h6>
      Список каталогов:
    </h6>
    <div class="admin_module_form">
      <a class="g-button" style="float:right" href="<?= $links['section_add']; ?>">создать каталог</a>
    </div>
    <div class="clear"></div>
    <div class="admin_module_list">
      <ul class="admin_list">
        <? foreach ( $catalog_section_list as $section ): ?>
          <li>
            <a href="<?= $section->link('cat_list'); ?>">
              <?= $section->title; ?> (Категории: <?= $section->num_categories; ?>)
            </a>
            <a href="<?= $section->link('del'); ?>" class="admin_form_action_page" onclick="if (confirm('Вы уверены? Будут удалены все категории в данном каталоге!')) return true; else return false;" title="удалить">
              <img title="удалить" alt="удалить" src="/img/admin/icon_delete_1.5.png" />
            </a>
            <a href="<?= $section->link('edit'); ?>" class="admin_form_action_page" title="редактировать">
              <img title="редактировать" alt="редактировать" src="/img/admin/icon_edit_1.5.png"/>
            </a>
          </li>
        <? endforeach; ?>
      </ul>
    </div>
  </div>
</div>