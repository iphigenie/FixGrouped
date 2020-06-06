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
        $result->addAttributeToSelect('vintage_single');
        $result->addAttributeToSelect('catalog_name');
        $result->addAttributeToSelect('bottlesize');
        $result->addAttributeToSelect('alcohol');
        $result->addAttributeToSelect('sugar');
        $result->addAttributeToSelect('acidity');
        $result->addAttributeToSelect('boughtin');
        $result->addAttributeToSelect('tasted');
        $result->addAttributeToSelect('keep');
        $result->addAttributeToSelect('mature');
        $result->addAttributeToSelect('is_current');
        $result->addAttributeToSelect('casesize');
        $result->addAttributeToSelect('tastingnotes');
        $result->addAttributeToSelect('tagline');

        return $result;
    }

}

?>