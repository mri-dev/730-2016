<script type="text/javascript">
	$(function(){
		$('.orders .each').click(function(){
			$('.orders .items').hide(0);
			$('.orders .items.i'+$(this).attr('oid')).slideDown(400);
		});
	})
</script>
<div class="page_content profil">
	<h1><?=__('Adataim')?></h1>
		<div class="pageSwitcher">
			<div page="pg1" class="<?=($this->gets[1] == '' || $this->gets[1] == 'purchase') ? 'active':''?>"><?=__('Alapadatok')?></div>
			<div page="szam" class="<?=($this->gets[1] == 'szam') ? 'active':''?>"><?=__('Számlázási adatok')?></div>
			<div page="szall" class="<?=($this->gets[1] == 'szall') ? 'active':''?>"><?=__('Szállítási adatok')?></div>
			<div page="settings" class="<?=($this->gets[1] == 'settings') ? 'active':''?>"><?=__('Beállítások')?></div>
		</div>

		<div class="pageView page-pg1 <?=($this->gets[1] == '' || $this->gets[1] == 'purchase') ? 'page-view-active':''?>">
			<div class="row">
				<div class="col-sm-6">
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Név')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_firstname]?>, <?=$this->user[data][szam_lastname]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('E-mail')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][email]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Regisztráció')?></em></div>
						<div class="tbl-cell"><strong><?=Helper::softDate($this->user[data][registeredAt])?></strong></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Megrendelések')?></em></div>
						<div class="tbl-cell"><strong><?=count($this->user[orders])?> <?=__('db megrendelés')?></strong></div>
					</div>
				</div>
			</div>
		</div>

		<div class="pageView page-szam <?=($this->gets[1] == 'szam') ? 'page-view-active':''?>">
			<div class="row">
				<div class="col-sm-6">
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Név')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_firstname]?>, <?=$this->user[data][szam_lastname]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Telefon')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_phone]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Cégnév')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_company]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Adószám')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_vat]?></strong></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Ország')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_country]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Állam/Megye')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_state]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Irányítószám')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_zipcode]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Város')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_city]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Utca, házszám')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szam_address]?> <?=$this->user[data][szam_housenumber]?></strong></div>
					</div>
				</div>
			</div>
		</div>

		<div class="pageView page-szall <?=($this->gets[1] == 'szall') ? 'page-view-active':''?>">
			<div class="row">
				<div class="col-sm-6">
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Név')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_firstname]?>, <?=$this->user[data][szall_lastname]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Telefon')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_phone]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Cégnév')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_company]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Adószám')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_vat]?></strong></div>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Ország')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_country]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Állam/Megye')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_state]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Irányítószám')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_zipcode]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Város')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_city]?></strong></div>
					</div>
					<div class="tbl">
						<div class="tbl-cell col-30"><em><?=__('Utca, házszám')?></em></div>
						<div class="tbl-cell"><strong><?=$this->user[data][szall_address]?> <?=$this->user[data][szall_housenumber]?></strong></div>
					</div>
				</div>
			</div>
		</div>
		<div class="pageView page-settings <?=($this->gets[1] == 'settings') ? 'page-view-active':''?>">
			<?=$this->settingsMsg?>
			<div class="row">
				<div class="col-sm-6">
					<br>
					<h4><?=__('Jelszó csere')?></h4>
					<?=$this->passwordChangeMsg?>
					<form action="/user/settings" method="post">
						<div class="row">
							<div class="col-sm-12">
								<input type="password" name="old" placeholder="<?=__('régi jelszó')?>" class="form-control"/>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-sm-12">
								<input type="password" name="new" placeholder="<?=__('új jelszó')?>" class="form-control"/>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-sm-12">
								<input type="password" name="new2" placeholder="<?=__('új jelszó ismét')?>" class="form-control"/>
							</div>
						</div>
						<br />
						<div class="row">
							<div class="col-sm-12">
								<button type="submit" name="changePassword" class="btn btn-default"/><i class="fa fa-save"></i> <?=__('Jelszó lecserélése')?></button>
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-6">
					<br>
					<h4><?=__('Fiók törlése')?></h4>
					<?=__('Fiókja törléséhez adja meg aktuális jelszavát. A fiók törlése nem visszavonható!')?>
					<br><br>
					<form action="/user/settings" method="post" onsubmit="var c = confirm( '<?=__('Biztos benne, hogy törli a fiókját?')?>' ); if(c){ return true; }else{return false;}">
						<div class="row">
							<div class="col-sm-12">
								<div class="input-group">
								<input type="password" name="password" placeholder="<?=__('Adja meg a jelszavát...')?>" class="form-control"/>
								<span class="input-group-btn">
									<button class="btn btn-danger" name="deleteAccount"><?=__('Végleges törlés')?> <i class="fa fa-trash-o"></i></button>
								</span>
								</div>
							</div>
						</div>
					</form>
				</div>
				<div class="col-sm-6"></div>
			</div>
		</div>
		<br />
		<div class="divider"></div>
		<h3><?=__('Megrendeléseim')?> (<?=count($this->user[orders])?>)</h3>
		<?
			if ( $this->gets[1] == 'pay' && $this->gets[2] == 'simple' ) {
			?>
			<div class="payu-trans">
				<div class="head">
					<?=__('Kártyás fizetés indítása')?>: <?=base64_decode($this->gets[3])?>
				</div>
				<?=$this->pay_form?>
				<br>
				<a href="<?=SIMPLE_PAY_TERM_URL?>" target="_blank">
						<img src="/images/simple_logo_long.png" height="18" alt="Simple - Online bankkártyás fizetés" title="Simple - Online bankkártyás fizetés"></a>
			</div>
			<?
			} else if( $this->gets[1] == 'pay' && $this->gets[2] == 'paypal' ){
			?>
			<div class="payu-trans">
				<div class="head">
					<?=__('Fizetés PayPal-al')?>: <?=base64_decode($this->gets[3])?>
				</div>
					<form method="post" action="https://www.paypal.com/cgi-bin/webscr" class="paypal-button" target="_top">
						<div class="hide" id="errorBox"></div>
						<input type="hidden" name="invoice" value="<?=base64_decode($this->gets[3])?>">
						<input type="hidden" name="button" value="buynow">

						<input type="hidden" name="item_name" value="www.diuss.hu <?=__('online rendelés')?>: <?=base64_decode($this->gets[3])?>">
						<input type="hidden" name="on0" value="<?=__('Megjegyzés')?>">
						<input type="hidden" name="os0" value="<?=($this->order['comment']) ?: '-'?>">
						<input type="hidden" name="quantity" value="1">
						<input type="hidden" name="amount" value="<?=round($this->order['total_net_price'])?>">

						<input type="hidden" name="charset" value="UTF-8">
						<input type="hidden" name="currency_code" value="<?=$this->order['priceCode']?>">
						<input type="hidden" name="shipping" value="<?=round(($this->order['transportPrice']) ?: 0)?>">
						<input type="hidden" name="tax" value="<?=round($this->order['total_tax'])?>">
						<input type="hidden" name="discount_amount" value="<?=$this->order['total_discount']?>">

						<input type="hidden" name="notify_url" value="<?=DOMAIN?>paypal/ipn">
						<input type="hidden" name="return" value="<?=DOMAIN?>user/pay/paypal_success/<?=$this->gets[3]?>">
						<input type="hidden" name="cancel_return" value="<?=DOMAIN?>user/">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="business" value="<?=PAYPAL_MERCHANT_ID?>">
						<input type="hidden" name="bn" value="JavaScriptButton_buynow">
						<button type="submit" class="paypal-button large"><img src="<?=IMG?>ppcom.svg" height="15" alt="PayPal"> <?=__('Fizetés indítása')?></button></form>
				</div>
			<?
			} else if( $this->gets[1] == 'order' && $this->gets[2] == 'cancel' ){
			?>
			<div class="cancel-order-view">
				<h4><?=__('Megrendelés lemondása / törlése')?></h4>
				<div class="last-alert-msg">
					<form method="post" action="">
					<input type="hidden" name="delorder" value="<?=$this->gets[3]?>">
					<strong><?=sprintf(__('Biztos benne, hogy törli a(z) %s számú megrendelését?'), $this->order_data[orderKey])?></strong>
					<div class="ab-btn"><a href="/user" class="btn btn-danger btn-sm"><?=__('nem')?></a> <button class="btn btn-success btn-sm"><?=__('igen')?></button></div>
					</form>
				</div>
			</div>
			<?
			}
		?>
		<?=$this->order_msg?>
		<div class="orders">
			<table class="table">
				<thead>
					<tr>
						<th><?=__('Azonosító')?></th>
						<th class="center"><?=__('Státusz')?></th>
						<th class="center"><?=__('Tétel')?></th>
						<th class="center"><?=__('Összesen')?></th>
						<th class="center"><?=__('Fiz. mód')?></th>
						<th class="center"><?=__('Száll. költség')?></th>
						<th class="center" width="40"><?=__('Kupon')?></th>
						<th class="center"><?=__('Megrendelve')?></th>
						<th class="center"></th>
					</tr>
				</thead>
				<tbody>
					<? if($this->user[orders] > 0): foreach($this->user[orders] as $d):
						$total = 0;
						$items = 0;
						foreach($d[items] as $i):
							$total += $i[totalPrice];
							$items += $i[pcs];
						endforeach;
					?>
					<tr class="each" oid="<?=$d[ID]?>" title="<?=__('Kattintson a részletekért')?>">
						<td>
							<strong><?=$d[orderKey]?></strong>
							<div>
								<? switch( $d[payMethod] ) {
									// PayU - Credit Card
									case 'Bankkártya': ?>
										<?
										// Fizetve státus
										if( $d[payu_state] == 'COMPLETE' ):?>
										<span class="paid_by_cart"><i class="fa fa-check"></i> <?=__('Fizetve')?></span>
										<?
										// Feldolgozás alatt
										elseif( $d[payu_state] != 'COMPLETE' && !is_null($d[payu_state]) && $d[payu_state] != '' ): ?>
											<span class="paid_by_cart in_progress"><i class="fa fa-refresh"></i> <?=__('Fizetés feldolgozás alatt.')?></span>
										<?
										// Ha még nincs fizetve
										elseif( is_null($d[payu_state]) || $d[payu_state] == ''): ?>
											<table cellpadding="3">
												<tbody>
													<tr>
														<td><a title="<?=__('Kattintson a kártyás fizetéshez')?>" href="/user/pay/simple/<?=base64_encode($d[orderKey])?>" class="pay_by_cart"><img src="<?=IMG?>payu_logo_small.png" height="15"/></a> &nbsp;</td>
														<td>&nbsp;<a title="<?=__('Kattintson a kártyás fizetéshez')?>" href="/user/pay/simple/<?=base64_encode($d[orderKey])?>" class="pay_by_cart"><?=__('Kártyás fizetés indítása')?></a></td>
													</tr>
												</tbody>
											</table>
										<? endif; ?>
									<? break;
									// PayPal
									case 'PayPal': ?>
										<?
										// Fizetve státus
										if( $d[payu_state] == 'COMPLETE' ):?>
										<span class="paid_by_cart"><i class="fa fa-check"></i> <?=__('Fizetve')?></span>
										<?
										// Feldolgozás alatt
										elseif( $d[payu_state] != 'COMPLETE' && !is_null($d[payu_state]) ): ?>
											<span class="paid_by_cart in_progress"><i class="fa fa-refresh"></i> <?=__('Fizetés feldolgozás alatt.')?></span>
										<?
										// Ha még nincs fizetve
										elseif( is_null($d[payu_state])): ?>
											<table cellpadding="3">
												<tbody>
													<tr>
														<td><a title="<?=__('Fizetés PayPal-al')?>" href="/user/pay/paypal/<?=base64_encode($d[orderKey])?>" class="pay_by_cart"><img src="<?=IMG?>ppcom.svg" height="15"/></a> &nbsp;</td>
														<td>&nbsp;<a title="<?=__('Fizetés PayPal-al')?>" href="/user/pay/paypal/<?=base64_encode($d[orderKey])?>" class="pay_by_cart"><?=__('Fizetés PayPal-al')?></a></td>
													</tr>
												</tbody>
											</table>
										<? endif; ?>
									<? break;
									// Utánvétel
									case 'Utánvétel': ?>
									<? break;
								}?>
							</div>
						</td>
						<td class="status center"><?=__($d[status])?></td>
						<td class="center"><?=$items?> <?=__('db')?></td>
						<td class="price center">
							<span class="code"><?=$d[priceCode]?></span> <?=Helper::cashFormat($d['total_price'])?>
						</td>
						<td class="center"><?=__($d[payMethod])?></td>

						<td class="price center">
							<span class="code"><?=($d[transportPriceLang])?strtoupper($d[transportPriceLang]):Lang::getPriceCode()?></span> <?=($d[transportPrice])?$d[transportPrice]:0?>
						</td>
						<td class="center">
						<? if($d[couponID]): ?>
							<?=$d[couponName]?> (-<?=$d[couponRate]?>%)
						<? else:?>
							-
						<? endif;?>
						</td>
						<td class="center"><?=Helper::softDate($d[orderedAt])?></td>
						<td class="center">
							<? if( $d['status'] == 'Feldolgozásra vár' && $d[payu_state] != 'COMPLETE' ): ?>
							<a href="/user/order/cancel/<?=md5($d[orderKey].'_'.$d[ID])?>" class="cancel-order"><?=__('Megrendelés lemondása')?></a>
							<? endif; ?>
						</td>
					</tr>
					<tr class="items i<?=$d[ID]?>">
						<td colspan="20">
							<table class="table">
								<thead>
									<tr>
										<th><?=__('Terméknév')?></th>
										<th class="center"><?=__('Variáció')?></th>
										<th class="center"><?=__('Me.')?></th>
										<th class="center"><?=__('Egységár')?></th>
										<th class="center"><?=__('Összeg')?></th>
									</tr>
								</thead>
								<tbody>
									<? foreach($d[items] as $i): ?>
									<tr>
										<td class="name"><strong><a href="<?=$i[url]?>"><?=$i[termekNev]?></a></strong></td>
										<td class="center"><?=$i[variationName]?></td>
										<td class="center"><?=$i[pcs]?> <?=__('db')?></td>
										<td class="price center"><span class="code"><?=$d[priceCode]?></span> <?=Helper::cashFormat($i[price])?></td>
										<td class="price center"><span class="code"><?=$d[priceCode]?></span> <strong><?=Helper::cashFormat($i[totalPrice])?></strong></td>
									</tr>
									<? endforeach; ?>
								</tbody>
							</table>
							<div class="divider"></div>
							<div class="comment"><?=__('Megjegyzés')?></div>
							<div><?=($d[comment] != '')?$d[comment]:__('nincs megjegyzés')?></div>
							<?php if ($d['giftcard']['total'] != 0): ?>
								<div class="divider"></div>
								<div class="comment"><?=__('Felhasznált ajándékkártyák')?></div>
								<?php foreach ($d['giftcard']['data'] as $g): ?>
									- <?=$g['code']?> / <?=$g['verify_code']?> &nbsp;&nbsp; -<?=$g['price']?> <?=$d['priceCode']?>
								<?php endforeach; ?>
							<?php endif; ?>
						</td>
					</tr>
					<? endforeach; else: ?>
						<tr>
							<td colspan="20">
								<div><em><?=__('Nincsennek rendeléseim.')?></em></div>
							</td>
						</tr>
					<? endif; ?>
				</tbody>
			</table>
		</div>
</div>
