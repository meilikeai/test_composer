```
加解密函数

use meilikeai\functions\Encrypt;

/**
 * 加解密函数
 * @param string $string  要加密的字符串
 * @param string $operation 操作  encode/decode
 * @param string $key 加密key
 * @param int $expiry 有效期
 * @return false|string
 */
Encrypt::authCode($str, $operation='decode', $key, $expiry);
```
```
Pinyin 中文转拼音
https://github.com/overtrue/pinyin
```
```
微信公众号获取access_token

use meilikeai\wechat\Auth;

/**
 * 获取微信access_token
 * @param array $wx_config 包含appid和appsecret的数组
 * @param bool $reset 是否强制重置access_token
 * @param string $expire token有效期
 * @return bool
 * @throws Exception
 */
Auth::getAccessToken($wx_config = [], $reset = false, $expire = '7200')
```

##### 常用验证类
```
use meilikeai\tool\ToolValidate;

is_idcard / is_email / is_zipcode / is_empty

```