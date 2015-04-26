<?php
/**
 *
 * Created by GuLang on 2015-04-23.
 */

namespace app\controllers\action;


use app\models\ProductForm;
use yii\base\Action;

class HelloAction extends Action
{
    public function run()
    {
        $productForm = new ProductForm();
        return $this->controller->renderAjax('testmodal', ['productForm' => $productForm]);
    }

}