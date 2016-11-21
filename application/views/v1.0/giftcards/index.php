<div class="giftcards-infopage">
  <h1><?=__('Ajándékkártya felhasználás')?></h1>
  <div class="desc"><?=__('Vásárlás során felhasználhatja ajándékkártyáit, amivel kedvezményekhez jut.')?></div>
  <?=$this->rmsg?>
  <div class="activator">
    <h2><?=__('Ajándékkártya megadása')?></h2>
    <form class="" action="" method="post">
      <div class="row">
        <div class="col-md-7">
          <label for="code"><?=__('Ajándékkártya azonosító kódja')?></label>
          <input type="text" class="form-control" name="code" value="">
        </div>
        <div class="col-md-3">
          <label for="code"><?=__('Biztonsági kód')?></label>
          <input type="number" class="form-control" name="seccode" pattern="[0-9]{3}" min="0" step="1" max="999">
        </div>
        <div class="col-md-2">
          <label for="" style="visibility:hidden;">1</label>
          <button type="submit" name="activateGiftcard" class="form-control btn btn-default"><?=__('Aktivál')?> <i class="fa fa-arrow-circle-right"></i></button>
        </div>
      </div>
    </form>
    <?php
      $gl = $this->giftcards_list;
      if ($gl['num'] != 0):
    ?>
      <h4><?=__('Aktivált ajándékkártyák')?></h4>
      <?php foreach ( $gl['codes'] as $g ): ?>
        <div class="added-codes">
          <strong><?=$g['code']?> / <em><?=$g['verify_code']?></em></strong>
          <span class="price">-<?=$g['price']?> <?=$gl['valuta']?></span>
        </div>
      <?php endforeach; ?>
      Összes levonás: <strong><?=$gl['total_price']?> <?=$gl['valuta']?></strong>
    <?php endif; ?>
  </div>
  <div class="usage">
    <h4><?=__('Felhasználási útmutató')?></h4>
    <ul>
      <li>&bull; <?=__('Több ajándékkártyát is felhasználhat egyidőben.')?></li>
      <li>&bull; <?=__('1 ajándékkártya csak egyszer használható fel.')?></li>
      <li>&bull; <?=__('Az ajándékkártya száma és 3 jegyű biztonsági kódja megadásával használhatja fel vásárlásai során.')?></li>
    </ul>
  </div>
</div>
