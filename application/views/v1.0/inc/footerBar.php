<div id="footer">
	<div class="page_width">
		<div class="footer-row">
			<div class="footer-col payu">
				<a href="http://simplepartner.hu/PaymentService/Fizetesi_tajekoztato.pdf" target="_blank">
						<img src="<?=IMG?>simple_logo_long.png" height="18" alt="Simple - Online bankkártyás fizetés" title="Simple - Online bankkártyás fizetés" /></a>
				</a>
			</div>
			<div class="socials footer-col">
				<ul>
					<? if(SOCIAL_FACEBOOK != ''): ?>
					<li><a href="<?=SOCIAL_FACEBOOK?>"><img src="<?=IMG?>social/facebook_25_dark.png" height='15' title="<?=__('Facebook')?>" alt="<?=__('Facebook')?>" /></a></li>
					<? endif; ?>
					<? if(SOCIAL_TWITTER != ''): ?>
					<li><a href="<?=SOCIAL_TWITTER?>"><img src="<?=IMG?>social/twitter_25_dark.png" height='12' title="<?=__('Twitter')?>" alt="<?=__('Twitter')?>" /></a></li>
					<? endif; ?>
					<? if(SOCIAL_YOUTUBE != ''): ?>
					<li><a href="<?=SOCIAL_YOUTUBE?>"><img src="<?=IMG?>social/youtube_25_dark.png" height='15' title="<?=__('Youtube csatorna')?>" alt="<?=__('Youtube csatorna')?>" /></a></li>
					<? endif; ?>
					<? if(SOCIAL_PINTEREST != ''): ?>
					<li><a href="<?=SOCIAL_PINTEREST?>"><i class="fa fa-pinterest" title="<?=__('Pinterest')?>" ></i></a></li>
					<? endif; ?>
					<? if(SOCIAL_INSTAGRAM != ''): ?>
					<li><a href="<?=SOCIAL_INSTAGRAM?>"><i class="fa fa-instagram" title="<?=__('Instagram')?>" ></i></a></li>
					<? endif; ?>
				</ul>
			</div>
			<div class="footer-col menu-list">
				<ul>
					<? foreach($this->bottomMenu as $d): ?>
						<li class=""><a href="<?=$d[link]?>"><?=$d[text]?></a></li>
					<? endforeach; ?>
				</ul>
			</div>

			<? if( false ): ?>
			<div class="footer-col">
				<div class="currency-selector" title="<?=__('Pénznem kiválasztása')?>">
					<div class="currency-active"><?
						$currency = Lang::getActiveCurrency();
						echo $currency[text];

					?></div>
					<div class="currency-list">
						<form action="" id="currency_selector" method="post" onsubmit="setCurrency(); return false;">
							<ul>
								<? foreach( Lang::getAvaiableCurrencies() as $c ): ?>
								<li class="<?=($c[active])?'active':''?>"><input id="curr_<?=$c[code]?>" type="radio" name="currency_select" value="<?=$c[code]?>"> <label for="curr_<?=$c[code]?>"><?=($c[active])?'<i class="fa fa-check"></i>':'<i class="fa fa-check"></i>'?> <?=$c[text]?></label></li>
								<? endforeach; ?>
							</ul>
						</form>
					</div>
				</div>
			</div>
			<? endif; ?>

			<div class="footer-col">
				<div class="copyright">
					&copy; <?=date('Y')?> <?=TITLE?> &nbsp;&nbsp; <?=__('Minden jog fenntartva!')?>
				</div>
			</div>
		</div>
	</div>
</div>
