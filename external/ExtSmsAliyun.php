<?php
/**
 * File Name: ExtSmsAli.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/1 7:42 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\sms\external;


use QTTX;
use qttx\web\External;

abstract class ExtSmsAliyun extends External
{
    /**
     * @var bool 是否开启测试模式
     * 测试模式,短信验证码固定为123456,并不会真正的发送短信
     */
    public $debug = false;

    /**
     * @var string 保存方式
     * 可以使用 `STORAGE_MODE_*`
     */
    public $storageModel = STORAGE_MODE_MYSQL;

    /**
     * @var int 发送短信间隔时间,单位秒
     */
    public $interval = 60;

    /**
     * @var int 验证码有效时间,单位秒
     */
    public $effective_time = 1200;


    /**
     * 必须的配置参数,从阿里云获取
     * @return string
     */
    abstract public function accessKeyID();

    /**
     * 必须的配置参数,从阿里云获取
     * @return string
     */
    abstract public function accessKeySecret();

    /**
     * 必须的配置参数,从阿里云获取
     * 短信签名
     * @return string
     */
    abstract public function signName();

    /**
     * 必须配置的参数,从阿里云获取
     * @return string
     * @see templateValue() 配置需要保持一致性
     */
    abstract public function templateCode();

    /**
     * 返回模板的变量
     * 值中的 {{code}} 会被替换为具体的验证码
     * @return array
     * @see templateCode() 配置需要保持一致性
     */
    public function templateValue()
    {
        return [
            'code' => '{{code}}',
        ];

        // 如果模板带有时间提醒,可以是下面格式
//        return [
//            'code'=>'{{code}}',
//            'timeout'=>'20分钟'
//        ];
    }

    /**
     * 保存验证码的表名,有多少个平台,就需要返回多少个
     * @param string $type 平台名称
     * @return string
     */
    public function codeTableName($type)
    {
        $tables = [
            'aliyun' => '{{%sms_code_ali}}',
            'baidu' => '',
            'huawei' => '',
            'tencent' => '',
        ];
        return $tables[$type];
    }

    /**
     * 用户主表 `user` 表名
     * @return string
     */
    public function userTableName()
    {
        return '{{%user}}';
    }

    /**
     * 如果使用 redis 存储,需要设置key
     * @param string $type 平台名称
     * @return string
     */
    public function redisKey($type)
    {
        $app_name = QTTX::getConfig('app_name');
        $keys = [
            'aliyun' => "{$app_name}_sms_code_ali:%s:h",
            'baidu' => '',
            'huawei' => '',
            'tencent' => '',
        ];
        return $keys[$type];
    }
}