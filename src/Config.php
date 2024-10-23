<?php

namespace Ledc\XhdPay;

use Closure;

/**
 * 小红点支付配置管理类
 * @property string $merchantNo 联合商户号
 * @property string $terminalNo 联合设备号
 * @property string $secretKey 密钥
 * @property bool|int $enable 启用易生支付
 * @property bool|int $debug 启用测试环境
 * @property string $notifyUrlPrefix 通知地址前缀
 */
class Config extends Contracts\Config
{
    /**
     * 配置项前缀
     */
    public const CONFIG_PREFIX = 'xhdpay_';

    /**
     * 测试请求地址
     * - 测试环境生成的也是真实交易，请用小金额测试
     */
    const API_TEST = 'http://mer.xmxhd.net/';

    /**
     * 生产请求地址
     */
    const API_PROD = 'https://gateway.xhdpay.com/';

    /**
     * 是否调试模式
     * @var bool true生产环境、false测试环境
     */
    protected bool $debug = false;

    /**
     * 支付原始请求
     * @var Closure
     */
    public static Closure $pay_payload_request;
    /**
     * 支付原始响应
     * @var Closure
     */
    public static Closure $pay_payload_response;
    /**
     * 通知处理类
     * - NotifyHandler::class 的子类
     * @var string|null
     */
    public static ?string $notifyHandler = null;

    /**
     * 必填项
     * @var array|string[]
     */
    protected array $requiredKeys = ['merchantNo', 'terminalNo', 'secretKey', 'enable', 'debug', 'notifyUrlPrefix'];

    /**
     * 支付通知URL
     * @param string $attach 业务标识
     * @return string
     */
    public function getPayNotifyUrl(string $attach): string
    {
        return $this->get('notifyUrlPrefix', '') . ($attach ? '/' . $attach : '');
    }

    /**
     * 拼接非空键值对
     * @param array $params
     * @return string
     */
    public function implodeKeyValue(array $params): string
    {
        // 将集合 M 内非空参数以字典序升序（忽略大小写）排列拼接成 URL 键值对格式的字符串
        $params = array_filter($params, function ($value) {
            return null !== $value && '' !== $value;
        });

        ksort($params, SORT_STRING | SORT_FLAG_CASE);
        $req = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $req[] = $key . '=' . json_encode($value);
            } else {
                $req[] = $key . '=' . $value;
            }
        }
        return implode('&', $req);
    }

    /**
     * 生成请求参数的签名
     * @param array $params
     * @return string
     */
    public function generateSignature(array $params): string
    {
        $original = $this->implodeKeyValue($params);

        // 将密钥直接拼接到第一步得到的 originalSignStr 后面，得到最终的待加密字符串
        $originalSignStr = $original . $this->secretKey;

        // 对 originalSignStr 进行 md5 加密，得到签名(小写)
        return strtolower(md5($originalSignStr));
    }

    /**
     * 验签
     * @param string $signature
     * @param array $params
     * @return bool
     */
    public function verifySignature(string $signature, array $params): bool
    {
        if (hash_equals($this->generateSignature($params), $signature)) {
            return true;
        }
        return false;
    }

    /**
     * 获取网关地址
     * @return string
     */
    public function getApiGateway(): string
    {
        return $this->isDebug() ? self::API_TEST : self::API_PROD;
    }

    /**
     * 是否调试模式
     * @return bool true生产环境、false测试环境
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * 设置调试模式
     * @param bool $debug
     * @return Config
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }
}
