<?php
/**
 *
 * Created by GuLang on 2015-05-20.
 */

namespace app\modules\api\controllers;


use app\modules\api\models\HttpResult;
use app\tools\MyConstant;
use app\models\Group;
use yii\data\Pagination;

class GroupController extends BaseController
{

    public function actionIndex()
    {
        $query = Group::find()->select(['id', 'name', 'introduce', 'creator']);
        $countQuery = clone $query;
        $pagination = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);
        $groups = $query->limit($pagination->limit)->offset($pagination->offset)->all();

        $result = new HttpResult();
        $result->code = MyConstant::VISIT_CODE_SUCCESS;
        $result->message = "获得团队信息成功";
        $result->result = [
            'pageCount' => $pagination->getPageCount(),
            'currentPage' => $pagination->getPage() + 1,
            'data' => $groups
        ];
        return $result->parseJson();
    }
//    public function actionIndex()
//    {
//        $query = Group::find();
//        $countQuery = clone $query;
//        $pagination = new Pagination([
//            'totalCount' => $countQuery->count(),
//            'pageSize' => 10,
//        ]);
//        $groups = $query->limit($pagination->limit)->offset($pagination->offset)->all();
//        foreach ($groups as $group) {
//            $group->creator = User::find()->select(['id', 'name'])->where(['id' => $group->creator])->one();
//            $group->member = User::find()->select(['id', 'name'])->where(['id' => Json::decode($group->member)])->all();
//        }
//
//        $result = new HttpResult();
//        $result->code = MyConstant::VISIT_CODE_SUCCESS;
//        $result->message = "获得团队信息成功";
//        $result->result = [
//            'pageCount' => $pagination->getPageCount(),
//            'currentPage' => $pagination->getPage() + 1,
//            'data' => $groups
//        ];
//        return $result->parseJson();
//    }
}