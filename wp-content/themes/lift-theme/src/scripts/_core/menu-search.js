$("header.site-header .search-submit-toggle").clickToggle(function () {
    $(this).parents('.search-form').toggleClass('active')
},function () {
    $(this).parents('.search-form').toggleClass('active')
});

$("header.site-header .search-submit-close").clickToggle(function () {
    $(this).parents('.search-form').toggleClass('active')
    $(this).parents('#header').toggleClass('search-active')
},function () {
    $(this).parents('.search-form').toggleClass('active')
    $(this).parents('#header').toggleClass('search-active')
});
