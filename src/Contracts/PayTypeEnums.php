<?php

namespace Ledc\XhdPay\Contracts;

/**
 * 支付类型
 */
class PayTypeEnums
{
    /**
     * 微信小程序
     */
    const WXMini = 'WXMini';
    /**
     * 微信公众号
     */
    const MP = 'MP';
    /**
     * 支付宝
     */
    const ALIPAY  = 'ALIPAY ';
    /**
     * 聚合收银台
     */
    const cashierPay  = 'cashierPay';
}
