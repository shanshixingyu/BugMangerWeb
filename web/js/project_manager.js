$('.seeModule').click(function () {
    $('#showModuleModal').modal('toggle');
    var row = $(this).parent().parent();

    $('#showModalHeader').text($('.projectName', row).text() + '——项目模块详情');
    $.get('index.php?r=project/see-module', {projectId: row.data('key')}, function (data) {
        var result = $.parseJSON(data);
        $('#showModalHeader').text();
        $('#showModuleTable').find('tr:gt(0)').remove();
        if (result.length <= 0) {
            $('#showModuleTable').append('<tr><td colspan="5" style="padding-left: 20px; ">sorry，暂时没找到该项目模块信息!</td></tr>');
        } else {
            $.each(result, function (idx, module) {
                $('#showModuleTable').append('<tr><td>' + module.name + '</td><td>' + module.fuzeren + '</td><td>' + module.introduce + '</td><td>' + module.creator + '</td><td>' + module.create_time + '</td></tr>');
            });
        }
    });
});
$('.deleteProject').click(function () {
    if (confirm("删除该项目信息后也会删除相项目模块信息,确定删除该项项目信息?")) {
        var row = $(this).parent().parent();
        $.get('index.php?r=project/delete-project', {projectId: row.data('key')}, function (result) {
            if (result !== null && result.toUpperCase() == "SUCCESS") {
                alert('数据删除成功！');
                window.location.reload();
            } else {
                alert('数据删除失败！');
            }
        });
    }
});


$('#closeModalBtn').click(function () {
    //alert('点击关闭按钮');
    $('#showModuleModal').modal('hide');
});
