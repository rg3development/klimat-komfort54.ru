<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?=$page_info['title'];?> - <?=$site_settings['title'];?></title>
	<meta name="keywords" content="<?=!empty($page_info['keywords']) ? $page_info['keywords'] : $site_settings['keywords'];?>">
	<meta name="description" content="<?=!empty($page_info['description']) ? $page_info['description'] : $site_settings['description'];?>">

	<link type="text/css" rel="stylesheet" href="/css/site/style.css"/>
	<link type="text/css" rel="stylesheet" href="/css/site/reset.css"/>
	<link type="text/css" rel="stylesheet" href="/js/lightbox/css/jquery.lightbox-0.5.css"/>
	<link type="text/css" rel="stylesheet" href="/js/jcarusel/style.css"/>

	<script type="text/javascript" src="/js/main.js"></script>
	<script type="text/javascript" src="/js/jquery-1.7.min.js"></script>
	<script type="text/javascript" src="/js/jquery.tinyscrollbar.min.js"></script>
	<script type="text/javascript" src="/js/lightbox/js/jquery.lightbox-0.5.pack.js"></script>
	<script type="text/javascript" src="/js/jcarusel/jquery.jcarousel.min.js"></script>

</head>
<div class="wraper">
	<div class="header">
		<div class="icon">
			<a href="/"><img src="/img/site/home.jpg"/></a><a href="/contacts"><img src="/img/site/mail.jpg"/></a>
		</div>
		<div class="phone">
			<span>+7(925)</span> 205-75-66
		</div>
	</div>
	<?
		$top_menu_array	= explode("</li>", $menu);
		$top_menu_left	= array();
		$top_menu_right	= array();

		for($i=0; $i<3; $i++) {
			$top_menu_left[] = $top_menu_array[$i].'</li>';
		}
		for($i=3; $i<6; $i++) {
			$top_menu_right[] = $top_menu_array[$i].'</li>';
		}
		$top_menu_left	= join('', $top_menu_left);
		$top_menu_right	= join('', $top_menu_right);
	?>
	<div class="menu">
		<div class="leftblock">
		<ul>
			<?=$top_menu_left;?>
		</ul>
		</div>
		<div class="midblock">
			<a href="/" class="logo"><?=$site_settings['logo'] != '' ? '<img src='.$site_settings['logo'].' />' : '';?></a>
		</div>
		<div class="rightblock">
		<ul>
			<?=$top_menu_right;?>
		</ul>
		</div>
	</div>
	<div class="redblock">
		<div><a class="call" href="/callback">ОБРАТНЫЙ ЗВОНОК</a></div>
		<div><a class="zamer" href="/order">ВЫЗОВ ЗАМЕРЩИКА</a></div>
		<div><a class="cost" href="/calculator">РАСЧЕТ СТОИМОСТИ</a></div>
	</div>
	<div class="sidebar">
		<div class="menubar">
			<div class="title"><span class="title">ПОТОЛКИ</span></div>
			<ul>
				<? foreach ($widgets['left_menu'] as $subpage) : ?>
				<li><a href="/<?=$subpage->url; ?>"><?=$subpage->title; ?></a></li>
				<? endforeach; ?>
			</ul>
		</div>
		<div class="title2"><a href="/response"><span class="title">ОТЗЫВЫ</a></span></div>
		<div class="title2"><a href="/fordirector"><span class="title">ПОЖАЛОВАТЬСЯ ДИРЕКТОРУ</a></span></div>
		<div class="sale"><?=$widgets['text_banner']['description'];?></div>
	</div>
