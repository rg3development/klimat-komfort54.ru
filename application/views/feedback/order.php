<p class="error"><?=validation_errors();?></p>
<form id="contact" action="" method="post">
	<div class="form">
		<input type="text" name="name" placeholder="Введите имя" class="input <?=form_error('name') ? 'error' : ''?>" value='<?=set_value('name');?>'><br/>
		<input type="text" name="email" placeholder="Введите e-mail" class="input <?=form_error('email') ? 'error' : ''?>" value='<?=set_value('email');?>'><br/>
		<input type="text" name="phone" placeholder="Введите номер телефона - НЕОБЯЗАТЕЛЬНО" class="input <?=form_error('phone') ? 'error' : ''?>" value='<?=set_value('phone');?>'><br/>
		<textarea name="message" class="<?=form_error('message') ? 'error' : ''?>" placeholder="Текст"><?=set_value('message');?></textarea>
		<input type="submit" class="button" value="ОТПРАВИТЬ"/>
	</div>
</form>