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
        $linktable = $target->getTable('catalog_product_link');
        $target->removeAllFieldsFromSelect();
        $target->addFieldToSelect(
                ['store_id', 'reviews_count' => new \Zend_Db_Expr('SUM(reviews_count)'),'rating_summary' => new \Zend_Db_Expr('SUM(rating_summary*reviews_count)/SUM(reviews_count)')]
        );
        $myentities = "";
        if (is_array($entityId)) {
            $myentities = join(',',$entityId);
        } else {
            $myentities = $entityId;
        }
  
        $target->getSelect()->where(
            "entity_pk_value IN ($myentities) or entity_pk_value in (SELECT linked_product_id FROM ".$linktable." WHERE product_id IN ($myentities) and link_type_id=3)"
            )->where('entity_type = ?', 1)->group(
            ['store_id']
        );
        return $target;
    }

}
