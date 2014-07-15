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

/**
 * CatalogInventory Stock Indexers Factory
 */
namespace Magento\CatalogInventory\Model\Resource\Indexer;

class StockFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * Default Stock Indexer resource model name
     *
     * @var string
     */
    protected $_defaultIndexer = 'Magento\CatalogInventory\Model\Resource\Indexer\Stock\DefaultStock';

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new indexer object
     *
     * @param string $indexerClassName
     * @param array $data
     * @return \Magento\CatalogInventory\Model\Resource\Indexer\Stock\StockInterface
     * @throws \InvalidArgumentException
     */
    public function create($indexerClassName = '', array $data = array())
    {
        if (empty($indexerClassName)) {
            $indexerClassName = $this->_defaultIndexer;
        }
        $indexer = $this->_objectManager->create($indexerClassName, $data);
        if (false == $indexer instanceof \Magento\CatalogInventory\Model\Resource\Indexer\Stock\StockInterface) {
            throw new \InvalidArgumentException(
                $indexerClassName .
                ' doesn\'t implement \Magento\CatalogInventory\Model\Resource\Indexer\Stock\StockInterface'
            );
        }
        return $indexer;
    }
}
