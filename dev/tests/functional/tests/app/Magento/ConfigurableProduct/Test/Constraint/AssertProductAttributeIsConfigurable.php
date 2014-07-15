<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright   Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Fixture\FixtureFactory;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\ConfigurableProduct\Test\Fixture\CatalogProductConfigurable;
use Magento\ConfigurableProduct\Test\Page\Adminhtml\CatalogProductNew;

/**
 * Class AssertProductAttributeIsConfigurable
 */
class AssertProductAttributeIsConfigurable extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Attribute frontend label
     *
     * @var string
     */
    protected $attributeFrontendLabel;

    /**
     * Assert check whether the attribute is used to create a configurable products
     *
     * @param CatalogProductAttribute $productAttribute
     * @param CatalogProductAttribute $attribute
     * @param CatalogProductIndex $productGrid
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductNew $newProductPage
     */
    public function processAssert
    (
        CatalogProductAttribute $attribute,
        CatalogProductIndex $productGrid,
        FixtureFactory $fixtureFactory,
        CatalogProductNew $newProductPage,
        CatalogProductAttribute $productAttribute = null
    ) {
        $this->attributeFrontendLabel = ($productAttribute)
            ? $productAttribute->getFrontendLabel()
            : $attribute->getFrontendLabel();
        $productGrid->open();
        $productGrid->getProductBlock()->addProduct('configurable');

        $productConfigurable = $fixtureFactory->createByCode(
            'catalogProductConfigurable',
            [
                'dataSet' => 'default',
                'data' => [
                    'configurable_attributes_data' => [
                        'value' => [
                            'label' => [
                                'value' => $this->attributeFrontendLabel
                            ]
                        ]
                    ]
                ],
            ]
        );

        $productBlockForm = $newProductPage->getForm();
        $productBlockForm->fill($productConfigurable);

        \PHPUnit_Framework_Assert::assertTrue(
            $newProductPage->getForm()->findAttribute($this->attributeFrontendLabel),
            "Product Attribute is absent on Product page."
        );
    }

    /**
     * Attribute '$this->attributeFrontendLabel' present on the product page in variations section
     *
     * @return string
     */
    public function toString()
    {
        return "$this->attributeFrontendLabel attribute present on the product page in variations section";
    }
}
