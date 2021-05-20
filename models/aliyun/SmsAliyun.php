<?php
/**
 * File Name: AliSms.php
 * ©2020 All right reserved Qiaotongtianxia Network Technology Co., Ltd.
 * @author: hyunsu
 * @date: 2021/5/1 7:43 下午
 * @email: hyunsu@foxmail.com
 * @description:
 * @version: 1.0.0
 * ============================= 版本修正历史记录 ==========================
 * 版 本:          修改时间:          修改人:
 * 修改内容:
 *      //
 */

namespace qh4module\sms\models\aliyun;


use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use qh4module\sms\external\ExtSmsAliyun;
use qh4module\sms\models\SmsBase;
use QTTX;
use qttx\exceptions\InvalidArgumentException;

class SmsAliyun extends SmsBase
{

    /// 接收参数请看父类

    /**
     * @var ExtSmsAliyun
     */
    protected $external;


    protected $AccessKeyID = '';
    protected $AccessKeySecret = '';

    /**
     * @inheritDoc
     */
    public function run()
    {
        $this->checkConf();

        $resp = $this->_send($this->generateQuery($this->mobile));

        return $this->checkResponse($resp);
    }

    /**
     * 检查阿里云返回
     * @param $resp
     * @return bool
     */
    protected function checkResponse($resp)
    {
        if (isset($resp['Code']) && $resp['Code'] == 'OK') {
            return true;
        } else {
            $this->addError('mobile', '短信发送失败');
            return false;
        }
    }

    /**
     * 检查扩展类配置
     */
    protected function checkConf()
    {
        if (!$this->external->debug) {
            $this->AccessKeyID = $this->external->accessKeyID();
            $this->AccessKeySecret = $this->external->accessKeySecret();
            if ((empty($this->AccessKeyID) || empty($this->AccessKeySecret))) {
                throw new InvalidArgumentException("没有配置阿里云相关参数");
            }
        }
    }

    /**
     * 生成验证码和发送用的数组
     * debug模式下,验证码始终为123456
     * @param $mobile
     * @return array
     */
    protected function generateQuery($mobile)
    {
        $param = json_encode($this->external->templateValue());

        $query = [
            'RegionId' => "cn-hangzhou",
            'PhoneNumbers' => $mobile,
            'SignName' => $this->external->signName(),
            'TemplateCode' => $this->external->templateCode(),
//            'TemplateParam' => "{\"code\":\"{$code}\"}",
            'TemplateParam' => $param,
        ];

        return $query;
    }

    /**
     * 真正发送短信
     * @param array $query 短信配置数组
     *              'query' => [
     * 'RegionId' => "cn-hangzhou",
     * 'PhoneNumbers' => "15653437356",
     * 'SignName' => "阿里云短信测试专用",
     * 'TemplateCode' => "SMS_125010035",
     * 'TemplateParam' => "{\"code\":\"002342\"}",
     * ],
     * @return false|array
     */
    protected function _send($query)
    {
        try {
            AlibabaCloud::accessKeyClient($this->AccessKeyID, $this->AccessKeySecret)
                ->regionId('cn-hangzhou')
                ->asDefaultClient();

            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => $query,
                ])
                ->request();

            return $result->toArray();

        } catch (ClientException $e) {
            QTTX::$app->log->error($e);
            return false;
        } catch (ServerException $e) {
            QTTX::$app->log->error($e);
            return false;
        }
    }
}
