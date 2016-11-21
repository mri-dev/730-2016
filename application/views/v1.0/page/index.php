<? $bg = StaticPage::getBackground($this->page); ?>
<style type="text/css">
	<? if($bg): ?>
	body{
		background-image:url(/<?=$bg?>) !important;
		background-repeat:no-repeat !important;
		background-position:center top !important; 
		background-size:auto !important;
	}
	<? endif; ?>
</style>
<script type="text/javascript">
	$(function(){
		$('.static_page .page.scrollers').jScrollPane( {
			showArrows: true,
			verticalDragMinHeight: 25,
			verticalDragMaxHeight: 25
		} );
	})	
</script>
<div class="static_page <?=(!$this->page)?'nopagefound':''?>">
	<div class="page <?=($this->page[scrollable] == '1')?'scrollers':''?>">
		<? if($this->page): ?>
		<!-- Oldal címe -->
		<h1><?=$this->page['title_'.Lang::getLang()]?></h1>
		<!--/Oldal címe -->
	
		<? 
		// Az igazi kincs - alapanyagok
		if( $this->page[ID] == 9 ): ?>
			<? $this->render( 'page/inc/rolunk-anyagok', true ); ?>
		<? endif; ?>
		
		<!-- Oldal Tartalom -->
		<?=html_entity_decode( stripslashes($this->page['content_'.Lang::getLang()]), ENT_QUOTES | ENT_HTML5, 'UTF-8')?>
		<!--/Oldal Tartalom -->
		
		<? 
		// Az igazi kincs - színvariációk
		if( $this->page[ID] == 9 ): ?>
			<? $this->render( 'page/inc/rolunk-szinvariaciok', true ); ?>
		<? endif; ?>
		<br />
		<br />
		<br />
		<? if( count( $this->page[hashtag] ) > 0 ): ?>
			<div class="hashtags">
			<? foreach( $this->page[hashtag] as $tags): ?>
				<div>#<?=$tags?></div>
			<? endforeach; ?>
			</div>
		<? endif; ?>
		<br />
		<div class="arrows rotate-to-left leftArrow small">
			<a href="<?=$_SERVER[HTTP_REFERER]?>"><?=__('vissza')?></a>
		</div>
		<? else: ?>
			<h1><?=__('Az oldal nem található')?></h1>
			<div class="left">
				<p><?=__('A kért oldal nem található a szerverünkön.')?></p>
				
				<?=__('Próbálja meg az alábbiakat')?>:
				<ul class="p10">
					<li><?=__('Ha a címet kézzel írta be, ellenőrizze a gépelési hibákat.')?></li>
					<li><?=__('Térjen vissza a')?> <a href="/"><?=__('kezdőlapra')?></a> <?=__('és próbálja újra onnan.')?></li>
					<li><?=__('Menjen')?> <a href="<?=$_SERVER[HTTP_REFERER]?>"><?=__('vissza')?></a> <?=__('és próbálja újra.')?></li>
				</ul>
			</div>
		<? endif; ?>
	</div>
</div>
<? $this->render( 'page/after/mobil_after_'.$this->page[ID], true ); ?>

