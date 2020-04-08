<?php

namespace meilikeai\wechat;

use \Exception;

/**
 * notes: Auth
 * author: peipei
 * date: 2020/4/8 14:26
 */
class Auth
{
    /**
     * 获取微信access_token
     * @param array $wx_config 包含appid和appsecret的数组
     * @param bool $reset 是否强制重置access_token
     * @param string $expire token有效期
     * @return bool
     * @throws Exception
     */
    public static function getAccessToken($wx_config = [], $reset = false, $expire = '7200')
    {
        //如果没有传值时
        if (!isset($wx_config['appid']) || !$wx_config['appid'] || !isset($wx_config['appsecret']) || !$wx_config['appsecret']) {
            throw new Exception("appid or appsecret is required");
        }
        //这里需要做判断，查找缓存文件去找到access_token的缓存
        if ($reset == false) {
            $check = self::checkAccessToken();
            if ($check) {
                return $check;
            }
        }
        $wx_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$wx_config['appid']}&secret={$wx_config['appsecret']}";
        $result = self::curlSend($wx_url);
        $cache = [
            'access_token' => $result['access_token'],
            'expire' => time() + $expire
        ];
        is_dir('../runtime') || @mkdir('../runtime', 0777);
        file_put_contents('../runtime/access_token.log', json_encode($cache));
        return $result['access_token'];
    }

    private static function curlSend($url, $data = '')
    {
        $ch = curl_init();
        if (class_exists('\CURLFile')) {
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
        } else {
            if (defined('CURLOPT_SAFE_UPLOAD')) {
                curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
            }
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不进行证书验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不进行主机头验证
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //结果不直接输出在屏幕上
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.86 Safari/537.36");
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        $data && curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data ? curl_setopt($ch, CURLOPT_POST, true) : curl_setopt($ch, CURLOPT_POST, false);  //发送的方式
        curl_setopt($ch, CURLOPT_URL, $url);   //发送的地址
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result, true);
    }

    private static function checkAccessToken()
    {
        if (file_exists('../runtime/access_token.log')) {
            $info = file_get_contents('../runtime/access_token.log');
            $info = json_decode($info, true);
            if (time() < $info['expire']) {
                return $info['access_token'];
            }
        }
        return false;
    }
}