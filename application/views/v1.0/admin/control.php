<?
	$page = $this->gets[1];
	$page = ($page == '' || !$page) ? 'orders' : $page;
?>
<div class="adminContainer">
	<div class="row">
		<!-- Admin menü -->
		<? if($this->gets[1] != 'images'): ?>
		<div class="col-sm-2 sideMenu">
			<div class="banner">
				<img src="<?=IMG?>logo-o.png" alt="<?=TITLE?>" class="logo" />
				<div><strong><?=$this->admin?> <a title="Kijelentkezés" href="<?=ADMROOT?>/exit"><i class="fa fa-sign-out"></i></a></strong></div>
			</div>
			<div class="menuItem">
				<ul>
					<li class="<?=($this->gets[1] == '' || $this->gets[1] == 'orders')?'on':''?>">
						<a href="<?=ADMROOT?>"><i class="fa fa-bars"></i> Megrendelések</a>
					</li>
					<li class="<?=($this->gets[1] == 'products')?'on':''?>">
						<a href="<?=ADMROOT?>/products"><i class="fa fa-cubes"></i> Termékek</a>
					</li>
					<li class="<?=($this->gets[1] == 'collections')?'on':''?>">
						<a href="<?=ADMROOT?>/collections"><i class="fa fa-star"></i> Kollekciók</a>
					</li>
					<li class="<?=($this->gets[1] == 'books')?'on':''?>">
						<a href="<?=ADMROOT?>/books"><i class="fa fa-book"></i> Katalógusok</a>
					</li>
					<li class="<?=($this->gets[1] == 'pages')?'on':''?>">
						<a href="<?=ADMROOT?>/pages"><i class="fa fa-file-o"></i> Oldalak</a>
					</li>
					<li class="<?=($this->gets[1] == 'users')?'on':''?>">
						<a href="<?=ADMROOT?>/users"><i class="fa fa-group"></i> Felhasználók</a>
					</li>
					<li class="<?=($this->gets[1] == 'ourstars')?'on':''?>">
						<a href="<?=ADMROOT?>/ourstars"><i class="fa fa-trophy"></i> Sztárjaink</a>
					</li>
					<li class="<?=($this->gets[1] == 'menus')?'on':''?>">
						<a href="<?=ADMROOT?>/menus"><i class="fa fa-ellipsis-h"></i> Menük</a>
					</li>
					<li class="<?=($this->gets[1] == 'coupons')?'on':''?>">
						<a href="<?=ADMROOT?>/coupons"><i class="fa fa-tags"></i> Kuponok</a>
					</li>
					<li class="<?=($this->gets[1] == 'giftcards')?'on':''?>">
						<a href="<?=ADMROOT?>/giftcards"><i class="fa fa-credit-card"></i> Ajándékkáryák</a>
					</li>
					<? if($this->admin_priority == 0): ?>
					<li class="<?=($this->gets[1] == 'settings')?'on':''?>">
						<a href="<?=ADMROOT?>/settings"><i class="fa fa-gears"></i> Beállítások</a>
					</li>
					<li>
						<!-- <a href='javascript:window.open("<?=ADMROOT?>/images", "Images", "width=960, height=600");'><i class="fa fa-picture-o"></i>Képfeltöltések</a>-->
						<a href='<?=FILE_BROWSER_IMAGE?>' data-fancybox-type="iframe" class="iframe-btn"><i class="fa fa-picture-o"></i>Képfeltöltések</a>

					</li>
					<? endif; ?>
				</ul>
			</div>
		</div>
		<!--/Admin menü -->

		<!-- Oldal tartalom -->
		<div class="col-sm-10">
			<div class="p10">
				<? $this->render('/admin/pages/'.$page, true); ?>
			</div>
		</div>
		<!--/Oldal tartalom -->
		<? else: ?>
		<!-- Oldal tartalom -->
		<div class="col-sm-12">
			<div class="p10">
				<? $this->render('/admin/pages/'.$page, true); ?>
			</div>
		</div>
		<!--/Oldal tartalom -->
		<? endif; ?>
	</div>

</div>
