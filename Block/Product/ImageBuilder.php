<?php
/**
 * ClassyLlama_ProductImageSwitcher
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright  Copyright (c) 2017 Classy Llama
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace MagentoEse\ProductImageSwitcher\Block\Product;

use Magento\Catalog\Model\Product;
use \Magento\Catalog\Block\Product\Image;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * Create image block
     *
     * @param Product|null $product
     * @param string|null $imageId
     * @param array|null $attributes
     * @return Image
     */
    public function create(Product $product = null, string $imageId = null, array $attributes = null)
    {
        $product = $product ?? $this->product;
        $imageId = $imageId ?? $this->imageId;
        $attributes = $attributes ?? $this->attributes;
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create();
        $helper->init($product, $imageId);

        $template = $helper->getFrame()
            ? 'Magento_Catalog::product/image.phtml'
            : 'MagentoEse_ProductImageSwitcher::product/image_with_borders.phtml';

        $imagesize = $helper->getResizedImageInfo();

        /*
         * To generate a cached image url, a new image helper is required. The width
         */
        $altImageHelper = $this->helperFactory->create();
        $altImageHelper->init(
            $product,
            'category_page_alt',
            [
                'width' => $helper->getWidth(),
                'height' => $helper->getHeight()
            ]
        );

        $altImageUrl = $altImageHelper->getUrl();
        $altImageUrl = strpos($altImageUrl, '/placeholder/') ? false : $altImageUrl;

        $data = [
            'template' => $template,
            'image_url' => $helper->getUrl(),
            'alt_image_url' => $altImageUrl,
            'width' => $helper->getWidth(),
            'height' => $helper->getHeight(),
            'label' => $helper->getLabel(),
            'ratio' =>  $this->getRatio($helper),
            'custom_attributes' => $this->getCustomAttributes(),
            'resized_image_width' => !empty($imagesize[0]) ? $imagesize[0] : $helper->getWidth(),
            'resized_image_height' => !empty($imagesize[1]) ? $imagesize[1] : $helper->getHeight(),
        ];

        return $this->imageFactory->create($product, $imageId, $data);
    }
}
