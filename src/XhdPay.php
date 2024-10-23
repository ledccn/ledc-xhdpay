<?php

namespace Ledc\XhdPay;

use Ledc\XhdPay\Contracts\Curl;
use Ledc\XhdPay\Contracts\PayTypeEnums;
use RuntimeException;

/**
 * 小红点支付SDK
 */
class XhdPay
{
    /**
     * 配置
     * @var Config
     */
    protected Config $config;

    /**
     * 构造函数
     * @param Config $config 配置
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * 获取配置
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * 统一支付
     * @param string $attach
     * @param array $order
     * @return array
     */
    public function pay(string $attach, array $order): array
    {
        return $this->wechatPay($attach, $order);
    }

    /**
     * 交易订单(退款)查询二合一接口
     * @param string $orderNo 网关订单号
     * @param string $merOrderNo 商户订单号
     * @param string $refundOrderNo 网关退货订单号（退款查询时必传）
     * @return array
     */
    public function query(string $orderNo, string $merOrderNo, string $refundOrderNo = ''): array
    {
        $curl = $this->request('hxdpay/query', array_filter([
            // 网关订单号
            'orderNo' => $orderNo,
            // 商户订单号
            'merOrderNo' => $merOrderNo,
            // 网关退货订单号（退款查询时必传）
            'refundOrderNo' => $refundOrderNo,
        ]));

        if ($curl->isError() || empty($curl->response)) {
            throw new RuntimeException($curl->getErrorMessage() ?: '红小点查询交易订单未返回错误描述');
        }

        return json_decode($curl->response, true);
    }

    /**
     * 交易退款接口
     * @param string $orderNo 网关订单号
     * @param string $merRefundOrderNo 商户退货订单号
     * @param string $refundAmount 退货金额，单位分
     * @return array
     */
    public function refund(string $orderNo, string $merRefundOrderNo, string $refundAmount): array
    {
        $curl = $this->request('hxdpay/refund', [
            // 网关订单号
            'orderNo' => $orderNo,
            // 红小点分配的联合商户号
            'merchantNo' => $this->getConfig()->merchantNo,
            // 商户退货订单号
            'merRefundOrderNo' => $merRefundOrderNo,
            // 退货金额，单位分
            'refundAmount' => (int)bcmul($refundAmount, '100'),
        ]);

        if ($curl->isError() || empty($curl->response)) {
            throw new RuntimeException($curl->getErrorMessage() ?: '红小点交易退款接口未返回错误描述');
        }

        return json_decode($curl->response, true);
    }

    /**
     * 创建订单(微信小程序、微信公众号、支付宝小程序、支付宝服务窗)
     * @param string $attach 业务的附加数据
     * @param array $order 订单数据
     * @return array
     */
    protected function wechatPay(string $attach, array $order): array
    {
        $curl = $this->request('hxdpay/make/wxpay', array_filter([
            // 红小点分配的联合商户号
            'merchantNo' => $this->getConfig()->merchantNo,
            // 联合设备号
            'terminalNo' => $this->getConfig()->terminalNo,
            // 报备的appId (cashierPay：聚合收银台非必填)
            'appId' => $order['appId'] ?? $this->getConfig()->get('appId'),
            // 对应appId获取的用户openId
            'openId' => $order['openId'],
            // 商户订单号
            'merOrderNo' => $order['merOrderNo'],
            // 商品描述
            'orderDesc' => $order['orderDesc'],
            // 订单金额，以分为单位，最小金额为1
            'orderAmount' => (int)bcmul($order['orderAmount'], '100'),
            // 接收平台通知的URL，需给绝对路径，255字符内格式
            'notifyUrl' => $order['notifyUrl'] ?? $this->getConfig()->getPayNotifyUrl($attach),
            // 支付方式
            'payType' => $order['payType'] ?? PayTypeEnums::cashierPay,
            // B2C分账
            'divJson' => $order['divJson'] ?? '',
        ]));

        if ($curl->isError() || empty($curl->response)) {
            throw new RuntimeException($curl->getErrorMessage() ?: '红小点支付未返回错误描述');
        }

        return json_decode($curl->response, true);
    }

    /**
     * 发送请求
     * @param string $url 接入点（不含网关地址）
     * @param array $data 数据报文
     * @param string $method 请求方法
     * @return Curl
     */
    public function request(string $url, array $data = [], string $method = 'POST'): Curl
    {
        $method = strtoupper($method);
        $url = $this->getFullUrl($url);
        $curl = new Curl();
        $curl->setSslVerify()->setTimeout();
        $curl->setHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        $curl->setHeader('Accept', 'application/json');

        $data['sign'] = $this->getConfig()->generateSignature($data);

        // 请求前回调，记录支付原始请求报文
        if (null !== Config::$pay_payload_request) {
            $fn = Config::$pay_payload_request;
            call_user_func_array($fn, compact('url', 'data', 'method'));
        }

        switch ($method) {
            case 'GET':
                $curl->get($url);
                break;
            case 'PUT':
                $curl->put($url, $data);
                break;
            case 'PATCH':
                $curl->patch($url, $data);
                break;
            default:
                $curl->post($url, $data);
                break;
        }

        return $curl;
    }

    /**
     * 获取请求的完整URL地址
     * @param string $url
     * @return string
     */
    protected function getFullUrl(string $url): string
    {
        return rtrim($this->getConfig()->getApiGateway(), '/') . '/' . ltrim($url, '/');
    }
}
