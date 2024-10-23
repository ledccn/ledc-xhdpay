<?php

namespace Ledc\XhdPay\Contracts;

/**
 * 通知报文 （仅biz节点的值）
 * @property string $notifyType 通知类型枚举
 * @property string $status 订单状态枚举
 * @property string $merchantNo 联合商户号
 * @property string $terminalNo 联合设备号
 * @property string $orderNo 网关订单号
 * @property string $merOrderNo 商户订单号
 * @property string $channelOrderNo 渠道订单号（微信、支付宝...）
 */
class NotifyMessage extends Config
{
    /**
     * 支付成功后biz节点的报文示例
     */
    //    "terminalNo": "联合设备号",
    //    "orderNo": "网关订单号",
    //    "merOrderNo": "商户订单号",
    //    "bankCardType": "U",
    //    "channelOrderNo": "渠道订单号（微信、支付宝...）",
    //    "paidInfo": {
    //      "billTime": "2024-10-23 16:13:54",
    //      "bankCardType": "U",
    //      "paidTime": "2024-10-23 16:13:54",
    //      "buyerId": "用户openid",
    //      "paidAmount": "1"
    //    },
    //    "buyerId": "用户openid",
    //    "payableAmount": "1",
    //    "orderAmount": "1",
    //    "orderTime": "2024-10-23 16:13:47",
    //    "notifyType": "Paid",
    //    "orderDesc": "ISG--ISG BioSkin 賦活奇....",
    //    "merchantNo": "联合商户号",
    //    "status": "Completed"
}
