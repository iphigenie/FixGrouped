<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alpine\FixGrouped\Plugin\Model\Review\ResourceModel\Review;
// was namespace Magento\Review\Model\ResourceModel\Review;
use Psr\Log\LoggerInterface;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;
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


    /**
     * Add entity filter
     *
     * @param int|string $entity
     * @param int|string $pkValue
     * @return $this
     */
    public function aroundAddEntityFilter(\Magento\Review\Model\ResourceModel\Review\Collection $target, \Closure $ignore, $entity, $pkValue)
    {

        $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        $logger->info('JN FixGrouped\Plugin\Model\Review\ResourceModel\Review aroundAddEntityFilter for ['.$entity.' '.$pkValue.']');  

        $reviewEntityTable = $target->getTable('review_entity');
        if (is_numeric($entity)) {
            $target->addFilter('entity', $target->getConnection()->quoteInto('main_table.entity_id=?', $entity), 'string');
        } elseif (is_string($entity)) {
            $target->getSelect()->join(
                $reviewEntityTable,
                'main_table.entity_id=' . $reviewEntityTable . '.entity_id',
                ['entity_code']
            );
            $target->addFilter(
                'entity',
                $target->getConnection()->quoteInto($reviewEntityTable . '.entity_code=?', $entity),
                'string'
            );
        }

        $idlist = $pkValue;
        if ( (is_string($entity) && $entity=='product') || ( is_numeric($entity) && $entity==1) ) {
            // should i test  is_numeric($pkValue)?
            $children = $this->helper->getGroupedChildren($pkValue);
            if (!empty($children[3])) 
            {
                foreach(array_keys($children[3]) as $key) {$idlist .= ','.$key;}
            }
            $logger->info('JN FixGrouped\Plugin\Model\Review\ResourceModel\Review aroundAddEntityFilter  ['.$idlist.']'); 
        }

        $target->addFilter(
            'entity_pk_value',
            'main_table.entity_pk_value in ('.$idlist.')',
            'string'
        );
        return $target;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param int $parentId
     * @return array
     */
    /*public function getGroupedChildren($parentId) 
    {
        $groupedworker = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\GroupedProduct\Model\ResourceModel\Product\Link::class);

        return $groupedworker->getChildrenIds(
            $parentId,
            \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED
        );
    }*/

}
