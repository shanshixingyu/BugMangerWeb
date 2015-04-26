<?php
/**
 *
 * Created by GuLang on 2015-04-26.
 */

namespace app\controllers\action;


use app\models\UserGroup;
use yii\base\Action;
use app\models\Product;
use yii\helpers\Json;

class GetGroupMemberAction extends Action
{
    public function run($productId)
    {
        //由产品id找到groupId，然后由groupId找到userId,然后从userId找到useName
        $chooseProduct = Product::find()->select(['group_id'])->where(['id' => $productId])->one();
        if ($chooseProduct == null)
            return '';
        $allMembers = UserGroup::find()->joinWith('user')->where(['group_id' => $chooseProduct->group_id])->all();
        $result = [];
        foreach ($allMembers as $member) {
            $temp = [];
            if (isset($member->user_id))
                $temp['userId'] = $member->user_id;
            else
                $temp['userId'] = '';
            if (isset($member->user->name))
                $temp['userName'] = $member->user->name;
            else
                $temp['userName'] = '';
            $result[] = $temp;
        }
        return Json::encode($result);
    }
}