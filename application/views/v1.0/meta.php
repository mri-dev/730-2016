<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta charset="UTF-8">
<!-- STYLES -->
<link rel="icon" href="<?=IMG?>icons/favicon.ico" type="image/x-icon">
<?=$this->addStyle('master', 'media="screen"')?>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<?=$this->addStyle('bootstrap.min', 'media="screen"')?>
<?=$this->addStyle('bootstrap-theme.min', 'media="screen"')?>
<?=$this->addStyle('scrollBar', 'media="screen"', false)?>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
<?=$this->addStyle('media', 'media="screen"', false)?>
<link rel="stylesheet" type="text/css" href="<?=JS?>fancybox/jquery.fancybox.css?v=2.1.4" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=JS?>fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
<link rel="stylesheet" type="text/css" href="<?=SSTYLE?>ie-mod.css" />

<!-- JS's -->
<?=$this->addJS('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',true)?>


<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<?=$this->addJS('bootstrap.min',false)?>
<?=$this->addJS('master',false,false)?>
<?=$this->addJS('user',false,false)?>
<?=$this->addJS('jquery.cookie',false)?>
<?=$this->addJS('collectionViewer',false, false)?>
<?=$this->addJS('jquery.scrollBar',false)?>
<?=$this->addJS('mousewhell',false)?>
<?=$this->addJS('mousewhellintent',false)?>
<?=$this->addJS('jquery.cookieaccept',false, false)?>
<script type="text/javascript" src="<?=JS?>fancybox/jquery.fancybox.js?v=2.1.4"></script>
<script type="text/javascript" src="<?=JS?>fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>


<!--[if IE]>
 <script src="<?=SJS?>html5shiv.js" type="text/javascript"></script>
 <script src="<?=SJS?>respond.js" type="text/javascript"></script>
 <script src="<?=SJS?>jquery.ie.min.js" type="text/javascript"></script>

 <link rel="stylesheet" type="text/css" href="<?=SSTYLE?>ie-fix.css" />
<![endif]-->



<!--[if lt IE 9]>
 <link rel="stylesheet" type="text/css" href="<?=SSTYLE?>ie8.css" />
<![endif]-->

