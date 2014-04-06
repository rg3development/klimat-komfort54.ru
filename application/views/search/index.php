			<div class="search">
				<form action="" method="get">
				<input id="search-input" name="s" type="text" class="text" value="<?=!empty($_GET['s']) ? $_GET['s'] : '';?>" />
				<input id="search-button" type="submit" class="submit" title="Поиск" value=""/>
				</form>
			</div>
			<span>найдено позиций:&nbsp;<b><?=sizeof($content)?></b></span>
			<table class="search-result">
			<?php if (sizeof($content) > 0) : ?>
			<?php foreach ($content as $key => $row) : ?>
			<tr>
				<td><?=$key+1;?>.&nbsp;</td>
				<td><div><?=$row['content'];?></div>
					<a href="/<?=$row['url'];?>">подробнее..</a>
				</td>
			</tr>
			<?php endforeach; ?>
			<?php endif;?>
			</table>