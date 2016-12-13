<script type="text/javascript">
	$(function(){
		$('#collectionSelect').change(function(){
			var v = $(this).val();

			document.location.href='/webshop/'+v;
		});
		$('#caregorySelect').change(function(){
			var v = $(this).val();
			var c = $('#collectionSelect').val();

			c = (c == '') ? '-' : c;

			document.location.href='/webshop/'+c+'/'+v;
		});
	})
</script>
<div class="webshop">
	<? if(false): ?>
	<div class="alert-use-phone"><?=_('<h2>Figyelem!</h2><br> Kérjük kedves vásárlóinkat, hogy a fizetési rendszerünk továbbfejlesztése ideje alatt <u>telefonon adják le megrendeléseiket</u>!<br> Telefonszám: +36 30 506 3280, +36 30 507 4905')?></div>
	<? endif; ?>
	<h1><?=__('Webshop')?></h1>
	<div class="subline">
		<a href="/"><i class="fa fa-home"></i></a> /
		<a href="/webshop"><?=__('Összes termék')?></a>
		<? if($this->s_collection):?>
			/ <a href="/webshop/<?=$this->s_collection?>"><?=__($this->s_collection)?></a>
		<? endif; ?>
		<? if($this->s_category):?>
			/ <a href="/webshop/<?=($this->s_collection)?$this->s_collection.'/':'-/'?><?=$this->s_category?>"><?=__($this->s_category)?></a>
		<? endif; ?>
	</div>

	<div class="mobilView softOvBg mobilProductFilter">
		<div class="tbl">
			<div class="tbl-cell"><em class="bw"><?=__('Kollekció')?>:</em></div>
			<div class="tbl-cell">
				<select class="form-control" id="collectionSelect">
					<option value=""><?=__('összes kollekció')?></option>
					<option value="" disabled="disabled"></option>
					<? foreach($this->collection as $d): ?>
					<option value="<?=$d[name]?>" <?=($d[name_hu] == $this->s_collection || $d[name_en] == $this->s_collection)?'selected="selected"':''?>><?=$d[name]?></option>
					<? endforeach;?>
				</select>
			</div>
			<div class="tbl-cell null-cell"><em class="bw">&nbsp;</em></div>
			<div class="tbl-cell"><em class="bw"><?=__('Kategória')?>:</em></div>
			<div class="tbl-cell">
				<select class="form-control" id="caregorySelect">
					<option value=""><?=__('összes kategória')?></option>
					<option value="" disabled="disabled"></option>
					<? foreach($this->category as $d): ?>
					<option value="<?=$d[name]?>" <?=($d[name_hu] == $this->s_category || $d[name_en] == $this->s_category)?'selected="selected"':''?>><?=$d[name]?></option>
					<? endforeach;?>
				</select>
			</div>
		</div>
	</div>

	<table style="table-layout:fixed;" width="100%" border="0">
			<tr>
				<td class="tbl-product-items">
					<div class="">
						<div class="productList">
							<ul class="all">
							<? if(count($this->products[data]) > 0): foreach($this->products[data] as $d): ?>
								<li>
									<div class="img"><a href="/webshop/product/<?=Helper::makeSafeUrl($d[name],'_-'.$d[ID])?>"><img src="/<?=$d[image]?>" alt="<?=$d[name]?>" /></a></div>
									<div class="name"><a href="/webshop/product/<?=Helper::makeSafeUrl($d[name],'_-'.$d[ID])?>"><?=$d[name]?></a></div>
									<div class="variations">
										<? foreach($d[variations] as $v): ?>
										<div>
											<div class="name"><?=$v[name]?></div><div class="price"><? if( $v[price] != 0 ): ?><span class="code"><?=Lang::getPriceCode()?></span> <?=Helper::cashFormat($v[price])?><? else: ?><?=__('Ár kérésre')?><? endif; ?></div><div class="stock">
												<? if($v[stock] > 0): ?>
													<i class="fa fa-check avaiable" title="<?=__('készleten')?>"></i>
												<? else: ?>
													<i class="fa fa-check not-avaiable" title="<?=__('nincs készleten')?>"></i>
												<? endif; ?>
											</div>
										</div>
										<? endforeach; ?>
									</div>
								</li>
							<? endforeach; ?>
							</ul>
							<ul class="pagination">
							  <li><a href="/<?=$this->gets[0]?>/<?
								if($this->s_collection): echo $this->s_collection.'/'; else: echo '-/'; endif;
								if($this->s_category): echo $this->s_category.'/'; else: echo '-/'; endif;
							  ?>1">&laquo;</a></li>
							  <? for($p = 1; $p <= $this->products[info][pages][max]; $p++): ?>
							  <li class="<?=(Helper::currentPageNum() == $p)?'active':''?>"><a href="/<?=$this->gets[0]?>/<?
								if($this->s_collection): echo $this->s_collection.'/'; else: echo '-/'; endif;
								if($this->s_category): echo $this->s_category.'/'; else: echo '-/'; endif;
							  ?><?=$p?>"><?=$p?></a></li>
							  <? endfor; ?>
							  <li><a href="/<?=$this->gets[0]?>/<?
								if($this->s_collection): echo $this->s_collection.'/'; else: echo '-/'; endif;
								if($this->s_category): echo $this->s_category.'/'; else: echo '-/'; endif;
							  ?><?=$this->products[info][pages][max]?>">&raquo;</a></li>
							</ul>
							<? else: ?>
								<div class="noItem"><em><?=__('Nincs találat!')?></em></div>
							<? endif; ?>
							<div style="clear:both;"></div>

						</div>
					</div>
				</td>
				<td class="tbl-product-sidebar">
					<div class="productFilters">
						<div class="box">
							<div class="h">
								<h3><?=__('Kollekciók')?></h3>
								<img src="/images/divider-cart.png" alt="" />
							</div>
							<div>
								<ul>
									<? foreach($this->collection as $d): ?>
									<li class="<?=($d[name_hu] == $this->s_collection || $d[name_en] == $this->s_collection)?'on':''?>"><a href="/webshop/<?=$d[name]?><?=($this->s_category)?'/'.$this->s_category:''?>"><?=$d[name]?></a></li>
									<? endforeach;?>
								</ul>
							</div>
						</div>
						<div>

						</div>
						<? if( false ): ?>
						<div class="box cat">
							<div class="h">
								<h3><?=__('Kategóriák')?></h3>
								<img src="/images/divider-cart.png" alt="" />
							</div>
							<div>
								<ul>
									<? foreach($this->category as $d): ?>
									<li class="<?=($d[name_hu] == $this->s_category || $d[name_en] == $this->s_category)?'on':''?>"><a href="/webshop/<?=($this->s_collection)?$this->s_collection:'-'?>/<?=$d[name]?>"><?=$d[name]?></a></li>
									<? endforeach;?>
								</ul>
							</div>
						</div>
						<? endif; ?>
					</div>
				</td>
			</tr>
	</table>
</div>
