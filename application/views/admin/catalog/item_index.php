<? if ( $is_item ): ?>
  <div class="admin_module_title">
    <h4><?= $module['title']; ?></h4>
  </div>
  <div id="content">
    <div class="text_module">
      <h6>
        Список категорий товара: <em><?= $item->title; ?></em>
      </h6>
      <div class="clear"></div>
      <div class="admin_module_list">
        <ul class="admin_list">
          <? foreach ( $categories as $category ): ?>
            <li>
              <a href="<?= $category->link('items'); ?>">
                <?= $category->title; ?>
              </a>
              <a href="<?= $item->link('unlink', $category->id); ?>" class="admin_form_action_page" onclick="if (confirm('Вы уверены?')) return true; else return false;" title="Отвязать от категории">
                <img title="Отвязать от категории" src="/img/admin/icon_delete_1.5.png" />
              </a>
            </li>
          <? endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
<? else: ?>
  <div class="admin_module_title">
    <h4><?= $module['title']; ?></h4>
  </div>
  <div id="content">
    <div class="text_module">
      <h6>
        Список товаров категории: <em><?= $category->title; ?></em>
      </h6>
      <div class="clear"></div>
      <div class="admin_module_list">
        <ul class="admin_list">
          <? foreach ( $items as $item ): ?>
            <li>
              <a href="<?= $item->link('item_list'); ?>">
                <?= $item->title; ?>
              </a>
              <a href="<?= $category->link('unlink', $item->id); ?>" class="admin_form_action_page" onclick="if (confirm('Вы уверены?')) return true; else return false;" title="Отвязать товар">
                <img title="Отвязать товар" src="/img/admin/icon_delete_1.5.png" />
              </a>
            </li>
          <? endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
<? endif; ?>