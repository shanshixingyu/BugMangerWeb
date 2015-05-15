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
}