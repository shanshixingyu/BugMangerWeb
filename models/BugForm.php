<?php
/**
 *
 * Created by GuLang on 2015-05-04.
 */

namespace app\models;

use app\tools\BugOpt;
use app\tools\ImageUtils;
use Yii;
use yii\helpers\Json;
use yii\base\Exception;
use app\tools\MyConstant;

class BugForm extends BaseForm
{
    public $bugId;
    public $name;
    public $projectId;
    public $moduleId;
    public $assignId;
    public $priority = 0;
    public $seriousId;
    public $images;
    public $attachment;
    public $introduce;
    public $reappear;

    public $isModify = false;

    public function rules()
    {
        return [
            ['name', 'required', 'message' => 'Bug名称必填'],
            ['name', 'match', 'pattern' => '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', 'message' => '只能输入中文、英文、数字字符、下划线'],
            ['projectId', 'required', 'message' => '项目必选'],
            ['projectId', 'validateProjectExist'],
            ['moduleId', 'validateModuleExist'],/* 当项目模块选取的时候验证其存在性 */
            ['assignId', 'required', 'message' => '指派目标必填'],
            ['assignId', 'validateAssignExist'],
            ['priority', 'required', 'message' => '优先级必选'],
            ['priority', 'in', 'range' => array_keys(Json::decode(BUG_PRIORITY)), 'message' => '优先级信息过期'],
            ['seriousId', 'required', 'message' => '影响程度必选'],
            ['seriousId', 'in', 'range' => array_keys(Json::decode(BUG_SERIOUS)), 'message' => '影响程度信息过期'],
            ['images', 'file', 'extensions' => 'gif,jpg,jpeg,png', 'maxFiles' => 4, 'maxSize' => 8 * 1024 * 1024 * 8, 'mimeTypes' => 'image/jpeg,image/gif,image/png', 'skipOnEmpty' => true],
            ['attachment', 'file', 'skipOnEmpty' => true],

        ];
    }

    /**
     * 验证项目是否存在
     * @param $attribute
     * @param $param
     */
    public function validateProjectExist($attribute, $param)
    {
        $project = Project::findOne(['id' => $this->projectId]);
        if ($project === null) {
            $this->addError($attribute, '项目信息过期，请刷新重试');
        }
    }

    /**
     * 验证项目中模块是否存在
     * @param $attribute
     * @param $param
     */
    public function validateModuleExist($attribute, $param)
    {
        $module = Module::findOne(['id' => $this->moduleId, 'project_id' => $this->projectId]);
        if ($module === null) {
            $this->addError($attribute, '模块信息过期，请刷新重试');
        }
    }

    /**
     * 验证指派的人是否存在,【并且是否是负责模块的模块的人（后期完善）】
     * @param $attribute
     * @param $param
     */
    public function validateAssignExist($attribute, $param)
    {
        $module = User::findOne(['id' => $this->assignId]);
        if ($module === null) {
            $this->addError($attribute, '模块信息过期，请刷新重试');
        }
    }

    public function attributeLabels()
    {
        return [
            'name' => 'Bug名称',
            'projectId' => '项目名称',
            'moduleId' => '模块名称',
            'assignId' => '指派给',
            'priority' => '优先级',
            'seriousId' => '影响程度',
            'images' => 'Bug截图',
            'attachment' => '附件',
            'introduce' => 'Bug注释',
            'reappear' => '重现步骤',
        ];
    }

    /**
     * 将Bug数据插入数据库
     * @return bool
     */
    public function addBugToDb()
    {
        $bug = new Bug();
        $bug->name = $this->name;
        $bug->project_id = $this->projectId;
        $bug->module_id = $this->moduleId;
        $bug->priority = $this->priority;
        $bug->serious_id = $this->seriousId;
        $bug->assign_id = $this->assignId;
        $bugStatus = Json::decode(BUG_STATUS);
        $bug->status = array_search(BUG_STATUS_UNSOLVED, $bugStatus);
        $bug->creator_id = Yii::$app->user->identity->getId();
        date_default_timezone_set('Asia/Shanghai');
        $bug->create_time = date('Y-m-d H:i:s', time());
        $bug->img_path = $this->images;
        $bug->file_path = $this->attachment;
        //处理Bug简介
        $bug->introduce = BugOpt::bugIntroduce($this->introduce, $bug->create_time);
        $bug->reappear = $this->reappear;
        $bug->setIsNewRecord(true);
        $result = false;
        try {
            $result = $bug->save();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * @param Bug $bug
     * @return bool
     * @throws Exception
     */
    public function modifyBugOfDb($bug)
    {
        if (!isset($bug))
            throw new Exception("修改bug信息方法中传入的bug信息为空!");
        if (!isset($this->bugId))
            throw new Exception('修改bug信息方法中bugId没有传入');
        $bug->id = $this->bugId;
        $bug->name = $this->name;
        $bug->project_id = $this->projectId;
        $bug->module_id = $this->moduleId;
        $bug->assign_id = $this->assignId;
        $bug->priority = $this->priority;
        $bug->serious_id = $this->seriousId;
        //修改截图的时候注意要把原来的图片删除
        ImageUtils::deleteImage($bug->img_path);
        //把原来的福建删除了
        if (isset($attachment) && trim($attachment) != '') {
            unlink(MyConstant::ATTACHMENT_PATH . $attachment);
        }
        $bug->img_path = $this->images;
        $bug->file_path = $this->attachment;
        date_default_timezone_set('Asia/Shanghai');
        $tempTime = date('Y-m-d H:i:s', time());
        $bug->introduce = BugOpt::addBugIntroduce($bug->introduce, $this->introduce, '修改', $tempTime);
        $bug->reappear = $this->reappear;
        $result = false;
        try {
            $result = $bug->update();
        } catch (Exception $e) {
            $result = false;
        }
        return $result;

    }


}