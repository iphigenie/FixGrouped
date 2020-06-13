<?php

namespace Alpine\FixGrouped\Plugin\Model\Review\ResourceModel\Review\Product;
// was namespace Magento\Review\Model\ResourceModel\Review\Product;
use Psr\Log\LoggerInterface;
use Alpine\FixGrouped\Helper\Data;

/**
 * Review Product Collection
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @since 100.0.2
 */

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
     * @param string|int $entityId
     * @return $this
     */

    public function aroundAddEntityFilter( \Magento\Review\Model\ResourceModel\Review\Product\Collection $target, \Closure $ignore, $entityId)
    {        
        if ($entityId) {
            $idlist = $entityId;
            $children = $this->helper->getGroupedChildren($entityId);
            if (!empty($children[3])) 
            {
                foreach(array_keys($children[3]) as $key) {$idlist .= ','.$key;}
            }
            $target->getSelect()->where('rt.entity_pk_value in (?)', $idlist);
        } else {
            $target->getSelect()->where('rt.entity_pk_value = ?', $entityId);
        }

        return $target;
    }

}
