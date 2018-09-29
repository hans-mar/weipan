$(function () {
    var webSetting = $("#webSetting").html();

    webSetting = eval("(" + webSetting + ")");

    // 加号按钮切换成叉按钮
    var createToDelete = function($obj) {
        $obj.removeClass('add-icon').addClass('delete-icon').html('&#xe6a6;');
        return $obj;
    };
    // 叉按钮切换成加号按钮
    var deleteToCreate = function($obj) {
        $obj.removeClass('delete-icon').addClass('add-icon').html('&#xe600;');
        return $obj;
    }

    // input[type=file]事件绑定
    $("#settingContent").on('change', 'input[type="file"]', function() {
        if (typeof $(this).attr("name") == "undefined") {
            $(this).attr('name', 'Upload[' + $(this).data('filename') + ']');
        }
    });

    // 一级菜单添加事件绑定
    $("#addTopParentLink").click(function () {
        if ($(this).hasClass('noSubmit')) {
            $("#addTopParentLi").prev().remove();
            deleteToCreate($(this)).removeClass('noSubmit');
        } else {
            var newTopParentInput = '<li>';
            newTopParentInput += '<input placeholder="请输入顶级菜单的名称" id="newTopParentName" class="menu-name-input">';
            newTopParentInput += '<input type="button" value="创建" id="newTopParentSubmit" class="menu-name-submit">';
            newTopParentInput += '</li>';
            $("#addTopParentLi").before(newTopParentInput);
            createToDelete($(this)).addClass('noSubmit');
        }
    });
    // 一级菜单创建
    $("#topParentUl").on('click', '#newTopParentSubmit', function () {
        var newTopParentName = $("#newTopParentName").val();
        if (!newTopParentName) {
            $.alert('请输入顶级菜单的名称！');
            return false;
        }
        $.post($("#addTopParentLink").attr('href'), {
            'Setting[name]': newTopParentName,
            'Setting[pid]': 0,
        }, function(msg) {
            deleteToCreate($("#addTopParentLink")).removeClass('noSubmit');
            $("#addTopParentLi").prev().remove();
            $("#addTopParentLi").before('<li><a href="javascript:;" class="topMenuList" data-id="' + msg.info + '">' + newTopParentName + '</a></li>');
            if (!$("#topParentId").val()) {
                $("#addTopParentLi").prev().find('a').trigger('click');
            }
        }, 'json');
    });
    // 一级菜单切换点击事件
    $("#topParentUl").on('click', '.topMenuList', function () {
        var $this = $(this);
        if ($this.parent().hasClass('current')) {
            return false;
        }
        $.get(window.location.href, {
            nowTopId: $this.data('id')
        }, function(msg) {
            $("#topParentUl li.current").removeClass('current');
            $this.parent().addClass('current');
            if (msg.state) {
                $("#settingContent").html(msg.info);
                keepShowMode();
            } else {
                $.alert(msg.info);
            }
        }, 'json');
    });

    // 二级菜单添加事件绑定
    $("#settingContent").on('click', '#addParentSpan', function () {
        if ($(this).hasClass('noSubmit')) {
            $("#addParentSpan").prev().remove();
            deleteToCreate($(this)).removeClass('noSubmit');
        } else {
            var newParentInput = '<span class="new-parent-span">';
            newParentInput += '<input placeholder="输入二级菜单的名称" id="newParentName" class="child-menu-input">';
            newParentInput += '<input type="button" value="创建" id="newParentSubmit" class="child-menu-submit">';
            newParentInput += '</span>';
            $("#addParentSpan").before(newParentInput);
            createToDelete($(this)).addClass('noSubmit');
        }
    });
    // 二级菜单创建
    $("#settingContent").on('click', '#newParentSubmit', function () {
        var newParentName = $("#newParentName").val();
        if (!newParentName) {
            $.alert('请输入二级菜单的名称！');
            return false;
        }
        var nowTopId = $("#topParentId").val();
        $.post($("#addParentSpan").attr('href') + '?nowTopId=' + nowTopId, {
            'Setting[name]': newParentName,
            'Setting[pid]': nowTopId,
            refresh: true
        }, function(msg) {
            if (msg.state) {
                $("#settingContent").html(msg.info);
                keepShowMode();
            } else {
                $.alert(msg.info);
            }
        }, 'json');
    });
    // 配置创建按钮
    $("#settingContent").on('click', '.addChildLink', function () {
        var pid = $(this).attr('pid');
        if ($(this).hasClass('noSubmit')) {
            $("#addChildSpan" + pid).prev().remove();
            deleteToCreate($(this)).removeClass('noSubmit');
        } else {
            var typeOption = ['text', 'radio', 'checkbox', 'select', 'textarea', 'file', 'custom'];
            var newChildInput = '<span>';
            newChildInput += '<input placeholder="请输入配置名" id="newChildOption' + pid + '" class="child-menu-input">';
            newChildInput += '<input placeholder="请输入配置描述" id="newChildName' + pid + '" class="child-menu-input">';
            newChildInput += '<select id="newChildType' + pid + '" class="add-child-select addChildSelect" pid="' + pid + '">';
            newChildInput += '<option disabled="disabled" value="">参数类型</option>';
            for (var key in typeOption) {
                newChildInput += '<option value="' + typeOption[key] + '">' + typeOption[key] + '</option>';
            }
            newChildInput += '</select>';
            newChildInput += '<input placeholder="请输入配置说明" id="newChildComment' + pid + '" class="child-menu-input">';
            newChildInput += '<input type="button" value="创建" class="newChildSubmit child-menu-submit" pid="' + pid + '">';
            newChildInput += '</span>';
            $("#addChildSpan" + pid).before(newChildInput);
            createToDelete($(this)).addClass('noSubmit');
        }
    });
    // 配置类型下拉事件
    $("#settingContent").on('change', '.addChildSelect', function () {
        var value = $(this).val();
        var pid = $(this).attr('pid');
        switch (value) {
            case 'radio':
            case 'checkbox':
            case 'select':
                $("#typeSelect" + pid).remove();
                var selectInput = '<input id="typeSelect' + pid + '" placeholder="key1=value1,key2=value2..." class="child-menu-input" style="width:180px">';
                $('#newChildType' + pid).after(selectInput);
                break;
            case 'custom':
                $("#typeSelect" + pid).remove();
                var inputCustom = '<input id="typeSelect' + pid + '" placeholder="class:method or function" class="child-menu-input" style="width:160px">';
                $('#newChildType' + pid).after(inputCustom);
                break;
            default:
                $("#typeSelect" + pid).remove();
                break;
        }
    });
    // 配置保存事件
    $("#settingContent").on('click', ".newChildSubmit", function () {
        var pid = $(this).attr('pid');
        $.post($(".addChildLink[pid='" + pid + "']").attr('href') + '?nowTopId=' + $("#topParentId").val(), {
            'Setting[name]': $("#newChildName" + pid).val(),
            'Setting[var]': $("#newChildOption" + pid).val(),
            'Setting[pid]': pid,
            'Setting[type]': $("#newChildType" + pid).val(),
            'Setting[comment]': $("#newChildComment" + pid).val(),
            'Setting[alter]': $("#typeSelect" + pid).val(),
            addSetting: true,
            refresh: true
        }, function(msg) {
            if (msg.state) {
                $("#settingContent").html(msg.info);
                keepShowMode();
            } else {
                $.alert(msg.info);
            }
        }, 'json');
    });
    // 配置保存按钮
    $("#settingContent").on('click', "#settingSubmit", function () {
        $("#settingForm").ajaxSubmit($.config('ajaxSubmit', {
            success: function(msg) {
                if (msg.state) {
                    $.alert('保存成功！', function () {
                        var file = msg.info;
                        for (var key in file) {
                            var $preview = $(".fancybox[data-id='" + key + "']");
                            if ($preview.length === 1) {
                                $preview.attr('href', file[key]);
                            } else {
                                $preview = $("<a>").addClass('fancybox').html('预览').attr({
                                    'href': file[key],
                                    'data-id': key
                                });
                                $("input[data-filename='" + key + "']").parent().append($preview);
                            }
                        }
                    });
                } else {
                    $.alert(msg.info);
                }
            }
        }));
    });
    // 配置删除按钮
    $("#settingContent").on('click', '.deleteItemLink', function () {
        var $img = $(this),
            extraInfo = '';
        if ($img.data('parent')) {
            extraInfo = '及其下的所有配置项';
        }
        $.confirm('确认删除 “' + $img.data('name') + '”' + extraInfo + '？', function() {
            $.post($img.attr('href') + '?nowTopId=' + $("#topParentId").val(), {
                id: $img.attr('data-id')
            }, function(msg) {
                if (msg.state) {
                    $("#settingContent").html(msg.info);
                    keepShowMode();
                } else {
                    $.alert(msg.info);
                }
            }, 'json');
        })
    });
    // 按钮状态切换的方法
    var changeShowText = function($obj) {
        $obj.addClass('showText');
        $obj.removeClass('showEdit');
    };
    var changeShowEdit = function($obj) {
        $obj.removeClass('showText');
        $obj.addClass('showEdit');
    };
    // 切换显示模式的方法
    var changeShowTextMode = function($obj) {
        $(".showMode").show();
        $obj.html('切换到显示模式');
        changeShowEdit($obj);
        $obj.attr('mode', 2);
    };
    var changeShowEditMode = function($obj) {
        $(".showMode").hide();
        $obj.html('切换到编辑模式');
        changeShowText($obj);
        $obj.attr('mode', 1);
    };
    var changeShowMode = function () {
        var $this = $("#changeEditBtn");
        var mode = $this.attr('mode');

        if (mode == '1') { // 模式1，表示当前为显示模式
            changeShowTextMode($this);
        } else { // 模式2,表示当前为编辑模式
            changeShowEditMode($this);
        }
    };
    var keepShowMode = function () {
        var $this = $("#changeEditBtn");
        var mode = $this.attr('mode');

        if (mode == '1') {
            changeShowEditMode($this);
        } else {
            changeShowTextMode($this);
        }
        $.initICheck();
    };
    // 切换编辑/显示的按钮
    $("#changeEditBtn")
        .hover(function () {
            if ($(this).attr('mode') == '1') {
                changeShowEdit($(this));
            } else {
                changeShowText($(this));
            }
        }, function () {
            if ($(this).attr('mode') == '1') {
                changeShowText($(this));
            } else {
                changeShowEdit($(this));
            }
        })
        .click(function () {
            changeShowMode();
        });
});