$(function () {
    var selector = 'form #authitem-roles label';
    var addRoleTag = function (roleList) {
        $.get($("#ajaxRoleInfoUrl").val(), {roleList: roleList}, function (msg) {
            removeRoleTag();
            for (var key in msg.info) {
                var permission = msg.info[key];
                var label = $("label input[value='" + permission + "']").parent();
                if (label.find('.Hui-iconfont').length === 0) {
                    label.append($("<span>").addClass('Hui-iconfont hint-role').html('&#xe6a7;'));
                }
            }
        });
    };

    var removeRoleTag = function () {
        $("form label .Hui-iconfont.hint-role").remove();
    };

    var check = function () {
        var roleList = [];
        $(selector + ' input:checked').each(function () {
            roleList.push($(this).val());
        });
        if (roleList.length === 0) {
            removeRoleTag();
        } else {
            addRoleTag(roleList);
        }
    }

    $(selector).click(function () {
        check();
    });
    check();
});