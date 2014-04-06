<? if (!empty($news_list)) :?>
	<? if ($news_category['show_title']) :?>
	<h1 class="page_title"><?=$news_category['title'];?></h1>
	<? else : ?>
	<h2 class="title"></h2>
	<? endif; ?>
	<!--Blog Post Block-->
	<div class="post-blog">
		<?=!empty($paginator) ? '<div class="paginator">'.$paginator.'</div>' : ''; ?>
		<div class="clear"></div>
		<? foreach ($news_list as $key => $news) :?>
		<div class="post-block <?=$key%2===0 ? 'odd' : 'even'; ?>">
			<div class="post-excerpt">
				<time><?=date("d-m-Y", $news['created']);?></time>
				<p class="title_news"><a href="<?=base_url().'content/'.$page_url.'?news_id='.$news['id'].'&per_page='.$offset;?>"><?=$news['title'];?></a></p>
				<p class="anno_news">
					<? if ($news['image_id'] > 0) :?>
						<img src="<?=base_url().$path.'/'.$news['filename_thumb'];?>" align="<?=$news['inner_position'];?>" />
					<? endif;?>
					<?=strip_tags($news['anno']);?>
				</p>
				<p class="link_news"><a href="<?=base_url().$page_url.'?news_id='.$news['id'].'&per_page='.$offset;?>">подробнее...</a></p>
			</div>
		</div>
		<? endforeach; ?>
	</div>
	<!--End Blog Post Block-->
	<div class="clear"></div>
	<?=!empty($paginator) ? '<div class="paginator">'.$paginator.'</div>' : ''; ?>
<? endif; ?>