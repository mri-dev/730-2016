<? if($this->gets[2] == 'o'): ?>
	<div style="float:right;">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> vissza</a>
	</div>
	<h1><em>Megrendelések /</em> <?=$this->order[userName]?> / <?=$this->gets[3]?> megrendelés</h1>
	<div class="divider"></div>
	<?=$this->error_msg?>
	<div>
		<h2>Megrendelő</h2>
		<br />
		<div class="row userInfo">
			<div class="col-sm-4">
				<h3>Számlázási adatok</h3>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Vezetéknév</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_firstname]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Keresztnév</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_lastname]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Cégnév</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_company]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Adószám</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_vat]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Ország</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_country]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Megye/Állam</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_state]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Város</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_city]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Utca, házszám</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_address]?> <?=$this->order[userData][data][szam_housenumber]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Telefonszám</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szam_phone]?></strong></div>
				</div>
			</div>
			<div class="col-sm-4">
				<h3>Szállítási adatok</h3>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Vezetéknév</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_firstname]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Keresztnév</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_lastname]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Cégnév</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_company]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Adószám</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_vat]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Ország</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_country]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Megye/Állam</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_state]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Város</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_city]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Utca, házszám</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_address]?> <?=$this->order[userData][data][szall_housenumber]?></strong></div>
				</div>
				<div class="tbl">
					<div class="tbl-cell col-40"><em>Telefonszám</em></div>
					<div class="tbl-cell"><strong><?=$this->order[userData][data][szall_phone]?></strong></div>
				</div>
			</div>
			<div class="col-sm-4">
				<?
					$total = 0;
					foreach($this->order[orderedItems] as $i){
						$total += $i[totalPrice];
					}
				?>
				<h3>Megrendelés adatok</h3>
				<form action="" method="post">
					<br />
					<div class="tbl" style="font-size:1.3em;">
						<div class="tbl-cell col-40"><strong>Termékek ára</strong></div>
						<div class="tbl-cell">
							<? if($this->order[couponID]): ?>
							<strike style="font-size:0.8em;"><?=Helper::cashFormat($total)?> <?=strtoupper($this->order[priceCode])?> (bruttó)</strike>
							<div><strong><?=Helper::cashFormat($total - ($total / 100 * $this->order[couponRate]))?>  <?=strtoupper($this->order[priceCode])?> (bruttó)</strong></div>
							<? else: ?>
							<strong><?=Helper::cashFormat($total)?> <?=strtoupper($this->order[priceCode])?> (bruttó)</strong>
							<? endif; ?>
						</div>
					</div>
					<? if($this->order[couponID]): ?>
					<br />
					<div class="tbl">
						<div class="tbl-cell col-40"><em>Felhasznált kupon</em></div>
						<div class="tbl-cell">
							<strong><a href="<?=ADMROOT?>/coupons/edit/<?=$this->order[couponID]?>"><?=$this->order[couponName]?></a></strong> (<?=$this->order[couponRate]?>%)
						</div>
					</div>
					<? endif; ?>
					<? if($this->order[giftcard]['total'] != 0): ?>
					<br />
					<div class="tbl">
						<div class="tbl-cell col-40"><em>Felhasznált ajándékkártya</em></div>
						<div class="tbl-cell">
							<?php foreach ($this->order[giftcard]['data'] as $gc): ?>
								<strong><?=$gc['code']?></strong> / <?=$gc['verify_code']?> (-<?=$gc['price']?> <?=$this->order['priceCode']?>)
							<?php endforeach; ?>
						</div>
					</div>
					<? endif; ?>
					<br />
					<div class="tbl" style="font-size:1.3em;">
						<div class="tbl-cell col-40"><strong>Végösszeg</strong></div>
						<div class="tbl-cell">
							<strong><?=Helper::cashFormat($this->order['total_price'])?> <?=strtoupper($this->order[priceCode])?> </strong>
						</div>
					</div>
					<br />
					<div class="tbl">
						<div class="tbl-cell col-40"><em>Megjegyzés</em></div>
						<div class="tbl-cell">
							<em><?=$this->order[comment]?></em>
						</div>
					</div>
					<br />
					<div class="tbl">
						<div class="tbl-cell col-40"><em>Státusz</em></div>
						<div class="tbl-cell">
							<select name="status" class="form-control">
								<? foreach($this->orderStatus as $d): ?>
								<option value="<?=$d?>" <?=($d==$this->order[status])?'selected="selected"':''?>><?=$d?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="tbl">
						<div class="tbl-cell col-40"><em>Fizetési mód</em></div>
						<div class="tbl-cell">
							<select name="payMethod" class="form-control">
								<? foreach($this->payMethodes as $d): ?>
								<option value="<?=$d?>" <?=($d==$this->order[payMethod])?'selected="selected"':''?>><?=$d?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="tbl">
						<div class="tbl-cell col-40"><em>Fizetési állapot</em></div>
						<div class="tbl-cell">
							<select name="payu_state" class="form-control">
								<option value="" selected="selected">n.a.</option>
								<option value="" disabled="disabled"></option>
								<? foreach($this->payStatuses as $da => $d): ?>
								<option value="<?=$da?>" <?=($da==$this->order[payu_state])?'selected="selected"':''?>><?=$d[text]?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="tbl">
						<div class="tbl-cell col-40"><em>Szállítási költség (bruttó)</em></div>
						<div class="tbl-cell">
							<input type="text" class="form-control" name="transportPrice" value="<?=($this->order[transportPrice])?$this->order[transportPrice]:0?>" />
						</div>
						<div class="tbl-cell">
							<select name="transportPriceLang" class="form-control">
								<? foreach($this->transportCurrencies as $d): ?>
								<option value="<?=$d?>" <?=($d==$this->order[transportPriceLang])?'selected="selected"':''?>><?=strtoupper($d)?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="tbl">
						<div class="tbl-cell cell-100 right">
							<button type="submit" name="editOrder" class="btn btn-sm btn-success">Mentés <i class="fa fa-save"></i></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<br />
	<div class="divider"></div>
	<h2>Megrendelt termékek (<?=count($this->order[orderedItems])?>)</h2>
	<br />
	<div>
		<table class="table aria">
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
				<? foreach($this->order[orderedItems] as $i): ?>
				<tr>
					<td class="name"><strong><a title="Termék adatlap" href="<?=$i[url]?>"><?=$i[termekNev]?></a></strong></td>
					<td class="center"><?=$i[variationName]?></td>
					<td class="center"><?=$i[pcs]?> <?=__('db')?></td>
					<td class="price center"><span class="code"><?=strtoupper($this->order[priceCode])?></span> <?=Helper::cashFormat($i[price])?></td>
					<td class="price center"><span class="code"><?=strtoupper($this->order[priceCode])?></span> <strong><?=Helper::cashFormat($i[totalPrice])?></strong></td>
				</tr>
				<? endforeach; ?>
			</tbody>
		</table>
	</div>
	<br /><br /><br />
	<div class="divider"></div>
	<div>
		<a href="/admin/orders/del/<?=$this->gets[3]?>" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Megrendelés végleges törlése</a>
	</div>
<? elseif($this->gets[2] == 'del'): ?>
<div class="panel panel-default">
	<div class="panel-heading"><h2>Művelet megerősítése</h2></div>
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-9 col-btn">
				Biztos benne, hogy törölni szeretné a(z) <u><?=$this->gets[3]?></u> számú megrendelést? <br /> A művelet nem visszavonható!
			</div>
			<div class="col-sm-3" align="right">
				<form action="" method="post">
					<a href="<?=ADMROOT?>/orders/o/<?=$this->gets[3]?>" class="btn btn-danger">NEM</a>
					<button class="btn btn-success" type="submit" name="delOrder">IGEN</button>
				</form>
			</div>
		</div>

	</div>
</div>
<? else: ?>
	<h1>Megrendelések</h1>
	<table class="table table-bordered orderList aria">
		<thead>
			<tr>
				<th><?=__('Azonosító')?> / <?=__('Megrendelő')?></th>
				<th class="center"><?=__('Státusz')?></th>
				<th class="center"><?=__('Tétel')?></th>
				<th class="center"><?=__('Összesen')?></th>
				<th class="center"><?=__('Fiz. mód')?></th>
				<th class="center"><?=__('Fizetési státusz')?></th>
				<th class="center"><?=__('Száll. költség')?></th>
				<th class="center" width="100"><?=__('Kupon kód')?></th>
				<th class="center" width="50"><?=__('Pénznem')?></th>
				<th class="center sorting_desc"><?=__('Megrendelve')?></th>
			</tr>
		</thead>
		<tbody>
			<? foreach($this->orders as $d):
					$total = 0;
					$items = 0;
					foreach($d[items] as $i):
						$total += $i[totalPrice];
						$items += $i[pcs];
					endforeach;
			?>
			<tr>
				<td>

					<div style="font-size:18px;"><a href="<?=ADMROOT?>/orders/o/<?=$d[orderKey]?>" title="Megrendelés adatlapja"><?=$d[orderKey]?></a></div>
					<strong><a style="color:#888;" href="<?=ADMROOT?>/users/u/<?=$d[userID]?>" title="Felhasználó adatlapja"><?=$d[who]?></a></strong>
				</td>
				<td class="status center"><?=__($d[status])?></td>
				<td class="center"><?=$items?> <?=__('db')?></td>
				<td class="price center">
					<div>
						<?=Helper::cashFormat($d[total_price])?> <?=$d[priceCode]?>
					</div>
				</td>
				<td class="center"><?=__($d[payMethod])?></td>
				<td class="center" style="color:#888; font-size:12px;">
					<?
						switch($d[payMethod]){
							case 'Bankkártya':
								switch($d[payu_state]){
									default:
										echo '<span class="pay-status pay-no">'.__('Nincs fizetve').'</span>';
									break;
									case 'COMPLETE':
										echo '<span class="pay-status pay-success">'.__('Fizetve - Igazolva').'</span>';
									break;
									case 'IN_PROGRESS':
										echo '<span class="pay-status pay-in_progress">'.__('Fizetve - Igazolásra vár').'</span>';
									break;
									case 'PAYEMENT_AUTHORIZED':
										echo '<span class="pay-status pay-success">'.__('Fizetve - Visszaigazolva').'</span>';
									break;
								}
							break;
							case 'Utánvétel':
								if( is_null($d[payu_paid_time])){
									echo 'n.a.';
								}else{
									echo '<span style="color:green;">Fizetve</span>';

								}
							break;
						}
					?>
				</td>
				<td class="price center"><span class="code"><?=($d[transportPriceLang])?strtoupper($d[transportPriceLang]):Lang::getPriceCode()?></span> <?=($d[transportPrice])?$d[transportPrice]:0?></td>
				<td class="center">
				<? if($d[couponID]): ?>
					<a href="<?=ADMROOT?>/coupons/edit/<?=$d[couponID]?>"><?=$d[couponName]?></a> (-<?=$d[couponRate]?>%)
				<? else:?>
					-
				<? endif;?>
				</td>
				<td class="center"><?=strtoupper($d[priceCode])?></td>
				<td class="center"><?=Helper::softDate($d[orderedAt])?></td>
			</tr>
			<? endforeach; ?>
		</tbody>
	</table>
<? endif; ?>
