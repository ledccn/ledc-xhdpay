<?php

namespace Ledc\XhdPay\Contracts;

/**
 * 通知类型
 */
class NotifyTypeEnums
{
    /**
     * 支付
     */
    const Paid = 'Paid';
    /**
     * 关闭/撤销
     */
    const Closed = 'Closed';
    /**
     * 退货
     */
    const Refund = 'Refund';
}
