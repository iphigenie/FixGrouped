<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alpine\FixGrouped\Plugin\Model\Review\ResourceModel\Review;
// was namespace Magento\Review\Model\ResourceModel\Review;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DB\Select;

/**
 * Review summary resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Summary 
{
    /**
     * Append review summary fields to product collection
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @param string $storeId
     * @param string $entityCode
     * @return Summary
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundAppendSummaryFieldsToCollection(
        \Magento\Review\Model\ResourceModel\Review\Summary $target,
        \Closure $ignore,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        string $storeId,
        string $entityCode
    ) {
        if (!$productCollection->isLoaded()) {
            
            $summaryEntitySubSelect = $target->getConnection()->select();
            $summaryEntitySubSelect
                ->from(
                    ['review_entity' => $target->getTable('review_entity')],
                    ['entity_id']
                )->where(
                    'entity_code = ?',
                    $entityCode
                );

            $aggregatedSummarySubselect1 = $target->getConnection()->select();
            $aggregatedSummarySubselect1->from(
                ['review_summary' => $target->getMainTable()],
                ['entity_pk_value',
                'entity_type',
                'reviews_count',
                'rating_summary',
                'store_id']
            );
            $aggregatedSummarySubselect2 = $target->getConnection()->select();
            $aggregatedSummarySubselect2->from(
                ['c' => $target->getTable('catalog_product_link')],
                ['entity_pk_value'=>'product_id']
            )->joinLeft(['s' => $target->getMainTable()],
                        new \Zend_Db_Expr(
                            " s.entity_pk_value = c.linked_product_id AND c.link_type_id=3"
                        ),
                    ['entity_type',
                    'reviews_count',
                    'rating_summary',
                    'store_id'])->group(
                        ['store_id','entity_type','product_id']
                    );
            
            $aggregatedSummarySelect = $target->getConnection()->select()->union([$aggregatedSummarySubselect1, $aggregatedSummarySubselect2], Select::SQL_UNION_ALL); 

            $aggregatedSummary=$target->getConnection()->select();
            $aggregatedSummary->from(
                ['review_summary' =>$aggregatedSummarySelect],
                ['entity_pk_value',
                'entity_type',
                'reviews_count' => new \Zend_Db_Expr("SUM(reviews_count)"),
                'rating_summary' => new \Zend_Db_Expr("SUM(`rating_summary`*`reviews_count`)/SUM(`reviews_count`)"),
                'store_id'
                ])->group(
                    ['store_id','entity_type','entity_pk_value']
                );

            $joinCond = new \Zend_Db_Expr(
                "e.entity_id = review_summary.entity_pk_value AND review_summary.store_id = {$storeId}"
                . " AND review_summary.entity_type = ({$summaryEntitySubSelect})"
            );
            $productCollection->getSelect()
                ->joinLeft(
                    ['review_summary' =>$aggregatedSummary], //['review_summary' => $this->getMainTable()],
                    $joinCond,
                    [
                        'reviews_count' => new \Zend_Db_Expr("IFNULL(review_summary.reviews_count, 0)"),
                        'rating_summary' => new \Zend_Db_Expr("IFNULL(review_summary.rating_summary, 0)")
                    ]
                );
        }

        return $target;
    }
}
