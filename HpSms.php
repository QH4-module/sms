<?php
/**
 * File Name: HpSms.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/1 7:40 下午
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
use QTTX;
use qttx\components\db\DbModel;

class HpSms
{
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
    {
        if ($external->storageModel == STORAGE_MODE_MYSQL) {
            if (is_null($db)) $db = $external->getDb();

            $result = $db->select('*')
                ->from($external->codeTableName('aliyun'))
                ->whereArray(['mobile' => $mobile])
                ->orderByDESC(['create_time'])
                ->row();

            if (empty($result)) return false;
            if ($result['code'] != $code) return false;
            if ($result['expiration_time'] < time()) return false;
            if ($result['is_used'] == 1) return false;

            if ($used) {
                $db->update($external->codeTableName('aliyun'))
                    ->col('is_used', 1)
                    ->whereArray(['id' => $result['id']])
                    ->query();
            }
            return true;
        }else if ($external->storageModel == STORAGE_MODE_REDIS) {
            $key = sprintf($external->redisKey('aliyun'), $mobile);
            $result = $external->getRedis()->hgetall($key);

            if (empty($result)) return false;
            if ($result['code'] != $code) return false;
            if ($result['expiration_time'] < time()) return false;
            if ($result['is_used'] == 1) return false;

            if ($used) {
                $external->getRedis()->hset($key, 'is_used', 1);
            }
            return true;
        }else{
            return false;
        }
    }
}