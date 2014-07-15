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
 * Test customer account controller
 */
namespace Magento\Customer\Controller;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AccountTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Controller\Account
     */
    protected $object;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\UrlInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $url;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerHelperMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAccountServiceMock;

    /**
     * List of actions that are allowed for not authorized users
     *
     * @var array
     */
    protected $openActions = array(
        'create',
        'login',
        'logoutsuccess',
        'forgotpassword',
        'forgotpasswordpost',
        'resetpassword',
        'resetpasswordpost',
        'confirm',
        'confirmation',
        'createpassword',
        'createpost',
        'loginpost'
    );

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_formKeyValidator;

    protected function setUp()
    {
        $this->request = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            array('isPost', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam'),
            array(),
            '',
            false
        );
        $this->response = $this->getMock(
            'Magento\Framework\App\ResponseInterface',
            array('setRedirect', 'sendResponse'),
            array(),
            '',
            false
        );
        $this->customerSession = $this->getMock(
            '\Magento\Customer\Model\Session',
            array('isLoggedIn', 'getLastCustomerId', 'getBeforeAuthUrl', 'setBeforeAuthUrl'),
            array(),
            '',
            false
        );
        $this->url = $this->getMockForAbstractClass('\Magento\Framework\UrlInterface');
        $this->objectManager = $this->getMock(
            '\Magento\Framework\ObjectManager\ObjectManager',
            array('get'),
            array(),
            '',
            false
        );
        $this->_formKeyValidator = $this->getMock(
            'Magento\Core\App\Action\FormKeyValidator',
            array(),
            array(),
            '',
            false
        );
        $this->customerHelperMock = $this->getMock(
            'Magento\Customer\Helper\Data',
            array(),
            array(),
            '',
            false
        );
        $this->formFactoryMock = $this->getMock(
            'Magento\Customer\Model\Metadata\FormFactory',
            array(),
            array(),
            '',
            false
        );
        $this->redirectMock = $this->getMockForAbstractClass('Magento\Framework\App\Response\RedirectInterface');
        $this->viewMock = $this->getMockForAbstractClass('Magento\Framework\App\ViewInterface');
        $this->customerAccountServiceMock =
            $this->getMockForAbstractClass('Magento\Customer\Service\V1\CustomerAccountServiceInterface');

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $objectManager->getObject(
            'Magento\Customer\Controller\Account',
            array(
                'request' => $this->request,
                'response' => $this->response,
                'customerSession' => $this->customerSession,
                'url' => $this->url,
                'objectManager' => $this->objectManager,
                'formKeyValidator' => $this->_formKeyValidator,
                'customerHelperData' => $this->customerHelperMock,
                'redirect' => $this->redirectMock,
                'view' => $this->viewMock,
                'customerAccountService' => $this->customerAccountServiceMock,
                ''
            )
        );
    }

    /**
     * @covers \Magento\Customer\Controller\Account::_getAllowedActions
     */
    public function testGetAllowedActions()
    {
        $this->assertAttributeEquals($this->openActions, '_openActions', $this->object);
        /**
         * @TODO: [TD] Protected methods must be tested via public. Eliminate _getAllowedActions method and write test
         *   for dispatch method using this property instead.
         */
        $method = new \ReflectionMethod('Magento\Customer\Controller\Account', '_getAllowedActions');
        $method->setAccessible(true);
        $this->assertEquals($this->openActions, $method->invoke($this->object));
    }

    public function testLoginPostActionWhenRefererSetBeforeAuthUrl()
    {
        $this->_formKeyValidator->expects($this->once())->method('validate')->will($this->returnValue(true));
        $this->objectManager->expects(
            $this->any()
        )->method(
            'get'
        )->will(
            $this->returnValueMap(
                array(
                    array('Magento\Customer\Helper\Data', new \Magento\Framework\Object(array('account_url' => 1))),
                    array(
                        'Magento\Framework\App\Config\ScopeConfigInterface',
                        new \Magento\Framework\Object(array('config_flag' => 1))
                    ),
                    array(
                        'Magento\Core\Helper\Data',
                        $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false)
                    )
                )
            )
        );
        $this->customerSession->expects($this->at(0))->method('isLoggedIn')->with()->will($this->returnValue(0));
        $this->customerSession->expects($this->at(4))->method('isLoggedIn')->with()->will($this->returnValue(1));
        $this->request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            \Magento\Customer\Helper\Data::REFERER_QUERY_PARAM_NAME
        )->will(
            $this->returnValue('referer')
        );
        $this->url->expects($this->once())->method('isOwnOriginUrl')->with();

        $this->object->loginPostAction();
    }

    /**
     * @return void
     */
    public function testCreateActionRegistrationDisabled()
    {
        $this->customerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->customerHelperMock->expects($this->once())
            ->method('isRegistrationAllowed')
            ->will($this->returnValue(false));

        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->response, '*/*', array())
            ->will($this->returnValue(false));

        $this->viewMock->expects($this->never())
            ->method('loadLayout');
        $this->viewMock->expects($this->never())
            ->method('getLayout');
        $this->viewMock->expects($this->never())
            ->method('renderLayout');

        $this->object->createAction();
    }

    /**
     * @return void
     */
    public function testCreateActionRegistrationEnabled()
    {
        $this->customerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->customerHelperMock->expects($this->once())
            ->method('isRegistrationAllowed')
            ->will($this->returnValue(true));

        $this->redirectMock->expects($this->never())
            ->method('redirect');

        $layoutMock = $this->getMock(
            'Magento\Framework\View\Layout',
            array(),
            array(),
            '',
            false
        );
        $layoutMock->expects($this->once())
            ->method('initMessages')
            ->will($this->returnSelf());

        $this->viewMock->expects($this->once())
            ->method('loadLayout')
            ->will($this->returnSelf());
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $this->viewMock->expects($this->once())
            ->method('renderLayout')
            ->will($this->returnSelf());

        $this->object->createAction();
    }

    /**
     * @return void
     */
    public function testCreatePostActionRegistrationDisabled()
    {
        $this->customerSession->expects($this->once())
            ->method('isLoggedIn')
            ->will($this->returnValue(false));

        $this->customerHelperMock->expects($this->once())
            ->method('isRegistrationAllowed')
            ->will($this->returnValue(false));

        $this->redirectMock->expects($this->once())
            ->method('redirect')
            ->with($this->response, '*/*/', array())
            ->will($this->returnValue(false));

        $this->customerAccountServiceMock->expects($this->never())
            ->method('createCustomer');

        $this->object->createPostAction();
    }
}
