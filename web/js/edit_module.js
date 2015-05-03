$('#moduleform-projectid').change(function(data){
    var projectId=$(this).find('option:selected').val();
    $.get('index.php?r=project/get-group-member',{projectId:projectId},function(data){
        var members=jQuery.parseJSON(data);
        $('#moduleform-fuzeren').empty();
        $.each(members,function(idx,member){
            $('#moduleform-fuzeren').append('<option value="'+member.id+'">'+member.name+'</option>');
        });
    });
});
$('#moduleform-id').change(function(data){
    var moduleId=$(this).find('option:selected').val();
    $.get('index.php?r=project/get-module',{moduleId:moduleId},function(data){
        var module=jQuery.parseJSON(data);
        $('#moduleform-name').val(module.name);
        try{
            $('#moduleform-fuzeren').find('option').attr("selected", false);
            $.each($.parseJSON(module.fuzeren),function(idx,item){
                $('#moduleform-fuzeren').find('option[value='+item+']').attr("selected", 'selected');
            });
        }catch (exception){
        }
        $('#moduleform-introduce').val(module.introduce);
        //alert(module.fuzeren);
    });
});