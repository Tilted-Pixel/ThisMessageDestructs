//Image preload
(function($){
    $.fn.preloadBgImage = function() {
        if ($(this).css('background')) {
            var src = $(this).css('background');
        } else {
            var src = $(this).css('background-image');
        }

        var urlMatches =        src.match(/url\((.*)\)/);

        if(urlMatches){
            preloadImage = new Image();
            preloadImage.src = urlMatches[1];
        }

    };
})(jQuery);
//      Responsive menu
function jsResponsive(){
    var body_width = parseInt(jQuery(window).width());
//    Responsive top navigation
    if(body_width < 865){
        jQuery('.header-navigation').addClass('responsive');
    } else{
        jQuery('.header-navigation').removeClass('responsive');
    }
//    Responsive site logo
    if(body_width < 599){
        jQuery('.site-header .logo').addClass('small');
    }else{
        jQuery('.site-header .logo').removeClass('small');
    }
//    Responsive hoem page rotator
    if(jQuery('#home-rotator').length){
        var rotator_video_width = parseInt(jQuery('#home-rotator .video').width());
        jQuery('#home-rotator iframe').each(function(){
            jQuery(this).attr('height', rotator_video_width * 0.5625).attr('width', rotator_video_width);
        });
    }
//    Imagine a website
    if(jQuery('.imagine-website').length){
        var header_height = parseInt(jQuery('.site-header').height());
        var window_height = parseInt(jQuery(window).height());
        var imagine_website_height = parseInt(jQuery('.imagine-website').height());
        if(imagine_website_height < window_height){
            jQuery('.home-page-content').css('margin-top', window_height);
            jQuery('.imagine-website').css('min-height', window_height-header_height);
            if(body_width > 599){
                jQuery('.imagine-website').css('padding-top', (window_height - imagine_website_height) / 2);
            }
        } else {
            jQuery('.imagine-website').css('position', 'static').css('padding-bottom', '10px');
        }
    }
//    Line between content
    if(jQuery('.section-content').length){
        if(body_width > 749){
            var section_height = parseInt(jQuery('.section-content').height());
            var aside_height = parseInt(jQuery('aside').outerHeight(true));
            if (aside_height < section_height){
                jQuery('aside').css('height', section_height-33);
            }
        }
    }
}
jQuery(document).ready(function(){
//    responsive
    jsResponsive();
    jQuery(window).resize(function(){
        jsResponsive();
    })
    if(jQuery('#home-rotator').length){
        //    Home page rotator
        jQuery("#home-rotator").responsiveSlides({
            auto: false,
            speed: 300,
            pager: true,
            nav: true,
            prevText: '',
            nextText: ''
        });
//    Video on home page roator
        jQuery('#home-rotator iframe').each(function(){
            var url = jQuery(this).attr('src');
            jQuery(this).attr('src',url+'?wmode=transparent');
            jQuery(this).attr('wmode','Opaque');
        });
    }
    if(jQuery('.client-logo').length){
        //    Home page rotator
        jQuery("#client-slid-1").responsiveSlides({
            timeout: 2500
        });
        jQuery("#client-slid-2").responsiveSlides({
            timeout: 8500
        });
        jQuery("#client-slid-3").responsiveSlides({
            timeout: 6500
        });
    }
//    Home page js
    if(jQuery('.imagine-website').length){
        var header_height = parseInt(jQuery('.site-header').height());
//        nice scroll
        var what_we_do_offset = jQuery('.what-we-do').offset();
        var your_project_form_offset = jQuery('.your-project').offset();
        var what_we_do_margin = parseInt(jQuery('.what-we-do').css('margin-top'));
        var your_project_form_margin = parseInt(jQuery('.your-project').css('margin-top'));
        jQuery('#learn-more').on('click', function(){
            jQuery('html, body').animate({
                    scrollTop: (what_we_do_offset.top - header_height-what_we_do_margin)
            }, 400,'linear'
            );
        });
        jQuery('#start-project').on('click', function(){
            jQuery('html, body').animate({
                    scrollTop: (your_project_form_offset.top - header_height-your_project_form_margin)
            }, 400,'linear'
            );
            setTimeout(function(){
                jQuery('#tell-us-project').click();
            },410)

        });
        jQuery(window).scroll( function(){
            var bottom_of_window = jQuery(window).scrollTop();
            if( bottom_of_window >= (what_we_do_offset.top - header_height-what_we_do_margin) ){
                jQuery('.what-we-do .image').each(function(){
                    jQuery(this).addClass('glow')
                });
            }
        });
    }
//    Top Menu
    jQuery('.header-navigation li:nth-child(4)').addClass('forth');
    jQuery('.header-navigation .responsive-icon').on('click', function(){
        jQuery('.header-navigation ul').toggleClass('open');
    });
//    Tell us about your project
    jQuery('#tell-us-project').on('click', function(){
        jQuery('#tell-us-form').toggleClass('open');
    })
//    Portfolio slide
    jQuery( '.portfolio-slideshow' ).cycle();
})
