<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package SEO Toolkit Base for Magento 2
 */

namespace Amasty\SeoToolkitLite\Test\Unit\Model\Redirect;

use Amasty\SeoToolkitLite\Api\Data\RedirectInterface;
use Amasty\SeoToolkitLite\Model\Redirect\RedirectGetter;
use Amasty\SeoToolkitLite\Model\ResourceModel\Redirect\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test class for the RedirectGetter class.
 */
class RedirectGetterTest extends TestCase
{
    /**
     * @var RedirectGetter
     */
    private $redirectGetter;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    protected function setUp(): void
    {
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $storeManagerMock = $this->getMockBuilder(StoreManager::class)
            ->setMethods(['getStore', 'getId'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $storeManagerMock->method('getStore')->willReturnSelf();
        $storeManagerMock->method('getId')->willReturn(1);

        $this->redirectGetter = new RedirectGetter(
            $this->collectionFactoryMock,
            $storeManagerMock
        );
    }

    /**
     * Test getRedirect method with a valid redirect.
     *
     * @dataProvider redirectValidData
     */
    public function testGetRedirectFound(string $pattern, string $request): void
    {
        $redirectMock = $this->getRedirectMock($pattern);

        // Set up the expectations
        $this->prepareCollection($redirectMock);

        // Call the method under test
        $result = $this->redirectGetter->getRedirect($request);

        // Assert that the result is the expected redirect object
        $this->assertSame($redirectMock, $result);
    }

    public function redirectValidData(): array
    {
        return [
            ['/aaa/bbb/ccc/', '/aaa/bbb/ccc'],
            ['/bbb/*', '/bbb/sss/'],
            ['/old-path', '/old-path'],
            ['old-path', '/old-path'],
            ['old-path/pa*', '/old-path/path'],
            ['some*', '/some/long/path'],
            ['some*', '/some?data=1&test[v]=test'],
            ['te*st*', '/teAAAstBBB'],
            ['/level*/test', '/level1/test'],
            ['/path?data=1&test[v]=test', '/path?data=1&test[v]=test'],
            //['/path?data=*', '/path?data=1&test[v]=test&isAjax'], TODO fix probably bag
        ];
    }

    /**
     * Test getRedirect method with an invalid redirect.
     *
     * @dataProvider redirectInvalidData
     */
    public function testGetRedirectWithInvalidRedirect(string $pattern, string $request): void
    {
        $redirectMock = $this->getRedirectMock($pattern);

        // Set up the expectations
        $this->prepareCollection($redirectMock);

        // Call the method under test
        $result = $this->redirectGetter->getRedirect($request);

        // Assert that the result is null
        $this->assertNull($result);
    }

    public function redirectInvalidData(): array
    {
        return [
            ['/old-path', '/new-path'],
            ['/some-path', '/some-pathh'],
            ['some-path', 'ssome-path'],
            ['eve*', '/reve'],
            ['/OLD_PATH', '/old-path'],
            ['*test', '/level1/test'],
            ['/level*/test', '/level1/level2/test'],
        ];
    }

    private function getRedirectMock($pattern)
    {
        $redirectMock = $this->createMock(RedirectInterface::class);
        $redirectMock->expects($this->once())
            ->method('getRequestPath')
            ->willReturn($pattern);

        return $redirectMock;
    }

    private function prepareCollection($redirectMock): void
    {
        $collectionMock = $this->createMock(\Amasty\SeoToolkitLite\Model\ResourceModel\Redirect\Collection::class);

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $collectionMock->method('addFieldToFilter')
            ->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('addStoreFilter')
            ->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('setOrders')
            ->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$redirectMock]));
    }
}
