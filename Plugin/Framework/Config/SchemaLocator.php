<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2017 Classy Llama
 */

namespace MagentoEse\ProductImageSwitcher\Plugin\Framework\Config;

class SchemaLocator {
    /**
     * @var \Magento\Framework\Config\Dom\UrnResolver
     */
    protected $urnResolver;

    /**
     * SchemaLocator constructor.
     * @param \Magento\Framework\Config\Dom\UrnResolver $urnResolver
     */
    public function __construct(\Magento\Framework\Config\Dom\UrnResolver $urnResolver)
    {
        $this->urnResolver = $urnResolver;
    }

    /**
     * After Get Schema
     *
     * Adding an <image> node of a type different than what is included in core Magento will result in the xml file
     * being considered invalid. To resolve this, the view.xsd file must include the new type. This changes the XSD file
     * to the one in this module that includes the new image type.
     *
     * @param \Magento\Framework\Config\SchemaLocator $schemaLocator
     * @param string $result
     * @return array
     */
    public function afterGetSchema(\Magento\Framework\Config\SchemaLocator $subject, $result)
    {
        return $this->urnResolver->getRealPath('urn:magento:module:MagentoEse_ProductImageSwitcher:etc/view.xsd');
    }
}
