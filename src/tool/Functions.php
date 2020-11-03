<?php

namespace meilikeai\tool;

class Functions
{
    /**
     * 将字节转换为可读文本
     * @param int $size 大小
     * @param string $delimiter 分隔符
     * @return string
     */
    public static function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 6; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $delimiter . $units[$i];
    }

    /**
     * 判断文件或文件夹是否可写
     * @param string $file 文件或目录
     * @return    bool
     */
    public static function is_really_writable($file)
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return is_writable($file);
        }
        if (is_dir($file)) {
            $file = rtrim($file, '/') . '/' . md5(mt_rand());
            if (($fp = @fopen($file, 'ab')) === false) {
                return false;
            }
            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return true;
        } elseif (!is_file($file) or ($fp = @fopen($file, 'ab')) === false) {
            return false;
        }
        fclose($fp);
        return true;
    }

    /**
     * DES加密
     * @param string $str 加密字符串
     * @param string $key 加密KEY
     * @return string
     * @author 牧羊人
     * @date 2019/6/6
     */
    public static function encrypt_des($str, $key = 'p@ssw0rd')
    {
        $prep_code = serialize($str);
        $block = mcrypt_get_block_size('des', 'ecb');
        if (($pad = $block - (strlen($prep_code) % $block)) < $block) {
            $prep_code .= str_repeat(chr($pad), $pad);
        }
        $encrypt = mcrypt_encrypt(MCRYPT_DES, $key, $prep_code, MCRYPT_MODE_ECB);
        return base64_encode($encrypt);
    }

    /**
     * DES解密
     * @param string $str 解密字符串
     * @param string $key 解密KEY
     * @return mixed
     * @author 牧羊人
     * @date 2019/6/6
     */
    public static function decrypt_des($str, $key = 'p@ssw0rd')
    {
        $str = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        $block = mcrypt_get_block_size('des', 'ecb');
        $pad = ord($str[($len = strlen($str)) - 1]);
        if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) {
            $str = substr($str, 0, strlen($str) - $pad);
        }
        return unserialize($str);
    }

    /**
     * 格式化手机号码
     * @param string $mobile 手机号码
     * @return string 返回结果
     * @author 牧羊人
     * @date 2019/4/5
     */
    public static function format_mobile($mobile)
    {
        return substr($mobile, 0, 5) . "****" . substr($mobile, 9, 2);
    }

    /**
     * 随机字符串
     * @param int $length 长度
     * @param int $type 类型(0：混合；1：纯数字)
     * @return string 返回结果
     * @author 牧羊人
     * @date 2019/4/5
     */
    public static function get_random_str($length = 16, $type = 1)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $type ? 10 : 35);
        $seed = $type ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($type) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /**
     * 获取服务端IP地址
     * @return string 返回IP地址
     * @author 牧羊人
     * @date 2019/4/5
     */
    public static function get_server_ip()
    {
        if (isset($_SERVER)) {
            if ($_SERVER['SERVER_ADDR']) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } else {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip;
    }

    /**
     * 获取客户端IP地址
     * @param int $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param bool $adv 否进行高级模式获取（有可能被伪装）
     * @return mixed 返回IP
     * @author 牧羊人
     * @date 2019/4/5
     */
    public static function get_client_ip($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null) {
            return $ip[$type];
        }
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) {
                    unset($arr[$pos]);
                }
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    /**
     * 根据月、日获取星座
     *
     * @param unknown $month 月
     * @param unknown $day 日
     * @return boolean|multitype:
     * @author 牧羊人
     * @date 2019-04-04
     */
    public static function get_zodiac_sign($month, $day)
    {
        // 检查参数有效性
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
            return false;
        }
        // 星座名称以及开始日期
        $signs = array(
            array("20" => "水瓶座"),
            array("19" => "双鱼座"),
            array("21" => "白羊座"),
            array("20" => "金牛座"),
            array("21" => "双子座"),
            array("22" => "巨蟹座"),
            array("23" => "狮子座"),
            array("23" => "处女座"),
            array("23" => "天秤座"),
            array("24" => "天蝎座"),
            array("22" => "射手座"),
            array("22" => "摩羯座")
        );
        list($sign_start, $sign_name) = each($signs[(int)$month - 1]);
        if ($day < $sign_start) {
            list($sign_start, $sign_name) = each($signs[($month - 2 < 0) ? $month = 11 : $month -= 2]);
        }
        return $sign_name;
    }

    /**
     * 获取格式化显示时间
     * @param int $time 时间戳
     * @return false|string 返回结果
     * @author 牧羊人
     * @date 2019/4/5
     */
    public static function get_format_time($time)
    {
        $time = (int)substr($time, 0, 10);
        $int = time() - $time;
        $str = '';
        if ($int <= 2) {
            $str = sprintf('刚刚', $int);
        } elseif ($int < 60) {
            $str = sprintf('%d秒前', $int);
        } elseif ($int < 3600) {
            $str = sprintf('%d分钟前', floor($int / 60));
        } elseif ($int < 86400) {
            $str = sprintf('%d小时前', floor($int / 3600));
        } elseif ($int < 1728000) {
            $str = sprintf('%d天前', floor($int / 86400));
        } else {
            $str = date('Y-m-d H:i:s', $time);
        }
        return $str;
    }

    /**
     * 递归创建目录
     * @param string $dir 需要创建的目录路径
     * @param int $mode 权限值
     * @return bool 返回结果true或false
     * @author 牧羊人
     * @date 2019/6/6
     */
    public static function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || mkdir($dir, $mode, true)) {
            return true;
        }
        if (!mkdirs(dirname($dir), $mode)) {
            return false;
        }
        return mkdir($dir, $mode, true);
    }

    /**
     * 字符串截取，支持中文和其他编码
     * @param string $str 需要转换的字符串
     * @param int $start 开始位置
     * @param int $length 截取长度
     * @param string $encoding 编码格式
     * @param string $suffix 截断显示字符
     * @return false|mixed|string 返回结果
     * @author 牧羊人
     * @date 2019/6/28
     */
    public static function mbsubstr($str, $start = 0, $length = null, $encoding = "utf-8", $suffix = '...')
    {
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $encoding);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $encoding);
            if (false === $slice) {
                $slice = '';
            }
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$encoding], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . $suffix : $slice;
    }

    /**
     * 对象转数组
     * @param object $obj 对象
     * @return array
     */
    function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }
        return $obj;
    }

    /**
     * 数组转对象
     * @param array $arr 数组
     * @return object
     */
    function array_to_object($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)array_to_object($v);
            }
        }
        return (object)$arr;
    }
}