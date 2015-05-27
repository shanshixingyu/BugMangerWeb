<?php
/**
 * 密码方面的操作类
 * Created by GuLang on 2015-05-11.
 */

namespace app\tools;


class PasswordUtils
{
    /**
     * 根据原始密码获得加密后的密码
     * @param string $rawPassword 原始密码，也是加密前的密码,默认密码为:123456
     * @return string 返回加密后的密码
     */
    public static function getEncryptedPassword($rawPassword = '123456')
    {
        if ($rawPassword === null || trim($rawPassword) == '') {
            $rawPassword = '123456';
        }
        $tempPassword = md5($rawPassword . PASSWORD_KEY);
        return substr($tempPassword, 3, 20);
    }

    /**
     * 结合当前时间，利用特定的算法，计算得到用户的重置码
     * @return string
     */
    public static function getResetPasswordParam()
    {
        //获取当前时间
        $currentTime = time();
        $tempResetPasswordParam = md5($currentTime . PASSWORD_KEY);
        return substr($tempResetPasswordParam, 3, 20);
    }

}