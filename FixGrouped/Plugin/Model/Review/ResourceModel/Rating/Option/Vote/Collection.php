<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Alpine\FixGrouped\Plugin\Model\Review\ResourceModel\Rating\Option\Vote;
// was Magento\Review\Model\ResourceModel\Rating\Option\Vote;
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
     * Set EntityPk filter
     *
     * @param int $entityId
     * @return $this
     */
    public function aroundSetEntityPkFilter(\Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection $target, \Closure $ignore, $entityId)
    {
        $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        $idlist = $entityId;
        if (  ( is_numeric($entityId)) ) {
            $children = $this->helper->getGroupedChildren($entityId);
            if (!empty($children[3])) 
            {
                foreach(array_keys($children[3]) as $key) {$idlist .= ','.$key;}
            }
            $target->getSelect()->where("entity_pk_value IN (".$idlist.")");
        }
        else  {$target->getSelect()->where("entity_pk_value = ?", $entityId);}
        return $target;
    }

}
