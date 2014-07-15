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
namespace Magento\Backup\Helper;

use Magento\Framework\App\Filesystem;
use Magento\Framework\App\State\MaintenanceMode;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backup\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var \Magento\Index\Model\Resource\Process\CollectionFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $processFactory;

    public function setUp()
    {
        $this->filesystem = $this->getMockBuilder('Magento\Framework\App\Filesystem')->disableOriginalConstructor()
            ->getMock();
        $this->processFactory = $this->getMockBuilder('Magento\Index\Model\Resource\Process\CollectionFactory')
            ->setMethods(['create'])->disableOriginalConstructor()->getMock();

        $this->helper = (new \Magento\TestFramework\Helper\ObjectManager($this))
            ->getObject('Magento\Backup\Helper\Data', [
                'filesystem' => $this->filesystem,
                'processFactory' => $this->processFactory
            ]);
    }

    public function testInvalidateIndexer()
    {
        $process = $this->getMockBuilder('Magento\Index\Model\Process')->disableOriginalConstructor()->getMock();
        $process->expects(
            $this->once()
        )->method(
                'changeStatus'
            )->with(
                \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX
            );
        $iterator = $this->returnValue(new \ArrayIterator([$process]));
        $collection = $this->getMockBuilder(
            'Magento\Index\Model\Resource\Process\Collection'
        )->disableOriginalConstructor()->getMock();
        $collection->expects($this->at(0))->method('getIterator')->will($iterator);
        $this->processFactory->expects($this->any())->method('create')->will($this->returnValue($collection));

        $this->helper->invalidateIndexer();
    }

    public function testGetBackupIgnorePaths()
    {
        $this->filesystem->expects($this->any())->method('getPath')
            ->will($this->returnValueMap([
                [MaintenanceMode::FLAG_DIR, MaintenanceMode::FLAG_DIR],
                [Filesystem::SESSION_DIR, Filesystem::SESSION_DIR],
                [Filesystem::CACHE_DIR, Filesystem::CACHE_DIR],
                [Filesystem::LOG_DIR, Filesystem::LOG_DIR],
                [Filesystem::VAR_DIR, Filesystem::VAR_DIR],
            ]));

        $this->assertEquals(
            [
                '.git',
                '.svn',
                'var/maintenance.flag',
                Filesystem::SESSION_DIR,
                Filesystem::CACHE_DIR,
                Filesystem::LOG_DIR,
                Filesystem::VAR_DIR . '/full_page_cache',
                Filesystem::VAR_DIR . '/locks',
                Filesystem::VAR_DIR . '/report',
            ],
            $this->helper->getBackupIgnorePaths()
        );
    }

    public function testGetRollbackIgnorePaths()
    {
        $this->filesystem->expects($this->any())->method('getPath')
            ->will($this->returnValueMap([
                [MaintenanceMode::FLAG_DIR, MaintenanceMode::FLAG_DIR],
                [Filesystem::SESSION_DIR, Filesystem::SESSION_DIR],
                [Filesystem::ROOT_DIR, Filesystem::ROOT_DIR],
                [Filesystem::LOG_DIR, Filesystem::LOG_DIR],
                [Filesystem::VAR_DIR, Filesystem::VAR_DIR],
            ]));

        $this->assertEquals(
            [
                '.svn',
                '.git',
                'var/maintenance.flag',
                Filesystem::SESSION_DIR,
                Filesystem::LOG_DIR,
                Filesystem::VAR_DIR . '/locks',
                Filesystem::VAR_DIR . '/report',
                Filesystem::ROOT_DIR . '/errors',
                Filesystem::ROOT_DIR . '/index.php',
            ],
            $this->helper->getRollbackIgnorePaths()
        );
    }
}
