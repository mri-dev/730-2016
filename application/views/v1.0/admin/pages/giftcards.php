<? if($this->gets[2] == 'add'): ?>
	<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"> vissza</i></a>
	</div>
	<h1>Ajándékkártyák / <em>Új kártya létrehozása</em></h1>
	<?=$this->error_msg?>
	<div>
		<div class="p10">
		<form action="" method="post">
			<div class="row">
				<div class="col-sm-2"><em class="bw">Ajándékkártya kódja*</em></div>
				<div class="col-sm-4">
					<input type="text" class="form-control" value="<?=$_POST[code]?>" name="code" placeholder="max. 12 alfanumerikus számjegy"  />
				</div>
			</div>
			<br />
			<div class="row rtg">
				<div class="col-sm-2"><em class="bw">Lejárati idő*</em></div>
				<div class="col-sm-2">
					<input type="datetime-local" class="form-control" value="<?=$_POST[expired]?>" name="expired" />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Kártya értéke (HUF)*</em></div>
				<div class="col-sm-1">
					<input type="number" min="0" class="form-control" value="<?=($this->err)?$_POST[amount_huf]:0?>" name="amount_huf"  />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Kártya értéke (EUR)*</em></div>
				<div class="col-sm-1">
					<input type="number" min="0" class="form-control" value="<?=($this->err)?$_POST[amount_eur]:0?>" name="amount_eur"  />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Kártya értéke (USD)*</em></div>
				<div class="col-sm-1">
					<input type="number" min="0" class="form-control" value="<?=($this->err)?$_POST[amount_usd]:0?>" name="amount_usd"  />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-12 left">
					<button type="submit" name="addGiftcard" class="btn btn-primary">Létrehozás <i class="fa fa-check"></i></button>
				</div>
			</div>
		</form>
		</div>
	</div>
<? elseif($this->gets[2] == 'del'): ?>
	<div class="panel panel-default">
		<div class="panel-heading"><h2>Művelet megerősítése</h2></div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-9 col-btn">
					Biztos benne, hogy törölni szeretné a(z) <strong class="txt-big"><?=$this->giftcard[code]?></strong> Ajándékkártyat? A művelet nem visszavonható!
				</div>
				<div class="col-sm-3" align="right">
					<form action="" method="post">
						<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-danger">NEM</a>
						<button class="btn btn-success" type="submit" name="delGiftcard">IGEN</button>
					</form>
				</div>
			</div>

		</div>
	</div>
<? elseif($this->gets[2] == 'edit'): ?>
<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"> vissza</i></a>
	</div>
	<h1>Ajándékkártya szerkesztése</em></h1>
	<?=$this->error_msg?>
	<div>
		<div class="p10">
		<form action="" method="post">
			<div class="row">
				<div class="col-sm-2"><em class="bw">Ajándékkártya kódja*</em></div>
				<div class="col-sm-4">
					<input type="text" class="form-control" value="<?=$this->giftcard[code]?>" name="code" placeholder="max. 12 alfanumerikus karakter"  />
				</div>
			</div>
			<br />
			<br />
			<div class="row rtg">
				<div class="col-sm-2"><em class="bw">Lejárati idő*</em></div>
				<div class="col-sm-2">
					<input type="datetime-local" class="form-control" value="<?=str_replace(' ','T',$this->giftcard[expired])?>" name="expired" />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Kártya értéke (HUF) *</em></div>
				<div class="col-sm-1">
					<input type="number" min="0" class="form-control" value="<?=$this->giftcard[amount_huf]?>" name="amount_huf"  />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Kártya értéke (EUR)</em></div>
				<div class="col-sm-1">
					<input type="number" min="0" class="form-control" value="<?=$this->giftcard[amount_eur]?>" name="amount_eur"  />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Kártya értéke (USD)</em></div>
				<div class="col-sm-1">
					<input type="number" min="0" class="form-control" value="<?=$this->giftcard[amount_usd]?>" name="amount_usd"  />
				</div>
			</div>
			<br />
			<div class="row">
				<div class="col-sm-12 left">
					<button type="submit" name="editGiftcard" class="btn btn-primary">Változások mentése <i class="fa fa-check"></i></button>
				</div>
			</div>
		</form>
		</div>
	</div>
<? else: ?>
	<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>/add" class="btn btn-primary btn-md">új ajándékkártya <i class="fa fa-plus-circle"></i></a>
	</div>
	<h1>Ajándékkártyák</h1>
	<table class="table aria table-bordered">
		<thead>
			<tr>
				<th>Ajándékkártya kódja</th>
				<th>Értéke (HUF)</th>
				<th>Értéke (EUR)</th>
				<th>Értéke (USD)</th>
				<th>Lejárati idő</th>
				<th width="20">Felhasználták</th>
				<th width="50"><i class="fa fa-gears"></i></th>
			</tr>
		</thead>
		<tbody>
			<? foreach($this->coupons[data] as $d): ?>
			<tr>
				<td class="center"><strong><?=strtoupper($d[code])?></strong></td>
				<td class="center"><?=$d[amount_huf]?> Ft</td>
				<td class="center">€<?=$d[amount_eur]?></td>
				<td class="center">$<?=$d[amount_usd]?></td>
				<td class="center"><?=$d[expired]?></td>
				<td class="center"><?=($d[when_used])?$d[when_used]:'NEM'?></td>
				<td class="center">
					<a title="Szerkesztés" href="<?=ADMROOT?>/<?=$this->gets[1]?>/edit/<?=$d[ID]?>"><i class="fa fa-pencil"></i></a> &nbsp;
					<a title="Törlés" href="<?=ADMROOT?>/<?=$this->gets[1]?>/del/<?=$d[ID]?>"><i class="fa fa-times"></i></a>
				</td>
			</tr>
			<? endforeach; ?>
		</tbody>
	</table>
<? endif; ?>
