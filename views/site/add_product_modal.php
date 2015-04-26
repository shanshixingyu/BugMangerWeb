<?php
/**
 * 添加产品模态框里面的布局内容
 * Created by GuLang on 2015-04-25.
 */
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->registerCssFile(CSS_PATH . 'test.css');
$addJS = <<<JS
jQuery('#addProductBtn').click(function(){
    var productName=$("#productform-name").val();
    var productGroupId=$("#productform-groupid").val();
    var productIntroduce=$("#productform-introduce").val();
    //jQuery.post('index.php?r=site/addProduct',$('#addProductForm').serialize(),function(data){
    jQuery.get('index.php?r=site/test',{name:productName,groupId:productGroupId,introduce:productIntroduce}, function(data){
        alert(data);
        //$('#productform-introduce').val(data);
    });
});
JS;
$this->registerJS($addJS);
?>
<div id="addProductInfo">
    <div class="addInfoForm">
        <?php $formLeft = ActiveForm::begin([
            'id' => 'addProductForm',
            'action' => '/BugManagerWeb/web/index.php?r=site/addProduct',
            'fieldConfig' => [
                'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div><div class="infoError">{error}</div></div>',
            ],
        ]); ?>
        <?php echo $formLeft->field($productForm, 'name')->textInput(); ?>
        <?php echo $formLeft->field($productForm, 'groupId')->dropDownList(['测试1', '测试2']); ?>
        <?php echo $formLeft->field($productForm, 'introduce', [
            'template' => '<div class="infoRow"><div class="infoLabel">{label}</div><div class="infoInput">{input}</div></div>',
        ])->textarea(['rows' => 7, 'cols' => 100, 'style' => 'width:320px;resize:none;']); ?>

        <!--        --><?php //echo Html::submitButton('添加产品', [
        //            'class' => 'btn btn-primary',
        //            'style' => 'margin-left:100px;margin-top:10px;margin-bottom:5px;',
        //        ]) ?>
        <!--        --><?php //echo Html::submitButton('添加产品', [
        //            'class' => 'btn btn-primary',
        //            'style' => 'margin-left:100px;margin-top:10px;margin-bottom:5px;',
        //        ]) ?>
        <?php echo Html::button('添加产品', [
            'id' => 'addProductBtn',
            'class' => 'btn btn-primary',
            'style' => 'margin-left:100px;margin-top:10px;margin-bottom:5px;',
        ]) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
