<?php
/**
 *
 * Created by GuLang on 2015-05-03.
 */

namespace app\models;


class SearchBugForm extends BaseForm
{
    public $projectId;
    public $moduleId;
    public $seriousId;
    public $assignId;
    public $creatorId;
    public $statusId;
    public $submitStart;
    public $submitEnd;
    public $keyword;

    public function rules()
    {
        return [];
    }


    public function attributeLabels()
    {
        return [
            'projectId' => '项目',
            'moduleId' => '模块',
            'seriousId' => '严重程度',
            'assignId' => '指派给',
            'creatorId' => '提交者',
            'statusId' => '当前状态',
            'submitStart' => '提交开始',
            'submitEnd' => '提交结束',
            'keyword' => '关键字',
        ];
    }


}