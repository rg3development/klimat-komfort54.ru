<script type="text/javascript">
  var RecaptchaOptions = {
    theme : 'clean'
  };
</script>

<? if ( validation_errors() ) : ?>
  <div class="alert">
    <?= validation_errors(); ?>
  </div>
<? endif; ?>

<form method="post" id="checkout">
  <input type="hidden" name="type" value="save">
  <div class="six columns alpha">
    <h4>Контактные данные</h4>
    <div class="six columns alpha">
      <label for="billing_company" class="">Представьтесь, пожалуйста <span class="required">*</span></label>
      <input type="text" class="input-text" name="full_name" value="<?=set_value('full_name');?>" id="billing_company" placeholder="Фамилия, имя" />
    </div>
    <div class="three columns alpha">
      <label for="billing_email" class="">Email <span class="required">*</span></label>
      <input type="text" class="input-text" name="email" value="<?=set_value('email');?>" id="billing_email" placeholder="Электронный адрес" />
    </div>
    <div class="three columns omega">
      <label for="billing_phone" class="">Телефон <span class="required">*</span></label>
      <input type="text" class="input-text" name="phone" value="<?=set_value('phone');?>" id="billing_phone" placeholder="Телефон" />
    </div>
    <div class="six columns alpha">
      <label for="billing_company" class="">Адрес</label>
      <input type="text" class="input-text" name="address" value="<?=set_value('address');?>" id="billing_company" placeholder="Адрес" />
    </div>
    <div class="clear"></div>
  </div>

  <div class="six columns omega">
    <h4>Дополнительная информация</h4>
    <label for="order_comments" class="">Ваши комментарии</label>
    <textarea name="comments" class="input-text" id="order_comments" placeholder="Ваши комментарии" rows="6"></textarea>

    <div class="six columns alpha">
      <script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=6Ld8Bt0SAAAAABcG3VS3G7LUbk0hTzMWhHpUwqbN"></script>
      <noscript>
        <iframe src="http://www.google.com/recaptcha/api/noscript?k=6Ld8Bt0SAAAAABcG3VS3G7LUbk0hTzMWhHpUwqbN" height="300" width="500" frameborder="0"></iframe>
        <br>
        <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
        <input type="hidden" name="recaptcha_response_field" value="manual_challenge">
      </noscript>
    </div>
  </div>

</form>

<div class="clear"></div>
<br><br>

<h4>Ваш заказ</h4>
<table class="checkout_cart">
  <thead>
    <tr>
      <th class="product">Товар</th>
      <th class="desc">Описание товара</th>
      <th class="unit-price">Цена</th>
      <th class="qty">Количество</th>
      <th class="total">Сумма</th>
    </tr>
  </thead>
  <tbody>
    <? foreach ( $this->cart->contents() as $index => $item ): ?>
      <? $cur_item = $this->catalog_mapper->get_object($item['id'], 'item'); ?>
      <tr>
        <td class="product">
          <a href="catalog<?= $cur_item->details(); ?>">
            <img style="width: 71px;" src="<?= $cur_item->image(); ?>" alt="" />
          </a>
        </td>
        <td class="desc">
          <a href="catalog<?= $cur_item->details(); ?>">
            <?= $cur_item->title; ?>
          </a>
        </td>
        <td class="unit-price">
          <?= $cur_item->price(); ?>
        </td>
        <td><?= $item['qty']; ?></td>
        <td>
          <?= $this->cart->format_number($item['subtotal']); ?>
        </td>
      </tr>
    <? endforeach; ?>
  </tbody>
</table>

<table class="checkout_totals">
  <tbody>
    <tr class="total">
      <th>Сумма :</th>
      <td>
        <?= $this->cart->format_number($this->cart->total()); ?>
      </td>
    </tr>
  </tbody>
</table>
<br>

<div id="payment">
  <div class="form-row">
    <a href="#" id="sendmail" class="button">Заказать</a>
  </div>
  <div class="clear"></div>
</div>

<script type="text/javascript">
  $('#sendmail').click(
    function ()
    {
      $('#checkout').submit();
    }
  );
</script>