<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2017 Classy Llama
 */

namespace MagentoEse\ProductImageSwitcher\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var eavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $connection = $setup->getConnection();
        try {
            $connection->beginTransaction();
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

            $eavSetup->addAttribute(
                Product::ENTITY,
                'alt_image',
                [
                    'type' => 'varchar',
                    'label' => 'Alt Image',
                    'input' => 'media_image',
                    'frontend' => 'Magento\Catalog\Model\Product\Attribute\Frontend\Image',
                    'required' => false,
                    'used_in_product_listing' => true,
                    'class' => '',
                    'source' => '',
                    'global' => Attribute::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '',
                    'unique' => false
                ]
            );

            $connection->commit();
        } catch (\Exception $e) {
            // If an error occured rollback the database changes as if they never happened
            $connection->rollback();
            throw $e;
        }
    }
}
