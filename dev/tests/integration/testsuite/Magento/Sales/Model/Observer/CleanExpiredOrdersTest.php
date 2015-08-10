<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Observer;

use Magento\TestFramework\Helper\Bootstrap;
use \Magento\Sales\Model\Order;

class CleanExpiredOrdersTest extends \PHPUnit_Framework_TestCase
{
    protected function checkFixture()
    {
        /** @var Order $order */
        $order = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        $order->load('100000004', 'increment_id');
        if (!$order->getId()) {
            $this->fail('Fixture failed to create order');
        }
    }

    /**
     * @magentoConfigFixture default sales/orders/delete_pending_after 0
     * @magentoConfigFixture current_store sales/orders/delete_pending_after 0
     * @magentoDataFixture Magento/Sales/_files/order_pending_payment.php
     */
    public function testExecute()
    {
        $this->checkFixture();
        /** @var CleanExpiredOrders $job */
        $job = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Observer\CleanExpiredOrders');
        $job->execute();

        /** @var Order $order */
        $order = Bootstrap::getObjectManager()->create('Magento\Sales\Model\Order');
        $order->load('100000004', 'increment_id');
        $this->assertEquals(Order::STATE_CANCELED, $order->getStatus());
    }
}
