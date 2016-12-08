<div id="top">
	<div class="page_width">
		<div class="top-row">
			<div class="buttons top-col float-left">
				<? if( strpos( $_SERVER[REQUEST_URI], '/webshop' ) === 0 ): ?>
					<? foreach( $this->all_category as $cats ): if($cats['filter_show'] == 0) continue; ?>
						<a href="/webshop/<?=($this->s_collection && $this->s_collection != 'product')?$this->s_collection:'-'?>/<?=$cats['name']?>" class="<?=$cats['keycode']?> <?=($this->s_category  == $cats['name'])?'on':''?>">
							<div><?=$cats['name']?></div>
						</a>
					<? endforeach; ?>
				<? else: ?>
					<? foreach( $this->all_category as $cats ): if($cats['filter_show'] == 0) continue; ?>
						<a href="/collections/<?=$cats['name']?>/<?=($this->gets[2] == 'more')?'more/':''?><?=Helper::currentPageNum()?>" class="<?=$cats['keycode']?> <?=($this->gets[1]  == $cats['name'])?'on':''?>">
							<div><?=$cats['name']?></div>
						</a>
					<? endforeach; ?>
				<? endif; ?>
			</div>

			<div class="mobilDevice bar top-col">
				<button class="md-deviceMenu viewBoxHandler mobilMenu"  style="height:50px;" vbox="mobilMenu"><i class="fa fa-bars"></i></button>
				<div class="md-deviceMenuContent viewBox box-mobilMenu">
					<div class="smart-language">
						<ul>
							<? foreach( $this->settings['avaiable_languages'] as $lang ): ?><li
							class="<?=(Lang::getLang() == $lang['code'])?'on':''?>"><a href="/language/<?=$lang['code']?>"><?=ucfirst($lang['name'])?></a>
						</li><? endforeach;?>
						</ul>
					</div>
					<? if( Lang::getLang() == 'en' ):
					$cur = Lang::getActiveCurrency();
					$acur = strtoupper($cur['code']);
					?>
					<div class="currencies" style="padding: 8px 0;">
						<a style="float:left; width: 50%; text-align:center;" class="<?=('EUR' == $acur)?'on':''?>" title="EUR" href="/currency/EUR">&euro; EUR</a>
						<a style="float:left; width: 50%; text-align:center;" class="<?=('USD' == $acur)?'on':''?>" title="USD" href="/currency/USD">$ USD</a>
						<div class="clr"></div>
					</div>
					<? endif; ?>
					<ul>
					<? if($this->user): ?>
						<li><a href="/user"><i class="fa fa-bars"></i> <?=__('Adatlap')?></a></li>
						<? else: ?>
						<li><a href="/login"><i class="fa fa-sign-in"></i> <?=__('Bejelentkezés')?></a></li>
						<? endif; ?>
						<li class="divider"></li>
					</ul>
					<ul class="smartDevice smartDevice-menu-list">
						<li class="header"><?=__('Menü')?></li>
						<? foreach($this->topMenu as $d): ?>
							<li class="<?=(($_SERVER[REQUEST_URI] == $d[link]) || ($d[link] != '/' && strpos($_SERVER[REQUEST_URI], $d[link]) === 0))?'on':''?> <?=($d[link] == '/webshop')?'ws':''?>"><a href="<?=$d[link]?>"><i class="fa fa-external-link"></i> <?=$d[text]?></a></li>
						<? endforeach; ?>
						<li class="divider"></li>
					</ul>
					<ul>
						<? foreach( $this->all_category as $cats ): ?>
							<li>
								<a href="/collections/<?=$cats['name']?>/<?=Helper::currentPageNum()?>" class="<?=$cats['keycode']?> <?=($this->gets[1]  == $cats['name'])?'on':''?>">
								<i class="fa fa-arrow-circle-right"></i> <?=$cats['name']?></a>
							</li>
						<? endforeach; ?>

						<? if($this->user): ?>
						<li class="divider"></li>
						<li><a href="/user/logout"><i class="fa fa-sign-out"></i> <?=__('Kijelentkezés')?></a></li>
						<? endif; ?>
					</ul>
				</div>
			</div>

			<div class="menu-list top-col">
				<ul>
					<? foreach($this->topMenu as $d): ?>
						<li class="<?=(($_SERVER[REQUEST_URI] == $d[link]) || ($d[link] != '/' && strpos($_SERVER[REQUEST_URI], $d[link]) === 0) || ($_SERVER[REQUEST_URI] == '/' && strpos($d[link],'/collections') === 0))?'on':''?> <?=($d[link] == '/webshop')?'ws':''?>"><a href="<?=$d[link]?>"><?=$d[text]?></a>

							<? if( !empty( $d['sub']) ):?>
							<ul class="sub">
							<? foreach( $d['sub'] as $sub ):  ?>
								<li><a href="<?=$sub['link']?>"><?=$sub['text']?></a></li>
							<? endforeach; ?>
							</ul>
							<? endif;?>
						</li>
					<? endforeach; ?>
				</ul>
			</div>

			<div class="mobilDevice top-col">
				<button class="cart viewBoxHandler" vbox="cart" title="<?=__('Kosár')?>">
					<div id="cart-item-num-v" class="cart-item-num-v"></div>
					<div class="v"><i class="fa fa-shopping-cart"></i></div>
				</button>
			</div>

			<div class="buttons top-col" style="width:130px;">
				<button class="cart viewBoxHandler" vbox="cart" title="<?=__('Kosár')?>">
					<div id="cart-item-num-v" class="cart-item-num-v"></div>
					<div class="v">&nbsp;</div>
				</button>
				<button class="users viewBoxHandler" vbox="users">
					<? if(!$this->user): ?>
					<div class="v"><?=__('Belépés')?></div>
					<? else: ?>
					<div class="v"><strong><?=$this->user[data][szam_firstname]?></strong></div>
					<? endif; ?>
				</button>
			</div>

			<div class="language top-col">
				<div>
				<? foreach( $this->settings['avaiable_languages'] as $lang ): ?>
					<a class="<?=($lang['code'] == Lang::getLang())?'on':''?>" title="<?=ucfirst($lang['name'])?>" href="/language/<?=$lang['code']?>"><?=$lang['code_show']?></a>
				<? endforeach; ?>
				</div>
				<? if( Lang::getLang() == 'en' ):
				$cur = Lang::getActiveCurrency();
				$acur = strtoupper($cur['code']);  ?>
				<div class="currencies">
					<a class="<?=('EUR' == $acur)?'on':''?>" title="EUR" href="/currency/EUR">&euro;</a>
					<a class="<?=('USD' == $acur)?'on':''?>" title="USD" href="/currency/USD">$</a>
				</div>
				<? endif; ?>
			</div>

			<div class="socials top-col">
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
		</div>

		<div class="dp-wrapper">
			<div class="viewBox box-cart">
				<form action="/user/purchase" method="post" onsubmit="var c = confirm('<?=__('Biztos benne, hogy megrendeli a kosárban lévő termékeket?')?>'); if(c){return true;}else{return false;}">
					<div id="cartContent" class="cartContent"></div>
					<div id="cart-item-price-origins" style="display: none;">
						<div class="cartDivider"></div>
						<div class="cartInfo tbl">
							<div class="tbl-cell col-40 left"><?=__('Eredeti ár')?></div>
							<div class="tbl-cell col-60 right price origin"><span class="code"><?=Lang::getPriceCode()?></span> <span id="cart-item-originprices" class="cart-item-prices">0</span></div>
						</div>
					</div>
					<div class="cartDivider"></div>
					<div class="cartInfo tbl">
						<div class="tbl-cell col-40 left"><?=__('Összesen')?></div>
						<div class="tbl-cell col-60 right price"><span class="code"><?=Lang::getPriceCode()?></span> <span id="cart-item-prices" class="cart-item-prices">0</span></div>
					</div>
					<div class="cartDivider"></div>
					<div class="cartBody">
						<a href="/page/postage-delivery" class="postageInfo bbtn"><?=__('Szállítási információk')?></a>
						<!-- ÚJ  -->
						<div class="tbl" style="width:95%; margin:0 auto;">
							<div class="tbl-cell col-50">
								<div class="coupon">
									<div class="tbl-cell col-100 title"><?=__('Kupon kód')?></div>
									<div class="tbl-cell col-100"><input type="text" class="form-control" name="couponKey" /></div>
								</div>
							</div>
							<div class="tbl-cell col-50">
								<div class="payMethod">
									<div class="tbl-cell col-100 title"><?=__('Fizetési mód')?>
										<a href="<?=SIMPLE_PAY_TERM_URL?>" target="_blank">
											<img src="<?=IMG?>payu_logo_small.png" height="12" alt="Simple - Online bankkártyás fizetés" title="Simple - Online bankkártyás fizetés" />
										</a>
									</div>
									<div class="tbl-cell col-100">
										<select name="payMethod" class="form-control">
											<option value="Bankkártya"><?=__('Bankkártya')?></option>
											<!-- <option value="PayPal"><?=__('PayPal')?></option>-->
											<option value="Utánvétel"><?=__('Utánvétel')?></option>
										</select>
									</div>
								</div>
							</div>
						</div>

						<div class="tbl" style="width:95%; margin:0 auto;">
							<div class="tbl-cell col-100">
								<div class="giftcards">
									<a href="/giftcards"><i class="fa fa-gift"></i> <?=__('Ajándékkártya aktiválás')?></a>
								</div>
							</div>
						</div>

						<div class="title"><?=__('Megjegyzés (pl.: gyűrű méret)')?></div>
						<div>
							<textarea name="comment"></textarea>
						</div>
						<div class="information-box">
							<? if($this->user): ?>
							<div class="tbl">
								<div class="tbl-cell col-100 center">
									<input type="submit" name="purchase" class="purchase" value="<?=__('Vásárlás')?>" />
								</div>
							</div>

							<div class="tbl">
								<div class="tbl-cell col-50 center user">
									<strong><?=$this->user[data][szam_firstname]?></strong>, <?=$this->user[data][szam_lastname]?>
								</div>
								<div class="tbl-cell col-50 center profil">
									<a href="/user">Profil <img src="/images/sun.png" height="18" alt="" /></a>
								</div>
							</div>
							<div class="tbl">
								<div class="tbl-cell col-50 center">
									<a href="javascript:void(0);" onclick="Cart.clear();"><strong><?=__('Kosár törlése')?></strong></a>
								</div>
								<div class="tbl-cell col-50 center">
									<a href="/user/logout"><strong><?=__('Kijelentkezés')?></strong></a>
								</div>
							</div>
							<? else: ?>
							<div class="tbl">
								<div class="tbl-cell col-100 center login">
									<a href="/login"><strong><?=__('Bejelentkezés vásárláshoz')?> <i class="fa fa-sign-in"></i></strong></a>
								</div>
							</div>
							<? endif; ?>
						</div>
						<!-- E: ÚJ -->
					</div>
				</form>
			</div>

			<? if(!$this->user): ?>
			<div class="viewBox box-users">
				<div class="userLogin">
					<h4><?=__('Belépés')?></h4>
					<form action="/login" method="post">
						<div class="title"><?=__('E-mail')?></div>
						<div><input type="email" class="form-control" name="login_email" /></div>
						<div class="title"><?=__('Jelszó')?></div>
						<div><input type="password" class="form-control" name="login_pw" /></div>
						<div class="tbl">
							<div class="tbl-cell left cell-btn">
								<a href="/register"><?=__('Regisztráció')?></a>
							</div>
							<div class="tbl-cell right">
								<input type="submit" name="loginUser" class="btn btn-default btn-sm" value="<?=__('Belépés')?>" />
							</div>
						</div>
					</form>
				</div>
			</div>
			<? else: ?>
			<div class="viewBox box-users">
				<div class="userLogin">
					<div class="center welcome">
						<?=__('Üdvözöljük,')?> <strong><?=$this->user[data][szam_firstname]?>, <?=$this->user[data][szam_lastname]?></strong>!
					</div>
					<div class="divider"></div>
					<div class="tbl">
						<div class="tbl-cell">
							<a href="/user"><i class="fa fa-user"></i> <?=__('adatlap')?></a>
						</div>
						<div class="tbl-cell right">
							<a href="/user/logout"><?=__('kijelentkezés')?> <i class="fa fa-times"></i></a>
						</div>
					</div>
				</div>
			</div>
			<? endif; ?>

		</div>

	</div>

</div>
