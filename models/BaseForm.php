<?php
/**
 *
 * Created by GuLang on 2015-04-23.
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Exception;

class BaseForm extends Model
{
    /**
     * 从Yii::$app->request->post("formName") 中加载数据
     * @return bool
     * @throws Exception
     */
    public function loadData()
    {
        $formName = $this->formName();
        $data = Yii::$app->request->post($formName);
        if (isset($data) && isset($data[$formName])) {
            $formData = $data[$formName];
            foreach ($formData as $key => $value) {
                try {
                    $this->$key = $value;
                } catch (Exception $e) {
                }
            }
            return true;
        }
        return false;
    }

}