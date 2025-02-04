# 红小点支付（易生支付）

## 安装

`composer require ledc/xhdpay`

## 使用

开箱即用，只需要传入一个配置，初始化一个实例即可：

```php
use Ledc\XhdPay\Config;
use Ledc\XhdPay\XhdPay;

$env_config = [
    'merchantNo' => '联合商户号',
    'terminalNo' => '联合设备号',
    'secretKey' => '密钥',
    'enable' => '启用支付',
    'debug' => '启用测试环境',
    'notifyUrlPrefix' => '通知地址前缀，格式如：https://iyuu.cn/xhdpay/callback',
];
$config = new Config($env_config);
$api = new XhdPay($config);
```

在创建实例后，所有的方法都可以有IDE自动补全；例如：

```php
// 获得完整的通知URL地址
$notifyUrl = $config->getPayNotifyUrl($attach);

// 验证签名（签名验证失败时，会抛出异常）
$config->verifySignature();

// 统一支付下单
$api->pay();

// 交易订单(退款)查询二合一接口
$api->query();

// 交易退款接口
$api->refund();
```

## 捐赠

![reward](reward.png)

## 支付成功回调报文

```json
{
  "biz": {
    "terminalNo": "联合设备号",
    "orderNo": "网关订单号",
    "merOrderNo": "商户订单号",
    "bankCardType": "U",
    "channelOrderNo": "渠道订单号（微信、支付宝...）",
    "paidInfo": {
      "billTime": "2024-10-23 16:13:54",
      "bankCardType": "U",
      "paidTime": "2024-10-23 16:13:54",
      "buyerId": "用户openid",
      "paidAmount": "1"
    },
    "buyerId": "用户openid",
    "payableAmount": "1",
    "orderAmount": "1",
    "orderTime": "2024-10-23 16:13:47",
    "notifyType": "Paid",
    "orderDesc": "ISG--ISG BioSkin 賦活奇....",
    "merchantNo": "联合商户号",
    "status": "Completed"
  },
  "success": 1,
  "sign": "32bd0227b2bb76c4d7260e048d4f11dd"
}
```

## 退款成功回调报文
```json
{
  "biz": {
    "terminalNo": "联合设备号",
    "orderNo": "网关订单号",
    "merOrderNo": "商户订单号",
    "bankCardType": "U",
    "channelOrderNo": "渠道退款订单号（微信、支付宝...）",
    "buyerId": "用户openid",
    "payableAmount": "1",
    "orderAmount": "1",
    "orderTime": "2024-10-23 17:33:42",
    "notifyType": "Refund",
    "refundInfo": {
      "refundOrderNo": "网关退货订单号",
      "refundTime": "2024-10-28 11:56:52",
      "refundStatus": "Success",
      "merRefundOrderNo": "商户退货订单号",
      "refundAmount": "1"
    },
    "orderDesc": "ISG--ISG BioSkin 賦活奇....",
    "merchantNo": "联合商户号",
    "status": "Closed"
  },
  "success": 1,
  "sign": "d6b3a75c949cdd4cd73e9738b0c6fd3c"
}
```
