<?
// цикл получения всех изображений товара.
/*
  <? foreach ( $item->images() as $product_image ) : ?>
    <img src="<?= $item->img_src($product_image); ?>" />
  <? endforeach; ?>
*/

// форма добавления товара в корзину
// менять только селектор обработчика JS .click()
/*
  <? $item->add_to_cart(); ?>
*/
?>

<div class="padcontent product-detail">
  <h3>
    <?= $item->title; ?>
  </h3>
  <div class="four columns alpha">
    <img src="<?= $item->image(); ?>" alt="" class="imgborder scale-with-grid" />
  </div>
  <div class="one_half lastcols">

    <div class="price">
      <?= $item->price(); ?>
    </div>
    <div class="variations_button">
        <button id="add_to_cart" class="button alt" type="submit">Добавить в корзину</button>
        <? $item->add_to_cart(); ?>
    </div>
  </div>
  <div class="clear"></div><br><br>

  <div class="tabcontainer">
    <ul class="tabs">
      <li><a href="#tab0">Описание товара</a></li>
    </ul>
    <div id="tab-body">
      <div id="tab0" class="tab-content">
        <?= $item->description; ?>
      </div>
    </div>
  </div>
  <div class="separator line"></div>
</div>


<script>

  $(document).ready(function() {

    $('#add_to_cart').click(function() {
      $('#cart_add').submit();
      return false;
    });

  });

</script>