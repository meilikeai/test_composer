<?php


namespace meilikeai\helper;

/**
 * Class Curl 网络请求类
 * @author peipei
 * @date 2021年02月25日14:38:08
 * @email 122528759@qq.com
 * @package meilikeai\helper
 */
class Curl
{
    /**
     * 发送get请求
     * @param $url
     * @param array $params
     */
    public function get($url, $params = [])
    {
        $curl = new \Curl\Curl();
        $curl->get($url, $params);
        if ($curl->error) {
            echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
        } else {
            echo 'Response:' . "\n";
            var_dump($curl->response);
        }
    }
}