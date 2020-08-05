<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace MagentoEse\ProductImageSwitcher\Block\Product;

use \Magento\Catalog\Block\Product\Image as ImageBlock;
use \Magento\Catalog\Model\View\Asset\ImageFactory as AssetImageFactory;
use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Model\Product\Image\ParamsBuilder;
use \Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use \Magento\Framework\ObjectManagerInterface;
use \Magento\Framework\View\ConfigInterface;
use \Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Create imageBlock from product and view.xml
 */
class ImageConstructor extends \Magento\Catalog\Block\Product\ImageFactory
{
    /**
     * @var ConfigInterface
     */
    protected $presentationConfig;

    /**
     * @var AssetImageFactory
     */
    protected $viewAssetImageFactory;

    /**
     * @var ParamsBuilder
     */
    protected $imageParamsBuilder;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var PlaceholderFactory
     */
    protected $viewAssetPlaceholderFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ConfigInterface $presentationConfig
     * @param AssetImageFactory $viewAssetImageFactory
     * @param PlaceholderFactory $viewAssetPlaceholderFactory
     * @param ParamsBuilder $imageParamsBuilder
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigInterface $presentationConfig,
        AssetImageFactory $viewAssetImageFactory,
        PlaceholderFactory $viewAssetPlaceholderFactory,
        ParamsBuilder $imageParamsBuilder
    ) {
        // all these fields were private in the parent class, copying forward
        $this->objectManager = $objectManager;
        $this->presentationConfig = $presentationConfig;
        $this->viewAssetPlaceholderFactory = $viewAssetPlaceholderFactory;
        $this->viewAssetImageFactory = $viewAssetImageFactory;
        $this->imageParamsBuilder = $imageParamsBuilder;
    }

    /**
     * Retrieve image custom attributes for HTML element
     *
     * Copied method from \Magento\Catalog\Block\Product\ImageFactory since it was private
     *
     * @param array $attributes
     * @return string
     */
    protected function getStringCustomAttributes(array $attributes): string
    {
        $result = [];
        foreach ($attributes as $name => $value) {
            $result[] = $name . '="' . $value . '"';
        }
        return !empty($result) ? implode(' ', $result) : '';
    }
    private function filterCustomAttributes(array $attributes): array
    {
        if (isset($attributes['class'])) {
            unset($attributes['class']);
        }
        return $attributes;
    }
    /**
     * Calculate image ratio
     *
     * Copied method from \Magento\Catalog\Block\Product\ImageFactory since it was private
     *
     * @param $width
     * @param $height
     * @return float
     */
    protected function getRatio($width, $height): float
    {
        if ($width && $height) {
            return $height / $width;
        }
        return 1.0;
    }

    /**
     * @param Product $product
     *
     * Copied method from \Magento\Catalog\Block\Product\ImageFactory since it was private
     *
     * @param string $imageType
     * @return string
     */
    protected function getLabel(Product $product, string $imageType): string
    {
        $label = $product->getData($imageType . '_' . 'label');
        if (empty($label)) {
            $label = $product->getName();
        }
        return (string) $label;
    }

    /**
     * Create image block from product
     *
     * @param Product $product
     * @param string $imageId
     * @param array|null $attributes
     * @return ImageBlock
     */
    public function create(Product $product, string $imageId, array $attributes = null): ImageBlock
    {
        $viewImageConfig = $this->presentationConfig->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            ImageHelper::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );

        $imageMiscParams = $this->imageParamsBuilder->build($viewImageConfig);
        $originalFilePath = $product->getData($imageMiscParams['image_type']);

        if ($originalFilePath === null || $originalFilePath === 'no_selection') {
            $imageAsset = $this->viewAssetPlaceholderFactory->create(
                [
                    'type' => $imageMiscParams['image_type']
                ]
            );
        } else {
            $imageAsset = $this->viewAssetImageFactory->create(
                [
                    'miscParams' => $imageMiscParams,
                    'filePath' => $originalFilePath,
                ]
            );
        }

        $args = [
            'template' => 'Magento_Catalog::product/image_with_borders.phtml',
            'image_url' => $imageAsset->getUrl(),
            'width' => $imageMiscParams['image_width'],
            'height' => $imageMiscParams['image_height'],
            'label' => $this->getLabel($product, $imageMiscParams['image_type']),
            'ratio' => $this->getRatio($imageMiscParams['image_width'], $imageMiscParams['image_height']),
            'custom_attributes' => $this->filterCustomAttributes($attributes),
            'product_id' => $product->getId()
        ];
        // BEGIN EDIT
        $args = \array_merge($args, $attributes);
        $data = [
            'data' => $args
        ];
        // END EDIT

        return $this->objectManager->create(ImageBlock::class, $data);
    }
}
