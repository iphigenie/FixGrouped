<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alpine\FixGrouped\Plugin\Model\Review\ResourceModel\Rating;
// was namespace Magento\Review\Model\ResourceModel\Rating;
use Alpine\FixGrouped\Helper\Data;

class Collection 
{

    /**
     * @var \Alpine\FixGrouped\Helper\Data
     */
    private $helper;

    /**
     * @param \Alpine\FixGrouped\Helper\Data $helper
     */
    public function __construct(
        \Alpine\FixGrouped\Helper\Data $helper
    ) {
        $this->helper = $helper;
    } 

    public function aroundAddEntitySummaryToItem(\Magento\Review\Model\ResourceModel\Rating\Collection $target, \Closure $ignore, $entityPkValue, $storeId)
    {
        $arrRatingId = $target->getColumnValues('rating_id');
        if (count($arrRatingId) == 0) {
            return $target;
        }

        $connection = $target->getConnection();

        $inCond = $connection->prepareSqlCondition('rating_option_vote.rating_id', ['in' => $arrRatingId]);
        $sumCond = new \Zend_Db_Expr("SUM(rating_option_vote.{$connection->quoteIdentifier('percent')})");
        $countCond = new \Zend_Db_Expr('COUNT(*)');
        $select = $connection->select()->from(
            ['rating_option_vote' => $target->getTable('rating_option_vote')],
            ['rating_id' => 'rating_option_vote.rating_id', 'sum' => $sumCond, 'count' => $countCond]
        )->join(
            ['review_store' => $target->getTable('review_store')],
            'rating_option_vote.review_id=review_store.review_id AND review_store.store_id = :store_id',
            []
        );
        if (!$target->getStoreManager->isSingleStoreMode()) {
            $select->join(
                ['rst' => $target->getTable('rating_store')],
                'rst.rating_id = rating_option_vote.rating_id AND rst.store_id = :rst_store_id',
                []
            );
        }
        $select->join(
            ['review' => $target->getTable('review')],
            'review_store.review_id=review.review_id AND review.status_id=1',
            []
        )->where(
            $inCond
        )->where(
            'rating_option_vote.entity_pk_value in ( :pk_value )'
        )->group(
            'rating_option_vote.rating_id'
        );


        $idlist = $entityPkValue;
        if (  ( is_numeric($entityPkValue)) ) {
            $children = $this->helper->getGroupedChildren($entityPkValue);
            if (!empty($children[3])) 
            {
                foreach(array_keys($children[3]) as $key) {$idlist .= ','.$key;}
            }
            $logger->info('JN FixGrouped\Plugin\Model\Review\ResourceModel\Rating aroundAddEntitySummaryToItem  ['.$idlist.']'); 
        }


        $bind = [':store_id' => (int)$storeId, ':pk_value' => $idlist];
        if (!$target->getStoreManager->isSingleStoreMode()) {
            $bind[':rst_store_id'] = (int)$storeId;
        }

        $data = $target->getConnection()->fetchAll($select, $bind);

        foreach ($data as $item) {
            $rating = $target->getItemById($item['rating_id']);
            if ($rating && $item['count'] > 0) {
                $rating->setSummary($item['sum'] / $item['count']);
            }
        }
        return $target;
    }


}
