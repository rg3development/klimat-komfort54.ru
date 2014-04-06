<?php if (!empty($news)) : ?>
<div class="text-block news-block">
	<time><?=date("d", $news['created']);?>-<?=date("m", $news['created']);?>-<?=date("y", $news['created']);?></time>
	<p class="title_news"><a href="<?=base_url().'content/'.$page_url.'?news_id='.$news['id'].'&per_page='.$offset;?>"><?=$news['title'];?></a></p>
	<?php if ($news['image_id'] > 0 && $news['inner_image']) : ?>
	<img src="<?=base_url().$path.'/'.$news['filename_thumb'];?>" align="<?=$news['inner_position'];?>" title="<?=$news['title'];?>" alt="<?=$news['title'];?>"/>
	<?php endif; ?>
	<?=$news['description'];?>
	<p><a class="news-back" href="<?=base_url().$page_url.'?news_id=0&offset='.$offset?>"><em>вернуться...</em></a></p>
</div>
<?php endif; ?>