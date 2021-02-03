<?php

namespace Alpine\FixGrouped\Plugin\Model\Product\Type;
// was \Magento\GroupedProduct\Model\Product\Type\Grouped ;
use \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection;

class Grouped
{

    /**
     * Retrieve collection of associated products
     *
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped $subject
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function afterGetAssociatedProductCollection(\Magento\GroupedProduct\Model\Product\Type\Grouped $subject, Collection $result)
    {

        $result->addAttributeToSelect('tagline');

        return $result;
    }

}

?>