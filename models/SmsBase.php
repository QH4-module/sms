<?php
/**
 * File Name: SmsBase.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/8 3:33 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\sms\models;


use qttx\web\ServiceModel;

class SmsBase extends ServiceModel
{
    /**
     * @var string 接收参数,手机号
     */
    public $mobile;

    /**
     * 接收参数,是否注册了才允许发短信
     * @var bool true 注册了才允许发送  false 未注册才允许发送 null 表示不限制
     */
    public $tag = null;


    /**
     * @inheritDoc
     */
    public function rules()
    {
        $rules = [
            [['mobile'], 'required'],
            [['mobile'], 'mobile'],
        ];
        if ($this->tag !== null) {
            if ($this->tag) {
                $rules[] = [['mobile'], 'exist', ['table' => $this->external->userTableName(), 'field' => 'mobile']];
            } else {
                $rules[] = [['mobile'], 'unique', ['table' => $this->external->userTableName(), 'field' => 'mobile']];
            }
        }
        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function attributeLangs()
    {
        return [
            'mobile' => '手机号'
        ];
    }



}