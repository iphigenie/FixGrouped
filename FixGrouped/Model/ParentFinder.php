<?php

namespace Alpine\FixGrouped\Model;

class ParentFinder extends \Magento\Catalog\Model\ResourceModel\Product\Link
{
    /**
     * {@inheritdoc}
     */
    public function getParentIds($childId)
    {
        return $this->getParentIdsByChild(
            $childId,
            \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED
        );
    }
}