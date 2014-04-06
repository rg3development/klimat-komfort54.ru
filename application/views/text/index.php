<?php if (!empty($text_object)) : ?>
<div class="text-block">
	<?php if ($text_object->show_title) :?>
	<h1 class="page_title"><?=$text_object->title;?></h1>
	<?php endif; ?>
	<?=$text_object->description;?>
</div>
<?php endif; ?>