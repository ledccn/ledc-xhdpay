<?php

namespace Ledc\XhdPay\Contracts;

/**
 * 回调通知处理类
 */
abstract class NotifyHandler
{
    /**
     * @var string
     */
    protected string $attach;
    /**
     * @var NotifyMessage
     */
    protected NotifyMessage $message;

    /**
     * 构造函数
     * @param string $attach 业务附加数据
     * @param NotifyMessage $message 通知报文
     */
    final public function __construct(string $attach, NotifyMessage $message)
    {
        $this->attach = $attach;
        $this->message = $message;
    }

    /**
     * 调度器
     * @return void
     */
    abstract public function dispatcher(): bool;
}
