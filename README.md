QH4框架扩展模块-短信模块

该模块根据你使用的平台初始化不同的 `External` 类

根据平台不同,数据库文件也略有区别,注意区分

注意: 该模块主要是为了验证码开发的,所以只提供了验证码的数据表.并不表示该模块只能发送验证码

现在提供的平台有 阿里云

### 外部依赖
```shell
阿里云短信依赖
composer require alibabacloud/client
```


### api 列表
```php
actionSendByAliyun()
```
通过阿里云发送短信

```php
actionSendCodeByAliyun()
```
通过阿里云发送短信验证码


### 方法列表
```php
/**
 * 验证阿里云短信验证码
 * @param string $mobile 手机号
 * @param string $code 验证码
 * @param ExtSmsAliyun $external
 * @param bool $used 是否标记为已使用
 * @param DbModel $db 如果是需要事务,可以传入该参数
 * @return bool
 */
public static function checkCodeAli($mobile, $code, ExtSmsAliyun $external, $used = true, $db = null)
```