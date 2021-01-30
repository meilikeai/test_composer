<?php

namespace meilikeai\helper;

/**
 * notes: Validate
 * author: peipei
 * date: 2020/4/8 15:45
 */
class Validate
{
    /**
     * 判断是否为邮箱
     * @param string $str 邮箱
     * @return false 返回结果true或false
     * @author peipei
     * @date 2019/4/5
     */
    public static function is_email($str)
    {
        return preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/', $str);
    }

    /**
     * 验证邮编是否正确
     * @param string $code 邮编
     * @return false 返回结果true或false
     * @author peipei
     * @date 2019/4/5
     */
    public static function is_zipcode($code)
    {
        return preg_match('/^[1-9][0-9]{5}$/', $code);
    }

    /**
     * 验证身份证是否正确
     * @param string $idno 身份证号
     * @return bool 返回结果true或false
     * @author peipei
     * @date 2019/4/5
     */
    public static function is_idcard($idno)
    {
        $idno = strtoupper($idno);
        $regx = '/(^\d{15}$)|(^\d{17}([0-9]|X)$)/';
        $arr_split = array();
        if (!preg_match($regx, $idno)) {
            return false;
        }
        // 检查15位
        if (15 == strlen($idno)) {
            $regx = '/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/';
            @preg_match($regx, $idno, $arr_split);
            $dtm_birth = "19" . $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            if (!strtotime($dtm_birth)) {
                return false;
            } else {
                return true;
            }
        } else {
            // 检查18位
            $regx = '/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/';
            @preg_match($regx, $idno, $arr_split);
            $dtm_birth = $arr_split[2] . '/' . $arr_split[3] . '/' . $arr_split[4];
            // 检查生日日期是否正确
            if (!strtotime($dtm_birth)) {
                return false;
            } else {
                // 检验18位身份证的校验码是否正确。
                // 校验位按照ISO 7064:1983.MOD 11-2的规定生成，X可以认为是数字10。
                $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
                $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
                $sign = 0;
                for ($i = 0; $i < 17; $i++) {
                    $b = (int)$idno{$i};
                    $w = $arr_int[$i];
                    $sign += $b * $w;
                }
                $n = $sign % 11;
                $val_num = $arr_ch[$n];
                if ($val_num != substr($idno, 17, 1)) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    /**
     * 判断是否为空
     * @param $value 参数值
     * @return bool 返回结果true或false
     * @author peipei
     * @date 2019/4/5
     */
    public static function is_empty($value)
    {
        // 判断是否存在该值
        if (!isset($value)) {
            return true;
        }
        // 判断是否为empty
        if (empty($value)) {
            return true;
        }
        // 判断是否为空字符串
        if (trim($value) === '') {
            return true;
        }
        // 默认返回false
        return false;
    }

    /**
     * 验证ip是否属于内网
     * @param $ip string 要验证的ip
     * @return bool
     */
    function is_intranet($ip)
    {
        $rs = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        return $rs === false;
    }

    /**
     * 验证url是否正确
     * @param string $url url地址
     * @return bool 如果URL有效，则返回true，否则返回false
     */
    public static function validateURL($url)
    {
        return (bool) filter_var($url, FILTER_VALIDATE_URL);
    }
}