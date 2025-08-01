$(document).ready(function(){
 "use strict";

/*==============================================================
    Fullscreen Height
==============================================================*/
function resizefullscreen() {
    var minheight = $(window).height();
    $(".fullscreen").css('min-height', minheight);
}
$(window).resize(function () {
    resizefullscreen();
});
resizefullscreen();

/*==============================================================
// toggler js
==============================================================*/

$("button.navbar-toggler").on('click', function(){
    $(".main-menu-area").addClass("active");
    $(".mm-fullscreen-bg").addClass("active");
    $("body").addClass("hidden");
});

$(".close-box").on('click', function(){
    $(".main-menu-area").removeClass("active");
    $(".mm-fullscreen-bg").removeClass("active");
    $("body").removeClass("hidden");
});

$(".mm-fullscreen-bg").on('click', function(){
    $(".main-menu-area").removeClass("active");
    $(".mm-fullscreen-bg").removeClass("active");
    $("body").removeClass("hidden");
});

/*==============================================================
  Newsletter Popup - Disabled to prevent conflict with promo popup
==============================================================*/
// $('#myModal1').modal('show'); // Commented out to prevent conflict

/*==============================================================
// cart js
==============================================================*/

$(".shopping-cart a.cart-count").on('click', function(){
    $(".mini-cart").addClass("show");
    $(".mm-fullscreen-bg").addClass("active");
    $("body").addClass("hidden");
});

$(".shopping-cart-close").on('click', function(){
    $(".mini-cart").removeClass("show");
    $(".mm-fullscreen-bg").removeClass("active");
    $("body").removeClass("hidden");
});

$(".mm-fullscreen-bg").on('click', function(){
    $(".mini-cart").removeClass("show");
    $(".mm-fullscreen-bg").removeClass("active");
    $("body").removeClass("hidden");
});

/*==============================================================
// header sticky
==============================================================*/
  $(window).scroll(function() {
    var sticky = $('.header-main-area'),
    scroll = $(window).scrollTop();
    if (scroll >= 150) {
      sticky.addClass('is-sticky');
    }
    else {
      sticky.removeClass('is-sticky');
    }
  });

/*==============================================================
// home slider
==============================================================*/
// Check if owlCarousel is available before initializing
if (typeof $.fn.owlCarousel !== 'undefined') {
    $('.home-slider').owlCarousel({
    loop: false,
    items: 1,
    margin: 0,
    nav: true,
    navText : ['<i class="fa fa-angle-double-left"></i>','<i class="fa fa-angle-double-right"></i>'],
    autoplay: false,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    smartSpeed: 0
});
} else {
    console.warn('Owl Carousel library not loaded');
}

$('.home-slider2').owlCarousel({
    loop: true,
    items: 1,
    margin: 0,
    nav: true,
    navText : ['<i class="fa fa-angle-double-left"></i>','<i class="fa fa-angle-double-right"></i>'],
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    fade: true,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

/*==============================================================
// category image slider
==============================================================*/
$('.home-category').owlCarousel({
    loop: true,
    margin: 30,
    nav: true,
    navText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: false,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0: {
          items: 1,
          margin: 15
        },
        320: {
          items: 2,
          margin: 15
        },
        479: {
          items: 2,
          margin: 15
        },
        540: {
          items: 3,
          margin: 15
        },
        750: {
          items: 3,
          margin: 15
        },
        768: {
          items: 3
        },
        979: {
          items: 5
        },
        1199: {
          items: 5
        },
        1399: {
          items: 6
        },
        1599: {
          items: 6
        }
    }
});

/*==============================================================
// trending products slider
==============================================================*/

$('.trending-products').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: [
        '<i class="fa fa-chevron-left" style="color: #ff6b35; font-size: 18px;"></i>',
        '<i class="fa fa-chevron-right" style="color: #ff6b35; font-size: 18px;"></i>'
    ],
    dots: false,
    autoplay: false,
    autoplayHoverPause: true,
    mouseDrag: true,
    touchDrag: true,
    responsive: {
      0: {
        items: 2,
        margin: 15,
        nav: false
      },
      479: {
        items: 2,
        margin: 15,
        nav: true
      },
      540: {
        items: 2,
        margin: 15,
        nav: true
      },
      640: {
        items: 3,
        margin: 15,
        nav: true
      },
      768: {
        items: 3,
        nav: true
      },
      979: {
        items: 4,
        nav: true
      },
      1199: {
        items: 4,
        nav: true
      }
    }
});

/*==============================================================
//quick view slider
==============================================================*/
  $('.quick-slider').owlCarousel({
    loop: false,
    margin: 10,
    autoHeight : true,
    nav: true,
    navText: [
        '<i class="fa fa-chevron-left" style="color: #ff6b35; font-size: 14px;"></i>',
        '<i class="fa fa-chevron-right" style="color: #ff6b35; font-size: 14px;"></i>'
    ],
    dots: false,
    autoplay: false,
    mouseDrag: true,
    touchDrag: true,
    sautoplayTimeout: 1000,
    autoplayHoverPause: true,
    responsive:{
      0:{
        items:3,
        nav: false
      },
      480:{
        items:3,
        nav: true
      },
      600:{
        items:3,
        nav: true
      },
      1000:{
        items:4,
        nav: true
      }
    }
  });

/*==============================================================
// deal countdown js
==============================================================*/
    if(document.getElementById('days1'))
    {
        const second = 1000,
        minute = second * 60,
        hour = minute * 60,
        day = hour * 24;
        x = setInterval(function() {
        if(document.querySelectorAll('.contdown_row').length == 1){
                document.getElementById('days').innerText = Math.floor(distance / (day)),
                document.getElementById('hours').innerText = Math.floor((distance % (day)) / (hour)),
                document.getElementById('minutes').innerText = Math.floor((distance % (hour)) / (minute)),
                document.getElementById('seconds').innerText = Math.floor((distance % (minute)) / second);
        }else{
            var i;
            for (i = 1; i <= document.querySelectorAll('.contdown_row').length; i++) {
                console.log($('[data-timer='+i+']').attr('data-date'));
                var date_date = $('[data-timer='+i+']').attr('data-date');
                var date_timer = $('.contdown_row').attr('data-timer');
                    var countDown = new Date(date_date).getTime();
                    var now = new Date().getTime();
                    var distance = countDown - now;
                    if(document.getElementById('days'+[i])){
                        document.getElementById('days'+[i]).innerText = Math.floor(distance / (day)),
                        document.getElementById('hours'+[i]).innerText = Math.floor((distance % (day)) / (hour)),
                        document.getElementById('minutes'+[i]).innerText = Math.floor((distance % (hour)) / (minute)),
                        document.getElementById('seconds'+[i]).innerText = Math.floor((distance % (minute)) / second);
                    }
                }
            }
        }, second)
    }

/*==============================================================
// swiper product-tab slider
==============================================================*/
var swiper = new Swiper('.swiper-container.home-pro-tab', {
    slidesPerView: 4,
    slidesPerColumn: 2,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        991: {
            slidesPerView: 3
        },
        1199: {
            slidesPerView: 4
        }
    }
});

/*==============================================================
// testimonials slider
==============================================================*/
$('.testi-m').owlCarousel({
    loop: false,
    rewind: true,
    nav: true,
    margin: 30,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 1,
            margin: 15
        },
        768: {
            items: 2
        },
        979: {
            items: 2
        },
        1199: {
            items: 2
        }
    }
});

/*==============================================================
// blog slider
==============================================================*/

$('.home-blog').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: false,
    dots: false,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 2,
            margin: 15
        },
        768: {
            items: 2
        },
        979: {
            items: 2
        },
        1199: {
            items: 3
        }
    }
});

/*==============================================================
// brand-logo slider
==============================================================*/

$('.brand-carousel').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: false,
    dots: false,
    autoplay: true,
    slideTransition: 'linear',
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 2
        },
        479: {
            items: 2
        },
        540: {
            items: 3
        },
        768: {
            items: 5
        },
        979: {
            items: 6
        },
        1199: {
            items: 6
        }
    }
});

/*==============================================================
// back to top js
==============================================================*/

$(window).on('scroll',function() {
    if ($(this).scrollTop() > 600) {
        $('#top').addClass('show');
    } else {
        $('#top').removeClass('show');
    }
});
$('#top').on('click',function() {
    $("html, body").animate({ scrollTop: 0 }, 600);
    return false;
});

// **************************************** home-2 ********************************************

/*==============================================================
// trending products sliders
==============================================================*/

$('.home2-trending').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items: 2,
            margin: 15
        },
        479:{
            items: 2,
            margin: 15
        },
        640:{
            items: 3,
            margin: 15
        },
        768:{
            items: 3
        },
        979:{
            items: 4
        },
        1199:{
            items: 5
        }
    }
});

/*==============================================================
//category image
==============================================================*/

$('.home2-cate-image').owlCarousel({
    loop: true,
    rewind: true,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:2,
            margin: 0
        },
        479:{
            items:2,
            margin: 0
        },
        600:{
            items:3,
            margin: 0
        },
        640:{
            items:4,
            margin: 0
        },
        768:{
            items:4,
            margin: 20
        },
        979:{
            items:5,
            margin: 20
        },
        1199:{
            items:7,
            margin: 20
        }
    }
});

/*==============================================================
// swiper product-tab slider
==============================================================*/
var swiper = new Swiper('.swiper-container.our-products-tab', {
    slidesPerView: 3,
    slidesPerColumn: 3,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 1,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 2
        },
        1024: {
            slidesPerView: 2,
            slidesPerColumn: 3
        }
    }
});

/*==============================================================
// testimonials slider
==============================================================*/

$('.home2-testi').owlCarousel({
    loop: false,
    rewind: true,
    nav: true,
    margin: 30,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 1,
            margin: 15
        },
        768: {
            items: 1
        },
        979: {
            items: 1
        },
        1199: {
            items: 1
        }
    }
});

/*==============================================================
// featured products slider
==============================================================*/

$('.featured').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items: 2,
            margin: 15
        },
        479:{
            items: 2,
            margin: 15
        },
        640:{
            items: 3,
            margin: 15
        },
        768:{
            items: 3
        },
        979:{
            items: 4
        },
        1199:{
            items: 5
        }
    }
});

/*==============================================================
// blog
==============================================================*/

$('.blog2').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: false,
    dots: false,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 2,
            margin: 15
        },
        768: {
            items: 2
        },
        979: {
            items: 3
        },
        1199: {
            items: 4
        }
    }
});

// **************************************** home-3********************************************

/*==============================================================
// home slider
==============================================================*/

$('.home-slider3').owlCarousel({
    loop: false,
    items: 1,
    margin: 0,
    nav: true,
    navText : ['<i class="fa fa-angle-double-left"></i>','<i class="fa fa-angle-double-right"></i>'],
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    fade: true,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

/*==============================================================
// swiper product-tab slider
==============================================================*/

var swiper = new Swiper('.swiper-container.our-pro-tab', {
    slidesPerView: 4,
    slidesPerColumn: 1,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 3
        },
        1024: {
            slidesPerView: 3
        },
        1199: {
            slidesPerView: 4
        }
    }
});

/*==============================================================
// special products swiper
==============================================================*/

var swiper = new Swiper('.swiper-container.special-pro', {
    slidesPerView: 1,
    slidesPerColumn: 3,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        0: {
            slidesPerColumn: 2,
            slidesPerView: 1,
        },
        640: {
            slidesPerColumn: 2,
            slidesPerView: 1,
        },
        768: {
            slidesPerColumn: 3,
            slidesPerView: 2,
        },
        1024: {
            slidesPerColumn: 2
        }
    }
});

/*==============================================================
// testimonials slider
==============================================================*/

$('.testi-3').owlCarousel({
    loop: false,
    rewind: true,
    nav: false,
    margin: 30,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 1,
            margin: 15
        },
        768: {
            items: 1
        },
        979: {
            items: 1
        },
        1199: {
            items: 1
        }
    }
});

/*==============================================================
// deal of the day
==============================================================*/

$('.deal-day').owlCarousel({
    loop: false,
    rewind: true,
    nav: true,
    dots:false,
    margin: 30,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 2,
            margin: 15
        },
        768: {
            items: 1
        },
        979: {
            items: 1
        },
        1199: {
            items: 1
        }
    }
});

/*==============================================================
// trending products swiper
==============================================================*/

var swiper = new Swiper('.swiper-container.trening-left-pro', {
    slidesPerView: 1,
    slidesPerColumn: 3,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        0: {
            slidesPerColumn: 2,
            slidesPerView: 1,
        },
        640: {
            slidesPerColumn: 2,
            slidesPerView: 1,
        },
        768: {
            slidesPerColumn: 3,
            slidesPerView: 2,
        },
        1024: {
            slidesPerColumn: 2
        }
    }
});

/*==============================================================
// featured products slider
==============================================================*/

$('.featured-products-slider').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items: 2,
            margin: 15
        },
        479:{
            items: 2,
            margin: 15
        },
        640:{
            items: 3,
            margin: 15
        },
        768:{
            items: 3
        },
        979:{
            items: 3
        },
        1199:{
            items: 4
        }
    }
});

/*==============================================================
//brand
==============================================================*/

$('.home3-brand').owlCarousel({
    loop: false,
    rewind: true,
    margin: 0,
    nav: false,
    dots: false,autoplay: true,
    slideTransition: 'linear',
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:2
        },
        479:{
            items:3
        },
        768:{
            items:4
        },
        979:{
            items:2
        },
        1199:{
            items: 2
        }
    }
});

/*==============================================================
//blog
==============================================================*/

$('.home3-blog').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    lazyLoad:true,
    nav: false,
    dots: false,responsive:{
        0:{
            items:1,
            margin: 15
        },
        479:{
            items:2,
            margin: 15
        },
        768:{
            items:2
        },
        979:{
            items:2
        },
        1199:{
            items:3
        }
    }
});

// **************************************** home-4********************************************

/*==============================================================
// home slider
==============================================================*/

$('.home4-slider').owlCarousel({
    loop: false,
    items: 1,
    margin: 0,
    nav: true,
    navText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    fade: true,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

/*==============================================================
// swiper product-tab slider
==============================================================*/

var swiper = new Swiper('.swiper-container.home4-tab', {
    slidesPerView: 5,
    slidesPerColumn: 2,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
    },
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 3
        },
        1024: {
            slidesPerView: 4
        }
    }
});

/*==============================================================
//category image
==============================================================*/

$('.home4-cate').owlCarousel({
    loop: true,
    rewind: true,
    nav: true,
    margin: 30,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:2,
            margin: 15
        },
        479:{
            items:3,
            margin: 15
        },
        768:{
            items:3,
        },
        979:{
            items:4,
        },
        1199:{
            items:5,
        }
    }
});

/*==============================================================
//home featured image
==============================================================*/

$('.home4-featured').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items: 2,
            margin: 15
        },
        479:{
            items: 2,
            margin: 15
        },
        640:{
            items: 3,
            margin: 15
        },
        768:{
            items: 3
        },
        979:{
            items: 4
        },
        1199:{
            items: 5
        }
    }
});

/*==============================================================
//brand slider
==============================================================*/

$('.home4-brand').owlCarousel({
    loop: false,
    rewind: true,
    margin: 0,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    slideTransition: 'linear',
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:2
        },
        479:{
            items:3
        },
        768:{
            items:4
        },
        979:{
            items:4
        },
        1199:{
            items:5
        }
    }
});


// **************************************** home-5********************************************

/*==============================================================
//swiper slider
==============================================================*/

var swiper = new Swiper('.home5-slider', {
    slidesPerColumn: 1,
    slidesPerView: 1,
    dots: false,
    effect: 'fade',
    navigation: {
        nextEl: '.swiper-next',
        prevEl: '.swiper-prev',
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
        renderBullet: function (index, className) {
            return '<span class="' + className + '">' + '0' + (index + 1) + '</span>';
        },
    }
});

/*==============================================================
//category image slider
==============================================================*/

$('.home5-cate-image').owlCarousel({
    loop: true,
    rewind: true,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:2,
            margin: 5
        },
        479:{
            items:3,
            margin: 5
        },
        768:{
            items:4,
            margin: 20
        },
        979:{
            items:5,
            margin: 20
        },
        1199:{
            items:6,
            margin: 20
        }
    }
});

/*==============================================================
// swiper product-tab slider
==============================================================*/

var swiper = new Swiper('.swiper-container.home5-tab', {
    slidesPerView: 4,
    slidesPerColumn: 2,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    navigation: {
        prevEl: '.swiper-button-prev',
        nextEl: '.swiper-button-next',
    },
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 3
        },
        1024: {
            slidesPerView: 4
        }
    }
});

/*==============================================================
//featured
==============================================================*/

$('.featured5-pro').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items: 2,
            margin: 15
        },
        479:{
            items: 2,
            margin: 15
        },
        640:{
            items: 3,
            margin: 15
        },
        768:{
            items: 3
        },
        979:{
            items: 4
        },
        1199:{
            items: 4 
        }
    }
});

/*==============================================================
// blog
==============================================================*/

$('.blog5').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 2,
            margin: 15
        },
        768: {
            items: 2
        },
        979: {
            items: 3
        },
        1199: {
            items: 3
        }
    }
});


// **************************************** home-6********************************************

/*==============================================================
// home slider
==============================================================*/

$('.home6-slider').owlCarousel({
    loop: false,
    items: 1,
    rewind: true,
    margin: 0,
    nav: true,
    navText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    fade: true,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

/*==============================================================
//category
==============================================================*/

$('.cate-6').owlCarousel({
    loop: false,
    rewind: true,
    nav: true,
    margin: 0,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,autoplay: false,
    responsive:{
        0:{
            items:1,
        },
        479:{
            items:2
        },
        768:{
            items:2
        },
        979:{
            items:3
        },
        1199:{
            items:4
        }
    }
});

/*==============================================================
// swiper product-tab slider
==============================================================*/

var swiper = new Swiper('.swiper-container.home6-tab', {
    slidesPerView: 5,
    slidesPerColumn: 2,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 3
        },
        1024: {
            slidesPerView: 4
        }
    }
});

/*==============================================================
// testimonials slider
==============================================================*/

$('.testi-6').owlCarousel({
    loop: false,
    rewind: true,
    nav: false,
    margin: 30,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
        },
        479: {
            items: 1,
        },
        768: {
            items: 2
        },
        979: {
            items: 2
        },
        1199: {
            items: 3
        }
    }
});


/*==============================================================
//featured product
==============================================================*/

$('.home6-featured').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    lazyLoad:true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items: 2,
            margin: 15
        },
        479:{
            items: 2,
            margin: 15
        },
        640:{
            items: 2,
            margin: 15
        },
        768:{
            items: 3
        },
        979:{
            items: 4
        },
        1199:{
            items: 5
        }
    }
});

/*==============================================================
// blog slider
==============================================================*/

$('.blog-6').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: false,
    dots: false,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 2,
            margin: 15
        },
        768: {
            items: 2
        },
        979: {
            items: 3
        },
        1199: {
            items: 4
        }
    }
});

// **************************************** home-7********************************************

/*==============================================================
// home slider
==============================================================*/

$('.home-slider7').owlCarousel({
    loop: false,
    items: 1,
    margin: 0,
    nav: true,
    dots: false,
    navText : ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    fade: true,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

/*==============================================================
//category
==============================================================*/

$('.cate-7').owlCarousel({
    loop: true,
    rewind: true,
    nav: false,
    margin: 60,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,autoplay: false,
    responsive:{
        0:{
            items:1,
        },
        479:{
            items:1
        },
        768:{
            items:2,
            margin: 30
        },
        979:{
            items:2,
            margin: 30
        },
        1199:{
            items:3,
            margin: 30
        }
    }
});

/*==============================================================
// swiper product-tab slider
==============================================================*/

var swiper = new Swiper('.home-7-tab', {
    slidesPerColumn: 2,
    slidesPerView: 4,
    spaceBetween: 30,
    observer: true,
    observeParents: true,navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 3
        },
        1024: {
            slidesPerView: 3
        }
    },
});

/*==============================================================
//special
==============================================================*/

$('.special-7').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    lazyLoad:true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,
    autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items: 2,
            margin: 15
        },
        479:{
            items: 2,
            margin: 15
        },
        640:{
            items: 3,
            margin: 15
        },
        768:{
            items: 3
        },
        979:{
            items: 3
        },
        1199:{
            items: 4
        }
    }
});

/*==============================================================
//Blog
==============================================================*/

$('.blog-7').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    lazyLoad:true,
    nav: false,
    dots: false,
    responsive: {
        0: {
            items: 1,
            margin: 15,
        },
        479: {
            items: 2,
            margin: 15,
        },
        768: {
            items: 2
        },
        979: {
            items: 3
        },
        1199: {
            items: 4
        }
    }
});

// **************************************** About page********************************************

/*==============================================================
//counter
==============================================================*/

(function ($) {
    $.fn.countTo = function (options) {
        options = options || {};

        return $(this).each(function () {
            // set options for current element
            var settings = $.extend({}, $.fn.countTo.defaults, {
                from:            $(this).data('from'),
                to:              $(this).data('to'),
                speed:           $(this).data('speed'),
                refreshInterval: $(this).data('refresh-interval'),
                decimals:        $(this).data('decimals')
            }, options);

            // how many times to update the value, and how much to increment the value on each update
            var loops = Math.ceil(settings.speed / settings.refreshInterval),
            increment = (settings.to - settings.from) / loops;

            // references & variables that will change with each update
            var self = this,
            $self = $(this),
            loopCount = 0,
            value = settings.from,
            data = $self.data('countTo') || {};

            $self.data('countTo', data);

            // if an existing interval can be found, clear it first
            if (data.interval) {
                clearInterval(data.interval);
            }
            data.interval = setInterval(updateTimer, settings.refreshInterval);

            // initialize the element with the starting value
            render(value);

            function updateTimer() {
                value += increment;
                loopCount++;

                render(value);

                if (typeof(settings.onUpdate) == 'function') {
                    settings.onUpdate.call(self, value);
                }

                if (loopCount >= loops) {
                    // remove the interval
                    $self.removeData('countTo');
                    clearInterval(data.interval);
                    value = settings.to;

                    if (typeof(settings.onComplete) == 'function') {
                        settings.onComplete.call(self, value);
                    }
                }
            }

            function render(value) {
                var formattedValue = settings.formatter.call(self, value, settings);
                $self.html(formattedValue);
            }
        });
    };

    $.fn.countTo.defaults = {
        from: 0,               // the number the element should start at
        to: 0,                 // the number the element should end at
        speed: 1000,           // how long it should take to count between the target numbers
        refreshInterval: 100,  // how often the element should be updated
        decimals: 0,           // the number of decimal places to show
        formatter: formatter,  // handler for formatting the value before rendering
        onUpdate: null,        // callback method for every time the element is updated
        onComplete: null       // callback method for when the element finishes updating
    };

    function formatter(value, settings) {
        return value.toFixed(settings.decimals);
    }

}(jQuery));

jQuery(function ($) {
    // custom formatting example
    $('.count-number').data('countToOptions', {
        formatter: function (value, options) {
            return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
        }
    });

    // start all the timers
    $('.timer').each(count);

    function count(options) {
        var $this = $(this);
        options = $.extend({}, options || {}, $this.data('countToOptions') || {});
        $this.countTo(options);
    }
});


// **************************************** cart page********************************************

/* ========================================== 
  Minus and Plus Btn Height
  ========================================== */

  $('.minus-btn,.minus-btn-1').on('click', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $input = $this.closest('div').find('input');
    var value = parseInt($input.val(),10);

    if (value > 1) {
      value = value - 1;
    } else {
      value = 0;
    }
    $input.val(value);
  });

  $('.plus-btn,.plus-btn-1').on('click', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $input = $this.closest('div').find('input');
    var value = parseInt($input.val(),10);

    if (value < 100) {
      value = value + 1;
    } else {
      value =100;
    }
    $input.val(value);
  });

// **************************************** product page ********************************************

    
  /* ========================================== 
  //additional
  ========================================== */
  
    $('.pro-page-slider').owlCarousel({
        loop: true,
        margin: 15,
        nav: true,
        navText: [
            '<i class="fa fa-chevron-left" style="color: #ff6b35; font-size: 16px;"></i>',
            '<i class="fa fa-chevron-right" style="color: #ff6b35; font-size: 16px;"></i>'
        ],
        dots: false,
        autoplay: false,
        autoplayHoverPause: true,
        mouseDrag: true,
        touchDrag: true,
        responsive:{
          0:{
            items:3,
            margin: 10,
            nav: false
          },
          480:{
            items:4,
            margin: 12,
            nav: true
          },
          600:{
            items:4,
            margin: 15,
            nav: true
          },
          768:{
            items:5,
            margin: 15,
            nav: true
          },
          1000:{
            items:5,
            margin: 15,
            nav: true
          }
        }
   });

    // Enhanced styling for all product sliders navigation
    $(document).ready(function() {
        // Universal slider navigation styling function
        function styleSliderNavigation(sliderClass, buttonSize = '45px', leftOffset = '-20px', rightOffset = '-20px') {
            $(sliderClass + ' .owl-nav').css({
                'position': 'absolute',
                'top': '50%',
                'width': '100%',
                'transform': 'translateY(-50%)',
                'pointer-events': 'none',
                'z-index': '10',
                'margin-top': '0'
            });

            $(sliderClass + ' .owl-nav button').css({
                'position': 'absolute',
                'background': 'rgba(255, 255, 255, 0.95)',
                'border': '2px solid #ff6b35',
                'border-radius': '50%',
                'width': buttonSize,
                'height': buttonSize,
                'display': 'flex',
                'align-items': 'center',
                'justify-content': 'center',
                'pointer-events': 'all',
                'transition': 'all 0.3s ease',
                'box-shadow': '0 4px 15px rgba(0, 0, 0, 0.1)',
                'opacity': '0.8'
            });

            $(sliderClass + ' .owl-nav .owl-prev').css({
                'left': leftOffset
            });

            $(sliderClass + ' .owl-nav .owl-next').css({
                'right': rightOffset
            });

            // Hover effects
            $(sliderClass + ' .owl-nav button').hover(
                function() {
                    $(this).css({
                        'background': '#ff6b35',
                        'transform': 'scale(1.1)',
                        'box-shadow': '0 6px 20px rgba(255, 107, 53, 0.3)',
                        'opacity': '1'
                    });
                    $(this).find('i').css('color', 'white');
                },
                function() {
                    $(this).css({
                        'background': 'rgba(255, 255, 255, 0.95)',
                        'transform': 'scale(1)',
                        'box-shadow': '0 4px 15px rgba(0, 0, 0, 0.1)',
                        'opacity': '0.8'
                    });
                    $(this).find('i').css('color', '#ff6b35');
                }
            );

            // Show/hide navigation on hover
            $(sliderClass).hover(
                function() {
                    $(this).find('.owl-nav button').css({
                        'opacity': '1',
                        'visibility': 'visible'
                    });
                },
                function() {
                    $(this).find('.owl-nav button').css({
                        'opacity': '0.8',
                        'visibility': 'visible'
                    });
                }
            );
        }

        // Apply styling to different sliders
        styleSliderNavigation('.trending-products', '45px', '-20px', '-20px');
        styleSliderNavigation('.pro-page-slider', '40px', '-15px', '-15px');
        styleSliderNavigation('.quick-slider', '35px', '-15px', '-15px');
        styleSliderNavigation('.home-category', '45px', '-20px', '-20px');
        styleSliderNavigation('.featured-products-slider', '45px', '-20px', '-20px');
    });

    $('.pro-pag-5-slider').owlCarousel({
        loop: false,
        margin: 15,
        nav: true,
        navText: ['<i class="ti-arrow-left"></i>','<i class="ti-arrow-right"></i>'],
        dots: false,
        responsive:{
          0:{
            items:3
          },
          600:{
            items:4
          },
          1000:{
            items:4
          }
        }
    });


    $('.pro-page .nav-item .nav-link').on( "click", function() {
        $('.pro-page .nav-item .nav-link').removeClass('active');
        $(this).addClass('active');
    });

  /* ========================================== 
   //related product
  ========================================== */
  $('.releted-products').owlCarousel({
  loop: false,
  rewind: true,
  margin: 30,
  nav: false,
  dots: false,
  autoplay: true,
  sautoplayTimeout: 5000,
  autoplayHoverPause: true,
  responsive:{
    0:{
      items:2,
      margin: 15
    },
    480:{
      items: 2
    },
    768:{
      items: 3
    },
    979:{
      items: 3
    },
    1200:{
      items: 4
    }
  }
});  

   /* ========================================== 
   // index 7
  ========================================== */

  $('.releted-products-7').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    nav: true,
    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
    dots: false,autoplay: true,
    sautoplayTimeout: 5000,
    autoplayHoverPause: true,
    responsive:{
      0:{
        items:2,
        margin: 15
      },
      480:{
        items:2
      },
      768:{
        items:2
      },
      979:{
        items:3
      }
    }
  });

// **************************************** coming soon ********************************************


if(document.getElementById('day')){
    var deadline = new Date("july 30, 2050 15:37:25").getTime();             
    var x = setInterval(function() {
       var currentTime = new Date().getTime();                
       var t = deadline - currentTime; 
       var days = Math.floor(t / (1000 * 60 * 60 * 24)); 
       var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60)); 
       var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60)); 
       var seconds = Math.floor((t % (1000 * 60)) / 1000); 
       
           document.getElementById("day").innerHTML = days ; 
           document.getElementById("hour").innerHTML =hours; 
           document.getElementById("minute").innerHTML = minutes; 
           document.getElementById("second").innerHTML =seconds; 
           if (t < 0) {
              clearInterval(x); 
              document.getElementById("time-up").innerHTML = "TIME UP"; 
              document.getElementById("day").innerHTML ='0'; 
              document.getElementById("hour").innerHTML ='0'; 
              document.getElementById("minute").innerHTML ='0' ; 
              document.getElementById("second").innerHTML = '0'; 
           } 
        

    }, 1000); 

}

if(document.getElementById('days')){
    //alert('sf');
    var deadline = new Date("july 30, 2026 15:37:25").getTime();             
    var x = setInterval(function() {
       var currentTime = new Date().getTime();                
       var t = deadline - currentTime; 
       var days = Math.floor(t / (1000 * 60 * 60 * 24)); 
       var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60)); 
       var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60)); 
       var seconds = Math.floor((t % (1000 * 60)) / 1000); 
       
           document.getElementById("days").innerHTML = days ; 
           document.getElementById("hours").innerHTML =hours; 
           document.getElementById("minutes").innerHTML = minutes; 
           document.getElementById("seconds").innerHTML =seconds; 
           if (t < 0) {
              clearInterval(x); 
              document.getElementById("time-up").innerHTML = "TIME UP"; 
              document.getElementById("days").innerHTML ='0'; 
              document.getElementById("hours").innerHTML ='0'; 
              document.getElementById("minutes").innerHTML ='0' ; 
              document.getElementById("seconds").innerHTML = '0'; 
           } 
    }, 1000); 
}

// **************************************** blog page ********************************************

$('.single-image-carousel').owlCarousel({
    loop: false,
    rewind: true,
    nav: false,
    margin: 30,
    autoplay: true,
    autoplayTimeout: 2000,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
            margin: 15
        },
        479: {
            items: 1,
            margin: 15
        },
        768: {
            items: 1
        },
        979: {
            items: 1
        },
        1199: {
            items: 1
        }
    }
});

/* ========================================== 
   //blog
  ========================================== */

$('.details-blog-carousel').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    lazyLoad:true,
    nav: false,
    dots: false,
    autoplay: true,
    autoplayTimeout: 2000,
    autoplayHoverPause: true,
    responsive:{
        0:{
            items:1,
            margin: 15
        },
        479:{
            items:2,
            margin: 20
        },
        768:{
            items:2
        },
        979:{
            items:3
        },
        1199:{
            items:3
        }
    }
});

// **************************************** home-8 ********************************************

$('.home-slider-main').owlCarousel({
    loop: false,
    items: 1,
    margin: 0,
    nav: true,
    navText : ['<i class="ti-angle-left"></i>','<i class="ti-angle-right"></i>'],
    autoplay: false,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    dots: false,
    fade: true,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

// category
var swiper = new Swiper('.swiper-container.category-slider', {
    slidesPerView: 4,
    slidesPerColumn: 1,
    spaceBetween: 30,
    loop: true,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        991: {
            slidesPerView: 3
        },
        1199: {
            slidesPerView: 3
        }
    }
});

// swiper product-tab slider
$( document ).ready(function() {
  var swiper = new Swiper('.swiper-container.home8-tab-product', {
    slidesPerColumn: 2,
    slidesPerView: 5,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    breakpoints: {
      0: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      640: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      767: {
        slidesPerView: 3,
        spaceBetween: 15
      },
      768: {
        slidesPerView: 3
      },
      1024: {
        slidesPerView: 4
      }
    },
  });
});

// blog
$('.blog-home8').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    lazyLoad:true,
    nav: false,
    dots: false,
    responsive: true,
    responsive:{
      0:{
        items:1,
        margin: 15
      },
      479:{
        items:1,
        margin: 15
      },
      640:{
        items:2,
        margin: 15
      },
      768:{
        items:2
      },
      979:{
        items:3
      },
      1199:{
        items:3
      }
    }
    });

// brand logo
$('.brand-slider').owlCarousel({
    loop: false,
    rewind: true,
    margin: 0,
    nav: false,
    dots: false,
    autoplay: true,
    slideTransition: 'linear',
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
      0:{
        items:2
      },
      479:{
        items:2
      },
      768:{
        items:4
      },
      979:{
        items:4
      },
      1199:{
        items:5
      }
    }
});

// **************************************** home-9 ********************************************
/*==============================================================
// top category slider
==============================================================*/
  var swiper = new Swiper('.swiper-container#top-category', {
    slidesPerColumn: 1,
    slidesPerView: 5,
    spaceBetween: 0,
    observer: true,
    loop: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-next-cat',
        prevEl: '.swiper-prev-cat',
    },
    breakpoints: {
      0: {
        slidesPerView: 1,
      },
      540: {
        slidesPerView: 1,
      },
      640: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 3,
      },
      1024: {
        slidesPerView: 3,
      },
      1599: {
        slidesPerView: 4,
      }
    }
  });

/*==============================================================
// home main slider slider
==============================================================*/
  var swiper = new Swiper('.swiper-container#home-slider-09', {
    slidesPerColumn: 1,
    slidesPerView: 1,
    spaceBetween: 0,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-next-slider',
        prevEl: '.swiper-prev-slider',
    },
  });

/*==============================================================
// swiper product-tab slider
==============================================================*/

var swiper = new Swiper('.swiper-container.product-tab', {
    slidesPerView: 5,
    slidesPerColumn: 2,
    spaceBetween: 30,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: false,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 3,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 3
        },
        1024: {
            slidesPerView: 4
        },
        1199: {
            slidesPerView: 4
        }
    }
});

/*==============================================================
// category slider
==============================================================*/
var swiper = new Swiper('.swiper-container#category-slider', {
slidesPerColumn: 2,
slidesPerView: 4,
spaceBetween: 30,
observer: true,
observeParents: true,
navigation: {
  nextEl: '.swiper-next-cat',
  prevEl: '.swiper-prev-cat',
},
breakpoints: {
  0: {
    slidesPerView: 1,
    spaceBetween: 15
  },
  320: {
    slidesPerView: 1,
    spaceBetween: 15
  },
  540: {
    slidesPerView: 2,
    spaceBetween: 15
  },
  767: {
    slidesPerView: 3,
    spaceBetween: 15
  },
  768: {
    slidesPerView: 3
  },
  1024: {
    slidesPerView: 2
  },
  1360: {
    slidesPerView: 3
  }
},
});
    
// testimonials 
var swiper = new Swiper('.swiper-container#testimonials', {
  slidesPerColumn: 1,
  slidesPerView: 3,
  spaceBetween: 30,
  observer: true,
  observeParents: true,
  navigation: {
    nextEl: '.swiper-next-testi',
    prevEl: '.swiper-prev-testi',
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    540: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    640: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    768: {
      slidesPerView: 1
    },
    1024: {
      slidesPerView: 2
    },
    1360: {
      slidesPerView: 3
    }
  },
});

// featured product
var swiper = new Swiper('.swiper-container#featured-9', {
  slidesPerColumn: 1,
  slidesPerView: 5,
  spaceBetween: 30,
  observer: true,
  observeParents: true,
  breakpoints: {
    0: {
      slidesPerView: 2,
      spaceBetween: 15
    },
    640: {
      slidesPerView: 2,
      spaceBetween: 15
    },
    767: {
      slidesPerView: 3,
      spaceBetween: 15
    },
    768: {
      slidesPerView: 3
    },
    1024: {
      slidesPerView: 4
    }
  },
});

// blog
 $('#blog-slider-09').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    lazyLoad:true,
    nav: false,
    dots: false,
    responsive: true,
    responsive:{
      0:{
        items:1,
        margin: 15
      },
      479:{
        items:1,
        margin: 15
      },
      640:{
        items:2,
        margin: 15
      },
      768:{
        items:2
      },
      979:{
        items:2
      },
      1199:{
        items: 3
      }
    }
  });

// **************************************** home-10 ********************************************
$('.home-10-slider').owlCarousel({
    loop: false,
    items: 1,
    margin: 0,
    nav: true,
    navText : ['<i class="ti-angle-left"></i>','<i class="ti-angle-right"></i>'],
    dots: false,
    fade: true,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

// product tab slider
var swiper = new Swiper('.swiper-container.home10-tab-product', {
slidesPerColumn: 2,
slidesPerView: 4,
spaceBetween: 30,
observer: true,
observeParents: true,
breakpoints: {
      0: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      640: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      767: {
        slidesPerView: 3,
        spaceBetween: 15
      },
      768: {
        slidesPerView: 3
      },
      1024: {
        slidesPerView: 3
      }
    },
});

// special product 
var swiper = new Swiper('.swiper-container.healthy-product', {
slidesPerColumn: 1,
slidesPerView: 4,
spaceBetween: 30,
observer: true,
observeParents: true,
breakpoints: {
  0: {
    slidesPerView: 2,
    spaceBetween: 15
  },
  640: {
    slidesPerView: 2,
    spaceBetween: 15
  },
  767: {
    slidesPerView: 3,
    spaceBetween: 15
  },
  768: {
    slidesPerView: 3
  },
  1024: {
    slidesPerView: 3
  }
},
});

// blog
$('.blog-home10').owlCarousel({
    loop: false,
    rewind: true,
    margin: 0,
    lazyLoad:true,
    nav: false,
    dots: false,
    responsive: true,responsive:{
      0:{
        items:1
      },
      479:{
        items:1
      },
      768:{
        items:2
      },
      979:{
        items:2
      },
      1199:{
        items:3
      }
    }
});

// brand logo
$('#brand-10').owlCarousel({
    loop: false,
    rewind: true,
    margin: 0,
    nav: false,
    dots: false,
    autoplay: true,
    slideTransition: 'linear',
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
      0:{
        items:2
      },
      479:{
        items:2
      },
      540:{
        items:4
      },
      768:{
        items:4
      },
      979:{
        items:4
      },
      1199:{
        items:5
      }
    }
});

// **************************************** home-11 ********************************************
/*==============================================================
// category slider
==============================================================*/
var swiper = new Swiper('.swiper-container#header-category', {
slidesPerColumn: 1,
slidesPerView: 5,
spaceBetween: 0,
loop: true,
observer: true,
observeParents: true,
navigation: {
  nextEl: '.swiper-next-cat',
  prevEl: '.swiper-prev-cat',
    },breakpoints: {
      0: {
        slidesPerView: 1
      },
      320: {
        slidesPerView: 1
      },
      540: {
        slidesPerView: 1
      },
      640: {
        slidesPerView: 2
      },
      768: {
        slidesPerView: 2
      },
      1024: {
        slidesPerView: 2
      },
      1599: {
        slidesPerView: 4
      }
    },
});

/*==============================================================
// swiper product-tab slider
==============================================================*/
var swiper = new Swiper('.swiper-container.home-pro-tab-slider', {
    slidesPerView: 4,
    slidesPerColumn: 2,
    spaceBetween: 30,
    loop: true,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: false,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    breakpoints: {
        0: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        640: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        767: {
            slidesPerView: 2,
            spaceBetween: 15
        },
        768: {
            slidesPerView: 2
        },
        1299: {
            slidesPerView: 3
        }
    }
});

/*==============================================================
    home category 
==============================================================*/
var swiper = new Swiper('.swiper-container#home-category-slider', {
  slidesPerColumn: 1,
  slidesPerView: 6,
  spaceBetween: 30,
  observer: true,
  loops: true, 
  observeParents: true,
  navigation: {
    nextEl: '.swiper-prev-cat',
    prevEl: '.swiper-next-cat',
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    540: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    640: {
      slidesPerView: 2,
      spaceBetween: 15
    },
    767: {
      slidesPerView: 3,
      spaceBetween: 15
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 30
    },
    1024: {
      slidesPerView: 4
    },
    1360: {
      slidesPerView: 5
    }
  },
});

/*==============================================================
// swiper testimonials slider
==============================================================*/
var swiper = new Swiper('.swiper-container#testi-slider', {
    slidesPerView: 1,
    slidesPerColumn: 1,
    spaceBetween: 30,
    loop: true,
    observer: true,
    observeParents: true,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    autoplay: false,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
});

// blog
 $('#blog-slider-11').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    lazyLoad:true,
    nav: false,
    dots: false,
    responsive: true,
    responsive:{
      0:{
        items:1,
        margin: 15
      },
      479:{
        items:1,
        margin: 15
      },
      640:{
        items:2,
        margin: 15
      },
      768:{
        items:2
      },
      979:{
        items:2
      },
      1199:{
        items: 2
      },
      1499:{
        items: 4
      }
    }
});

// **************************************** home-12 ********************************************
/*==============================================================
// slider
==============================================================*/
$('#home-12-slider').owlCarousel({
    loop: false,
    items: 1,
    margin: 0,
    nav: true,
    navText : ['<i class="ti-angle-left"></i>','<i class="ti-angle-right"></i>'],
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    fade: true,
    dots:false,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn'
});

//  category 
var swiper = new Swiper('.swiper-container#cat-slider', {
  slidesPerColumn: 1,
  slidesPerView: 6,
  spaceBetween: 0,
  observer: true,
  loops: true, 
  observeParents: true,
  navigation: {
    nextEl: '.cat-button-prev',
    prevEl: '.cat-button-next',
  },breakpoints: {
    0: {
      slidesPerView: 1
    },
    540: {
      slidesPerView: 2
    },
    640: {
      slidesPerView: 3
    },
    768: {
      slidesPerView: 3
    },
    1024: {
      slidesPerView: 4
    },
    1360: {
      slidesPerView: 5
    }
  },
});

// product tab slider
var swiper = new Swiper('.swiper-container#product-tab', {
slidesPerColumn: 2,
slidesPerView: 4,
spaceBetween: 30,
observer: true,
observeParents: true,
    breakpoints: {
      0: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      640: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      767: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      768: {
        slidesPerView: 2
      },
      1024: {
        slidesPerView: 3
      }
    },
});

// popular product tab slider
var swiper = new Swiper('.swiper-container#popular-product-tab', {
slidesPerColumn: 3,
slidesPerView: 3,
spaceBetween: 30,
observer: true,
observeParents: true,
    breakpoints: {
      0: {
        slidesPerView: 2,
        spaceBetween: 15
      },
      540: {
        slidesPerView: 1,
        spaceBetween: 15
      },
      768: {
        slidesPerView: 2
      },
      1024: {
        slidesPerView: 3
      }
    },
});

// blog
var swiper = new Swiper('.swiper-container#blog-home12', {
  slidesPerColumn: 1,
  slidesPerView: 3,
  spaceBetween: 30,
  observer: true,
  loops: true, 
  observeParents: true,
  navigation: {
    nextEl: '.blog-button-next',
    prevEl: '.blog-button-prev',
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    540: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    640: {
      slidesPerView: 1,
      spaceBetween: 15
    },
    767: {
      slidesPerView: 2,
      spaceBetween: 15
    },
    768: {
      slidesPerView: 2
    },
    1024: {
      slidesPerView: 2
    },
    1360: {
      slidesPerView: 3
    }
  },
});

// **************************************** home-13 ********************************************
// slider
var swiper = new Swiper('.swiper-container#home-slider-13', {
    slidesPerColumn: 1,
    slidesPerView: 1,
    spaceBetween: 0,
    observer: true,
    observeParents: true,
    autoplay: true,
    autoplayTimeout: 4000,
    autoplayHoverPause: true,
    fade: true,
    dots:false,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    navigation: {
        nextEl: '.swiper-next-slider',
        prevEl: '.swiper-prev-slider',
    },
  });

// category
var swiper = new Swiper('.swiper-container#home-cat', {
    slidesPerColumn: 1,
    slidesPerView: 6,
    spaceBetween: 30,
    observer: true,
    loops: true, 
    observeParents: true,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplayHoverPause: true,
    fade: true,
    navigation: {
        nextEl: '.cat-swiper-prev',
        prevEl: '.cat-swiper-next',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 15
            },
            540: {
              slidesPerView: 2,
              spaceBetween: 15
            },
            640: {
              slidesPerView: 2,
              spaceBetween: 15
            },
            768: {
              slidesPerView: 3,
              spaceBetween: 15
            },
            1024: {
              slidesPerView: 4,
              spaceBetween: 15
            },
            1360: {
              slidesPerView: 5
            }
        }
    });
    
    // home product
    var swiper = new Swiper('.swiper-container#home-pro-slider', {
        slidesPerColumn: 1,
        slidesPerView: 4,
        spaceBetween: 30,
        loop: true,
        observer: true,
        observeParents: true,
        navigation: {
          nextEl: '.swiper-next-cat',
          prevEl: '.swiper-prev-cat',
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            320: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            540: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            767: {
                slidesPerView: 3,
                spaceBetween: 15
            },
            768: {
                slidesPerView: 3
            },
            1024: {
                slidesPerView: 3
            },
            1599: {
                slidesPerView: 4
            }
        },
    });

    // testimonials 
    var swiper = new Swiper('.swiper-container#testimonials-13', {
      slidesPerColumn: 1,
      slidesPerView: 2,
      spaceBetween: 30,
      observer: true,
      observeParents: true,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navigation: {
        nextEl: '.next-testi-slider',
        prevEl: '.prev-testi-slider',
      },breakpoints: {
        0: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        540: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        640: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        768: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        1024: {
          slidesPerView: 2,
          spaceBetween: 15
        },
        1360: {
          slidesPerView: 2
        }
      },
    });

    // brand logo
    $('#brand-home-slider').owlCarousel({
    loop: false,
    rewind: true,
    margin: 0,
    nav: false,
    dots: false,autoplay: true,
    slideTransition: 'linear',
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
      0:{
        items:2
      },
      479:{
        items:2
      },
      540:{
        items:3
      },
      768:{
        items:4
      },
      979:{
        items:4
      },
      1199:{
        items: 5
      }
    }
  });

// blog
 $('#blog-slider-13').owlCarousel({
    loop: false,
    rewind: true,
    margin: 30,
    lazyLoad:true,
    nav: false,
    dots: false,
    responsive: true,
    responsive:{
      0:{
        items:1,
        margin: 15
      },
      479:{
        items:1,
        margin: 15
      },
      640:{
        items:2,
        margin: 15
      },
      768:{
        items:2
      },
      979:{
        items:2
      },
      1199:{
        items: 3
      }
    }
  });

// **************************************** home-14 ********************************************
// slider
var swiper = new Swiper('.swiper-container#home-slider-14', {
    slidesPerColumn: 1,
    slidesPerView: 1,
    spaceBetween: 0,
    observer: true,
    observeParents: true,
    autoplay: false,
    autoplayTimeout: 4000,
    autoplayHoverPause: true,
    fade: true,
    dots:false,
    transitionStyle: "fade",
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    navigation: {
        nextEl: '.slider-swiper-next',
        prevEl: '.slider-swiper-prev',
    },
  });

// category
    var swiper = new Swiper('.swiper-container#home-cat-14', {
        slidesPerColumn: 1,
        slidesPerView: 6,
        spaceBetween: 15,
        observer: true,
        loops: true, 
        observeParents: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        fade: true,
        navigation: {
            nextEl: '.cat-swiper-prev',
            prevEl: '.cat-swiper-next',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 15
            },
            540: {
              slidesPerView: 2,
              spaceBetween: 15
            },
            640: {
              slidesPerView: 3,
              spaceBetween: 15
            },
            768: {
              slidesPerView: 4,
              spaceBetween: 15
            },
            1024: {
              slidesPerView: 4,
              spaceBetween: 15
            },
            1360: {
              slidesPerView: 5
            }
        }
    });

    // home product
    var swiper = new Swiper('.swiper-container#trending-pro-14', {
        slidesPerColumn: 1,
        slidesPerView: 4,
        spaceBetween: 30,
        loop: true,
        observer: true,
        observeParents: true,
        navigation: {
          nextEl: '.pro-14-swiper-next',
          prevEl: '.pro-14-swiper-prev',
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            320: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            540: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            767: {
                slidesPerView: 3,
                spaceBetween: 15
            },
            991: {
                slidesPerView: 3
            },
            1199: {
                slidesPerView: 4
            },
            1280: {
                slidesPerView: 4
            },
            1599: {
                slidesPerView: 4
            }
        },
    });

    // testimonials 
    var swiper = new Swiper('.swiper-container#testimonials-14', {
      slidesPerColumn: 1,
      slidesPerView: 2,
      spaceBetween: 30,
      observer: true,
      observeParents: true,
      autoplay: false,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navigation: {
        nextEl: '.testi-swiper-prev',
        prevEl: '.testi-swiper-next',
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        540: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        640: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        767: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        768: {
          slidesPerView: 1,
          spaceBetween: 30
        },
        1024: {
          slidesPerView: 2
        },
        1360: {
          slidesPerView: 2
        }
      },
    });

    // blog
    $('#blog-slider-14').owlCarousel({
        loop: false,
        rewind: true,
        margin: 30,
        lazyLoad:true,
        nav: false,
        dots: false,
        responsive: true,
        responsive:{
          0:{
            items:1,
            margin: 15
          },
          479:{
            items:1,
            margin: 15
          },
          640:{
            items:2,
            margin: 15
          },
          768:{
            items:2
          },
          979:{
            items:2
          },
          1199:{
            items: 3
          }
        }
      });
});

// masonry
// $('.grid').imagesLoaded(function () {
//     $('.grid').isotope({
//         itemSelector: 'li',
//         layoutMode: 'masonry'
//     });
// });
 

// **************************************** home-15 ******************************************** 

    // slider
    var swiper = new Swiper('.swiper-container#home-slider-15', {
        slidesPerColumn: 1,
        slidesPerView: 1,
        spaceBetween: 0,
        observer: true,
        observeParents: true,
        autoplay: false,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        fade: true,
        dots:false,
        transitionStyle: "fade",
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        navigation: {
            nextEl: '.slider-swiper-next',
            prevEl: '.slider-swiper-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });

    // category
    var swiper = new Swiper('.swiper-container#home-cat-15', {
        slidesPerColumn: 1,
        slidesPerView: 6,
        spaceBetween: 30,
        observer: true,
        loops: true, 
        observeParents: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        fade: true,
        navigation: {
            nextEl: '.cat-swiper-prev',
            prevEl: '.cat-swiper-next',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              spaceBetween: 15
            },
            320: {
              slidesPerView: 1,
              spaceBetween: 15
            },
            540: {
              slidesPerView: 2,
              spaceBetween: 15
            },
            767: {
              slidesPerView: 3,
              spaceBetween: 15
            },
            768: {
              slidesPerView: 4
            },
            1024: {
              slidesPerView: 4
            },
            1360: {
              slidesPerView: 5
            }
        }
    });

    var swiper = new Swiper('.swiper-container#home-15-tab-pro', {
      slidesPerColumn: 2,
      slidesPerView: 3,
      spaceBetween: 30,
      observer: true,
      loops: true, 
      observeParents: true,
      navigation: {
        nextEl: '.tab-button-next',
        prevEl: '.tab-button-prev',
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        540: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        640: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        767: {
          slidesPerView: 2,
          spaceBetween: 15
        },
        768: {
          slidesPerView: 2
        },
        1024: {
          slidesPerView: 2
        }
      },
    });

    // brand logo
    $('#home-15-brand-logo').owlCarousel({
    loop: false,
    rewind: true,
    margin: 0,
    nav: false,
    dots: false,autoplay: true,
    slideTransition: 'linear',
    autoplaySpeed: 3000,
    autoplayHoverPause: true,
    responsive:{
      0:{
        items:2
      },
      479:{
        items:2
      },
      540:{
        items:3
      },
      768:{
        items:4
      },
      979:{
        items:5
      },
      1199:{
        items: 6
      }
    }
  });

    // testimonials 
    var swiper = new Swiper('.swiper-container#testimonials-15', {
      slidesPerColumn: 1,
      slidesPerView: 3,
      spaceBetween: 30,
      observer: true,
      observeParents: true,
      autoplay: false,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navigation: {
        nextEl: '.testi-swiper-prev',
        prevEl: '.testi-swiper-next',
      },
      breakpoints: {
        0: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        540: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        640: {
          slidesPerView: 1,
          spaceBetween: 15
        },
        767: {
          slidesPerView: 2,
          spaceBetween: 15
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 30
        },
        1024: {
          slidesPerView: 2
        },
        1360: {
          slidesPerView: 3
        }
      },
    });

    // blog
    $('#blog-slider-15').owlCarousel({
        loop: false,
        rewind: true,
        margin: 30,
        lazyLoad:true,
        nav: false,
        dots: false,
        responsive: true,
        responsive:{
          0:{
            items:1,
            margin: 15
          },
          479:{
            items:1,
            margin: 15
          },
          640:{
            items:2,
            margin: 15
          },
          768:{
            items:2
          },
          979:{
            items:2
          },
          1199:{
            items: 3
          }
        }
      });

    // **************************************** home-16 ********************************************
    // slider
    var swiper = new Swiper('.swiper-container#home-slider-16', {
        slidesPerColumn: 1,
        slidesPerView: 1,
        spaceBetween: 0,
        observer: true,
        observeParents: true,
        autoplay: false,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        fade: true,
        dots:false,
        transitionStyle: "fade",
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });

    // slider side product
    var swiper = new Swiper('.swiper-container#home-slider-side-pro', {
        slidesPerColumn: 3,
        slidesPerView: 1,
        spaceBetween: 30,
        observer: true,
        loops: true, 
        observeParents: true,
        navigation: {
            nextEl: '.tab-button-next',
            prevEl: '.tab-button-prev',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              slidesPerColumn: 2,
              spaceBetween: 15
            },
            540: {
              slidesPerView: 1,
              slidesPerColumn: 2,
              spaceBetween: 15
            },
            640: {
              slidesPerView: 1,
              slidesPerColumn: 2,
              spaceBetween: 15
            },
            767: {
              slidesPerView: 2,
              slidesPerColumn: 2,
              spaceBetween: 15
            },
            768: {
              slidesPerView: 2,
              slidesPerColumn: 2,
              spaceBetween: 30
            },
            1024: {
              slidesPerView: 2,
              slidesPerColumn: 2,
              spaceBetween: 30
            }
        },
    });

    $(document).on("click", ".custom-pro .custom-pro-btn", function(e) {
        $(".custom-pro").removeClass("open");
        $(".custom-pro").removeClass("open");
            var hotspot = $(this).closest(".custom-pro"),
            main_menu = hotspot.find(".pro-block").eq(0),
            tthotspotcontent = main_menu.find(".list-pro").eq(0);
            if(hotspot.hasClass("open")){
            setTimeout(function() {
                hotspot.removeClass("open");
            }, 300);
                tthotspotcontent.removeClass("open");
            }else{
                hotspot.addClass("open");
                setTimeout(function() {
                    tthotspotcontent.addClass("open");
                }, 300);
            }
        e.stopPropagation(); 
        e.preventDefault();
    });

    // home product
    var swiper = new Swiper('.swiper-container#trending-pro-16', {
        slidesPerColumn: 1,
        slidesPerView: 4,
        spaceBetween: 30,
        loop: true,
        observer: true,
        observeParents: true,
        navigation: {
          nextEl: '.pro-14-swiper-next',
          prevEl: '.pro-14-swiper-prev',
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            320: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            540: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            767: {
                slidesPerView: 3,
                spaceBetween: 15
            },
            991: {
                slidesPerView: 3
            },
            1199: {
                slidesPerView: 3
            },
            1280: {
                slidesPerView: 4
            },
            1599: {
                slidesPerView: 4
            }
        },
    });

    // testimonials 
    var swiper = new Swiper('.swiper-container#testimonials-16', {
        slidesPerColumn: 1,
        slidesPerView: 4,
        spaceBetween: 30,
        loop: true,
        observer: true,
        observeParents: true,
        autoplay: false,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        navigation: {
            nextEl: '.testi-swiper-prev',
            prevEl: '.testi-swiper-next',
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            540: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            640: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            767: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            768: {
                slidesPerView: 2,
                spaceBetween: 30
            },
            1024: {
                slidesPerView: 2
            },
            1360: {
                slidesPerView: 3
            }
        },
    });

    $('#testimonials-16').owlCarousel({
        loop: true,
        rewind: true,
        margin: 30,
        nav: true,
        dots: false,
        autoHeight: true,
        navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
        responsive:{
            0:{
                items:1,
                margin: 15,
                nav: false,
                dots: true
            },
            479:{
                items:1,
                margin: 15,
                nav: false,
                dots: true
            },
            640:{
                items:2,
                margin: 15,
                nav: false,
                dots: true
            },
            768:{
                items:2,
                nav: false,
                dots: true
            },
            979:{
                items:3,
                nav: false,
                dots: true
            },
            1024:{
                items:3,
                nav: false,
                dots: true
            },
            1199:{
                items:3
            },
            1399:{
                items:4
            }
        }
    });

    // blog
    $('#blog-slider-16').owlCarousel({
        loop: false,
        rewind: true,
        margin: 30,
        lazyLoad:true,
        nav: false,
        dots: false,
        responsive: true,
        responsive:{
            0:{
                items:1,
                margin: 15
            },
            479:{
                items:1,
                margin: 15
            },
            640:{
                items:2,
                margin: 15
            },
            768:{
                items:2
            },
            979:{
                items:2
            },
            1199:{
                items: 3
            }
        }
    });

    // home product details
    $('.home-pro-info-slider').owlCarousel({
        loop: true,
        margin: 15,
        nav: false,
        navText: ['<i class="ti-arrow-left"></i>','<i class="ti-arrow-right"></i>'],
        dots: false,
        responsive:{
          0:{
            items:3
          },
          600:{
            items:4
          },
          1000:{
            items:4
          }
        }
    });

    $('.home-product-info .nav-item .nav-link').on( "click", function() {
        $('.home-product-info .nav-item .nav-link').removeClass('active');
        $(this).addClass('active');
    });

    // **************************************** home-17 ********************************************
    // category
    var swiper = new Swiper('.swiper-container#home-cat-17', {
        slidesPerColumn: 1,
        slidesPerView: 6,
        spaceBetween: 30,
        observer: true,
        loops: true, 
        observeParents: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        fade: true,
        navigation: {
            nextEl: '.cat-swiper-prev',
            prevEl: '.cat-swiper-next',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            320: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            360: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            540: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            640: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            767: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            991: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1024: {
              slidesPerView: 4,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1199: {
              slidesPerView: 4,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1360: {
              slidesPerView: 5,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1399: {
              slidesPerView: 6,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            }
        }
    });

    // home product
    var swiper = new Swiper('.swiper-container#trending-pro-17', {
        slidesPerColumn: 1,
        slidesPerView: 5,
        spaceBetween: 30,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        loop: true,
        observer: true,
        observeParents: true,
        navigation: {
          nextEl: '.pro-14-swiper-next',
          prevEl: '.pro-14-swiper-prev',
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            320: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            540: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            767: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            991: {
                slidesPerView: 2
            },
            1199: {
                slidesPerView: 3
            },
            1399: {
                slidesPerView: 4
            },
            1499: {
                slidesPerView: 5
            }
        },
    });

    // category
    var swiper = new Swiper('.swiper-container#testimonials-17', {
        slidesPerColumn: 1,
        slidesPerView: 3,
        spaceBetween: 30,
        observer: true,
        loops: true, 
        observeParents: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        fade: true,
        navigation: {
            nextEl: '.cat-swiper-prev',
            prevEl: '.cat-swiper-next',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            320: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            360: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            540: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            640: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            767: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            991: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1024: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1199: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1360: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1399: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            }
        }
    });

    // category
    var swiper = new Swiper('.swiper-container#brand-logo-17', {
        slidesPerColumn: 1,
        slidesPerView: 6,
        spaceBetween: 30,
        observer: true,
        loops: true, 
        observeParents: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        fade: true,
        navigation: {
            nextEl: '.cat-swiper-prev',
            prevEl: '.cat-swiper-next',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            320: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            360: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            540: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            640: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            767: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            991: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1024: {
              slidesPerView: 5,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1199: {
              slidesPerView: 5,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1360: {
              slidesPerView: 6,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1399: {
              slidesPerView: 6,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            }
        }
    });

    // blog
    var swiper = new Swiper('.swiper-container#blog-slider-17', {
        slidesPerColumn: 1,
        slidesPerView: 4,
        spaceBetween: 30,
        observer: true,
        loops: true, 
        observeParents: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        fade: true,
        navigation: {
            nextEl: '.cat-swiper-prev',
            prevEl: '.cat-swiper-next',
        },
        breakpoints: {
            0: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            320: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            360: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            540: {
              slidesPerView: 1,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            640: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            767: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 12
            },
            991: {
              slidesPerView: 2,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1024: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1199: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1399: {
              slidesPerView: 3,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            },
            1499: {
              slidesPerView: 4,
              grid: {
                rows: 1,
                fill: 'row' | 'column',
              },
              spaceBetween: 30
            }
        }
    });

    // deal product
    var swiper = new Swiper('.swiper-container#deal-pro-17', {
        slidesPerColumn: 1,
        slidesPerView: 4,
        spaceBetween: 30,
        loop: true,
        observer: true,
        observeParents: true,
        navigation: {
          nextEl: '.pro-14-swiper-next',
          prevEl: '.pro-14-swiper-prev',
        },
        breakpoints: {
            0: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            320: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            540: {
                slidesPerView: 1,
                spaceBetween: 15
            },
            640: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            767: {
                slidesPerView: 2,
                spaceBetween: 15
            },
            991: {
                slidesPerView: 2
            },
            1199: {
                slidesPerView: 3
            },
            1399: {
                slidesPerView: 3
            },
            1499: {
                slidesPerView: 4
            }
        },
    });