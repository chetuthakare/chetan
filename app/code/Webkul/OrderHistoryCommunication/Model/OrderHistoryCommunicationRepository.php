<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_OrderHistoryCommunication
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\OrderHistoryCommunication\Model;

use Webkul\OrderHistoryCommunication\Api\Data;
use Webkul\OrderHistoryCommunication\Api\OrderHistoryCommunicationRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Webkul\OrderHistoryCommunication\Model\ResourceModel\OrderHistoryCommunication as ResourceTest;
use Webkul\OrderHistoryCommunication\Model\ResourceModel\OrderHistoryCommunication\CollectionFactory;
use Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;

class OrderHistoryCommunicationRepository implements
    \Webkul\OrderHistoryCommunication\Api\OrderHistoryCommunicationRepositoryInterface
{
        /**
         * @var ResourceBlock
         */
    protected $resource;

    /**
     * @var BlockFactory
     */
    protected $orderHistoryCommunication
    ;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Data\OrderResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Webkul\OrderHistoryCommunication\Api\Data\OrderHistoryCommunicationInterfaceFactory
     */
    protected $orderHistoryInterfaceFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceTest $resource
     * @param OrderHistoryCommunication $orderHistoryCommunication
     * @param OrderHistoryCommunicationInterfaceFactory $orderHistoryInterfaceFactory
     * @param CollectionFactory $collectionFactory
     * @param Data\OrderResultInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceTest $resource,
        OrderHistoryCommunication $orderHistoryCommunication,
        OrderHistoryCommunicationInterfaceFactory $orderHistoryInterfaceFactory,
        CollectionFactory $collectionFactory,
        Data\OrderResultInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->orderHistoryCommunication = $orderHistoryCommunication;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->orderHistoryInterfaceFactory = $orderHistoryInterfaceFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Load data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Webkul\OrderHistoryCommunication\Model\ResourceModel\OrderHistoryCommunication\Collection
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->collectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }

        $searchResults->setTotalCount($collection->getSize());
        $sortOrdersData = $criteria->getSortOrders();
        if ($sortOrdersData) {
            foreach ($sortOrdersData as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }

        $collection->setCurPage($criteria->getCurrentPage());

        $collection->setPageSize($criteria->getPageSize());

        $searchResults->setItems($collection->toArray());
        return $searchResults;
    }
 
    /**
     * get by id
     *
     * @param int $id
     * @return \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication
     */
    public function save(\Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication $subject)
    {
        try {
            $subject->save();
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }
         return $subject;
    }

    /**
     * get by id
     *
     * @param int $id
     * @return \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication
     */
    public function getById($id)
    {
        $model = $this->testFactory->create()->load($id);
        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __(
                    'The CMS block with the "%1" ID doesn\'t exist.',
                    $id
                )
            );
        }
        return $model;
    }

    /**
     * delete
     *
     * @param \Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication $subject
     * @return boolean
     */
    public function delete(\Webkul\OrderHistoryCommunication\Model\OrderHistoryCommunication $subject)
    {
        try {
            $subject->delete();
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
