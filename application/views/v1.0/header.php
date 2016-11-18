<!DOCTYPE html>
<html
      xmlns:og="http://ogp.me/ns#"
      xmlns:fb="http://www.facebook.com/2008/fbml" lang="<?=(Lang::getLang() == 'hu')?'hu':'en'?>">
<head>
	<title><?=$this->title?></title>
    <?=$this->addMeta('robots','index,folow')?>
    <?=$this->SEOSERVICE?>
   	<? $this->render('meta'); ?>	
	<!-- Google Analytics -->
		<? if(GOOGLE_UA != ''):?>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', '<?=GOOGLE_UA?>', 'auto');
		  ga('send', 'pageview');

		</script>
		<? endif; ?>
	<!--/Google Analytics -->
	<script type="text/javascript">(function(w, d, s) { function go(){ var js, fjs = d.getElementsByTagName(s)[0], load = function(url, id) { if (d.getElementById(id)) { d.getElementById(id).onload = d.getElementById(id).onreadystatechange = function() { if (typeof v4u != 'undefined') v4u.track("an1xZoOPHJ");}; return; } js = d.createElement(s); js.src = url; js.id = id; fjs.parentNode.insertBefore(js, fjs); js.onload = js.onreadystatechange = function() { if (typeof v4u != 'undefined') v4u.track("an1xZoOPHJ"); };}; load("//video.vid4u.org/v4ubeacon.js", "v4s_1"); } go(); }(window, document, "script"));</script>
</head>
<body class="<?=($this->gets[0] == 'collections' && $this->gets[1] == 'book')?'book-view':''?>">

<div class="wire">
	<div class="page_container">
	<? $this->render('inc/topBar', true);?>
	
