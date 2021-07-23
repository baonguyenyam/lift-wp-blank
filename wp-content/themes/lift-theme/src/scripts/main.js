var LIFT_APP = {
	xs: 0,
	sm: 576,
	md: 768,
	lg: 992,
	xl: 1200,
	xxl: 1400,
	lift_clear_canvas_menu: function lift_clear_canvas_menu(e) {
		$('header.site-header .menu-offcanvas-'+e).removeAttr('style')
	},
	lift_gen_canvas_menu: function lift_gen_canvas_menu(e) {
		var getHeaderNormal = $('header#header').outerHeight(true)
		if($('header#header .navbar').hasClass('navbar-expand-'+e)){
			$('header.site-header .menu-offcanvas-'+e).css({
				'top': getHeaderNormal+'px'
			})
		} else {
			$('header.site-header .menu-offcanvas-'+e).removeAttr('style')
		}
	},
	lift_fixed_header: function lift_fixed_header() {
		var getHeader = $('header#header.fixed-top').outerHeight(true)
		if (getHeader) {
			$('html').css({
				'padding-top': getHeader + 'px'
			})
		}
	},
	lift_active_header: function lift_active_header() {
		var st = $(window).scrollTop();
		if (st > 0) {
			$('header#header.fixed-top').addClass('active')
		} else {
			$('header#header.fixed-top').removeClass('active')
		}
	},
	lift_canvas_header: function lift_canvas_header() {
		LIFT_APP.lift_gen_canvas_menu('all')
		if($(window).width() < LIFT_APP.sm){
			LIFT_APP.lift_gen_canvas_menu('sm')
		} else {
			LIFT_APP.lift_clear_canvas_menu('sm')
		}
		if($(window).width() <= LIFT_APP.md){
			LIFT_APP.lift_gen_canvas_menu('md')
		} else {
			LIFT_APP.lift_clear_canvas_menu('md')
		}
		if($(window).width() <= LIFT_APP.lg){
			LIFT_APP.lift_gen_canvas_menu('lg')
		} else {
			LIFT_APP.lift_clear_canvas_menu('lg')
		}
		if($(window).width() <= LIFT_APP.xl){
			LIFT_APP.lift_gen_canvas_menu('xl')
		} else {
			LIFT_APP.lift_clear_canvas_menu('xl')
		}
		if($(window).width() <= LIFT_APP.xxl){
			LIFT_APP.lift_gen_canvas_menu('xxl')
		} else {
			LIFT_APP.lift_clear_canvas_menu('xxl')
		}
	}
}

///////////////////////////////////////////////////
// INIT APP 
///////////////////////////////////////////////////
liftDOMChange(() => {
});

$(function() {
	LIFT_APP.lift_fixed_header()
});

$( document ).ready(function() {
	LIFT_APP.lift_fixed_header()
	LIFT_APP.lift_canvas_header()
});

$(window).scroll(function () {
	LIFT_APP.lift_active_header()
});

$(window).resize(function () {
	LIFT_APP.lift_fixed_header()
	LIFT_APP.lift_canvas_header()
});