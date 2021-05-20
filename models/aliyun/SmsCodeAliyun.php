<?php
/**
 * File Name: SmsAliyunCode.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/8 3:36 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\sms\models\aliyun;


use QTTX;

class SmsCodeAliyun extends SmsAliyun
{
    /// 接收参数请看父类

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->checkConf();

        // 检查短信发送间隔
        $last_time = $this->getLastSendMsgTime();
        if ($last_time + $this->external->interval > time()) {
            $this->addError('mobile', '发送过于频繁');
            return false;
        }

        // 生成验证码和发送参数
        list($code, $query) = $this->generateCode($this->mobile);

        // 发送短信
        $resp = [];
        if (!$this->external->debug) {
            $resp = $this->_send($query);
            if (!$resp) {
                $this->addError('mobile', '短信发送失败');
                return false;
            }
        }

        // 保存验证码
        $this->saveCode($code, $query, $resp);

        if ($this->external->debug) return true;

        return $this->checkResponse($resp);
    }

    /**
     * 生成验证码和发送用的数组
     * debug模式下,验证码始终为123456
     * @param $mobile
     * @return array
     */
    protected function generateCode($mobile)
    {
        $code = mt_rand(110000, 999999);
        if ($this->external->debug) {
            $code = '123456';
        }

        $param = json_encode($this->external->templateValue());
        $param = str_replace('{{code}}', $code, $param);

        $query = [
            'RegionId' => "cn-hangzhou",
            'PhoneNumbers' => $mobile,
            'SignName' => $this->external->signName(),
            'TemplateCode' => $this->external->templateCode(),
//            'TemplateParam' => "{\"code\":\"{$code}\"}",
            'TemplateParam' => $param,
        ];

        return [$code, $query];
    }

    /**
     * 保存验证码
     * @param $code
     * @param $query
     * @param array $send_result
     */
    protected function saveCode($code, $query, $send_result = [])
    {
        $_cols = [
            'mobile' => $query['PhoneNumbers'],
            'code' => $code,
            'create_time' => time(),
            'expiration_time' => time() + $this->external->effective_time,
            'is_used' => 0,
            'response' => json_encode($send_result,JSON_UNESCAPED_UNICODE)
        ];

        if ($this->external->storageModel === STORAGE_MODE_REDIS) {
            $key = sprintf($this->external->redisKey('aliyun'), $_cols['mobile']);
            $this->external->getRedis()->hmset($key, $_cols);
            $this->external->getRedis()->expire($key, $this->external->effective_time);
        } else {
            $this->external->getDb()
                ->insert($this->external->codeTableName('aliyun'))
                ->cols($_cols)
                ->query();
        }
    }

    /**
     * 获取手机号最后一次发送短信的时间
     * @return int|mixed
     */
    protected function getLastSendMsgTime()
    {

        if ($this->external->storageModel == STORAGE_MODE_REDIS) {
            $key = sprintf($this->external->redisKey('aliyun'), $this->mobile);
            $result = $this->external->getRedis()->hgetall($key);
            if (empty($result))
                return 0;
            else
                return $result['create_time'];
        } else {
            $result = $this->external->getDb()
                ->select('create_time')
                ->from($this->external->codeTableName('aliyun'))
                ->where('mobile= :mobile and is_used=0')
                ->bindValue('mobile', $this->mobile)
                ->orderByDESC(['create_time'])
                ->row();
            if (empty($result))
                return 0;
            else
                return $result['create_time'];
        }
    }
}
