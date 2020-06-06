<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alpine\FixGrouped\Plugin\Model\Review\ResourceModel\Review\Summary;
// was namespace Magento\Review\Model\ResourceModel\Review\Summary;
use Psr\Log\LoggerInterface;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;

class Collection 
{
    /**
     * Add entity filter
     * @param int|string $entityId
     * @param int $entityType
     * @return $this
     */
    public function aroundAddEntityFilter(\Magento\Review\Model\ResourceModel\Review\Summary\Collection $target, \Closure $ignore, $entityId, $entityType = 1)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        //$logger->info('FixGrouped\Plugin\Model\Review\ResourceModel\Review\Summary aroundAddEntityFilter requested for ['.$entityId.']');  

        $linktable = $target->getTable('catalog_product_link');
        $target->removeAllFieldsFromSelect();
        $target->addFieldToSelect(
                ['store_id', 'reviews_count' => new \Zend_Db_Expr('SUM(reviews_count)'),'rating_summary' => new \Zend_Db_Expr('SUM(rating_summary*reviews_count)/SUM(reviews_count)')]
        );
        //$logger->info('FixGrouped\PLUGIN\Model\Review\ResourceModel\Review\Summary  query 1 ['. $target->getSelect().']'); 
        $target->getSelect()->where(
            "entity_pk_value IN ($entityId) or entity_pk_value in (SELECT linked_product_id FROM ".$linktable." WHERE product_id IN ($entityId) and link_type_id=3)"
            )->where('entity_type = ?', 1)->group(
            ['store_id']
        );

        //$logger->info('FixGrouped\PLUGIN\Model\Review\ResourceModel\Review\Summary  query 2 ['. $target->getSelect().']'); 
        return $target;
    }

}
