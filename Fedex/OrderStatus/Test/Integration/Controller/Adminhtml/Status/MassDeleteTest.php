<?php
declare(strict_types=1);

namespace Fedex\OrderStatus\Test\Integration\Controller\Adminhtml\Status;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\User\Model\UserFactory;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\TestCase;
use Fedex\OrderStatus\Model\StatusFactory;
use Fedex\OrderStatus\Model\ResourceModel\Status\CollectionFactory;

/**
 * Integration test for Fedex\OrderStatus\Controller\Adminhtml\Status\MassDelete
 */
class MassDeleteTest extends TestCase
{
    private StatusFactory $statusFactory;
    private CollectionFactory $collectionFactory;
    private AdminSession $adminSession;
    private Http $request;
    private ResponseHttp $response;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $this->statusFactory = $objectManager->get(StatusFactory::class);
        $this->collectionFactory = $objectManager->get(CollectionFactory::class);
        $this->adminSession = $objectManager->get(AdminSession::class);
        $this->request = $objectManager->get(Http::class);
        $this->response = $objectManager->get(ResponseHttp::class);

        $this->authenticateAdminUser($objectManager);
    }

    /**
     * Authenticate as admin user (required for backend actions).
     */
    private function authenticateAdminUser($objectManager): void
    {
        /** @var UserFactory $userFactory */
        $userFactory = $objectManager->get(UserFactory::class);

        /** @var \Magento\User\Model\User $user */
        $user = $userFactory->create();
        $user->loadByUsername('admin'); // Assumes 'admin' user exists

        $this->adminSession->setUser($user);
    }

    /**
     * Test that MassDelete deletes selected status records.
     *
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testMassDelete(): void
    {
        // Create two test status records
        $status1 = $this->statusFactory->create()->setData([
            'order_id' => '100000001',
            'status' => 'pending',
        ]);
        $status1->save();

        $status2 = $this->statusFactory->create()->setData([
            'order_id' => '100000001',
            'status' => 'in_progress',
        ]);
        $status2->save();

        // Confirm they exist
        $collectionBefore = $this->collectionFactory->create();
        $this->assertGreaterThanOrEqual(2, $collectionBefore->count());

        // Prepare request for mass delete
        $this->request->setParams(['selected' => [$status1->getId(), $status2->getId()]]);
        $this->request->setMethod(Http::METHOD_POST);

        /** @var \Magento\Framework\App\FrontControllerInterface $frontController */
        $frontController = Bootstrap::getObjectManager()->get(\Magento\Framework\App\FrontControllerInterface::class);
        $frontController->dispatch($this->request);

        // Reload collection and verify deletions
        $collectionAfter = $this->collectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => [$status1->getId(), $status2->getId()]]);

        $this->assertCount(0, $collectionAfter);
    }
}
