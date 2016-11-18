$(function(){
	$.cookieAccepter('/page/aszf');
	$('#adatlap a.zoom, a.zoom').fancybox({
		padding: 0,
		uIndex: 99999,
		openEffect : 'elastic',
		openSpeed  : 250,

		closeEffect : 'elastic',
		closeSpeed  : 250,

		closeClick : true,

		helpers : {
			overlay : null,
			buttons	: {
				position : 'bottom'
			},
			title: {
				type: 'over'
			}
		}
	});

	$('.iframe-btn').fancybox({
		maxWidth	: 800,
		maxHeight	: 600,
		fitToView	: false,
		width		: '70%',
		height		: '70%',
		autoSize	: false,
		closeClick	: false,
		openEffect	: 'none',
		closeEffect	: 'none',
		closeBtn 	: false,
		padding		: 0
    });

	$('.currency-selector').hover( function(){
		$('.currency-list').show(0);
	});

	$('.currency-selector').mouseleave( function(){

		$('.currency-list').hide(0);
	});

	$('.currency-selector input[type=radio]').change(function(){
		$.cookie('__countryCurrency', $(this).val(), { path : '/' });
		document.location.reload(true);
	});

	/* $('.static_page.scrollers').perfectScrollbar({
		includePadding : true
	 });*/

	$('.viewBoxHandler').click(function(event){
		var key 	= $(this).attr('vbox');
		var state 	= ( $('.viewBox.box-'+key).hasClass( 'opened' ) ) ? true : false;

		if( state ){
			$('.viewBox.box-'+key).hide( 0 ).removeClass( 'opened' );
			$('.viewBoxHandler.'+key).removeClass( 'on' );
		}else{
			$('.viewBox').hide(0);
			$('.viewBoxHandler').removeClass('on');

			$('.viewBox.box-'+key).show(0).addClass( "opened" );
			$('.viewBoxHandler.'+key).addClass('on');
		}
	});


	$('.pageSwitcher > div').click(function(){
		var lang = $(this).attr('page');

		$('.pageSwitcher > div').removeClass('active');
		$('.pageView').removeClass('page-view-active');

		$(this).addClass('active');
		$('.pageView.page-'+lang).addClass('page-view-active');
	});

	getCartInfo(function(e){
		refreshCart(e);
		Cart.reLoad(e);
	});

	//getLocation();
})

function Hashtag(){
	this.add = function( target, cont ){
		var lang 		= target.attr('lng');
		var tag 		= target.val();
		var pre_value 	= cont.val();

		if( tag != '' ){
			cont.val( pre_value + "::" + tag );


			$( '#tag_content_' + lang ).append(' <div class="tag" title="törlés" onclick="Hashtag.remove( $(this), \''+lang+'\' )">' + tag + '</div> ');
			target.val('').focus();
		}

		target.val('').focus();
	}

	this.remove = function( e, lang ){
		var tag = e.text();
		var val = $( '#hashtag_'+lang ).val();

		$( '#hashtag_'+lang ).val( val.replace( '::'+tag, '') );

		e.remove();
	}
}

function Cart(){
	this.content = ".cartContent";
	this.push = function(i){
		var oi = $(this.content).find(".item");
		var ec = '<div class="item i'+i.termekID+'">'+
		'<div class="info">';
			if (i.isProduct == 1) {
				ec += '<input type="hidden" name="buyItem['+i.termekID+']['+i.variationID+']" value="'+i.pcs+'" />';
				ec += '<div class="desci"><img src="/images/cart-minus.png" alt="" onclick="Cart.removeItem('+i.termekID+', '+i.variationID+')" /></div>';
			} else {
				ec += '<div class="desci"><img src="/images/cart-minus.png" alt="" onclick="Cart.removeItem(\''+i.termekID+'\', \''+i.variationID+'\')" /></div>';
			}
		ec += '<div class="name">';
		if (i.isProduct == 1) {
			ec += '<a href="'+i.url+'">'+i.termekNev+'</a><br />'+
			i.pcs+'x <em>'+i.variationName+'</em>';
		} else {
			ec += i.termekNev+'<br />'+
			'<em>'+i.variationName+'</em>';
		}

		ec += '</div>';
		if (i.price) {
			ec += '<div class="price"><span class="code">'+i.priceCode+'</span> '+i.price+'</div>';
		} else {
			ec += '<div class="coupon"><span class="code">'+i.discount+'</span></div>';
		}

		ec += '</div>'+
		'<div class="clr"></div></div>';
		if(oi.length == 0){
			$(this.content).html(ec);
		}else{
			$(ec).insertAfter(this.content+' .item:last');
		}
	}
	this.addItem = function(id, vid){
		var parent = this;
		$.post('/ajax/post/',{
			type : 'cart',
			mode : 'addItem',
			id 	 : id,
			vid  : vid
		},function(d){
			var p = $.parseJSON(d);
			if(p.success == 1){
				getCartInfo(function(e){
					refreshCart(e);
					parent.reLoad(e);
				});
			}else{
				aler(p.msg);
			}
		},"html");
	}
	this.removeItem = function(id, vid){
		var parent = this;
		$.post('/ajax/post/',{
			type : 'cart',
			mode : 'removeItem',
			id 	 : id,
			vid  : vid
		},function(d){
			var p = $.parseJSON(d);
			if(p.success == 1){
				getCartInfo(function(e){
					refreshCart(e);
					parent.reLoad(e);
				});
			}else{
				alert(p.msg);
			}
		},"html");
	}
	this.reLoad = function(e){
		$(this.content).html('<div class="noItem"><div class="inf"></div></div>');
		buildCartItems(e);
	}
	this.remove = function(id){
		var c = this.content;
		var parent = this;
		$.post('/ajax/post/',{
			type : 'cart',
			mode : 'remove',
			id 	 : id
		},function(d){
			var p = $.parseJSON(d);
			if(p.success == 1){
				$(c+' .item.i'+id).remove();
				var oi = $(c).find(".item");
				if(oi.length == 0){
					$(c).html('<div class="noItem"><div class="inf"></div></div>');
				}
				getCartInfo(function(e){
					refreshCart(e);
					parent.reLoad(e);
				});
			}else{
				aler(p.msg);
			}
		},"html");
	}
	this.clear = function(){
		console.log('CLEAR');
		var c = this.content;
		var parent = this;
		$.post('/ajax/post/',{
			type : 'cart',
			mode : 'clear'
		},function(d){
			var p = $.parseJSON(d);
			if(p.success == 1){

				var oi = $(c).find(".item");
				if(oi.length == 0){
					$(c).html('<div class="noItem"><div class="inf"></div></div>');
				}
				getCartInfo(function(e){
					refreshCart(e);
					parent.reLoad(e);
				});
			}else{
				aler(p.msg);
			}
		},"html");
	}
}

var Cart 		= new Cart();
var Hashtag 	= new Hashtag();

function openCloseBox(elem, flag){
	var flagState 	= localStorage.getItem(flag);
	var disp 		= $(elem).css('display');
	if(disp != 'none'){
		localStorage.removeItem(flag);
		$(elem).toggle("slide");
	}else{
		localStorage.setItem(flag,1);
		$(elem).toggle("slide");
	}

	console.log(flagState);
}


function buildCartItems(c){
	var i = c.items;

	for(var s = 0; s < i.length; s++){
		var e = i[s];
		Cart.push(e);
	}
}

function getCartInfo(callback){
	$.post('/ajax/get/',{
		type : 'cartInfo'
	},function(d){
		var p = $.parseJSON(d);
		callback(p);
	},"html");
}
function refreshCart(p){
	$('.cart-item-num-v').text(p.itemNum).css('visibility','visible');
	$('.cart-item-num').text(p.itemNum);
	$('.cart-item-prices').text(p.totalPriceTxt);

	if ( p.totalPrice != p.originPrice ) {
		$('#cart-item-originprices').text(p.originPriceTxt);
		$('#cart-item-price-origins').show();
	}

	if(p.itemNum > 0){
		$('.cart-item-num-v').css('visibility','visible');
	}else{
		$('.cart-item-num-v').css('visibility','hidden');
	}
}
function addToCart(termekID, me){
	$.post('/ajax/post/',{
		type : 'cart',
		mode : 'add',
		t 	 : termekID,
		m    : me
	},function(d){
		var p = $.parseJSON(d);
		if(p.success == 1){
			getCartInfo(function(e){
				refreshCart(e);
				Cart.reLoad(e);
			});
		}
	},"html");
}
/*
function getLocation() {
	var ts = new Date().getTime(),
		cs = $.cookie( 'geo_lastrefresh' ),
		go = false,
		diff;

	diff_hr = ((ts - cs) / 1000 / 60 / 60);

	if( diff_hr > 24 ) {
		go = true;
	}

	if( typeof cs === 'undefined' ) {
		go = true;
	}

	if( go ) {
		if (navigator.geolocation) {
	        navigator.geolocation.getCurrentPosition( showPosition );
	    } else {

	    }
	}
}

function showPosition(position) {
	var ct = ( new Date().getTime() ) + 3600 * 24 * 30;

	$.cookie( 'geo_lastrefresh',  new Date().getTime() );
	$.cookie( 'geo_latlng',  position.coords.latitude+","+position.coords.longitude );
	var ctc 	= $.cookie( 'geo_countrycode' );

	if( !ctc ) {
		$.getJSON('http://ws.geonames.org/countryCode', {
	        lat: position.coords.latitude,
	        lng: position.coords.longitude,
	        username: 'mridev',
	        type: 'JSON'
	    }, function(result) {
	        $.cookie( 'geo_countrycode', result.countryCode, { path: '/', expires: ct } );
			$.cookie( 'geo_country', result.countryName, { path: '/', expires: ct } );
			$.cookie( 'geo_needrefreshpage', '1', { path: '/', expires: ct } );

			location.reload(true);
	    });
	}
}
*/
