<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Unique Product URL for Magento 2
 */

namespace Amasty\SeoSingleUrl\Model\UrlRewrite;

use Magento\CatalogUrlRewrite\Model\ResourceModel\Category\Product;

class Storage extends \Magento\UrlRewrite\Model\Storage\DbStorage
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function findAllByDataWithoutCategory(array $data)
    {
        $rows = $this->doFindAllByDataWithoutCategory($data);

        $urlRewrites = [];
        foreach ($rows as $row) {
            $urlRewrites[] = $this->createUrlRewrite($row);
        }

        return $urlRewrites;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindAllByDataWithoutCategory(array $data)
    {
        return $this->connection->fetchAll($this->prepareSelectWithoutCategory($data));
    }

    /**
     * @param array $data
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelectWithoutCategory(array $data)
    {
        $select = $this->connection->select();
        $select->from(['url_rewrite' => $this->resource->getTableName('url_rewrite')])
            ->joinLeft(
                ['relation' => $this->resource->getTableName(Product::TABLE_NAME)],
                'url_rewrite.url_rewrite_id = relation.url_rewrite_id'
            )
            ->where('url_rewrite.entity_id IN (?)', $data['entity_id'])
            ->where('url_rewrite.entity_type = ?', $data['entity_type'])
            ->where('url_rewrite.store_id IN (?)', $data['store_id'])
            ->order('url_rewrite.url_rewrite_id');

        return $select;
    }

    /**
     * @param $data
     *
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
     */
    public function getUrlWithoutCategory($data)
    {
        $select = $this->prepareSelectWithoutCategory($data);
        $select->order('target_path ASC');
        $data = $this->connection->fetchRow($select);

        $urlRewrite = $data ? $this->createUrlRewrite($data) : null;

        return $urlRewrite;
    }
}
