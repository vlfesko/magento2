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

namespace Magento\UrlRewrite\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Mtf\TestCase\Injectable;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteIndex;
use Magento\UrlRewrite\Test\Page\Adminhtml\UrlrewriteEdit;
use Mtf\Fixture\FixtureFactory;
use Magento\UrlRewrite\Test\Fixture\UrlRewrite;

/**
 * Test Creation for UpdateCategoryUrlRewritesEntity
 *
 * Test Flow:
 *
 * Precondition:
 * 1. SubCategory is created.
 * 2. Category URL Rewrite is created.
 *
 * Steps:
 * 1. Log in to backend as Admin.
 * 2. Go to the Marketing-> SEO & Search->URL Redirects.
 * 3. Click Category URL Rewrite from grid.
 * 4. Edit test value(s) according to dataSet.
 * 5. Click 'Save' button.
 * 6. Perform all asserts.
 *
 * @group URL_Rewrites_(PS)
 * @ZephyrId MAGETWO-24838
 */
class UpdateCategoryUrlRewriteEntityTest extends Injectable
{
    /**
     * Url rewrite index page
     *
     * @var UrlrewriteIndex
     */
    protected $urlRewriteIndex;

    /**
     * Url rewrite edit page
     *
     * @var UrlrewriteEdit
     */
    protected $urlRewriteEdit;

    /**
     * Prepare dataSets and pages
     *
     * @param UrlrewriteIndex $urlRewriteIndex
     * @param UrlrewriteEdit $urlRewriteEdit
     * @param FixtureFactory $fixtureFactory
     * @param CatalogCategory $category
     * @return array
     */
    public function __inject(
        UrlrewriteIndex $urlRewriteIndex,
        UrlrewriteEdit $urlRewriteEdit,
        FixtureFactory $fixtureFactory,
        CatalogCategory $category
    ) {
        $this->urlRewriteIndex = $urlRewriteIndex;
        $this->urlRewriteEdit = $urlRewriteEdit;
        $category->persist();
        $categoryRedirect = $fixtureFactory->createByCode(
            'urlRewrite',
            [
                'dataSet' => 'default',
                'data' => ['id_path' => ['category/' . $category->getId()]]
            ]
        );
        $categoryRedirect->persist();

        return ['categoryRedirect' => $categoryRedirect, 'category' => $category];
    }

    /**
     * Update category URL rewrites
     *
     * @param UrlRewrite $categoryRedirect
     * @param UrlRewrite $urlRewrite
     * @return void
     */
    public function testUpdateCategoryUrlRewrite(UrlRewrite $categoryRedirect, UrlRewrite $urlRewrite)
    {
        //Steps
        $this->urlRewriteIndex->open();
        $filter = ['request_path' => $categoryRedirect->getRequestPath()];
        $this->urlRewriteIndex->getUrlRedirectGrid()->searchAndOpen($filter);
        $this->urlRewriteEdit->getFormBlock()->fill($urlRewrite);
        $this->urlRewriteEdit->getPageMainActions()->save();
    }
}
