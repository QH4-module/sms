<?php
/**
 * File Name: TraitSmsController.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/8 3:23 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\sms;


use qh4module\sms\external\ExtSmsAliyun;
use qh4module\sms\models\aliyun\SmsAliyun;
use qh4module\sms\models\aliyun\SmsCodeAliyun;

trait TraitSmsController
{
    /**
     * @return ExtSmsAliyun
     */
    public function ext_sms_aliyun()
    {
        // 需要覆盖重写
    }


    /**
     * 通过阿里云发送短信
     * @return mixed
     */
    public function actionSendByAliyun()
    {
        $model = new SmsAliyun([
            'external' => $this->ext_sms_aliyun()
        ]);

        return $this->runModel($model);
    }

    /**
     * 通过阿里云发送短信验证码
     * @return array
     */
    public function actionSendCodeByAliyun()
    {
        $model = new SmsCodeAliyun([
            'external' => $this->ext_sms_aliyun()
        ]);

        return $this->runModel($model);
    }
}