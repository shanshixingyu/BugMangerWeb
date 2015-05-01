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


    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }

    /**
     * 数据库表自身连接需要注意要给表起别名
     * @return static
     */
    public function getCreateUser()
    {
        return $this->hasOne(User::className(), ['id' => 'creator'])->from(['second' => User::tableName()]);
//        return $this->hasOne(User::className(), ['id' => 'creator'])->from(User::tableName() . ' second');
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