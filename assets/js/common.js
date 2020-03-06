function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}
$(document).ready(function() {
	var myLazyLoad = new LazyLoad({
		elements_selector: ".lazy"
	});

	// Highlight any found errors
	$('.text-danger').each(function() {
		$(this).parents('.form-group').addClass('has-error');
	});

	// Currency
	$('.currency-selector [data-code]').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).data('code'));

		$('#form-currency').submit();
	});

	// Language
	$('.language-selector [data-code]').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).data('code'));

		$('#form-language').submit();
	});

	/* Search */
	$('#header-search').on('submit', function(e) {
		e.preventDefault();

		var url = $('#header-search').attr('action');//$('base').attr('href') + 'index.php?route=product/search';

		var value = $('#header-search input[name=\'keyword\']').val();

		if (value) {
			url += '&keyword=' + encodeURIComponent(value);
		}

		location = url;
	});

	// Menu
	$('#menu .dropdown-menu').each(function() {
		var menu = $('#menu').offset();
		var dropdown = $(this).parent().offset();

		var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

		if (i > 0) {
			$(this).css('margin-left', '-' + (i + 10) + 'px');
		}
	});

	if ($.cookie('notification') == 'close') {
		$('.top-notification').hide();
	}
	$('.top-notification .close').on('click', function(){
		$('.top-notification').hide();
		$.cookie('notification', 'close', { expires: 7 });
	});
 
    if ($(window).width() > 769) {
        /*$('.nav .dropdown').hover(function() {
            $(this).find('.dropdown-menu').first().stop(true, true).delay(250).slideDown();
        }, function() {
            $(this).find('.dropdown-menu').first().stop(true, true).delay(100).slideUp();
        });*/

        $('.dropdown > a').click(function(){
        	if ($(this).attr('href') != '#') 
            	location.href = $(this).attr('href');
        });

    }

	// Product List
	$('#list-view').click(function() {
		$('#content .product-list > .clearfix').remove();

		$('#content .row > .product-list').attr('class', 'product-list col-xs-12');
		$('#grid-view').removeClass('active');
		$('#list-view').addClass('active');

		localStorage.setItem('display', 'list');
	});

	// Product Grid
	$('#grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .product-list').attr('class', 'product-list col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .product-list').attr('class', 'product-list col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .product-list').attr('class', 'product-list col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		$('#list-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display', 'grid');
	});

	if (localStorage.getItem('display') == 'list') {
		$('#list-view').trigger('click');
		$('#list-view').addClass('active');
	} else {
		$('#grid-view').trigger('click');
		$('#grid-view').addClass('active');
	}
	/* Agree to Terms */
	$(document).delegate('.agree', 'click', function(e) {
	  e.preventDefault();

	  $('#modal-agree').remove();

	  var element = this;

	  $.ajax({
	    url: $(element).attr('href'),
	    type: 'get',
	    data: {'content': 'description'},
	    dataType: 'html',
	    success: function(data) {
	      html  = '<div id="modal-agree" class="modal">';
	      html += '  <div class="modal-dialog">';
	      html += '    <div class="modal-content">';
	      html += '      <div class="modal-header">';
	      html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
	      html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
	      html += '      </div>';
	      html += '      <div class="modal-body">' + data + '</div>';
	      html += '    </div>';
	      html += '  </div>';
	      html += '</div>';

	      $('body').append(html);

	      $('#modal-agree').modal('show');
	    }
	  });
	});
	// Checkout
	$(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function(e) {
		if (e.keyCode == 13) {
			$('#collapse-checkout-option #button-login').trigger('click');
		}
	});
	
	$(document).on('click change', '.quantity-box input', function(){
		var input = $(this);
		var box = input.parent();
		var qtyInput = $('.quantity', box);
		var minimum = typeof(qtyInput.data('minimum')) != 'undefined' ? qtyInput.data('minimum') : 1;
		var currentval = parseInt(qtyInput.val());
		if (input.hasClass('minus')) {
			qtyInput.val(currentval-1).trigger('change');
			if(qtyInput.val() <= 0 || qtyInput.val() < minimum){
				if (minimum > 1) {
					$.notify({
						message: '本商品最低购买量为 '+minimum
					},{
						offset: $(window).height()/2-50,
						placement: {
							from: 'top',
							align: 'center'
						},
						delay: 2000,
						allow_dismiss: false,
						type: 'notify'
					});
				}
				qtyInput.val(minimum).trigger('change');
			}
		};
		if (input.hasClass('plus')) {
			qtyInput.val(currentval+1).trigger('change');
		};
		if (input.hasClass('quantity')) {
			if (qtyInput.val() < minimum) {
				if (minimum > 1) {
					$.notify({
						message: '本商品最低购买量为 '+minimum
					},{
						offset: $(window).height()/2-50,
						placement: {
							from: 'top',
							align: 'center'
						},
						delay: 2000,
						allow_dismiss: false,
						type: 'notify'
					});
				}

				qtyInput.val(minimum).trigger('change');
			}
		};
	});
	
	$(document).on('change', '.variations .radio input, .variations .checkbox input', function(){
		var input = $(this);
		var label = input.parent();
		if (input.is(':checked')) {
			label.addClass('selected').append('<div class="checkmark"><i class="icon-checkmark"></i></div>');
		} else {
			label.removeClass('selected').find('.checkmark').remove();
		}

		input.closest('.form-group').removeClass('has-error');

		input.closest('.radio, .checkbox').siblings().find('input:not(:checked)').each(function(){
			var input = $(this);
			var label = input.parent();
			if (input.is(':checked')) {
				label.addClass('selected').append('<div class="checkmark"><i class="icon-checkmark"></i></div>');
			} else {
				label.removeClass('selected').find('.checkmark').remove();
			}
		});
	});

	$(document).on('change', '.area-selector select', function(){
		var input = $(this);
		var box = input.parents('.area-selector');
		var form = input.parents('form');
		if (input.attr('name') == 'country_id') {
			$.ajax({
				url: 'index.php?route=account/account/country&country_id=' + input.val(),
				dataType: 'json',
				beforeSend: function() {
					input.prop('disabled', true);
				},
				complete: function() {
					input.prop('disabled', false);
				},
				success: function(json) {
					if (json['postcode_required'] == '1') {
						$('input[name=\'postcode\']', form).parent().parent().addClass('required');
					} else {
						$('input[name=\'postcode\']', form).parent().parent().removeClass('required');
					}

					html = htmlEmpty = '<option value="0">' + $cnoc.text_select + '</option>';

					if (json['zone'] && json['zone'] != '') {
						for (i = 0; i < json['zone'].length; i++) {
							html += '<option value="' + json['zone'][i]['zone_id'] + '"';
							
							if (json['zone'][i]['zone_id'] == input.data('zone-id')) {
								html += ' selected="selected"';
							}
							
							html += '>' + json['zone'][i]['name'] + '</option>';
						}

						$('select[name=\'zone_id\']', box).removeClass('none');
					} else {
						html += '<option value="0" selected="selected">' + $cnoc.text_none + '</option>';

						$('select[name=\'zone_id\']', box).addClass('none');
					}

					$('select[name=\'zone_id\']', box).html(html);
					$('select[name=\'city_id\'], select[name=\'district_id\']', box).html(htmlEmpty).addClass('none');
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		} else {
			$.ajax({
				url: 'index.php?route=account/account/zone&zone_id=' + input.val(),
				dataType: 'json',
				beforeSend: function() {
					input.prop('disabled', true);
				},
				complete: function() {
					input.prop('disabled', false);
				},
				success: function(json) {					
					html = htmlEmpty = '<option value="0">' + $cnoc.text_select + '</option>';

					if (json['zone'] && json['zone'] != '') {
						for (i = 0; i < json['zone'].length; i++) {
							html += '<option value="' + json['zone'][i]['zone_id'] + '"';
							
							if (json['zone'][i]['zone_id'] == input.data('zone-id')) {
								html += ' selected="selected"';
							}
							
							html += '>' + json['zone'][i]['name'] + '</option>';
						}

						if (input.attr('name') == 'zone_id') {
							$('select[name=\'city_id\']', box).prop('disabled', false).removeClass('none');

							$('select[name=\'district_id\']', box).addClass('none');
						} else if (input.attr('name') == 'city_id') {
							$('select[name=\'district_id\']', box).prop('disabled', false).removeClass('none');
						}
					} else {
						html += '<option value="0" selected="selected">' + $cnoc.text_none + '</option>';

						if (input.attr('name') == 'zone_id') {
							$('select[name=\'city_id\'], select[name=\'district_id\']', box).prop('disabled', true).addClass('none');
						} else if (input.attr('name') == 'city_id') {
							$('select[name=\'district_id\']', box).prop('disabled', true).addClass('none');
						}
					}

					if (input.attr('name') == 'zone_id') {
						$('select[name=\'city_id\']', box).html(html);
						$('select[name=\'district_id\']', box).html(htmlEmpty);
					} else if (input.attr('name') == 'city_id') {
						$('select[name=\'district_id\']', box).html(html);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	});

	// tooltips on hover
	$('[data-toggle=\'tooltip\']').tooltip({container: 'body', trigger: 'hover'});

	// Makes tooltips work on ajax generated content
	$(document).ajaxStop(function() {
		$('[data-toggle=\'tooltip\']').tooltip({container: 'body', trigger: 'hover'});
	});

	$('body').append('<a href="#" class="back-to-top" style="display:none"><i class="icon-chevron-up"></i></a>');

	if ($(window).scrollTop() > 70) {
		$('.back-to-top').fadeIn();
	} else {
		$('.back-to-top').fadeOut();
	}

	$(function(){
		$(window).scroll(function () {
			if ($(this).scrollTop() > 70) {
				$('.back-to-top').fadeIn();
			} else {
				$('.back-to-top').fadeOut();
			}
		});
		$('.back-to-top').click(function() {
			$('body, html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});

	$('[data-countdown]').each(function(){
		var $this = $(this), finalDate = $(this).data('countdown');
		$this.countdown(finalDate, function(event) {
			$this.html(event.strftime('<span>%D</span>' + $cnoc.text_countdown_days + '<span>%H:%M:%S</span>'));
		});
	});
});

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				$('.alert-dismissible, .text-danger').remove();
				$('[data-toggle=tooltip]').tooltip('hide');

				/*if (json['redirect']) {
					location = json['redirect'];
				}*/
				if (json['error']) {
					if (json['error']['message']) {
						swal({
							html: json['error']['message'],
							type: json['state']
						});
					}
				}

				if (json['state'] == 'success' && json['message']) {
					//$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="icon-checkmark"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					swal({
						html: json['message'],
						type: json['state'],
						showCancelButton: true,
						cancelButtonText: $cnoc.button_confirm,
						confirmButtonText: $cnoc.button_checkout,
						onClose: function(){$('[data-toggle="tooltip"]:focus').tooltip('hide').focusout();}
					}).then(function(){
						location = json['checkout'];
					});

					// Need to set timeout otherwise it wont update the total
					setTimeout(function () {
						$('.cart-quantity').html(json['cart_quantity']);
					}, 100);

					//$('html, body').animate({ scrollTop: 0 }, 'slow');

					$('.quick-cart-box').load('index.php?route=common/cart/info .quick-cart-wrapper');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('.cart-quantity').html(json['cart_quantity']);
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = $cnoc.config_seo_url == '1' ? 'cart' : 'index.php?route=checkout/cart';
				} else {
					$('.quick-cart-box').load('index.php?route=common/cart/info .quick-cart-wrapper');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('.cart-quantity').html(json['cart_quantity']);
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout' || $('.cart-form').length) {
					location = $cnoc.config_seo_url == '1' ? 'cart' : 'index.php?route=checkout/cart';
				} else {
					$('.quick-cart-box').load('index.php?route=common/cart/info .quick-cart-wrapper');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {
				$('#cart > button').button('loading');
			},
			complete: function() {
				$('#cart > button').button('reset');
			},
			success: function(json) {
				// Need to set timeout otherwise it wont update the total
				setTimeout(function () {
					$('#cart > button').html('<span id="cart-total"><i class="icon-cart"></i> ' + json['total'] + '</span>');
				}, 100);

				if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
					location = $cnoc.config_seo_url == '1' ? 'cart' : 'index.php?route=checkout/cart';
				} else {
					$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

var wishlist = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();
				$('[data-toggle=tooltip]').tooltip('hide');

				if (json['redirect']) {
					location = json['redirect'];
				}

				if (json['message']) {
					//$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="icon-checkmark"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
					swal({
						html: json['message'],
						type: json['state']
					});
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);

				//$('html, body').animate({ scrollTop: 0 }, 'slow');
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
}

var compare = {
	'add': function(product_id) {
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
			success: function(json) {
				$('.alert-dismissible').remove();
				$('[data-toggle=tooltip]').tooltip('hide');

				if (json['message']) {
					/*$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="icon-checkmark"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

					$('#compare-total').html(json['total']);

					$('html, body').animate({ scrollTop: 0 }, 'slow');*/
					swal({
						html: json['message'],
						type: json['state']
					});
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function() {

	}
};

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				html = '';

				if (json.length) {
					for (i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);

var cnoc = {
	'notice': function(text, options) {
		this.type = 'notify large';

		$.extend(this, options);

		$.notifyClose();
		$.notify({
			message: text 
		},{
			offset: $(window).height()/2-60,
			placement: {
				from: 'top',
				align: 'center'
			},
			delay: 2000,
			allow_dismiss: false,
			type: this.type
		});
	},
	'alert': function(text, options) {
		this.state = 'success';

		$.extend(this, options);

		swal({
			html: text,
			type: this.state
		});
	}
};
