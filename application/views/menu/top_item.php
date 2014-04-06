<?
/* current menu item */
/*
<?= ( strrpos($_SERVER['REQUEST_URI'], '/'.$page->url) !== false ) ? 'class="current"' : '' ; ?>
*/
?>

<li>
  <a href="<?= $page->url; ?>" id="page<?= $page->id; ?>" <?=strrpos($_SERVER['REQUEST_URI'], '/'.$page->url) !== false ? 'class="page-'.$page->url.' selected"' : 'class="page-'.$page->url.'"' ;?>>
    <?=  ( $page->show_alias && $page->alias ) ? $page->alias : $page->title; ?>
  </a>
  <? if ( $submenu ): ?>
    <ul>
      <?= $submenu; ?>
    </ul>
  <? endif; ?>
</li>