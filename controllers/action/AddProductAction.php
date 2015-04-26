<?php
/**
 * 添加产品界面Action
 * Created by GuLang on 2015-04-23.
 */

namespace app\controllers\action;


use app\models\GroupDetail;
use app\models\ProductForm;
use yii\base\Action;
use Yii;

class AddProductAction extends Action
{
    public function run()
    {
        $productForm = new ProductForm();

        if (isset($_POST['ProductForm']) && $productForm->loadData() && $productForm->validate()) {
            $result = $productForm->addProductToDb();
            if ($result) {
                /* 数据插入成功 */
                return $this->controller->refresh();
            } else if ($result) {
                /*返回的是错误信息*/
                $this->controller->getView()->params[OPT_RESULT] = $result['error'];
            }
        }

        $groupDetails = GroupDetail::find()->select(['id', 'name'])->all();
        return $this->controller->render('add_product', [
            'productForm' => $productForm,
            'groupDetails' => $groupDetails
        ]);
    }

}