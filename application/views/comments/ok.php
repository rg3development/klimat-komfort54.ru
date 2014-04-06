<section id="comment">
  <? if ( isset($comments) && !empty($comments) ): ?>
    <h4 class="titleBold">
      Комментарии: <?= count($comments); ?>
    </h4>
    <ol class="commentlist">
      <? foreach ( $comments as $key => $value ) : ?>
        <li>
          <div class="comment-body">
            <div class="avatar-img"><img src="/img/site/content/avatar.gif" alt="" class="avatar"/></div>
            <cite class="fn">
              <a href="mailto:<?= $value->email; ?>"><?= $value->name; ?></a>
            </cite>
            <span class="tdate"><?= $value->date; ?></span>
            <div class="commenttext">
              <p><?= $value->message; ?></p>
            </div>
          </div>
        </li>
      <? endforeach; ?>
    </ol>
  <? endif; ?>
  <h4 id="comments" class="titleBold">
    <?= isset($approved_message) ? $approved_message : ''; ?>
  </h4>
</section>