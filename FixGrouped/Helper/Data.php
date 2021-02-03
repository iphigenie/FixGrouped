<?php

namespace Alpine\FixGrouped\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\GroupedProduct\Model\ResourceModel\Product\Link;
use Alpine\FixGrouped\Model\ParentFinder;

class Data extends AbstractHelper
{
    /**
     * @var \Alpine\FixGrouped\Model\ParentFinder
     */
    protected $parentFinder;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface    $productRepository
     * @param Context                                     $context
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Alpine\FixGrouped\Model\ParentFinder $parentFinder,
        Context $context
    ) {
        $this->productRepository = $productRepository;
        $this->parentFinder = $parentFinder;
        parent::__construct($context);
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
    public function getGroupedChildren($parentId) 
    {
        $groupedworker = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\GroupedProduct\Model\ResourceModel\Product\Link::class);

        return $groupedworker->getChildrenIds(
            $parentId,
            \Magento\GroupedProduct\Model\ResourceModel\Product\Link::LINK_TYPE_GROUPED
        );
    }

    /**
     * Get current product parent URL
     *
     * @return string
     */
    public function getProductFixedUrl($product)
    {

        if ($product) {
            $parent = $this->getParent($product);
            $product = $parent ?: $product;
        }

        return $product
            ? $product->getUrlModel()->getUrl(
                $product,
                ['_ignore_category' => true]
            )
            : '';
    }    

    /**
     * Get current product parent URL
     *
     * @return string
     */
    public function getProductUrl($product)
    {

        if ($product) {
            $parent = $this->getParent($product);
            $product = $parent ?: $product;
        }

        return $product
            ? $product->getUrlModel()->getUrl(
                $product,
                ['_ignore_category' => true]
            )
            : '';
    }    

    public function getParent($child)
    {
            //$allowedTypes = array('grouped'); // hard coded below in parentfinder as only for grouped
            $parentIds = $this->parentFinder->getParentIds($child->getId());
            if ($parent = $this->getFirstAvailableProduct($parentIds)) {
                return $parent;
            }
        return null;
    }

    /**
     * @param  array  $productIds
     * @return [type]
     */
    protected function getFirstAvailableProduct(array $productIds)
    {
        foreach ($productIds as $productId) {
            try {
                $product = $this->productRepository->getById($productId);
                if ($product->isVisibleInSiteVisibility()) {
                    return $product;
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }
        return null;
    }    

}
