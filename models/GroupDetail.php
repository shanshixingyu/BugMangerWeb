<?php
/**
 *
 * Created by GuLang on 2015-04-22.
 */

namespace app\models;


use yii\db\ActiveRecord;

class GroupDetail extends ActiveRecord
{
    public static function tableName()
    {
        return 'group_detail';
    }

}