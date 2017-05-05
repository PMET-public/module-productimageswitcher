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
namespace MagentoEse\ProductImageSwitcher\Setup;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Class InstallData
 * @package MagentoEse\ProductImageSwitcher\Setup
 */
class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var eavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Install new attribute
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Exception
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
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
