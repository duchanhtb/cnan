$(function () {
    //Update Message popup
    $.fn.center = function () {
        this.animate({"top": ($(window).height() - this.height() - 200) / 2 + $(window).scrollTop() + "px"}, 100);
        this.css("left", ($(window).width() - 150) / 2 + "px");
        return this;
    }

    _open = false;
    $(".add-module button").click(function () {
        if (!_open) {
            $("#list-module").animate({left: '200px'}, 200);
            _open = true;
        } else {
            $("#list-module").animate({left: '-5px'}, 200);
            _open = false;
        }
    })

    $(".cms-pos > li h3").click(function () {
        $('.cms-pos > li').removeClass('active');
        $(this).parent().addClass('active');
        $('.wrap-module').slideUp(300).hide(100);
        $(this).parent().find('.wrap-module:hidden').slideDown(300).show(100);
        $("#list-module").animate({left: '-5px'}, 200);
        _open = false;
    })

    $('.module').sortable({
        axis: 'y',
        connectWith: '.module',
        cursor: 'move',
        opacity: 0.4,
        stop: function (event, ui) {
        }
    }).disableSelection();



    $("#list-module li").click(function () {
        var obj = $(this);
        var _module = obj.data('module');
        var _html = '<li class="module-name" data-module="' + _module + '" ><h4>' + _module + '<a href="#"onclick="deleteModule(this)">' + lang.delete + '?</a></h4></li>';
        $(".cms-pos .active ul").append(_html);
    })

    $(".module-name a").click(function () {
        $(this).parent().parent().remove();
    })

    $("#menu-toggle").toggle(function () {
        $(this).find('img').attr('src', 'images/media_icon_next.png');
        $('body').animate({'marginLeft': '0px'}, 200);
        $(".cms-module-left").hide(100);
        $("#list-module").hide(100);
        $.cookie('menu_toggle', "is_show", {expires: 7, path: '/'});
    }, function () {
        $(this).find('img').attr('src', 'images/media_icon_prev.png');
        $('body').animate({'marginLeft': '200px'}, 200);
        $(".cms-module-left").show(100);
        $("#list-module").show(100);
        $.cookie('menu_toggle', "is_hide", {expires: 7, path: '/'});
    })

})


function deleteModule(obj) {
    $(obj).parent().parent().remove();
}

function deletePositionModule(_page, _pos, _module, _eq) {
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            cmd: 'delete_module',
            page: _page,
            pos: _pos,
            module: _module,
            eq: _eq
        },
        beforeSend: function () {
            $("#update-page-loading").center().fadeTo(500, 0.5).show();
        },
        success: function (response) {
            $("#update-page-loading").fadeOut(800).hide();
            history.go(0);
        }
    }); //  end ajax
}

function savePosition(curent_page) {
    /*
     var posObj = {
     "post_header":["module_1","module_2","module_3","module_4","module_5"],
     "post_content":["module_5","module_6","module_7","module_8","module_9"],
     "post_footer":["module_9","module_10"]
     }
     */
    $("#update-page-loading").center().fadeTo(500, 1).show();

    $("#list-module").animate({left: '-5px'}, 200);
    var posObj = {};
    $(".module").each(function () {
        var obj = $(this);
        var pos = obj.data('pos');
        var i = 0;
        var arrModule = [];
        $(this).find('.module-name').each(function () {
            var objModule = $(this);
            var module = objModule.data('module');
            arrModule.push(module);
        })
        posObj[pos] = arrModule;

    })

    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: {
            cmd: 'update_page_info',
            page: curent_page,
            position: posObj
        },
        beforeSend: function () {
            $('#img-loading').show();
        },
        success: function (response) {
            $("#update-page-loading").fadeOut(800).hide();
            $('#img-loading').hide();
            document.getElementById('cms-iframe').contentWindow.location.reload(true);
        }
    }); //  end ajax
}

function seachModule() {
    var kw = $("#seach_module").val();
    kw = kw.trim().toLowerCase();
    if (kw) {
        $("#list-module li").each(function () {
            var module_class = $(this).attr('class').toLowerCase();
            var n = module_class.indexOf(kw);
            if (n > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        })
    } else {
        $("#list-module li").show();
    }
}