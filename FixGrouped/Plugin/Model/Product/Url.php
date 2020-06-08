<?php
/**
 * swapping child to parent 
 * 
 */
namespace Alpine\FixGrouped\Plugin\Model\Product;

use Alpine\FixGrouped\Helper\Data;
use Psr\Log\LoggerInterface;

class Url 
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
     * Build and cache url by requested path and parameters
     *
     * @param   \Magento\Catalog\Model\Product\Url $subject
     * @param \Magento\Catalog\Model\Product $product
     * @param array $params
     * @return  string
     */

    public function beforeGetUrl(\Magento\Catalog\Model\Product\Url $subject, \Magento\Catalog\Model\Product $product, array $params = [])
    {     
        //$logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        $params['_ignore_category']= true;
        //$oldid=$product->getId();
        if ($product) {
            $parent = $this->helper->getParent($product);
            $product = $parent ?: $product;
            //$logger->info('JN Alpine\FixGrouped\Plugin\Model\Product URL is start: '.$oldid.' now: '.$product->getId());
        }
        return [$product, $params];
    }
}