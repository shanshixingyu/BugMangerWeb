$('.seeModule').click(function () {
    $('#showModuleModal').modal('toggle');
    var row = $(this).parent().parent();

    $('#showModalHeader').text($('.productName', row).text() + '——产品模块详情');
    $.get('index.php?r=product/see-module', {productId: row.data('key')}, function (data) {
        var result = $.parseJSON(data);
        $('#showModalHeader').text();
        $('#showModuleTable').find('tr:gt(0)').remove();
        if (result.length <= 0) {
            $('#showModuleTable').append('<tr><td colspan="5" style="padding-left: 20px; ">sorry，暂时没找到该产品模块信息!</td></tr>');
        } else {
            $.each(result, function (idx, module) {
                $('#showModuleTable').append('<tr><td>' + module.name + '</td><td>' + module.fuzeren + '</td><td>' + module.introduce + '</td><td>' + module.creator + '</td><td>' + module.create_time + '</td></tr>');
            });
        }
    });
});
$('.deleteProduct').click(function () {
    if (confirm("删除该产品信息后也会删除相产品模块信息,确定删除该项产品信息?")) {
        var row = $(this).parent().parent();
        $.get('index.php?r=product/delete-product', {productId: row.data('key')}, function (result) {
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
