<script type="text/javascript">
	function addVariation(){
		$('.productVariations .each.items:last').after('<div class="each items">'+$('.productVariations .each.items:last').html()+'</div>');
	}

	function showVariation(id){
		$('tr.variations').hide();

		$('tr.variations.p'+id).slideDown(400);
	}
</script>
<? if($this->gets[2] == 'add'): ?>
	<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"> vissza</i></a>
	</div>
	<h1>Termékek / <em>Új termék hozzáadása</em></h1>
	<?=$this->error_msg?>
	<div class="p10">
		<form action="" method="post" enctype="multipart/form-data">
				<div class="languageSwitcher">
					<? foreach( $this->settings['all_languages'] as $lang ):?>
					<div lang="<?=$lang['code']?>" class="<?=($lang['code'] == 'hu') ? 'active':''?>"><?=$this->settings['language_flag'][$lang['code']]?> <?=strtoupper($lang['code'])?> - <?=$lang['name']?></div>
					<? endforeach; ?>
				</div>
				<? foreach( $this->settings['all_languages'] as $lang ): ?>
				<div class="languageView lang-<?=$lang['code']?>  <?=($lang['code'] == 'hu') ? 'lang-view-active':''?>">
					<div>
						<?=$this->settings['language_flag'][$lang['code']]?> Jelenleg <strong><?=$lang['name']?></strong> nyelvi értékeket ad meg!
					</div>
					<div class="divider"></div>
					<div class="row">
						<div class="col-sm-2"><em class="bw">Termék név*</em></div>
						<div class="col-sm-5">
							<input type="text" class="form-control" value="<?=$_POST['name'][$lang['code']]?>" name="name[<?=$lang['code']?>]"  />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-2"><em class="bw">Kulcsszavak (SEO)</em></div>
						<div class="col-sm-10">
							<input type="text" class="form-control" value="<?=$_POST['keywords'][$lang['code']]?>" placeholder="pl.: kulcsszó1, kulcsszó2, kulcsszó3, stb..." name="keywords[<?=$lang['code']?>]"  />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-2"><em class="bw">Rövid ismertető</em></div>
						<div class="col-sm-10">
							<textarea name="content[<?=$lang['code']?>]"><?=$_POST['content'][$lang['code']]?></textarea>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-2">
							<em class="bw">Termék jellemzők</em>
							<div>
								<small>pl.: súly, anyag, méret</small>
							</div>
						</div>
						<div class="col-sm-10">
							<textarea name="parameters[<?=$lang['code']?>]"><?=$_POST['description'][$lang['code']]?></textarea>
						</div>
					</div>
				</div>
				<? endforeach; ?>
			<br />
			<div class="divider"></div>
			<br />
			<div class="row">
				<div class="col-sm-4 rpd">
					<div class="row">
						<div class="col-sm-4"><em class="bw">Kollekció kiválasztása*</em></div>
						<div class="col-sm-5">
							<select name="collection" class="form-control" id="">
								<option value="">-- kérjük válasszon --</option>
								<option value="" disabled="disabled"></option>
								<? foreach($this->collections[data] as $d): ?>
								<option value="<?=$d[ID]?>" <?=($this->err && $_POST[collection] == $d[ID])?'selected="selected"':''?>><?=$d[name_hu]?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Kategória kiválasztása*</em></div>
						<div class="col-sm-5">
							<select name="categoryID" class="form-control" id="">
								<option value="">-- kérjük válasszon --</option>
								<option value="" disabled="disabled"></option>
								<? foreach($this->categories[data] as $d): ?>
								<option value="<?=$d[ID]?>" <?=($this->err && $_POST[categoryID] == $d[ID])?'selected="selected"':''?>><?=$d[name_hu]?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Termék cikkszám*</em></div>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="<?=$_POST[productNumber]?>" name="productNumber"  />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">ÁFA (%)*</em></div>
						<div class="col-sm-3">
							<input type="number" class="form-control" value="<?=($_POST[afa])?$_POST[afa]:'27'?>" name="afa"  />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Legyen kedvenc</em></div>
						<div class="col-sm-3">
							<select name="is_favorite" class="form-control">
								<option value="0">Nem</option>
								<option value="1">Igen</option>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Legyen újdonság termék</em></div>
						<div class="col-sm-3">
							<select name="is_news" class="form-control">
								<option value="0">Nem</option>
								<option value="1">Igen</option>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Státusz</em></div>
						<div class="col-sm-3">
							<select name="showed" class="form-control">
								<option value="1">Látható</option>
								<option value="0">Rejtett</option>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4">
							<em class="bw">Termék képek</em> <br />
							<small class="txt-light">Fájltípus: .jpg</small><br />
							<small class="txt-light">Méret: max. 1MB</small>
						</div>
						<div class="col-sm-8">
							<input type="file" class="form-control" multiple="multiple" name="img[]" />
						</div>
					</div>
				</div>
				<div class="col-sm-8">
					<h3>Termék variációk</h3>
					<small>Az árakat nettó árban adja meg!</small>
					<div class="productVariations">
						<div class="each head">
							<? foreach( $this->settings['all_languages'] as $lang ):?>
							<div class="col-10">Elnevezés - <?=strtoupper($lang['code'])?></div>
							<? endforeach; ?>
							<div class="price col-10">Ár - HUF</div>
							<div class="price col-10">Ár - USD</div>
							<div class="price col-10">Ár - EUR</div>
							<div class="stock col-10">Készlet</div>
						</div>
						<? if($this->product[variations]) foreach($this->product[variations] as $v): ?>
							<div class="each">
								<? foreach( $this->settings['all_languages'] as $lang ):?>
								<div class="col-10"><?=$v['name_'.$lang['code']]?></div>
								<? endforeach; ?>
								<div class="price col-10"><?=$v[price_huf]?></div>
								<div class="price col-10"><?=$v[price_usd]?></div>
								<div class="price col-10"><?=$v[price_eur]?></div>
								<div class="stock col-10"><?=$v[stock]?></div>
								<div class="col-10">
									<a title="Szerkesztés" href="<?=ADMROOT?>/product_variation/edit/<?=$v[ID]?>"><i class="fa fa-pencil"></i></a>
									<a title="Törlés" href="<?=ADMROOT?>/product_variation/del/<?=$v[ID]?>/?return=<?=$_SERVER['REQUEST_URI']?>"><i class="fa fa-times"></i></a>
								</div>
							</div>
							<br />
						<? endforeach; ?>
						<? if($_POST[productVariation][name][hu][0] != ''):
							$s = -1;
							foreach($_POST[productVariation][name][hu] as $ps): $s++;
								if($ps == '') break;
								$s_price_huf = $_POST[productVariation][price][huf][$s];
								$s_price_usd = $_POST[productVariation][price][usd][$s];
								$s_price_eur = $_POST[productVariation][price][eur][$s];
								$s_stock = $_POST[productVariation][stock][$s];
							?>
							<div class="each items">
								<? foreach( $this->settings['all_languages'] as $lang ):?>
									<div class="col-10"><input type="text" class="form-control" placeholder="pl.: arany, ezüst" name="productVariation[name][<?=$lang['code']?>][]" value="<?=$_POST[productVariation][name][$lang['code']][$s]?>" /></div>
								<? endforeach; ?>
								<div class="price col-10"><input type="number"  value="<?=$s_price_huf?>" class="form-control" name="productVariation[price][huf][]" /></div>
								<div class="price col-10"><input type="number" value="<?=$s_price_usd?>" class="form-control" name="productVariation[price][usd][]" /></div>
								<div class="price col-10"><input type="number" value="<?=$s_price_eur?>" class="form-control" name="productVariation[price][eur][]" /></div>
								<div class="stock col-10"><input type="number" value="<?=$s_stock?>" class="form-control" name="productVariation[stock][]" /></div>

							</div>
							<? endforeach; ?>
						<? endif; ?>
						<div class="each items">
							<? foreach( $this->settings['all_languages'] as $lang ):?>
								<div class="col-10"><input type="text" class="form-control" placeholder="pl.: arany, ezüst" name="productVariation[name][<?=$lang['code']?>][]" /></div>
							<? endforeach; ?>
							<div class="price col-10"><input type="number" value="0" class="form-control" name="productVariation[price][huf][]" /></div>
							<div class="price col-10"><input type="number" value="0" class="form-control" name="productVariation[price][usd][]" /></div>
							<div class="price col-10"><input type="number" value="0" class="form-control" name="productVariation[price][eur][]" /></div>
							<div class="stock col-10"><input type="number" value="0" class="form-control" name="productVariation[stock][]" /></div>
						</div>
						<div class="addVariation"><a href="javascript:addVariation();"> <i class="fa fa-plus"></i> új variáció</a></div>
					</div>
				</div>
			</div>
			<br />
			<div class="divider"></div>
			<div class="row">
				<div class="col-sm-12" align="right">
					<button type="submit" class="btn btn-primary" name="addProduct">Létrehozás <i class="fa fa-check"></i></button>
				</div>
			</div>
		</form>
	</div>
<? elseif($this->gets[2] == 'del'): ?>
<div class="panel panel-default">
	<div class="panel-heading"><h2>Művelet megerősítése</h2></div>
	<div class="panel-body">
		<div class="row">
			<div class="col-sm-9 col-btn">
				Biztos benne, hogy törölni szeretné a(z) <strong class="txt-big"><?=$this->product[collection_name_hu]?> <?=$this->product[name_hu]?></strong> terméket és az összes termék variációt? <br /> A művelet nem visszavonható!
			</div>
			<div class="col-sm-3" align="right">
				<form action="" method="post">
					<a href="<?=ADMROOT?>/products" class="btn btn-danger">NEM</a>
					<button class="btn btn-success" type="submit" name="delProduct">IGEN</button>
				</form>
			</div>
		</div>

	</div>
</div>
<? elseif($this->gets[2] == 'edit'): ?>
	<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"> vissza</i></a>
	</div>
	<h1>Termék szerkesztése</h1>
	<?=$this->error_msg?>
	<div class="p10">
		<form action="" method="post" enctype="multipart/form-data">
			<div class="languageSwitcher">
				<? foreach( $this->settings['all_languages'] as $lang ):?>
				<div lang="<?=$lang['code']?>" class="<?=($lang['code'] == 'hu') ? 'active':''?>"><?=$this->settings['language_flag'][$lang['code']]?> <?=strtoupper($lang['code'])?> - <?=$lang['name']?></div>
				<? endforeach; ?>
			</div>
			<? foreach( $this->settings['all_languages'] as $lang ): ?>
			<div class="languageView lang-<?=$lang['code']?>  <?=($lang['code'] == 'hu') ? 'lang-view-active':''?>">
				<div>
						<?=$this->settings['language_flag'][$lang['code']]?> Jelenleg <strong><?=$lang['name']?></strong> nyelvi értékeket ad meg!
					</div>
					<div class="divider"></div>
				<div class="row">
					<div class="col-sm-2"><em class="bw">Termék név*</em></div>
					<div class="col-sm-5">
						<input type="text" class="form-control" value="<?=$this->product['name_'.$lang['code']]?>" name="name[<?=$lang['code']?>]"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Kulcsszavak (SEO)</em></div>
					<div class="col-sm-10">
						<input type="text" class="form-control" value="<?=$this->product['keywords_'.$lang['code']]?>" placeholder="pl.: kulcsszó1, kulcsszó2, kulcsszó3, stb..." name="keywords[<?=$lang['code']?>]"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Rövid ismertető</em></div>
					<div class="col-sm-10">
						<textarea name="content[<?=$lang['code']?>]"><?=$this->product['content_'.$lang['code']]?></textarea>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2">
						<em class="bw">Termék jellemzők</em>
						<div>
							<small>pl.: súly, anyag, méret</small>
						</div>
					</div>
					<div class="col-sm-10">
						<textarea name="parameters[<?=$lang['code']?>]"><?=$this->product['description_'.$lang['code']]?></textarea>
					</div>
				</div>
			</div>
			<? endforeach; ?>
			<br />
			<div class="divider"></div>
			<br />
			<div class="row">
				<div class="col-sm-4 rpd">
					<div class="row">
						<div class="col-sm-4"><em class="bw">Kollekció kiválasztása*</em></div>
						<div class="col-sm-5">
							<select name="collection" class="form-control" id="">
								<option value="">-- kérjük válasszon --</option>
								<option value="" disabled="disabled"></option>
								<? foreach($this->collections[data] as $d): ?>
								<option value="<?=$d[ID]?>" <?=($this->product[collectionID] == $d[ID])?'selected="selected"':''?>><?=$d[name_hu]?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Kategória kiválasztása*</em></div>
						<div class="col-sm-5">
							<select name="categoryID" class="form-control" id="">
								<option value="">-- kérjük válasszon --</option>
								<option value="" disabled="disabled"></option>
								<? foreach($this->categories[data] as $d): ?>
								<option value="<?=$d[ID]?>" <?=($this->product[categoryID] == $d[ID])?'selected="selected"':''?>><?=$d[name_hu]?></option>
								<? endforeach; ?>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Termék cikkszám*</em></div>
						<div class="col-sm-8">
							<input type="text" class="form-control" value="<?=$this->product[productNumber]?>" name="productNumber"  />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">ÁFA (%)*</em></div>
						<div class="col-sm-3">
							<input type="number" class="form-control" value="<?=($this->product[afa])?>" name="afa"  />
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Legyen kedvenc</em></div>
						<div class="col-sm-3">
							<select name="is_favorite" class="form-control">
								<option value="0" <?=($this->product[is_favorite] == '0')?'selected="selected"':''?>>Nem</option>
								<option value="1" <?=($this->product[is_favorite] == '1')?'selected="selected"':''?>>Igen</option>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Legyen újdonság termék</em></div>
						<div class="col-sm-3">
							<select name="is_news" class="form-control">
								<option value="0" <?=($this->product[is_news] == '0')?'selected="selected"':''?>>Nem</option>
								<option value="1" <?=($this->product[is_news] == '1')?'selected="selected"':''?>>Igen</option>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4"><em class="bw">Státusz</em></div>
						<div class="col-sm-3">
							<select name="showed" class="form-control">
								<option value="1" <?=($this->product[showed] == '1')?'selected="selected"':''?>>Látható</option>
								<option value="0" <?=($this->product[showed] == '0')?'selected="selected"':''?>>Rejtett</option>
							</select>
						</div>
					</div>
					<br />
					<div class="row">
						<div class="col-sm-4">
							<em class="bw">Termék képek feltöltése</em> <br />
							<small class="txt-light">Fájltípus: .jpg</small><br />
							<small class="txt-light">Méret: max. 1MB</small>
						</div>
						<div class="col-sm-8">
							<input type="file" class="form-control" multiple="multiple" name="img[]" />
						</div>
					</div>
				</div>
				<div class="col-sm-8">
					<h3>Termék variációk</h3>
					<small>Az árakat nettó árban adja meg!</small>
					<div class="productVariations">
						<div class="each head">
							<? foreach( $this->settings['all_languages'] as $lang ):?>
							<div class="col-10">Elnevezés - <?=strtoupper($lang['code'])?></div>
							<? endforeach; ?>
							<div class="price col-10">Ár - HUF</div>
							<div class="price col-10">Ár - USD</div>
							<div class="price col-10">Ár - EUR</div>
							<div class="stock col-10">Készlet</div>
						</div>
						<? foreach($this->product[variations] as $v): ?>
							<div class="each">
								<? foreach( $this->settings['all_languages'] as $lang ):?>
								<div class="col-10"><?=$v['name_'.$lang['code']]?></div>
								<? endforeach; ?>
								<div class="price col-10"><?=$v[price_huf]?></div>
								<div class="price col-10"><?=$v[price_usd]?></div>
								<div class="price col-10"><?=$v[price_eur]?></div>
								<div class="stock col-10"><?=$v[stock]?></div>
								<div class="col-10">
									<a title="Szerkesztés" href="<?=ADMROOT?>/product_variation/edit/<?=$v[ID]?>"><i class="fa fa-pencil"></i></a>
									<a title="Törlés" href="<?=ADMROOT?>/product_variation/del/<?=$v[ID]?>/?return=<?=$_SERVER['REQUEST_URI']?>"><i class="fa fa-times"></i></a>
								</div>
							</div>
							<br />
						<? endforeach; ?>
						<? if($_POST[productVariation][name][hu][0] != ''):
							$s = -1;
							foreach($_POST[productVariation][name][hu] as $ps): $s++;
								if($ps == '') break;
								$s_price_huf = $_POST[productVariation][price][huf][$s];
								$s_price_usd = $_POST[productVariation][price][usd][$s];
								$s_price_eur = $_POST[productVariation][price][eur][$s];
								$s_stock = $_POST[productVariation][stock][$s];
							?>
							<div class="each items">
								<? foreach( $this->settings['all_languages'] as $lang ):?>
									<div class="col-10"><input type="text" class="form-control" placeholder="pl.: arany, ezüst" name="productVariation[name][<?=$lang['code']?>][]" value="<?=$_POST[productVariation][name][$lang['code']][$s]?>" /></div>
								<? endforeach; ?>
								<div class="price col-10"><input type="number"  value="<?=$s_price_huf?>" class="form-control" name="productVariation[price][huf][]" /></div>
								<div class="price col-10"><input type="number" value="<?=$s_price_usd?>" class="form-control" name="productVariation[price][usd][]" /></div>
								<div class="price col-10"><input type="number" value="<?=$s_price_eur?>" class="form-control" name="productVariation[price][eur][]" /></div>
								<div class="stock col-10"><input type="number" value="<?=$s_stock?>" class="form-control" name="productVariation[stock][]" /></div>

							</div>
							<? endforeach; ?>
						<? endif; ?>
						<div class="each items">
							<? foreach( $this->settings['all_languages'] as $lang ):?>
								<div class="col-10"><input type="text" class="form-control" placeholder="pl.: arany, ezüst" name="productVariation[name][<?=$lang['code']?>][]" /></div>
							<? endforeach; ?>
							<div class="price col-10"><input type="number" value="0" class="form-control" name="productVariation[price][huf][]" /></div>
							<div class="price col-10"><input type="number" value="0" class="form-control" name="productVariation[price][usd][]" /></div>
							<div class="price col-10"><input type="number" value="0" class="form-control" name="productVariation[price][eur][]" /></div>
							<div class="stock col-10"><input type="number" value="0" class="form-control" name="productVariation[stock][]" /></div>
						</div>
						<div class="addVariation"><a href="javascript:addVariation();"> <i class="fa fa-plus"></i> új variáció</a></div>
					</div>
				</div>
			</div>
			<br />
			<div class="divider"></div>
			<br />
			<h3>Feltöltött képek (<?=count($this->product[images])?>)</h3>
			<br />
			<div>
				<div class="productImages">
				<? if(count($this->product[images]) > 0): foreach($this->product[images] as $d): ?>
					<div class="img">
						<img src="/<?=$d[image]?>" height="120" alt="" />
						<div class="functions">
							<label>Alapé.: <input type="radio" name="defaultImg" <?=($d[is_default] == '1')?'checked="checked"':''?> value="<?=$d[ID]?>" /></label><br />
							<label>Törlés: <input type="checkbox" name="delImage[]" value="<?=$d[ID]?>" /></label>
						</div>
					</div>
				<? endforeach; else: ?>
					<div class="p10 txt-light"><?=__('Nincs feltöltött kép a termékhez.')?></div>
				<? endif; ?>
				</div>
			</div>
			<br />
			<div class="divider"></div>
			<br />
			<div class="row">
				<div class="col-sm-12" align="right">
					<button type="submit" class="btn btn-success" name="saveProduct">Változások mentése <i class="fa fa-save"></i></button>
				</div>
			</div>
		</form>
	</div>
<? else: ?>
	<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>/add" class="btn btn-primary btn-md">új termék <i class="fa fa-plus-circle"></i></a>
	</div>
	<h1>Termékek</h1>
	<form action="" method="post">
	<div>

		<? if($_COOKIE[filtered]): ?>
			<strong><?=$this->products[info][total_num]?> db termék </strong>: <span style="color:red;">szűrt lista eredménye</span>
		<? else: ?>
			<strong><?=$this->products[info][total_num]?> db termék </strong>
		<? endif; ?>
	</div>
	<br />
	<table class="table table-bordered products">
		<thead>
			<tr>
				<th width="120">Cikkszám</th>
				<th width="120">Kollekció</th>
				<th>Elnevezése</th>
				<th>Kategória</th>
				<th width="50">Készlet</th>
				<th width="25">ÁFA</th>
				<th width="25">Kedvenc</th>
				<th width="25">Új</th>
				<th width="130">Módosítva</th>
				<th width="130">Hozzáadva</th>
				<th width="20" title="Státusz"><i class="fa fa-eye"></i></th>
				<th width="100"></th>
			</tr>
		</thead>
		<tbody>
			<tr class="search  <? if($_COOKIE[filtered] == '1'): ?>filtered<? endif;?>">
				<td>
					<input type="text" class="form-control <?=($_COOKIE[filter_productNumber])?'on':''?>" name="productNumber" value="<?=$_COOKIE[filter_productNumber]?>" />
				</td>
				<td>
					<select name="collection" class="form-control <?=($_COOKIE[filter_collection])?'on':''?>">
						<option value="">Összes</option>
						<option value="" disabled="disabled"></option>
						<? foreach($this->collections[data] as $d): ?>
						<option value="<?=$d[ID]?>" <?=($d[ID] == $_COOKIE[filter_collection])?'selected="selected"':''?>><?=$d[name_hu]?></option>
						<? endforeach; ?>
					</select>
				</td>
				<td>
					<input type="text" class="form-control <?=($_COOKIE[filter_name])?'on':''?>" name="name" value="<?=$_COOKIE[filter_name]?>" />
				</td>
				<td>
					<select name="category" class="form-control <?=($_COOKIE[filter_category])?'on':''?>">
						<option value="">Összes</option>
						<option value="" disabled="disabled"></option>
						<? foreach($this->categories[data] as $d): ?>
						<option value="<?=$d[ID]?>" <?=($d[ID] == $_COOKIE[filter_category])?'selected="selected"':''?>><?=$d[name_hu]?></option>
						<? endforeach; ?>
					</select>
				</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align="center">
					<? if($_COOKIE[filtered] != ''): ?>
					<a href="<?=ADMROOT?>/<?=$this->gets[1]?>/clearfilters" title="Szűrés eltávolítása" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
					<? endif; ?>
					<button title="Szűrőfeltételek alkalmazása" name="filterList" class="btn btn-default btn-sm"><i class="fa fa-search"></i></button>
				</td>
			</tr>
			<? if(count($this->products[data]) > 0): foreach($this->products[data] as $d): ?>
			<tr>
				<td align="center"><em><?=$d[productNumber]?></em></td>
				<td align="center"><?=$d[collection_name_hu]?></td>
				<td><?=$d[name_hu]?></td>
				<td align="center"><?=$d[category_name_hu]?></td>
				<td align="center">
					<?
						$stock = 0;
						foreach( $d[variations] as $v ){
							$stock += $v[stock];
						}
					?>
					<a href="javascript:showVariation(<?=$d[ID]?>);"><?=$stock?> db</a>
				</td>
				<td align="center"><?=$d[afa]?>%</td>
				<td align="center">
					<? if($d[is_favorite] == '1'): ?>
						<i title="Igen" class="fa fa-check" style="color:green;"></i>
					<? else: ?>
						<i title="Nem" class="fa fa-minus" style="color:#ccc;">
					<? endif; ?>
				</td>
				<td align="center">
					<? if($d[is_news] == '1'): ?>
						<i title="Igen" class="fa fa-check" style="color:green;"></i>
					<? else: ?>
						<i title="Nem" class="fa fa-minus" style="color:#ccc;">
					<? endif; ?>
				</td>
				<td align="center"><?=Helper::softDate($d[lastModified])?></td>
				<td align="center"><?=Helper::softDate($d[addedAt])?></td>
				<td align="center"><? if($d[showed] == '1'): ?><i title="Látható" class="fa fa-eye" style="color:green;"></i><? else: ?><i title="Rejtve" class="fa fa-eye" style="color:#aaa;"><? endif; ?></td>
				<td align="center">
					<a title="Szerkesztés" href="<?=ADMROOT?>/<?=$this->gets[1]?>/edit/<?=$d[ID]?>"><i class="fa fa-pencil"></i></a> &nbsp;
					<a title="Törlés" href="<?=ADMROOT?>/<?=$this->gets[1]?>/del/<?=$d[ID]?>"><i class="fa fa-times"></i></a>
				</td>
			</tr>
			<tr class="variations p<?=$d[ID]?>" style="display:none;">
				<td colspan="25">
					<h4>Termék variációk</h4>
					<table class="table table-bordered variations">
						<thead>
							<tr>
								<? foreach( $this->settings['all_languages'] as $lang ):?>
								<th><?=$this->settings['language_flag'][$lang['code']]?> Elnevezés - <?=strtoupper($lang['code'])?></th>
								<? endforeach; ?>
								<th width="100">Ár - HUF</th>
								<th width="100">Ár - USD</th>
								<th width="100">Ár - EUR</th>
								<th width="50">Készlet</th>
								<th width="25">Elérhető</th>
								<th width="50"></th>
							</tr>
						</thead>
						<tbody>
							<? foreach($d[variations] as $v): ?>
							<tr>
								<? foreach( $this->settings['all_languages'] as $lang ):?>
								<td><?=$v['name_'.$lang['code']]?></td>
								<? endforeach; ?>
								<td align="center">
									<div><em>nettó</em> <?=Helper::cashFormat($v[price_huf])?></div>
									<div><em>bruttó</em> <?=Helper::cashFormat($v[price_huf] * ($d[afa] / 100 + 1))?></div>
								</td>
								<td align="center">
									<div><em>nettó</em> <?=Helper::cashFormat($v[price_usd])?></div>
									<div><em>bruttó</em> <?=Helper::cashFormat($v[price_usd] * ($d[afa] / 100 + 1))?></div>
								</td>
								<td align="center">
									<div><em>nettó</em> <?=Helper::cashFormat($v[price_eur])?></div>
									<div><em>bruttó</em> <?=Helper::cashFormat($v[price_eur] * ($d[afa] / 100 + 1))?></div>
								</td>
								<td align="center"><?=$v[stock]?> db</td>
								<td align="center"><?=($v[avaiable] == '1')?'<i class="fa fa-check" style="color:green;"></i>':'<i class="fa fa-times" style="color:red;"></i>'?></td>
								<td align="center">
									<a title="Szerkesztés" href="<?=ADMROOT?>/product_variation/edit/<?=$v[ID]?>"><i class="fa fa-pencil"></i></a> &nbsp;
									<a title="Törlés" href="<?=ADMROOT?>/product_variation/del/<?=$v[ID]?>"><i class="fa fa-times"></i></a>
								</td>
							</tr>
							<? endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
			<? endforeach; else: ?>
			<tr>
				<td colspan="15" align="center">
					<div style="padding:25px;">Nincsennek termékek létrehozva!</div>
				</td>
			</tr>
			<? endif; ?>
		</tbody>
	</table>
	<ul class="pagination">
	  <li><a href="/<?=$this->gets[0]?>/<?=$this->gets[1]?>/1">&laquo;</a></li>
	  <? for($p = 1; $p <= $this->products[info][pages][max]; $p++): ?>
	  <li class="<?=(Helper::currentPageNum() == $p)?'active':''?>"><a href="/<?=$this->gets[0]?>/<?=$this->gets[1]?>/<?=$p?>"><?=$p?></a></li>
	  <? endfor; ?>
	  <li><a href="/<?=$this->gets[0]?>/<?=$this->gets[1]?>/<?=$this->products[info][pages][max]?>">&raquo;</a></li>
	</ul>
	</form>
<? endif; ?>
