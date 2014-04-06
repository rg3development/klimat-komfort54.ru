<?php if (!empty($gallery)) :?>
<br/>
<div class="block_gallery">
    <ul id="mycarousel" class="jcarousel-skin-tango">
        <?php foreach ($gallery as $image) :?>
        <li style="text-align:center;">
            <a href="<?=$path.'/'.$image['filename'];?>" title="<?=$image['title'];?>" rel="prettyPhoto[portfolio]" class="gallery-link">
                <div class="gallery-item-carusel" style="background-image: url(<?=$path.'/'.$image['thumbnail'];?>)"  title='<?=$image['title'];?>'></div>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear"></div>
<script type="text/javascript">
    function mycarousel_initCallback(carousel) {
        // Disable autoscrolling if the user clicks the prev or next button.
        carousel.buttonNext.bind('click', function() {
            carousel.startAuto(0);
        });
    
        carousel.buttonPrev.bind('click', function() {
            carousel.startAuto(0);
        });
    
        // Pause autoscrolling if the user moves with the cursor over the clip.
        carousel.clip.hover(function() {
            carousel.stopAuto();
        }, function() {
            carousel.startAuto();
        });
        $('.gallery-link').lightBox({
            imageLoading: '/js/lightbox/images/loading.gif',
            imageBtnClose: '/js/lightbox/images/close.gif',
            imageBtnPrev: '/js/lightbox/images/prev.gif',
            imageBtnNext: '/js/lightbox/images/next.gif',
        });
    };
    
    jQuery(document).ready(function() {
        jQuery('#mycarousel').jcarousel({
            auto: 2,
            wrap: 'last',
            initCallback: mycarousel_initCallback
        });
    });
</script>
<?php endif; ?>