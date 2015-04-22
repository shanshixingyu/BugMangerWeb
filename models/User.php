<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


/**
 * 用户表模型
 * Created by GuLang on 2015-04-16.
 */
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user';
    }

    public static function findIdentity($id)
    {
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getId()
    {
    }

    public function getAuthKey()
    {
    }

    public function validateAuthKey($authKey)
    {
    }


}