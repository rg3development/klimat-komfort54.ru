<?php if (!empty($gallery)) :?>
<?=!empty($paginator) ? '<div class="paginator">'.$paginator.'</div>' : ''; ?>
<br/>
<div class="block_gallery">
    <ul class="gallery">
        <?php foreach ($gallery as $image) :?>
        <li data-id="id-1" data-type="illustration">
            <a href="<?=$path.'/'.$image['filename'];?>" title="<?=$image['title'];?>" rel="prettyPhoto[portfolio]" class="gallery-link">
                <div class="gallery-item" style="background-image: url(<?=$path.'/'.$image['thumbnail'];?>)"  title='<?=$image['title'];?>'></div>
            </a>
            <h3><?=$image['title'];?></h3>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear"></div>
<?=!empty($paginator) ? '<div class="paginator">'.$paginator.'</div>' : ''; ?>
<script type="text/javascript">
    $('.gallery-link').lightBox({
        imageLoading: '/js/lightbox/images/loading.gif',
        imageBtnClose: '/js/lightbox/images/close.gif',
        imageBtnPrev: '/js/lightbox/images/prev.gif',
        imageBtnNext: '/js/lightbox/images/next.gif',
    });
</script>
<?php endif; ?>