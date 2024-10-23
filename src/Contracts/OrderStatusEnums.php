<?php

namespace Ledc\XhdPay\Contracts;

/**
 * 订单状态
 */
class OrderStatusEnums
{
    /**
     * 支付中
     */
    const Paymenting = 'Paymenting';
    /**
     * 退货中（包含部分和全额退货）
     */
    const Refunding = 'Refunding';
    /**
     * 完成
     */
    const Completed = 'Completed';
    /**
     * 关闭（包含超时未支付、支付失败、全额退货成功的订单）
     */
    const Closed = 'Closed';
    /**
     * 未知
     */
    const Unknown = 'Unknown';
}
