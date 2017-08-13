<?php
namespace App\Contracts;

use Illuminate\Support\Collection;

/**
 * Bigbigads的统一支付服务
 */
interface PaymentService
{
    const GATEWAY_NONE = "none";
    const GATEWAY_STRIPE = "stripe";
    const GATEWAY_PAYPAL = "paypal";

    /**
     * 同步计划
     * @param Array $gateways 为空值时表示同步所有计划;否则同步指定的计划。每个数组项应从GATEWAY_*中取值。
     * @return void
     * @throw Exception
     */
    public function syncPlans(Array $gateways);

    /**
     * 同步所有支付记录，将本地没有远程有的记录导入到本地数据库。该接口主要解决两个问题：
     * 1. 早前的Paypal并没有在数据库留下支付记录，所以需要遍历Paypal的所有订阅，导出卡住、成功和退款的支付记录;
     * 2. Paypal的Webhook是个不可靠的机制，为了防止此问题，也需要定期执行同步命令;
     * @param Array $gateways 为空值时表示同步所有计划;否则同步指定的计划。每个数组项应从GATEWAY_*中取值。
     */
    public function syncPayments(Array $gateways);


    /**
     * 同步订阅计划, 解决以下问题：
     * 1. 以前的订阅计划的计划数据从Plan对象获取，但Plan对象可能改变其扣款周期等，已经完成的订阅不应该受此影响。
     * 2. 一些新增字段需要根据计划状态填充初始值
     * @param Array $gateways 为空值时表示同步所有计划;否则同步指定的计划。每个数组项应从GATEWAY_*中取值。
     */
    public function syncSubscriptions(Array $gateways);

    /**
     * 用于调试目的日志输出
     * @param mixed $logger 控制台或者Log
     */
    public function setLogger($logger);
}
