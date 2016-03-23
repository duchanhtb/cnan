(function ($) {
    "use strict";
    var CNAN = CNAN || {};

    /* 1: Functions
     --------------------------------------------------------------------------------------------- */
    var isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function () {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };


    /* 2: slick slider
     --------------------------------------------------------------------------------------------- */
    CNAN.slick = function () {
        if ($().slick) {

            // home slider
            $('.slide').slick({
                lazyLoad: "ondemand",
                dots: true,
                dotsClass: "slick-dots slide-direction",
                customPaging: function (b, a) {
                    return"<span></span>"
                },
                prevArrow: ".slide-prev",
                nextArrow: ".slide-next",
                infinite: true,
                speed: 800,
                fade: true,
                slide: "div",
                cssEase: "linear",
                autoplay: true,
                autoplaySpeed: 3000,
            });

            // footer slider
            $("#home-slide").slick({
                dots: false,
                infinite: false,
                speed: 1500,
                slide: "div",
                autoplay: true,
                autoplaySpeed: 5000,
                slidesToShow: 3,
                slidesToScroll: 3,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            infinite: true,
                            dots: true
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        } // end if
    }; // end slick


    /* 2: pgwSlider slider
     --------------------------------------------------------------------------------------------- */
    CNAN.pgwSlider = function () {
        if ($().pgwSlider) {
             $('.pgwSlider').pgwSlider();
        } // end if
    }; // end slick


    /* 3: color box
     --------------------------------------------------------------------------------------------- */
    CNAN.colorbox = function () {
        if ($().colorbox) {
            /*
             $(".has-colorbox img").each(function(){
             var prObj 		= $(this).parent();
             var tag_name 	= prObj.prop("tagName");
             var img_src 	= $(this).attr('src');
             var img_alt 	= $(this).attr('alt');
             
             if(tag_name !=  'A'){
             $(this).wrap( "<a class='colorbox' href='"+img_src+"' title='"+img_alt+"'></div>" );	
             }
             })
             */
            $(".colorbox").colorbox({rel: 'colorbox'});
            $(".iframe-colorbox").colorbox({iframe: true, width: "80%", height: "80%"});


        } // end if
    };



    /* 4: social sharing
     --------------------------------------------------------------------------------------------- */
    CNAN.social = {
        url: '',
        share: function (el) {
            var type = el.data('net');
            if (type == 'print') {
                var data = $("#recipe-print-content").html();
                //var content = $(data).find('.report').remove().end().find('.recipe-category').remove().end().html();
                //alert(content);
                var print_content = '<p>&nbsp;</p><div id="content" class="detail-content"><div id="recipe-detail">' + data + '</div></div>';
                var recipe_title = $("#content .recipe-title").html();
                CNAN.print(recipe_title, print_content);
                return true;
            }
            switch (el.data('net')) {
                case 'gp':
                    CNAN.social.url = 'https://plus.google.com/share?url=' + el.attr('href');
                    break;
                case 'tw':
                    CNAN.social.url = 'https://twitter.com/intent/tweet?text?=' + el.data('title') + '&url=' + el.attr('href');
                    break;
                case 'pt':
                    CNAN.social.url = 'http://pinterest.com/pin/create/button/?url=' + el.attr('href') + '&description=' + el.data('title');
                    break;
                case 'fb':
                    CNAN.social.url = 'https://www.facebook.com/dialog/feed?app_id=552042118236053&display=popup&caption=cachnauanngon.com&redirect_uri=http://cachnauanngon.com&link=' + el.attr('href');
                    break;
            }
            window.open(CNAN.social.url, 'share', 'height=400,width=600');
            return false;
        }

    };



    /* 5: navigation
     --------------------------------------------------------------------------------------------- */
    CNAN.navigation = function () {
        var content = $("#content");
        var pull = $('#nav-tongle');
        var menu = $('#main-nav');
        var menuHeight = menu.height();
        var open = false;

        $(content).on('click', function (e) {
            $('body').removeClass('over-hidden').height('auto');
            menu.animate({left: '-240px'}, 200);
            open = false;
        })

        $(pull).on('click', function (e) {
            e.preventDefault();
            if (!open) {
                $('body').addClass('over-hidden').height(menuHeight+ 'px');
                menu.animate({left: '0px'}, 200);
                open = true;
            } else {
                $('body').removeClass('over-hidden').height('auto');
                menu.animate({left: '-240px'}, 200);
                open = false;
            }
        });


        $(window).resize(function () {
            var w = $(window).width();
            if (w > 320 && menu.is(':hidden')) {
                menu.removeAttr('style');
            }
        });
    };


    /* 6: custom scroll
     --------------------------------------------------------------------------------------------- */
    CNAN.CustomScroll = function () {
        if ($().mCustomScrollbar) {
            $('.fn-scrollbar').mCustomScrollbar({
                scrollInertia: 0,
                scrollbarPosition: 'outside',
                autoHideScrollbar: true,
                autoExpandScrollbar: true,
                mouseWheel: {
                    scrollAmount: 100,
                    preventDefault: true
                }
            });
        } // end if

        // fixed full height for category
        var footer_height = 120;
        var h = $(window).height() - footer_height;
        $(".fn-scrollbar").css('height', h + 'px');
        $(window).resize(function () {
            var h = $(window).height() - footer_height;
            $(".fn-scrollbar").css('height', h + 'px');
        });

        if ($("#category-menu").length > 0) {
            var x = $("#category-menu").offset();
            var offset_left = x.left;
            var offset_top = x.top;
            $(window).scroll(function () {
                if ($(this).scrollTop() > 145) {
                    $("#category-menu").css('position', 'fixed');
                    $("#category-menu").css('left', offset_left);
                    $("#category-menu").css('top', '0px');
                } else {
                    $("#category-menu").css('position', 'static');
                }
            });
        }

    };


    /* 7: Gmap
     --------------------------------------------------------------------------------------------- */
    CNAN.Gmap = function () {
        $('.gmap').each(function () {
            var $this = $(this);
            var id = $this.attr('id');

            var map = new GMaps({
                div: '#' + id,
                lat: $this.data('lat'),
                lng: $this.data('lng'),
                zoom: $this.data('zoom')
            });

            if ($this.data('marker') == true) {
                var args = {
                    lat: $this.data('lat'),
                    lng: $this.data('lng'),
                    title: $this.data('address')
                }
                if ($this.data('info'))
                    args['infoWindow'] = {content: '<div class="gmap-info">' + $this.data('info') + '</div>'};

                var marker = map.addMarker(args);	// add marker
                if ($this.data('open_info') == true) {
                    setTimeout(function () {
                        google.maps.event.trigger(marker, 'click')
                    }, 500);
                }

            }	// if marker	

        });	// each
    }	// CNAN showmap



    /* 8: Popup
     --------------------------------------------------------------------------------------------- */
    CNAN.popup = {
        show: function (obj) {
            $('.body-mask').removeClass('none');
            $(obj).show();
            return false;
        },
        hide: function () {
            $('.body-mask').addClass('none');
            $(".overlay").hide();
        },
        login: function () {
            $('.body-mask').removeClass('none');
            $("#loginPopup").show();
        }
    };


    /* Init functions
     --------------------------------------------------------------------------------------------- */
    $(document).ready(function () {
        CNAN.navigation();
        CNAN.pgwSlider();
        CNAN.colorbox();
        CNAN.CustomScroll();
        CNAN.Gmap();

        // right tab
        $(".tab-nav li a").click(function () {
            var tab = this.href.split("#")[1];
            $(".tab-nav li a").removeClass('active');
            $(this).addClass('active');
            $("#top-place ul").hide();
            $("#top-place ul#content-" + tab).show();
        })
        // search tab
        $("ul.search-tab li a").click(function () {
            var tab = this.href.split("#")[1];
            $("ul.search-tab li a").removeClass('active');
            $(this).addClass('active');
            $(".search-content").hide();
            $("#search-content-" + tab).show();
        })
        
        $('.fn-sharelink').click(function () {
            CNAN.social.share($(this));
            return false;
        });

        $(".fn-login").click(function () {
            CNAN.popup.show('#loginPopup');
        })

        $(".fn-report").click(function () {
            CNAN.popup.show('#error-reporting');
        })

        $('.close').click(function () {
            CNAN.popup.hide();
        })


        // add google images
        $(".g-image").each(function () {
            var obj = $(this);
            var i_name = obj.data('name');
            var i_id  = obj.data('id');
            $.ajaxQueue({
                url: BASE_URL + "ajax.php?cmd=get_ingridient_image&q=" + i_name + "&id=" + i_id,
                type: "GET",
                data: {},
                beforeSend: function (xhr) {},
                timeout: 10000,
                success: function (response, status) {
                    obj.attr('src', response)
                }
            });
        })

        // hide thong bao loi khi nguoi dung nhap thong tin
        $("#report-content").keyup(function(){
            $("#report-msg").hide();
        })
        $("#error-reporting-form").submit(function () {
            var error_report = $(this).data('error-report');
            var _id = $("#report-id").val();
            var _email = $("#report-email").val();
            var _content = $("#report-content").val();
            if (_email == '') {
                $("#report-email").focus();
                $("#report-msg").html('Bạn cần nhập email').show();
                return false;
            }
            if (_content == '') {
                $("#report-msg").html('Bạn cần nhập nội dung').show();
                $("#report-content").focus();
                return false;
            }
            $.ajax({
                url: BASE_URL + "ajax.php?cmd=" + error_report,
                type: "POST",
                data: {
                    report_id: _id,
                    email: _email,
                    content: _content
                },
                beforeSend: function (xhr) {
                    $("#report-msg").html('Đang gửi...').show();
                },
                timeout: 10000,
                success: function (response, status) {
                    var response_data = $.parseJSON(response);
                    switch (response_data.status) {
                        case "error":
                                $("#report-msg").html(response_data.msg).show();
                                return false;
                            break;
                            
                        case "success":
                            $("#report-msg").html(response_data.msg)
                                    .delay(3000)
                                    .fadeOut(500, function(){
                                        CNAN.popup.hide();
                                    });
                            $("#report-content").val('');
                            return false;
                            break;

                        default:
                            return false;
                            break;
                    }
                }
            });
            return false;
        })
        
        
        // youtube duration
        $(".video").each(function(){
            var obj = $(this);
            var youtube_id = obj.data('youtube-id');
            $.ajaxQueue({
                url: BASE_URL + "ajax.php?cmd=get_youtube_duration&id=" + youtube_id,
                type: "GET",
                data: {},
                beforeSend: function (xhr) {},
                timeout: 10000,
                success: function (response, status) {
                    if(response != '00:00'){
                        $('<div class="duration">'+response+'</div>').insertAfter(obj.find('.des'));
                    }
                }
            });
        })


    });
    
    
    function addLink() {
        //Get the selected text and append the extra info
        var selection = window.getSelection(),
            pagelink = '<br /><br /> Nguồn: ' + document.location.href,
            copytext = selection + pagelink,
            newdiv = document.createElement('div');

        //hide the newly created container
        newdiv.style.position = 'absolute';
        newdiv.style.left = '-99999px';

        //insert the container, fill it with the extended text, and define the new selection
        document.body.appendChild(newdiv);
        newdiv.innerHTML = copytext;
        selection.selectAllChildren(newdiv);

        window.setTimeout(function () {
            document.body.removeChild(newdiv);
        }, 100);
    }
    document.addEventListener('copy', addLink);
    
})(jQuery);	// EOF


// submit form
function submitForm(_form) {
    $(_form).submit();
}

// open popup
function openPopup(url, title) {
    window.open(url, title, 'height=600,width=1200');
}
    