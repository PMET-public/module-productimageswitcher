<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2017 Classy Llama
 */
namespace MagentoEse\ProductImageSwitcher\Block\Product;

class ImageBuilder extends \Magento\Catalog\Block\Product\ImageBuilder
{
    /**
     * Create image block
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function create()
    {
        /** @var \Magento\Catalog\Helper\Image $helper */
        $helper = $this->helperFactory->create()
            ->init($this->product, $this->imageId);

        $template = $helper->getFrame()
            ? 'Magento_Catalog::product/image.phtml'
            : 'MagentoEse_ProductImageSwitcher::product/image_with_borders.phtml';

        $imagesize = $helper->getResizedImageInfo();

        /*
         * To generate a cached image url, a new image helper is required. The width
         */
        $altImageHelper = $this->helperFactory->create()
            ->init(
                $this->product,
                'category_page_alt',
                [
                    'width' => $helper->getWidth(),
                    'height' => $helper->getHeight()
                ]
            );

        $altImageUrl = $altImageHelper->getUrl();
        $altImageUrl = strpos($altImageUrl, '/placeholder/') ? false : $altImageUrl;

        $data = [
            'data' => [
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
            ],
        ];

        return $this->imageFactory->create($data);
    }
}
