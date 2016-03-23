
// function reload captcha
function reloadCaptcha(obj, w, h, c) {
    var link = BASE_URL + 'captcha.php?width=' + w + '&height=' + h + '&characters=' + c + '&r=' + Math.random();
    $(obj).attr('src', link);
}

// call submit form
function submitForm(obj) {
    $(obj).submit();
}



$(function () {
    // profile
    $(window).load(function () {
        var tab = location.href.split("#")[1];
        if (tab != undefined) {
            showUserTab(tab);
        } else {
            var tab = $.cookie('policy_tab');
            if (tab != undefined) {
                showUserTab(tab);
            } else {
                showUserTab('tab-login-info');
            }
        }
    });
    $("#menu-list a").click(function () {
        var tab = this.href.split("#")[1];
        $.cookie('policy_tab', tab);
        showUserTab(tab);
    })

    $(".rg-input input").focus(function () {
        $('.tipguide').hide();
        $(this).siblings('.tipguide').show();
    }).blur(function () {
        $('.tipguide').hide();
    })


    /* ------------------------------------------------------------------ */
    /* login
     /* ------------------------------------------------------------------ */
    $("#user-login-form").submit(function () {
        var ok = true;

        var username = $("#lg-username").val();
        if (username == '') {
            $(".tip-username").show();
            ok = false;
        }
        $("#lg-username").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-username").hide();
            }
        });

        var password = $("#lg-password").val();
        if (password == '') {
            $(".tip-password").show();
            ok = false;
        }
        $("#lg-password").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-password").hide();
            }
        });

        var captcha = $("#lg-captcha").val();
        if (captcha == '') {
            $(".tip-captcha").show();
            ok = false;
        }
        $("#lg-captcha").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-captcha").hide();
            }
        });

        if (ok == false) {
            return false;
        }
    })



    /* ------------------------------------------------------------------ */
    /* register
     /* ------------------------------------------------------------------ */
    // show tooltip
    $("#user-register-form input").focus(function () {
        $('.tipguide').hide();
        $(this).siblings('.tipguide').show();
    })
    $("#user-register-form input").blur(function () {
        $(this).siblings('.tipguide').hide();
    })
    // submit form
    $("#user-register-form").submit(function () {
        var ok = true;

        var fullname = $("#rg-fullname").val();
        if (fullname == '') {
            $(".tip-fullname").show();
            ok = false;
        }
        $("#rg-fullname").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-fullname").hide();
            }
        });


        var email = $("#rg-email").val();
        if (email == '') {
            $(".tip-email").show();
            ok = false;
        }
        $("#rg-email").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-email").hide();
            }
        });

        var re_email = $("#rg-re-email").val();
        if (re_email == '') {
            $(".tip-re-email").show();
            ok = false;
        }
        $("#rg-re-email").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-re-email").hide();
            }
        });


        var password = $("#rg-password").val();
        if (password == '') {
            $(".tip-password").show();
            ok = false;
        }
        $("#rg-password").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-password").hide();
            }
        });

        var captcha = $("#rg-captcha").val();
        if (captcha == '') {
            $(".tip-captcha").show();
            ok = false;
        }
        $("#rg-captcha").on('keyup', function () {
            if (this.value.length > 1) {
                $(".tip-captcha").hide();
            }
        });

        if (ok == false) {
            return false;
        }
    })


    /* ------------------------------------------------------------------ */
    /* policy
     /* ------------------------------------------------------------------ */
    var tab = location.href.split("#")[1];
    if (tab == undefined) {
        tab = 'tab1';
    }
    showPolicy(tab);
    $("#policy .list a").click(function () {
        var tab = this.href.split("#")[1];
        showPolicy(tab);
    })



    /* ------------------------------------------------------------------ */
    /* add ingridient
     /* ------------------------------------------------------------------ */
    $("#add-ingridient").click(function () {
        var igd = $('.ingredient')[0].outerHTML;
        $("#ingredients").append(igd);
        $('.ingredient').last().find('input').val('');
        return false;
    })

    $('#post-form .image-url').blur(function () {
        var src = $(this).val();
        if (src != '' && src.indexOf('http') != -1) {
            $('#img-preview').attr('src', src);
        }
    })

    $(document).on("click", '.ingredient .button', function () {
        prObj = $(this).parent();
        if ($(".ingredient").length > 1) {
            prObj.remove();
        } else {
            prObj.find('input').val('');
        }
        return false;
    });

    if ($('.radio').length > 0) {
        $('.radio').prettyCheckable();
        $('input[name=cooking_time]').siblings('a').removeClass("checked");
        $('input[name=cooking_time]:checked').siblings('a').addClass("checked");
    }

    $('.category').each(function () {
        $(this).prettyCheckable();
    })

    if ($(".editor").length > 0) {
        var editor = CKEDITOR.replace("editor");
        CKFinder.setupCKEditor(editor, BASE_URL + 'editor/ckfinder/');

    }
    
    // paste from clipboard
    $(".ingredient-name" ).first().on('paste',function(e) {
        e.preventDefault();
        var text = (e.originalEvent || e).clipboardData.getData('text/plain') || prompt('Paste something..');
        var lines = text.split('\n');
        
        // add more line of diff
        if(lines.length > $( "input[name='ingridient[]']").length){
            var diff = lines.length - $( "input[name='ingridient[]']").length;
            for(var n = 1 ; n < diff ; n++){
                $("#add-ingridient").click();
            }
        }
        
        for(var i = 0; i < lines.length;i++){
            var ingri_name = getNameIngridient(lines[i]);
            var unit_value = getUnitIngridient(lines[i]);
            $( "input[name='ingridient[]']:eq( " + i + ")" ).val(ingri_name);
            $( "input[name='unit[]']:eq( " + i + ")" ).val(unit_value);
        }
        //window.document.execCommand('insertText', false, text);
    });



    /* ------------------------------------------------------------------ */
    /* locate
     /* ------------------------------------------------------------------ */
    if ($('#somecomponent').length > 0) {
        $('#somecomponent').locationpicker();
    }
    $('#post-form .image-url').blur(function () {
        var src = $(this).val();
        if (src != '' && src.indexOf('http') != -1) {
            $('#img-preview').attr('src', src);
        }
    })

    if ($('#map-content').length > 0) {
        var map_latitude = $('#map-latitude').val();
        var map_longitude = $('#map-longitude').val();
        
        if(map_latitude == '' && map_longitude == ''){
            navigator.geolocation.watchPosition(function(position) {
                $('#map-content').locationpicker({
                    location: {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    },
                    radius: 0,
                    inputBinding: {
                        latitudeInput: $('#map-latitude'),
                        longitudeInput: $('#map-longitude'),
                        radiusInput: $('#map-radius'),
                        locationNameInput: $('#map-address')
                    },
                    enableAutocomplete: true
                });
          });
        }else{
            $('#map-content').locationpicker({
                location: {
                    latitude: map_latitude,
                    longitude: map_longitude
                },
                radius: 0,
                inputBinding: {
                    latitudeInput: $('#map-latitude'),
                    longitudeInput: $('#map-longitude'),
                    radiusInput: $('#map-radius'),
                    locationNameInput: $('#map-address')
                },
                enableAutocomplete: true
            });
        }
    }


    /* ------------------------------------------------------------------ */
    /* Multi upload
     /* ------------------------------------------------------------------ */
    if ($().fileupload) {
        var ul = $('#thumbnails');
        $('#drop a').click(function () {
            // Simulate a click on the file input button
            // to show the file browser dialog
            $(this).parent().find('input').click();
        });
        var type = ul.data('type');
        // Initialize the jQuery File Upload plugin
        $('#frm').fileupload({
            // url upload handler
            url: "upload.php?type_upload=multiimages&type=" + type,
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
                        '<img src="uploads/bg-upload.png" height="120" width="120" />' +
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
                var obj = $.parseJSON(data.jqXHR.responseText);
                var imgObj = data.context.find('img');
                imgObj.attr('src', BASE_URL + obj.thumb);
                data.context.find('.wrap-process').parent().attr('id', 'image-' + obj.id);
                data.context.find('.wrap-process').remove();
                $('<div class="rwmb-image-bar"><a onclick="deleteImages(' + obj.id + ',\'' + type + '\')" class="rwmb-delete-file" href="javascript:void(0)">×</a></div>').insertAfter(imgObj);

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
    /* AjaxUpload
     /* ------------------------------------------------------------------ */
    $('.input-image').each(function () {
        var obj = $(this);
        var id = obj.attr('id');
        var button = obj.find('a.btnUpload');
        var label = obj.find('span.lblUpload');
        var type = obj.data('type');
        new AjaxUpload(id, {
            action: 'upload.php?type_upload=single&type=' + type,
            data: {},
            onSubmit: function (file, ext) {
                // Allow only images. You should add security check on the server-side.
                if (ext && /^(jpg|png|jpeg|gif|JPG|PNG|JPEG|GIF)$/.test(ext)) {
                    /* Setting data */
                    this.setData({
                        'key': 'This string will be send with the file',
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
                button.text('Đổi ảnh');
                var data = $.parseJSON(response);
                obj.siblings('.post-input').find('input').val(data.src);
                obj.find('img').attr('src', BASE_URL + data.thumb);

            }
        });
    });  // input-images


    /* ------------------------------------------------------------------ */
    /* post recipe, locate
     /* ------------------------------------------------------------------ */
    $('input.number').keyup(function (event) {
        // skip for arrow keys
        if (event.which >= 37 && event.which <= 40) {
            event.preventDefault();
        }

        $(this).val(function (index, value) {
            return value
                    .replace(/\D/g, "")
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    });

    $(".must-set").each(function () {
        $(this).keyup(function () {
            var name = $(this).attr('name');
            $(".msg-" + name).hide();
        })
    })
    $("#frm").submit(function () {
        // update element for serialize
        for (instance in CKEDITOR.instances)
            CKEDITOR.instances[instance].updateElement();

        // warning error if exists
        var ok = true;
        $(".must-set").each(function () {
            var name = $(this).attr('name');
            if ($(this).val() == '') {
                ok = false;
                $(".msg-" + name).show();
            }
        })

        // scroll to error and focus
        if (ok == false) {
            $(".must-set").each(function () {
                var name = $(this).attr('name');
                if ($(this).val() == '') {
                    $('html, body').animate({
                        scrollTop: $(".msg-" + name).offset().top
                    }, 1000);
                    $(this).focus();
                    return false;
                }
            })
            return false;
        }

        // post data
        var data_post = $('#frm').serialize();
        var action = $('#frm').data('action');
        $.ajax({
            url: BASE_URL + 'ajax.php?cmd=' + action,
            type: "POST",
            data: data_post,
            beforeSend: function (xhr) {
                $('#cnan-modal .cnan-content').html('Đang xử lý...');
                $('#cnan-modal').modal('show');
            },
            timeout: 10000,
            success: function (response, status) {
                response_data = $.parseJSON(response);
                switch (response_data.status) {
                    case "success":
                        $('#cnan-modal .modal-body').html(response_data.msg);
                        $('#post-id').val(response_data.id);
                        $("#button-post a").html('Cập nhật');
                        history.pushState(null, response_data.title, '?id='+response_data.id);
                        break;
                }
            }
        });
        return false;
    })
})


// get GEO position
function showPosition(position) {
    $('#map-content').locationpicker({
        location: {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude
        },
        radius: 0,
        inputBinding: {
            latitudeInput: $('#map-latitude'),
            longitudeInput: $('#map-longitude'),
            radiusInput: $('#map-radius'),
            locationNameInput: $('#map-address')
        },
        enableAutocomplete: true
    });	
}

function showPolicy(tab) {
    $("#policy .list li").removeClass('active');
    $('a[href="#' + tab + '"]').parent().addClass('active');
    $("#policy .content").hide();
    $("#policy #content-" + tab).show();
}

function showUserTab(tab) {
    $("#menu-list li a").removeClass('active');
    $('a[href="#' + tab + '"]').addClass('active');
    $(".wrap-content").hide();
    $("#wr-" + tab).show();
}


function SerializeForm() {
    // Make sure we have the reformatted version of the initial content in the textarea
    CKEDITOR.instances.myEditor.updateElement();

    // Save the initial serialization
    form_data.edit_initial = $('#frm').serialize();
}



function deleteImages(id, type) {
    if (confirm('Bạn có chắc muốn xóa ảnh này?')) {
        $("#image-" + id).hide();
        $.ajax({
            url: BASE_URL + 'ajax.php?cmd=delete_image&id=' + id + '&type=' + type,
            type: "POST",
            data: {},
            beforeSend: function (xhr) {
            },
            timeout: 10000,
            success: function (response, status) {
                switch (response) {
                    case "SS":
                        $("#image-" + id).remove();
                        break;
                        
                    default:
                        $("#image-" + id).show();
                        break;
                }
            }
        });
    }
}

// get name of ingridient from string
function getNameIngridient(str){
    var listCharRemove = [" ", "-", "+"];
    for(var i = 0; i < listCharRemove.length ; i++){
        str = trimChar(str,listCharRemove[i]);
    }
    var res = str.split(":");
    return res[0].trim();
}


// get unit of ingridient from string
function getUnitIngridient(str){
    var listCharRemove = [" ", "-", "+",",",".",";"];
    for(var i = 0; i < listCharRemove.length ; i++){
        str = trimChar(str,listCharRemove[i]);
    }
    
    var res = str.split(":");
    if(res[1] != undefined){
        return res[1].trim();
    }
    return '';
}

var escapeRegExp = function(strToEscape) {
    // Escape special characters for use in a regular expression
    return strToEscape.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
};

var trimChar = function(origString, charToTrim) {
    charToTrim = escapeRegExp(charToTrim);
    var regEx = new RegExp("^[" + charToTrim + "]+|[" + charToTrim + "]+$", "g");
    return origString.replace(regEx, "");
};


var swapIngridient = function(){
    var num = $( "input[name='ingridient[]']").length;
    for(var i = 0 ; i < num ; i++){
        var tmp_ingridient = $( "input[name='ingridient[]']:eq( " + i + ")" ).val();
        var tmp_unit = $( "input[name='unit[]']:eq( " + i + ")" ).val();
        $( "input[name='unit[]']:eq( " + i + ")" ).val(tmp_ingridient);
        $( "input[name='ingridient[]']:eq( " + i + ")" ).val(tmp_unit);
    }
}
