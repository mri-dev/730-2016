<script type="text/javascript">
	$( function(){
		$('form input').keydown( function( e ){
		if( e.keyCode == 13 ) {
			return false;
		  }
		} );
	})
</script>
<? if($this->gets[2] == 'add'): ?>
	<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"> vissza</i></a>
	</div>
	<h1>Oldalak / <em>Új oldal létrehozása</em></h1>
	<?=$this->error_msg?>
	<div>
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
					<div class="col-sm-2"><em class="bw">Oldal címe*</em></div>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?=$_POST[title][$lang['code']]?>" name="title[<?=$lang['code']?>]"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Egyedi url*</em></div>
					<div class="col-sm-4">
						<input type="text" class="form-control" placeholder="pelda-url-ekezet-nelkul" value="<?=$_POST[url][$lang['code']]?>" name="url[<?=$lang['code']?>]"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Rövid leírás (SEO)</em></div>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="description[<?=$lang['code']?>]" value="<?=$_POST[description][$lang['code']]?>"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Kulcsszavak (SEO)</em></div>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="keywords[<?=$lang['code']?>]" value="<?=$_POST[keywords][$lang['code']]?>" placeholder="kulcsszo, kulcsszo, kulcsszo, stb..."  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Oldal tartalom (HU - magyar)</em></div>
					<div class="col-sm-10">
						<textarea name="content[<?=$lang['code']?>]" style="min-height:450px;"><?=stripslashes($_POST[content][$lang['code']])?></textarea>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Hashtag</em></div>
					<div class="col-sm-5">
						<input type="text" lng="hu" class="form-control hashtag" /> 
						<input type="hidden" id="hashtag_<?=$lang['code']?>" name="hashtag[<?=$lang['code']?>]" value="::<?=$this->page['hashtag_'.$lang['code']]?>"/>
						<?
							$tags = array(); 
							$xtag = explode( '::', $this->page['hashtag_'.$lang['code']] );
							foreach($xtag as $xt){
								if($xt != '')
									$tags[] = $xt;
							}
						?>
						<div id="tag_content_<?=$lang['code']?>" class="tag_content"><? foreach( $tags as $tag ): ?><div class="tag" title="törlés" onclick="Hashtag.remove( $(this), '<?=$lang['code']?>' )"><?=$tag?></div><? endforeach; ?></div>
					</div>
					<div class="col-sm-5">
						&nbsp; <em class="bw"><a href="javascript:void(0);" onclick="Hashtag.add( $('.hashtag[lng=<?=$lang['code']?>]'), $('#hashtag_<?=$lang['code']?>') )">+ hozzáadás</a></em>
					</div>
				</div>
			</div>
			<? endforeach;?>

			<!--Egyedi háttér-->
			<div class="languageView lang-img">
				<div class="row">
					<div class="col-sm-12">
						<em>Az ideális (ajánlott) méret: <strong>1920x1080</strong> pixel, jpg kép.</em> <strong style="color:red;">A kép az aktuális oldal "egyedi url" alapján töltődik be. Ezen adat változásával a képet újra fel kell tölteni.</strong>
						<br />
						Jelen esetben az egyedi url(ek) a következő(k): <u><?=$this->page[url_text_hu]?></u>, <u><?=$this->page[url_text_en]?></u> - a háttérkép ezekre illeszkedik!
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Háttér kiválasztása</em></div>
					<div class="col-sm-3">
						<input type="file" name="bgImg[]" class="form-control" />
					</div>
				</div>
			</div>
			<!--/Egyedi háttér-->
			<br />
			<div class="divider"></div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Státusz</em></div>
				<div class="col-sm-1">
					<select name="status" class="form-control">
						<option value="1">Látható</option>
						<option value="0">Rejtett</option>
					</select>
				</div>
				<div class="col-sm-2">&nbsp;&nbsp;<em class="bw">Görgethetős megjelenés</em></div>
				<div class="col-sm-1">
					<select name="scrollable" class="form-control">
						<option value="0" <?=($this->page[scrollable] == '0')?'selected="selected"':''?>>Nem</option>
						<option value="1" <?=($this->page[scrollable] == '1')?'selected="selected"':''?>>Igen</option>
					</select>
				</div>
				<div class="col-sm-6" align="right">
					<button type="submit" class="btn btn-primary" name="addPage">Létrehozás <i class="fa fa-check"></i></button>
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
					Biztos benne, hogy törölni szeretné a(z) <strong class="txt-big"><?=$this->page[title_hu]?></strong> oldalt? A művelet nem visszavonható!
				</div>
				<div class="col-sm-3" align="right">
					<form action="" method="post">
						<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-danger">NEM</a>
						<button class="btn btn-success" type="submit" name="delPage">IGEN</button>
					</form>
				</div>
			</div>
			
		</div>
	</div>
<? elseif($this->gets[2] == 'edit'): ?>
<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"> vissza</i></a>
	</div>
	<h1>Oldalak szerkesztése</em></h1>
	<?=$this->error_msg?>
	<div>
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
					<div class="col-sm-2"><em class="bw">Oldal címe*</em></div>
					<div class="col-sm-4">
						<input type="text" class="form-control" value="<?=$this->page['title_'.$lang['code']]?>" name="title[<?=$lang['code']?>]"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Egyedi url*</em></div>
					<div class="col-sm-4">
						<input type="text" class="form-control" placeholder="pelda-url-ekezet-nelkul" value="<?=$this->page['url_text_'.$lang['code']]?>" name="url[<?=$lang['code']?>]"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Rövid leírás (SEO)</em></div>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="description[<?=$lang['code']?>]" value="<?=$this->page['description_'.$lang['code']]?>"  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Kulcsszavak (SEO)</em></div>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="keywords[<?=$lang['code']?>]" value="<?=$this->page['keywords_'.$lang['code']]?>" placeholder="kulcsszo, kulcsszo, kulcsszo, stb..."  />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Oldal tartalom (HU - magyar)</em></div>
					<div class="col-sm-10">
						<textarea name="content[<?=$lang['code']?>]" style="min-height:450px;"><?=stripslashes($this->page['content_'.$lang['code']])?></textarea>
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Hashtag</em></div>
					<div class="col-sm-5">
						<input type="text" lng="hu" class="form-control hashtag" /> 
						<input type="hidden" id="hashtag_<?=$lang['code']?>" name="hashtag[<?=$lang['code']?>]" value="::<?=$this->page['hashtag_'.$lang['code']]?>"/>
						<?
							$tags = array(); 
							$xtag = explode( '::', $this->page['hashtag_'.$lang['code']] );
							foreach($xtag as $xt){
								if($xt != '')
									$tags[] = $xt;
							}
						?>
						<div id="tag_content_<?=$lang['code']?>" class="tag_content"><? foreach( $tags as $tag ): ?><div class="tag" title="törlés" onclick="Hashtag.remove( $(this), '<?=$lang['code']?>' )"><?=$tag?></div><? endforeach; ?></div>
					</div>
					<div class="col-sm-5">
						&nbsp; <em class="bw"><a href="javascript:void(0);" onclick="Hashtag.add( $('.hashtag[lng=<?=$lang['code']?>]'), $('#hashtag_<?=$lang['code']?>') )">+ hozzáadás</a></em>
					</div>
				</div>
			</div>
			<? endforeach;?>
			
			<!--Egyedi háttér-->
			<div class="languageView lang-img">
				<div class="row">
					<div class="col-sm-12">
						<em>Az ideális (ajánlott) méret: <strong>1920x1080</strong> pixel, jpg kép.</em> <strong style="color:red;">A kép az aktuális oldal "egyedi url" alapján töltődik be. Ezen adat változásával a képet újra fel kell tölteni.</strong>
						<br />
						Jelen esetben az egyedi url(ek) a következő(k): <u><?=$this->page[url_text_hu]?></u>, <u><?=$this->page[url_text_en]?></u> - a háttérkép ezekre illeszkedik!
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Új háttér feltöltése</em></div>
					<div class="col-sm-3">
						<input type="file" name="bgImg[]" class="form-control" />
					</div>
				</div>
				<br />
				<div class="row">
					<div class="col-sm-2"><em class="bw">Aktuális háttér</em></div>
					<div class="col-sm-3">
						<? $bg = StaticPage::getBackground($this->page); ?>
						<? if($bg): ?>
							<a href="/<?=$bg?>" target="_blank"><img style="background:#eee; border:1px solid #d7d7d7; padding:5px;" src="/<?=$bg?>" width="250" alt="" /></a>
						<? else: ?>
							<em>Nincs feltöltve egyedi háttér!</em>
						<? endif; ?>
					</div>
				</div>
			</div>
			<!--/Egyedi háttér-->
			<br />
			<div class="divider"></div>
			<br />
			<div class="row">
				<div class="col-sm-2"><em class="bw">Státusz</em></div>
				<div class="col-sm-1">
					<select name="status" class="form-control">
						<option value="1" <?=($this->page[showed] == '1')?'selected="selected"':''?>>Látható</option>
						<option value="0" <?=($this->page[showed] == '0')?'selected="selected"':''?>>Rejtett</option>
					</select>
				</div>
				<div class="col-sm-2">&nbsp;&nbsp;<em class="bw">Görgethetős megjelenés</em></div>
				<div class="col-sm-1">
					<select name="scrollable" class="form-control">
						<option value="0" <?=($this->page[scrollable] == '0')?'selected="selected"':''?>>Nem</option>
						<option value="1" <?=($this->page[scrollable] == '1')?'selected="selected"':''?>>Igen</option>
					</select>
				</div>
				<div class="col-sm-6" align="right">
					<button type="submit" class="btn btn-success" name="editPage">Változások mentése <i class="fa fa-save"></i></button>
				</div>
			</div>
		</form>
		<? $this->render('inc/imageUploadHelper', true) ?>
		</div>
	</div>
<? else: ?>
	<div class="float-right">
		<a href="<?=ADMROOT?>/<?=$this->gets[1]?>/add" class="btn btn-primary btn-md">új oldal <i class="fa fa-plus-circle"></i></a>
	</div>
	<h1>Oldalak</h1>

	<table class="table aria table-bordered">
		<thead>
			<tr>
				<? foreach( $this->settings['all_languages'] as $lang ):?>
				<th><?=$this->settings['language_flag'][$lang['code']]?> Oldal címe (<?=strtoupper($lang['code'])?>)</th>
				<? endforeach; ?>
				<th>Módosítva</th>
				<th width="80">Látható</th>
				<th width="80"><i class="fa fa-gears"></i></th>				
			</tr>
		</thead>
		<tbody>
			<? if(count($this->pages[data]) > 0): foreach($this->pages[data] as $d): ?>
			<tr>
				<? foreach( $this->settings['all_languages'] as $lang ):?>
				<td>
					<strong><?=$d['title_'.$lang['code']]?></strong>
					<? if($d['url_text_'.$lang['code']] != ''): ?>
					<div><em><?=DOMAIN?>page/<?=$d['url_text_'.$lang['code']]?></em></div>
					<? endif; ?>
				</td>
				<? endforeach; ?>
				<td align="center">
					<?=Helper::softDate($d[lastModified]);?>
				</td>
				<td align="center">
					<? if($d[showed] == '1'): ?><i title="Látható" class="fa fa-eye" style="color:#008800;"></i><? else: ?><i title="Rejtve / Nem látható" class="fa fa-eye" style="color:#ccc;"></i><? endif; ?>
				</td>
				<td align="center">
					<a title="Szerkesztés" href="<?=ADMROOT?>/<?=$this->gets[1]?>/edit/<?=$d[ID]?>"><i class="fa fa-pencil"></i></a> &nbsp;
					<a title="Törlés" href="<?=ADMROOT?>/<?=$this->gets[1]?>/del/<?=$d[ID]?>"><i class="fa fa-times"></i></a>
				</td>
			</tr>
			<? endforeach; else: ?>
			<tr>
				<td colspan="15" align="center">
					<div style="padding:25px;">Nincsennek oldalak létrehozva!</div>
				</td>
			</tr>
			<? endif; ?>
		</tbody>
	</table>
<? endif; ?>