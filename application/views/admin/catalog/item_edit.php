<div class="admin_module_title">
  <h4><?= $module['title']; ?></h4>
</div>
<div id="content">
  <div class="text_module">
    <form action="<?= $item->link('edit'); ?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="cmd" value="1">
      <input type="hidden" name="parent_section_id" value="<?= $section->id; ?>">
      <table class="admin_module_form photo_module_edit">
        <tr>
          <td class="admin_module_form_title"></td><td class="admin_error_message"><?=validation_errors();?></td>
        </tr>
        <tr>
          <td class="admin_module_form_title">Каталог</td>
          <td>
            <input name="section_title" class="styler" type="text" disabled="disabled" value="<?= $section->title; ?>">
          </td>
        </tr>
        <tr>
          <td class="admin_module_form_title">категория товара</td>
          <td>
            <select multiple="multiple" name="parent_category_id[]" width="400px">
              <option value="0">не выбрано</option>
              <optgroup label="категории">
                <? foreach ( $category_list as $category ): ?>
                  <option value="<?= $category->id; ?>" id="<?= $category->id; ?>" <?= in_array($category->id, $item_links) ? 'selected' : ''; ?>>
                    <?= $category->title; ?>
                  </option>
                <? endforeach; ?>
              </optgroup>
            </select>
            Добавить несколько категорий через Ctrl.
            <input type="submit" value="сохранить" name="save" class="admin_module_form_submit styler" />
            <a class="g-button" style="float:right" href="<?= $links['section_index']; ?>">в начало</a>
          </td>
        </tr>

        <tr>
          <td class="admin_module_form_title">Фотографии товара</td>
          <td>
            <div class="img_section clearfix">
              <? foreach ( $item_images as $image ): ?>
                <div class="image-item" style="background-image: url(<?= '/upload/images/catalog/' . $image->getFilenameThumb(); ?>)">
                  <p class="image-delete">
                    <a href="/admin/catalog/imgdel/<?= $item->id ?>/<?= $image->getId(); ?>" onclick="if (confirm('Вы уверены?')) return true; else return false;">
                      удалить
                    </a>
                  </p>
                </div>
              <? endforeach; ?>
            </div>
            <div class="clearfix">
              <input type="button" class="styler addImages" value="Добавить" />
              <input type="button" class="styler delImages" value="Удалить" />
              <input type="hidden" value="1" id="img_count" name="img_count">
            </div>
            <div class="section clearfix">
              <div class="image_add">
                <input type="file" name="image_1" />
              </div>
            </div>
          </td>
        </tr>

        <tr>
          <td class="admin_module_form_title">Название</td>
          <td><input class="styler" type="text" name="title" value='<?= $item->title; ?>' /></td>
        </tr>
        <tr>
          <td class="admin_module_form_title">Артикул</td>
          <td><input class="styler" type="text" name="article" value='<?= $item->article; ?>' /></td>
        </tr>
        <tr>
          <td class="admin_module_form_title">Цена</td>
          <td><input class="styler" type="text" name="price" value='<?= $item->price; ?>' /></td>
        </tr>
        <tr>
          <td class="admin_module_form_title">Описание</td>
          <td>
            <textarea id="description" name="description"><?= $item->description; ?></textarea>
          </td>
        </tr>
        <? if ( isset($user_values) && !empty($user_values) ): ?>
          <input type="hidden" name="form_type" value="0">
          <? foreach ($user_values as $key => $value): ?>
            <tr>
              <input type="hidden" value="<?= $value['user_field_id']; ?>" name="uf_ids[]">
              <input type="hidden" value="<?= $value['type']; ?>" name="uf_types[]">
              <td class="admin_module_form_title"><?= $value['title']; ?></td>
              <td>
                <?
                switch ( $value['type'] )
                {
                  case 1:
                    $f_value = $value['value_int'];
                    break;
                  case 2:
                    $f_value = $value['value_float'];
                    break;
                  case 3:
                    $f_value = $value['value_string'];
                    break;
                  case 4:
                    $f_value = $value['value_text'];
                    break;
                  case 5:
                    if ( $value['value_date'] )
                    {
                      $f_value = date('Y-m-d', strtotime($value['value_date']));
                    } else {
                      $f_value = 'NULL';
                    }
                    break;
                  default:
                    $f_value = '';
                    break;
                }
                ?>
                <input type="text" name="uf_values[]" value="<?= ( $f_value == 'NULL' ) ? '' : $f_value; ?>">
              </td>
            </tr>
          <? endforeach; ?>
        <? elseif ( isset($user_fields) ): ?>
          <input type="hidden" name="form_type" value="1">
          <? foreach ($user_fields as $key => $value): ?>
            <tr>
              <input type="hidden" value="<?= $value['id']; ?>" name="uf_ids[]">
              <input type="hidden" value="<?= $value['type']; ?>" name="uf_types[]">
              <td class="admin_module_form_title"><?= $value['title']; ?></td>
              <td>
                <input type="text" value="" name="uf_values[]">
              </td>
            </tr>
          <? endforeach; ?>
        <? endif; ?>
      </table>
    </form>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(
    function() {
      jQuery('#description').redactor({convertDivs: true, convertLinks: false, observeImages: true, fileUpload: '/admin/map/imeravi_upload_file', imageUpload: '/admin/map/imeravi_upload_image'});
    }
  );
</script>

<script language="javascript">

  $('input.addImages').click(function() {
    var cur_count = $('#img_count').val();
    var img_count = parseInt(cur_count) + 1;
    $('#img_count').val(img_count);

    $('div.section').append('<div class="image_add"><input type="file" name="image_' + img_count + '"></div>');
    $('input:file').styler();
  });

  $('input.delImages').click(function() {
    var count = $('div.section div.image_add').length;
    if ( count > 1 )
    {
      var cur_count = $('#img_count').val();
      var img_count = parseInt(cur_count) - 1;
      $('#img_count').val(img_count);

      $('div.section div.image_add:last').remove();
      $('input:file').styler();
    } else {
      alert('Не возможно удалить изображение!');
    }
  });

</script>