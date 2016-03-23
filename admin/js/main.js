$(document).ready(function () {

    var reload = false;
    // media modal close
    $(".model-close").click(function () {
        if (window.reload == true) {
            //window.location = admin_url + 'index.php?f=media&current_page=1';
            addOrUpdateUrlParam('current_page', "1");
            window.location.href = window.location.href + "&current_page=1";
        }
    })

    // auto focus first input or text area
    $("input:text, textarea").first().focus();

    $(".detail-form").focusout(function () {
        _id = $(this).find('[name="media_id"]').val();
        _title = $(this).find('[name="title"]').val();
        _caption = $(this).find('[name="caption"]').val();
        _alt = $(this).find('[name="alt"]').val();
        _desc = $(this).find('[name="desc"]').val();
        $.ajax({
            type: "POST",
            url: "ajax.php?cmd=update_media_info",
            data: {
                id: _id,
                title: _title,
                caption: _caption,
                alt: _alt,
                desc: _desc
            },
            beforeSend: function () {
                $('#img-loading-' + _id).show();
            },
            success: function (response) {
                $('#img-loading-' + _id).hide();
            }
        }); //  end ajax
    })


    $("#search-google-input").keyup(function (e) {
        if (e.keyCode == 13) {
            searchGoogleImages();
        }
    });

    // when click media thumbnail
    $(".media-thumbnail").click(function () {
        $(".media-thumbnail").parent().removeClass('active');
        $(this).parent().addClass('active');
    })


    $('.checkAll').click(function () {
        var checked = $(this).attr('checked') ? true : false;
        $('.idItem').each(function () {
            if (checked) {
                $(this).parent('td').parent('tr').css('background-color', '#fff6b9');
            } else {
                $(this).parent('td').parent('tr').css('background-color', '');
            }
            $(this).attr('checked', checked);
        });
    });

    $('.idItem').click(function () {
        var hasChecked = ($('.idItem:not(:checked)').length > 0) ? false : true;
        $('#checkAll').attr('checked', hasChecked);
        if ($(this).attr('checked')) {
            $(this).parent('td').parent('tr').css('background-color', '#fff6b9');
        } else {
            $(this).parent('td').parent('tr').css('background-color', '');
        }
    });

    $('.btnDelete').click(function () {
        if ($('.idItem:checked').length == 0) {
            alert(lang.msg_no_select.replace('%s', $('#listName').val()));
            return false;
        }
        var idList = "0";
        $('.idItem:checked').each(function () {
            idList += "," + $(this).val();
        });
        $('#listID').val(idList);
        $('#action').val("del");
        if (confirm(lang.msg_confirm_delete.replace('%s', $('#listName').val()), false) == true) {
            $('#frm').submit();
        }
    });

    $('.btnNew').click(function () {
        $('#action').val("new");
        $('#frm').submit();
    });


    // show modal upload form
    $("#btnAddModel, .btnAddModel").click(function () {
        showModel(0);
        $('#frm').submit(function () {
            return false;
        });
    })

    // show modal search google form
    $("#btnSearchGoogleImages, .btnSearchGoogleImages").click(function () {
        showModel('google');
        $('#frm').submit(function () {
            return false;
        });
    })

    // next and prevous keypress
    $(document).keyup(function (e) {
        if(e.keyCode == 39){
            $(".wrap-model:visible").find('.arrow-next-modal').click();
        }
        
        if(e.keyCode == 37){
            $(".wrap-model:visible").find('.arrow-prev-modal').click();
        }
    });


    //css
    $('.row').each(function () {
        $(this).mouseover(function () {
            $(this).find('td').css('background-color', '#ffffaa');
        });
        $(this).mouseout(function () {
            $(this).find('td').css('background-color', 'none');
        });
    });


    //menu
    $('.nav li a.parent_link').each(function () {
        $(this).click(function () {
            if ($(this).parent('li').hasClass('parent_menu')) {
                $(this).parent('li').removeClass('parent_menu');
                $(this).parent('li').addClass('parent_selected');
            } else if ($(this).parent('li').hasClass('parent_selected')) {
                $(this).parent('li').addClass('parent_menu');
                $(this).parent('li').removeClass('parent_selected');
            }
        });

    });

    $('.price, .input-number').keyup(function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (!(evt.keyCode == 8                                // backspace
                || evt.keyCode == 46                              // delete
                || (evt.keyCode >= 35 && evt.keyCode <= 40)     // arrow keys/home/end
                || (evt.keyCode >= 48 && evt.keyCode <= 57)     // numbers on keyboard
                || (evt.keyCode >= 96 && evt.keyCode <= 105))   // number on keypad
                ) {
            evt.preventDefault();     // Prevent character input
            return false;
        }

        var value = $(this).val();

        /*Action*/
        if (value != '') {
            value = value.replace(/,/g, "");
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            value = Number(value).toLocaleString('en');
            if ('NaN' == value) {
                $(this).val('');
            } else {
                $(this).val(value);
            }
        }
    });


    // su kien cua nhung input-price
    $('.price-input').each(function () {
        var ichars = "!@#$%^&*()+=[]\\\';/{}|\":<>?~`.- ABCDEFGHIJKLMNOPQRSTUVWXYZÉÈẺẸẼÊẾỂỀỆỄÝỶỲỴỸÚỦÙỤŨƯỨỬỰỪỮÓỎÒỌÕÔỐỔỘỒỖƠỚỞỜỢỠÂẤẦẨẬẪĂẮẲẶẰẴĐabcdefghijklmnopqrstuvwxyzéèẻẹẽêếểềệễýỷỳỵỹúủùụũưứửựừữóỏòọõôốổộồỗơớởờợỡâấầẩậẫăắẳặằẵđ";
        s = "0123456789".split('');
        for (i = 0; i < s.length; i++)
            if (ichars.indexOf(s[i]) != -1)
                s[i] = "\\" + s[i];
        var allow = s.join('|');

        var reg = new RegExp(allow, 'gi');
        var ch = ichars;
        ch = ch.replace(reg, '');

        $(this).keypress(function (e) {
            if (e.charCode == 0) {
                $(this).val('');
            } else {
                if (!e.charCode)
                    k = String.fromCharCode(e.which);
                else
                    k = String.fromCharCode(e.charCode);

                if (($(this).val() == "") && (k == '0'))
                    e.preventDefault();
                else {
                    if (ch.indexOf(k) != -1)
                        e.preventDefault();
                    else {
                        var arr = $(this).val() + k;
                        arr = arr.replace(/[^0-9]/g, '');
                        arr = arr.split('');
                        var result = "";
                        var count = 0;
                        for (i = arr.length - 1; i >= 0; i--) {
                            if (arr[i] != "") {
                                count++;
                                result = arr[i] + result;
                                if ((count >= 3) && (i > 0)) {
                                    count = 0;
                                    result = "," + result;
                                }
                            }
                        }

                        $(this).val(result);
                        $('#' + $(this).attr('valto')).val(result.replace(/[^0-9]/g, ''));
                    }

                }
                if (e.ctrlKey && k == 'v')
                    e.preventDefault();
                e.preventDefault();
            }
        });
        // su kien thay doi gia tri o price				
        $(this).change(function () {
            var arr = $(this).val();
            arr = arr.replace(/[^0-9]/g, '');
            arr = arr.split('');
            var result = "";
            var count = 0;
            for (i = arr.length - 1; i >= 0; i--) {
                if (arr[i] != "") {
                    count++;
                    result = arr[i] + result;
                    if ((count >= 3) && (i > 0)) {
                        count = 0;
                        result = "," + result;
                    }
                }
            }

            $(this).val(result);
        });

        $(this).val($(this).attr('rel'));

        $(this).blur(function () {
            if ($(this).val() == "") {
                $(this).val('0');
            }
            $('#' + $(this).attr('valto')).val($(this).val().replace(/[^0-9]/g, ''));
        });

        $(this).focus(function () {
            if ($(this).val() == "0") {
                $(this).val('');
            }
        });

    });


    //update image in add
    $('.input-image').each(function () {
        $(this).blur(function () {
            if ($(this).val() == "") {
                $(this).parent('td').find('.image_review').attr('src', 'images/noimage.jpg');
            } else {
                var image_url = $(this).val();
                var http_prefix = image_url.substring(0, 4);
                if(http_prefix != 'http'){
                    image_url = base_url + image_url;
                }
                $(this).parent('td').find('.image_review').attr('src', image_url);
            }
        });
    });




    /* ------------------------------------------------------------------ */
    /* Validate
     /* ------------------------------------------------------------------ */
    if ($().validate) {
        var validator = $("#frm").validate({
            errorElement: "em",
            errorPlacement: function (error, element) {
                //error.appendTo( element.parent("td").next("td") );
                error.appendTo(element.parent("td").find("span.error"));
                element.parent("td").find("span.error").show();
            },
            success: function (label) {
                label.text("Ok!").addClass("success");
            }
        });
    }


    /* ------------------------------------------------------------------ */
    /* AjaxUpload
     /* ------------------------------------------------------------------ */
    // upload images
    $('.input-image').each(function () {
        var id = $(this).parent('td').find('a').attr('id');
        var button = $(this).parent('td').find('a.btnUpload');
        var label = $(this).parent('td').find('span.lblUpload');
        new AjaxUpload(id, {
            action: 'upload-handler.php?type_upload=single',
            data: {},
            onSubmit: function (file, ext) {
                // Allow only images. You should add security check on the server-side.
                if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)) {
                    /* Setting data */
                    this.setData({
                        'key': 'This string will be send with the file',
                        'type': type
                    });
                    interval = window.setInterval(function () {
                        var text = label.text();
                        if (text.length < 13) {
                            label.text(text + '.');
                        } else {
                            label.text('Đang tải');
                        }
                    }, 200);

                } else {
                    // extension is not allowed
                    label.html('<font color=red>' + lang.msg_allow_extension + ' (jpg|png|jpeg|gif)</font>');
                    // cancel upload
                    return false;
                }
            },
            onComplete: function (file, response) {
                window.clearInterval(interval);
                label.html('(<i>' + file + '</i>)');
                button.text(lang.change_images);
                var obj = $.parseJSON(response);
                button.parent('td').find('input').val(obj.src);
                button.parent('td').find('img').attr('src', base_url + obj.src);
                window.reload = true;
            }
        });
    });  // input-images

    // upload file
    $('.input-file').each(function () {
        var id = $(this).parent('td').find('a').attr('id');
        var button = $(this).parent('td').find('a.browse');
        var label = $(this).parent('td').find('span');
        new AjaxUpload(id, {
            action: 'upload-file.php',
            data: {'type': type},
            onSubmit: function (file, ext) {
            },
            onComplete: function (file, response) {
                button.text(lang.change_file);
                var obj = $.parseJSON(response);
                if (obj.status == 'sucsess') {
                    button.parent('td').find('input').val(obj.path);
                    button.parent('td').find('em').html(obj.path);
                    window.reload = true;
                } else {
                    button.parent('td').find('input').val('');
                    button.parent('td').find('em').css('color', 'red').html(obj.msg);
                }

            }
        });
    }); // input-file



    // multi upload
    if ($().fileupload) {
        var ul = $('#thumbnails');
        $('#drop a').click(function () {
            // Simulate a click on the file input button
            // to show the file browser dialog
            $(this).parent().find('input').click();
        });

        // Initialize the jQuery File Upload plugin
        $('#frm').fileupload({
            // url upload handler
            url: admin_url + "upload-handler.php?type=" + type,
            // set data type
            dataType: 'json',
            // set limit file upload
            limitConcurrentUploads: 3,
            // This element will accept file drag/drop uploading
            dropZone: $('#drop'),
            // This function is called when a file is added to the queue;
            // either via the browse button, or via drag/drop:
            add: function (e, data) {
                var tpl = $('<li>' +
                        '<img src="images/bg-upload.png" height="100" width="100" />' +
                        '<div class="wrap-process">' +
                        '<div class="progress">' +
                        '<div class="percent"></div>' +
                        '<div class="bar" style="width: 0%;"></div>' +
                        '</div>' +
                        '</div>' +
                        '</li>');


                // Add the HTML to the UL element
                data.context = tpl.appendTo(ul);

                // Automatically upload the file once it is added to the queue
                var jqXHR = data.submit();
            },
            progress: function (e, data) {
                // Calculate the completion percentage of the upload
                var progress = parseInt(data.loaded / data.total * 100, 10);

                // Update the hidden input field and trigger a change
                // so that the jQuery knob plugin knows to update the dial
                data.context.find('.bar').css('width', progress + '%');

                if (progress == 100) {
                    data.context.removeClass('working');
                }
            },
            done: function (e, data) {
                window.reload = true;
                var obj = $.parseJSON(data.jqXHR.responseText);
                var imgObj = data.context.find('img');
                imgObj.attr('src', base_url + obj.src);
                data.context.find('.wrap-process').parent().attr('id', 'media-' + obj.id);
                data.context.find('.wrap-process').remove();
                $('<div class="rwmb-image-bar"><a onclick="deleteFileUpload(' + obj.id + ',\'' + type + '\')" class="rwmb-delete-file" href="javascript:void(0)">×</a></div>').insertAfter(imgObj);

            },
            fail: function (e, data) {
                // Something has gone wrong!
                data.context.addClass('error');
            }

        });

        // Prevent the default action when a file is dropped on the window
        $(document).on('drop dragover', function (e) {
            e.preventDefault();
        });


        // Helper function that formats the file sizes
        function formatFileSize(bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }
            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB';
            }
            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB';
            }
            return (bytes / 1000).toFixed(2) + ' KB';
        }
    }



    /* ------------------------------------------------------------------ */
    /* DatePicker
     /* ------------------------------------------------------------------ */
    if ($().datetimepicker) {
        switch(current_lang){
            case "en":
                    month_names = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                    day_names_short = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                    day_names_min = ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'];
                break;
            
            case "vi":
            default:
                    month_names = ['Tháng 01', 'Tháng 02', 'Tháng 03', 'Tháng 04', 'Tháng 05', 'Tháng 06', 'Tháng 07', 'Tháng 08', 'Tháng 09', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
                    day_names_short = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                    day_names_min = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                break;
        }
        $('.inputdate').each(function () {
            $(this).datetimepicker({
                closeText: lang.done,
                prevText: lang.previous,
                nextText: lang.next,
                currentText: lang.current,
                monthNames: month_names,
                dayNamesShort: day_names_short,
                dayNamesMin: day_names_min,
                weekHeader: 'Не',
                dateFormat: 'yy-mm-dd',
                timeFormat: 'hh:mm:ss',
                showMonthAfterYear: false,
                yearSuffix: '',
                changeYear: false,
                timeText: lang.time,
                hourText: lang.hour,
                minuteText: lang.minute,
                secondText: lang.seconds
            });
        });
    } // endif


    /* ------------------------------------------------------------------ */
    /* lazyload images
     /* ------------------------------------------------------------------ */
    if ($().lazyload) {
        //$(".content img:not(.sticky_images, .wrap-model img, #of_container img, .media-content-thumb img)").lazyload({
        //    effect: "fadeIn"
        //});
    }
    
    /* ------------------------------------------------------------------ */
    /* Modal popup
     /* ------------------------------------------------------------------ */
    if ($().modal) {
        $('.popup').click(function (e) {
            $('#basic-modal-content').modal();
            return false;
        });

        $(".wrap-media").click(function () {
            $(this).modal();
        })

        $(".model-close").click(function () {
            $("body").css("overflow", "visible");
            $.modal.close();
            history.pushState(null, "HCMS media!", "?f=media");
        })
        
        $(".simplemodal-overlay").click(function(e){
            model-close
            $.modal.close();
        })
    }


    /* ------------------------------------------------------------------ */
    /* Numeric
     /* ------------------------------------------------------------------ */
    if ($().numeric) {
        $('.input-number').each(function () {
            $(this).numeric();
        });
    }


    /* ------------------------------------------------------------------ */
    /* Tipsy
     /* ------------------------------------------------------------------ */
    if ($().tipsy) {
        $('li[rel=media-tooltip]').each(function () {
            $(this).tipsy({
                className: "left",
                gravity: 's',
                delayIn: 300,
                html: true
            });
        });
    }


});

// delete event
function del(id) {
    if (confirm(lang.delete + ' ' + $('#listName').val() + '?')) {
        $('#action').val("del");
        $('#listID').val(id);
        $('#frm').submit();
    }
}
// edit event
function edit(id) {
    $('#action').val("edit");
    $('#listID').val(id);
    $('#frm').submit();
}
// sort event
function order(orderby, dir) {
    $('#current_page').val(1);
    $('#orderby').val(orderby);
    $('#orderby_dir').val(dir);
    $('#action').val("order");
    $('#frm').submit();
}
// change page event
function change_page(obj) {
    $('#current_page').val($(obj).val());
    $('#action').val("view");
    $('#frm').submit();
}
// change rop (row on page)
function set_rop() {
    $('#current_page').val(1);
    $('#action').val("set_rop");
    $('#frm').submit();
}
// save event
function save() {
    var field = [];
    $(".save-field").each(function () {
        field.unshift($(this).attr("rel"));
    });
    $('#field').val(field);
    var idList = "0";
    $('.idItem').each(function () {
        idList += "," + $(this).val();
    });
    $('#listID').val(idList);
    $('#action').val("save");
    $('#frm').submit();
}
// switch val event
function switchval(fieldname, id, val) {
    /*
     $('#action').val("switchval");
     $('#field').val(fieldname);
     $('#singleid').val(id);
     $('#singleval').val(val);
     $('#frm').submit();
     */
    src = $('#' + fieldname + '-' + id).find('img').attr('src');
    switch (src) {
        case 'images/check.png':
            val = 0;
            src = 'images/uncheck.png';
            break;

        case 'images/uncheck.png':
            val = 1;
            src = 'images/check.png';
            break;

    }
    $.ajax({
        type: "POST",
        url: document.URL,
        data: {
            action: 'switchval',
            field: fieldname,
            singleid: id,
            singleval: val
        },
        beforeSend: function () {
            $('#' + fieldname + '-' + id).find('img').attr('src', src);
        },
        success: function (response) {
            $('#' + fieldname + '-' + id).find('img').attr('src', src);
        }
    });

}
// submit form
function submitForm(_form) {
    $("#" + _form).submit()
}
// don't allow submit form
function notSubmit(_form) {
    $("#" + _form).submit(function () {
        return false;
    })
}
// delete default images
function deleteDefaultImages(obj) {
    if (confirm(lang.confirm_delete)) {
        var inputObj = $(obj).closest('.wrap-images').siblings('input');
        var imgObj = $(obj).closest('.wrap-images').find('img');
        var lblObj = $(obj).closest('.wrap-images').siblings('.lblUpload');
        var _src = inputObj.val();
        if (_src && _src != 'undefined') {
            $.ajax({
                type: "POST",
                url: "ajax.php?cmd=delete_default_images&src=" + _src,
                success: function (response) {
                    inputObj.val('');
                    lblObj.html('');
                    imgObj.attr('src', admin_url + 'images/noimage.jpg');
                }
            }); //  end ajax
        }
    }
}

// delete images with type="input:multiimages"
function deleteFileUpload(_id, _f) {
    if (confirm(lang.confirm_delete)) {
        $("#img_" + _id).hide();
        $.ajax({
            type: "POST",
            url: "ajax.php?cmd=delete_file&id=" + _id + "&f=" + _f,
            success: function (response) {
                if (response == 'ss') {
                    $("#media-" + _id).remove();
                } else {
                    alert(lang.msg_error_delete_images);
                    $("#media-" + _id).show();
                }
            }
        }); //  end ajax
    }  // end if      
}

// cms clock
function cmsClock(_element, _date) {
    var nowDateTime = new Date(_date);
    var d = nowDateTime.getTime();
    var mkHour, mkMinute, mkSecond;
    setInterval(function () {
        d = parseInt(d) + 1000;
        var nowDateTime = new Date(d);
        mkHour = new String(nowDateTime.getHours());
        if (mkHour.length == 1) {
            mkHour = "0" + mkHour;
        }
        mkMinute = new String(nowDateTime.getMinutes());
        if (mkMinute.length == 1) {
            mkMinute = "0" + mkMinute;
        }
        mkSecond = new String(nowDateTime.getSeconds());
        if (mkSecond.length == 1) {
            mkSecond = "0" + mkSecond;
        }
        var runDateTime = mkHour + ":" + mkMinute + ":" + mkSecond;
        $(_element).html(runDateTime);
    }, 1000);
}
// add attribute
function addAttribute() {
    if ($('.attribute').length == 1 && $('.attribute:hidden').length == 1) {
        $('.attribute').show();
    } else {
        var _class = 'attribute';
        var _attrHTML = $('.attribute')[0].outerHTML;
        var _inputVal = $(_attrHTML).find('input').val();
        var _attrHTML = _attrHTML.replace(_inputVal, "");
        var _textareaVal = $(_attrHTML).find('textarea').val();
        var _attrHTML = _attrHTML.replace(_textareaVal, "");
        $(".wrap-attribute").append(_attrHTML);
    }

}
// delete attribute
function deleteAttribute(obj) {
    var _element = $(obj).parent().closest('.attribute');
    if (confirm(lang.confirm_delete)) {
        if ($(".attribute").length > 1) {
            _element.remove();
        } else {
            _element.find('input').val('');
            _element.find('textarea').val('');
            _element.hide();
        }
    }
}

// delete media
function deleteMedia(_id) {
    if (confirm(lang.confirm_delete_file)) {
        $("#media-" + _id).hide();
        $.ajax({
            type: "POST",
            data: {
                cmd: 'delete_media',
                list_id: _id
            },
            url: ajax_url,
            beforeSend: function () {
                $("#media-" + _id).remove();
                if ($().modal) {
                    $("body").css("overflow", "visible");
                    $.modal.close();
                    history.pushState(null, "HCMS media!", "?f=media");
                }
            },
            success: function (response) {
            },
            error: function () {
                $("#media-" + _id).show();
            }
        }); //  end ajax
    }
}

// get url param for CKeditor
function getUrlParam(paramName) {
    var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
    var match = window.location.search.match(reParam);

    return (match && match.length > 1) ? match[ 1 ] : null;
}


// show media modal
function showMediaModel(obj) {
    var id = $(obj).data('id');
    showModel(id);
}


// show modal
function showModel(id) {
    var query_string = window.location.search;
    $.modal.close();
    $("body").css("overflow", "hidden");
    $("#modal-content-" + id).modal();
    query_string = removeURLParameter(query_string, 'id');
    history.pushState(null, "Media", query_string + "&id=" + id);
}


// remove query string parameter
function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0; ) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        url = urlparts[0] + '?' + pars.join('&');
        return url;
    } else {
        return url;
    }
}


// function for editor
function setUrlParam(obj) {
    var fileUrl = $(obj).data('url');
    var filePath = $(obj).data('path');
    // Helper function to get parameters from the query string.
    var funcNum = getUrlParam('CKEditorFuncNum');
    // add path to input
    var inputId = getUrlParam('inputId');
    if (inputId) {
        elem = window.opener.document.getElementById(inputId);
        elem.value = filePath;
        var event = new Event('blur');
        elem.dispatchEvent(event);
        window.close();
    } else if (funcNum) {
        window.opener.CKEDITOR.tools.callFunction(funcNum, fileUrl);
        window.close();
    } else {
        showMediaModel(obj)
    }
}

// open popup
function popitup(url) {
    var w = 800;
    var h = 500;
    var left = (screen.width / 2) - (w / 2);
    var top = (screen.height / 2) - (h / 2);

    param = 'height=' + h + ',width=' + w + ',scrollbars=1,status=1,top=' + top + ',left=' + left;
    newwindow = window.open(url, 'HCMS', param);
    if (window.focus) {
        newwindow.focus()
    }
    return false;
}


// google images search (for media)
function searchGoogleImages() {
    var _q = $("#search-google-input").val();
    $.ajax({
        type: "POST",
        url: "ajax.php?cmd=get_google_image&q=" + _q,
        data: {},
        beforeSend: function () {
            $("#search-google-content").html('');
            $("#search-google-content").addClass('bg-loading');
        },
        success: function (response) {
            $("#search-google-content").removeClass('bg-loading');
            var result_html = '';
            var sticky_tooltip = '';
            var obj = $.parseJSON(response);
            var count = 0;
            for (index = 0; index < obj.length; ++index) {
                count++;
                var image_info = obj[index]['info'];
                result_html += '<li style="background: url(' + obj[index]['thumb'] + ') no-repeat center center;">';
                result_html += '<a href="#" data-tooltip="sticky_' + count + '" data-url="' + obj[index]['url'] + '" onclick="saveImage(this)"><span>' + lang.use_this_image + '?</span><em class="info"> ' + image_info + '</em></a></li>';

                sticky_tooltip += '<div id="sticky_' + count + '" class="atip"><img src="' + obj[index]['url'] + '" class="sticky_images" /></div>';
            }

            if (sticky_tooltip != '') {
                sticky_tooltip = '<div id="mystickytooltip" class="stickytooltip">' + sticky_tooltip + '</div>';
            }

            $("#search-google-content").html(result_html);
            $('body').append(sticky_tooltip);
            stickytooltip.init("*[data-tooltip]", "mystickytooltip");
        }
    }); //  end ajax
}

// save image search from google
function saveImage(obj) {
    var _url = $(obj).data('url');
    $.ajax({
        type: "POST",
        url: "ajax.php?cmd=save_google_image",
        data: {
            src: _url
        },
        beforeSend: function () {
            $(obj).html('<span class="gimage-loading"></span>').css('visibility', 'visible');
            ;
        },
        success: function (response) {
            var html_status = '';
            var resultResponse = $.parseJSON(response);
            $(obj).css('visibility', 'visible');
            switch (resultResponse.status) {
                case 200:
                    html_status = '<span class="gimage-complted"></span>';
                    window.reload = true;
                    break;

                default:
                    html_status = '<span class="gimage-fail"></span><em>' + resultResponse.msg + '</em>';
                    break;
            }
            $(obj).html(html_status);
        }
    }); //  end ajax
}

// add or update url param
function addOrUpdateUrlParam(name, value) {
  var href = window.location.href;
  var regex = new RegExp("[&\\?]" + name + "=");
  if(regex.test(href)) {
    regex = new RegExp("([&\\?])" + name + "=\\d+");
    window.location.href = href.replace(regex, "$1" + name + "=" + value);
  }
  else {
    if(href.indexOf("?") > -1)
      window.location.href = href + "&" + name + "=" + value;
    else
      window.location.href = href + "?" + name + "=" + value;
  }
}