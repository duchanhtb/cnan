/**
 * SMOF js
 *
 * contains the core functionalities to be used
 * inside SMOF
 */



/** Fire up jQuery - let's dance! 
 */

$(function () {

    options_url = $('#options_url').html();

    //(un)fold options in a checkbox-group
    $('.fld').click(function () {
        var $fold = '.f_' + this.id;
        $($fold).slideToggle('normal', "swing");
    });

    $(".hcms-color-preview").click(function () {
        $(this).siblings('.hcms-color').trigger('click');
    })

    $('.hcms-color').colpick({
        layout: 'hex',
        submit: 0,
        //colorScheme:'dark',
        onChange: function (hsb, hex, rgb, el, bySetColor) {
            $(el).siblings('.hcms-color-preview').css('background-color', '#' + hex);
            // Fill the text box just if the color was set using the picker, and not the colpickSetColor function.
            if (!bySetColor)
                $(el).val('#' + hex);
        }
    }).keyup(function () {
        $(this).colpickSetColor(this.value);
    });

    //hides warning if js is enabled			
    $('#js-warning').hide();

    //Tabify Options			
    $('.group').hide();

    // Get the URL parameter for tab
    function getURLParameter(name) {
        return decodeURI(
                (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, ''])[1]
                );
    }

    // If the $_GET param of tab is set, use that for the tab that should be open
    if (getURLParameter('tab') != "") {
        $.cookie('of_current_opt', '#' + getURLParameter('tab'), {expires: 7, path: '/'});
    }

    // Display last current tab	
    if ($.cookie("of_current_opt") === null) {
        $('.group:first').fadeIn('fast');
        $('#of-nav li:first').addClass('current');
    } else {
        $('#of-option-' + $.cookie("of_current_opt")).fadeIn();
        $('#of-nav li.' + $.cookie("of_current_opt")).addClass('current');
    }

    //Current Menu Class
    $('#of-nav li a').click(function (evt) {
        // event.preventDefault();

        $('#of-nav li').removeClass('current');
        $(this).parent().addClass('current');

        var clicked_option = $(this).data('option');

        $.cookie('of_current_opt', clicked_option, {expires: 7, path: '/'});

        $('.group').hide();

        $("#of-option-" + clicked_option).fadeIn('fast');
        
        return false;

    });


    //update image in add
    $('.of-input').each(function () {
        $(this).blur(function () {
            if ($(this).val() == "") {
                $(this).parent('.controls').find('.screenshot').show()
                        .find('img').attr('src', admin_url + 'images/noimage.jpg');
                $(this).parent('.controls').find('.screenshot').show()
                        .find('a').attr('href', admin_url + 'images/noimage.jpg');
            } else {
                $(this).parent('.controls').find('.screenshot').show()
                        .find('img').show().attr('src', base_url + $(this).val());

                $(this).parent('.controls').find('.screenshot').show()
                        .find('a').show().attr('href', base_url + $(this).val());
            }
        });
    });

    //Expand Options 
    var flip = 0;

    $('#expand_options').click(function () {
        if (flip == 0) {
            flip = 1;
            $('#of_container #of-nav').hide();
            $('#of_container #content').width(755);
            $('#of_container .group').add('#of_container .group h2').show();

            $(this).removeClass('expand');
            $(this).addClass('close');
            $(this).text('Close');

        } else {
            flip = 0;
            $('#of_container #of-nav').show();
            $('#of_container #content').width(595);
            $('#of_container .group').add('#of_container .group h2').hide();
            $('#of_container .group:first').show();
            $('#of_container #of-nav li').removeClass('current');
            $('#of_container #of-nav li:first').addClass('current');

            $(this).removeClass('close');
            $(this).addClass('expand');
            $(this).text('Expand');

        }

    });

    //Update Message popup
    $.fn.center = function () {
        this.animate({"top": ($(window).height() - this.height() - 200) / 2 + $(window).scrollTop() + "px"}, 100);
        this.css("left", 250);
        return this;
    }


    $('#of-popup-save').center();
    $('#of-popup-reset').center();
    $('#of-popup-fail').center();

    $(window).scroll(function () {
        $('#of-popup-save').center();
        $('#of-popup-reset').center();
        $('#of-popup-fail').center();
    });


    //Masked Inputs (images as radio buttons)
    $('.of-radio-img-img').click(function () {
        $(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
        $(this).addClass('of-radio-img-selected');
    });
    $('.of-radio-img-label').hide();
    $('.of-radio-img-img').show();
    $('.of-radio-img-radio').hide();

    //Masked Inputs (background images as radio buttons)
    $('.of-radio-tile-img').click(function () {
        $(this).parent().parent().find('.of-radio-tile-img').removeClass('of-radio-tile-selected');
        $(this).addClass('of-radio-tile-selected');
    });
    $('.of-radio-tile-label').hide();
    $('.of-radio-tile-img').show();
    $('.of-radio-tile-radio').hide();

    // Style Select
    (function ($) {
        styleSelect = {
            init: function () {
                $('.select_wrapper').each(function () {
                    $(this).prepend('<span>' + $(this).find('.select option:selected').text() + '</span>');
                });
                $('.select').live('change', function () {
                    $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
                });
                $('.select').bind($.browser.msie ? 'click' : 'change', function (event) {
                    $(this).prev('span').replaceWith('<span>' + $(this).find('option:selected').text() + '</span>');
                });
            }
        };
        $(document).ready(function () {
            styleSelect.init()
        })
    })($);


    /** Aquagraphite Slider MOD */

    //Hide (Collapse) the toggle containers on load
    $(".slide_body").hide();

    //Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
    $(".slide_edit_button").live('click', function () {
        /*
         //display as an accordion
         $(".slide_header").removeClass("active");	
         $(".slide_body").slideUp("fast");
         */
        //toggle for each
        $(this).parent().toggleClass("active").next().slideToggle("fast");
        return false; //Prevent the browser jump to the link anchor
    });

    // Update slide title upon typing		
    function update_slider_title(e) {
        var element = e;
        if (this.timer) {
            clearTimeout(element.timer);
        }
        this.timer = setTimeout(function () {
            $(element).parent().prev().find('strong').text(element.value);
        }, 100);
        return true;
    }

    $('.of-slider-title').live('keyup', function () {
        update_slider_title(this);
    });


    //Remove individual slide
    $('.slide_delete_button').live('click', function () {
        // event.preventDefault();
        var agree = confirm("Are you sure you wish to delete this slide?");
        if (agree) {
            var $trash = $(this).parents('li');
            //$trash.slideUp('slow', function(){ $trash.remove(); }); //chrome + confirm bug made slideUp not working...
            $trash.animate({
                opacity: 0.25,
                height: 0,
            }, 500, function () {
                $(this).remove();
            });
            return false; //Prevent the browser jump to the link anchor
        } else {
            return false;
        }
    });

    //Add new slide
    $(".slide_add_button").live('click', function () {
        var slidesContainer = $(this).prev();
        var sliderId = slidesContainer.attr('id');

        var numArr = $('#' + sliderId + ' li').find('.order').map(function () {
            var str = this.id;
            str = str.replace(/\D/g, '');
            str = parseFloat(str);
            return str;
        }).get();

        var maxNum = Math.max.apply(Math, numArr);
        if (maxNum < 1) {
            maxNum = 0
        }
        ;
        var newNum = maxNum + 1;

        var newSlide = '<li class="temphide"><div class="slide_header"><strong>Slide ' + newNum + '</strong><input type="hidden" class="slide of-input order" name="' + sliderId + '[' + newNum + '][order]" id="' + sliderId + '_slide_order-' + newNum + '" value="' + newNum + '"><a class="slide_edit_button" href="#">Edit</a></div><div class="slide_body" style="display: none; "><label>Alt tag</label><input class="slide of-input of-slider-title" name="' + sliderId + '[' + newNum + '][title]" id="' + sliderId + '_' + newNum + '_slide_title" value=""><label>Image URL</label><input class="upload slide of-input" name="' + sliderId + '[' + newNum + '][url]" id="' + sliderId + '_' + newNum + '_slide_url" value=""><div class="upload_button_div"><span class="button media_upload_button" id="' + sliderId + '_' + newNum + '">Upload</span><span class="button remove-image hide" id="reset_' + sliderId + '_' + newNum + '" title="' + sliderId + '_' + newNum + '">Remove</span></div><div class="screenshot"></div><a class="slide_delete_button" href="#">Delete</a><div class="clear"></div></div></li>';

        slidesContainer.append(newSlide);
        var nSlide = slidesContainer.find('.temphide');
        nSlide.fadeIn('fast', function () {
            $(this).removeClass('temphide');
        });

        optionsframework_file_bindings(); // re-initialise upload image..

        return false; //prevent jumps, as always..
    });

    //Sort slides
    $('.slider').find('ul').each(function () {
        var id = $(this).attr('id');
        $('#' + id).sortable({
            placeholder: "placeholder",
            opacity: 0.6,
            handle: ".slide_header",
            cancel: "a"
        });
    });


    /**	Sorter (Layout Manager) */
    $('.sorter').each(function () {
        var id = $(this).attr('id');
        $('#' + id).find('ul').sortable({
            items: 'li',
            placeholder: "placeholder",
            connectWith: '.sortlist_' + id,
            opacity: 0.6,
            update: function () {
                $(this).find('.position').each(function () {

                    var listID = $(this).parent().attr('id');
                    var parentID = $(this).parent().parent().attr('id');
                    parentID = parentID.replace(id + '_', '')
                    var optionID = $(this).parent().parent().parent().attr('id');
                    $(this).prop("name", optionID + '[' + parentID + '][' + listID + ']');

                });
            }
        });
    });


    /**	Ajax Backup & Restore MOD */
    //backup button
    $('#of_backup_button').live('click', function () {

        var answer = confirm("Click OK to backup your current saved options.")

        if (answer) {

            var clickedObject = $(this);
            var clickedID = $(this).attr('id');

            var nonce = $('#security').val();

            var data = {
                cmd: 'of_ajax_post_action',
                type: 'backup_options',
                security: nonce
            };

            $.post(options_url, data, function (response) {

                //check nonce
                if (response == -1) { //failed

                    var fail_popup = $('#of-popup-fail');
                    fail_popup.fadeIn();
                    window.setTimeout(function () {
                        fail_popup.fadeOut();
                    }, 2000);
                }

                else {

                    var success_popup = $('#of-popup-save');
                    success_popup.fadeIn();
                    window.setTimeout(function () {
                        location.reload();
                    }, 1000);
                }

            });

        }

        return false;

    });

    //restore button
    $('#of_restore_button').live('click', function () {

        var answer = confirm("'Warning: All of your current options will be replaced with the data from your last backup! Proceed?")

        if (answer) {

            var clickedObject = $(this);
            var clickedID = $(this).attr('id');

            var nonce = $('#security').val();

            var data = {
                cmd: 'of_ajax_post_action',
                type: 'restore_options',
                security: nonce
            };

            $.post(options_url, data, function (response) {

                //check nonce
                if (response == -1) { //failed

                    var fail_popup = $('#of-popup-fail');
                    fail_popup.fadeIn();
                    window.setTimeout(function () {
                        fail_popup.fadeOut();
                    }, 2000);
                }

                else {

                    var success_popup = $('#of-popup-save');
                    success_popup.fadeIn();
                    window.setTimeout(function () {
                        location.reload();
                    }, 1000);
                }

            });

        }

        return false;

    });

    /**	Ajax Transfer (Import/Export) Option */
    $('#of_import_button').live('click', function () {

        var answer = confirm("Click OK to import options.")

        if (answer) {

            var clickedObject = $(this);
            var clickedID = $(this).attr('id');

            var nonce = $('#security').val();

            var import_data = $('#export_data').val();

            var data = {
                cmd: 'of_ajax_post_action',
                type: 'import_options',
                security: nonce,
                data: import_data
            };

            $.post(options_url, data, function (response) {
                var fail_popup = $('#of-popup-fail');
                var success_popup = $('#of-popup-save');

                //check nonce
                if (response == -1) { //failed
                    fail_popup.fadeIn();
                    window.setTimeout(function () {
                        fail_popup.fadeOut();
                    }, 2000);
                }
                else
                {
                    success_popup.fadeIn();
                    window.setTimeout(function () {
                        location.reload();
                    }, 1000);
                }

            });

        }

        return false;

    });

    /** AJAX Save Options */
    $('#of_save').live('click', function () {

        var nonce = $('#security').val();

        $('.ajax-loading-img').fadeIn();

        //get serialized data from all our option fields			
        var serializedReturn = $('#of_form :input[name][name!="security"][name!="of_reset"]').serialize();

        $('#of_form :input[type=checkbox]').each(function () {
            if (!this.checked) {
                serializedReturn += '&' + this.name + '=0';
            }
        });

        var data = {
            type: 'save',
            cmd: 'of_ajax_post_action',
            security: nonce,
            data: serializedReturn
        };

        $.post(options_url, data, function (response) {
            var success = $('#of-popup-save');
            var fail = $('#of-popup-fail');
            var loading = $('.ajax-loading-img');
            loading.fadeOut();

            if (response == 1) {
                success.fadeIn();
            } else {
                fail.fadeIn();
            }

            window.setTimeout(function () {
                success.fadeOut();
                fail.fadeOut();
            }, 2000);
        });

        return false;

    });


    /* AJAX Options Reset */
    $('#of_reset').click(function () {

        //confirm reset
        var answer = confirm(lang.msg_option_reset);

        //ajax reset
        if (answer) {

            var nonce = $('#security').val();

            $('.ajax-reset-loading-img').fadeIn();

            var data = {
                type: 'reset',
                cmd: 'of_ajax_post_action',
                security: nonce,
            };

            $.post(options_url, data, function (response) {
                var success = $('#of-popup-reset');
                var fail = $('#of-popup-fail');
                var loading = $('.ajax-reset-loading-img');
                loading.fadeOut();

                if (response == 1)
                {
                    success.fadeIn();
                    window.setTimeout(function () {
                        location.reload();
                    }, 1000);
                }
                else
                {
                    fail.fadeIn();
                    window.setTimeout(function () {
                        fail.fadeOut();
                    }, 2000);
                }


            });

        }

        return false;

    });


    /**	Tipsy @since v1.3 */
    if ($().tipsy) {
        $('.typography-size, .typography-height, .typography-face, .typography-style, .of-typography-color').tipsy({
            fade: true,
            gravity: 's',
            opacity: 0.7,
        });
    }


    /**
     * JQuery UI Slider function
     * Dependencies 	 : jquery, jquery-ui-slider
     * Feature added by : Smartik - http://smartik.ws/
     * Date 			 : 03.17.2013
     */
    $('.smof_sliderui').each(function () {

        var obj = $(this);
        var sId = "#" + obj.data('id');
        var val = parseInt(obj.data('val'));
        var min = parseInt(obj.data('min'));
        var max = parseInt(obj.data('max'));
        var step = parseInt(obj.data('step'));

        //slider init
        obj.slider({
            value: val,
            min: min,
            max: max,
            step: step,
            range: "min",
            slide: function (event, ui) {
                $(sId).val(ui.value);
            }
        });

    });


    /**
     * Switch
     * Dependencies 	 : jquery
     * Feature added by : Smartik - http://smartik.ws/
     * Date 			 : 03.17.2013
     */
    $(".cb-enable").click(function () {
        var parent = $(this).parents('.switch-options');
        $('.cb-disable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.main_checkbox', parent).attr('checked', true);

        //fold/unfold related options
        var obj = $(this);
        var $fold = '.f_' + obj.data('id');
        $($fold).slideDown('normal', "swing");
    });
    $(".cb-disable").click(function () {
        var parent = $(this).parents('.switch-options');
        $('.cb-enable', parent).removeClass('selected');
        $(this).addClass('selected');
        $('.main_checkbox', parent).attr('checked', false);

        //fold/unfold related options
        var obj = $(this);
        var $fold = '.f_' + obj.data('id');
        $($fold).slideUp('normal', "swing");
    });
    //disable text select(for modern chrome, safari and firefox is done via CSS)
    if (($.browser.msie && $.browser.version < 10) || $.browser.opera) {
        $('.cb-enable span, .cb-disable span').find().attr('unselectable', 'on');
    }


    /**
     * Google Fonts
     * Dependencies 	 : google.com, jquery
     * Feature added by : Smartik - http://smartik.ws/
     * Date 			 : 03.17.2013
     */
    function GoogleFontSelect(slctr, mainID) {

        var _selected = $(slctr).val(); 						//get current value - selected and saved
        var _linkclass = 'style_link_' + mainID;
        var _previewer = mainID + '_ggf_previewer';

        if (_selected) { //if var exists and isset

            $('.' + _previewer).fadeIn();

            //Check if selected is not equal with "Select a font" and execute the script.
            if (_selected !== 'none' && _selected !== 'Select a font') {

                //remove other elements crested in <head>
                $('.' + _linkclass).remove();

                //replace spaces with "+" sign
                var the_font = _selected.replace(/\s+/g, '+');

                //add reference to google font family
                $('head').append('<link href="http://fonts.googleapis.com/css?family=' + the_font + '" rel="stylesheet" type="text/css" class="' + _linkclass + '">');

                //show in the preview box the font
                $('.' + _previewer).css('font-family', the_font + ', sans-serif');
                //console.log(the_font);
            } else {

                //if selected is not a font remove style "font-family" at preview box
                $('.' + _previewer).css('font-family', '');
                $('.' + _previewer).fadeOut();

            }

        }

    }

    //init for each element
    $('.google_font_select').each(function () {
        var mainID = $(this).attr('id');
        GoogleFontSelect(this, mainID);
    });

    //init when value is changed
    $('.google_font_select').change(function () {
        var mainID = $(this).attr('id');
        GoogleFontSelect(this, mainID);
    });




    function optionsframework_remove_file(selector) {
        selector.find('.remove-image').hide().addClass('hide');//hide "Remove" button
        selector.find('.upload').val('');
        selector.find('.of-background-properties').hide();
        selector.find('.screenshot').slideUp();
        selector.find('.remove-file').unbind();
        // We don't display the upload button if .upload-notice is present
        // This means the user doesn't have the WordPress 3.5 Media Library Support
        if ($('.section-upload .upload-notice').length > 0) {
            $('.media_upload_button').remove();
        }
        optionsframework_file_bindings();
    }

    function optionsframework_file_bindings() {
        $('.remove-image, .remove-file').on('click', function () {
            optionsframework_remove_file($(this).parents('.section-upload, .section-media, .slide_body'));
        });

        $('.media_upload_button').unbind('click').click(function (event) {
            optionsframework_add_file(event, $(this).parents('.section-upload, .section-media, .slide_body'));
        });
    }

    optionsframework_file_bindings();
}); //end doc ready
