<?php
require_once dirname(__FILE__).'/config.tests.inc.php';
require_once $SOURCE_ROOT_PATH.'extlib/simpletest/autorun.php';
ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.$INCLUDE_PATH);

require_once $SOURCE_ROOT_PATH.'tests/classes/class.ThinkUpBasicUnitTestCase.php';
require_once $SOURCE_ROOT_PATH.'webapp/controller/class.ThinkUpController.php';
require_once $SOURCE_ROOT_PATH.'webapp/controller/class.ThinkUpAuthController.php';
require_once $SOURCE_ROOT_PATH.'tests/classes/class.TestAuthController.php';
require_once $SOURCE_ROOT_PATH.'extlib/Smarty-2.6.26/libs/Smarty.class.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.SmartyThinkUp.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.Config.php';
require_once $SOURCE_ROOT_PATH.'webapp/config.inc.php';

/**
 * Test TestAuthController class
 *
 * TestController isn't a real ThinkUp controller, this is just a template for all Controller tests.
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 */
class TestOfTestAuthController extends ThinkUpBasicUnitTestCase {
    /**
     * Constructor
     */
    public function __construct() {
        $this->UnitTestCase('TestController class test');
    }

    public function setUp(){
        parent::setUp();
        $config = Config::getInstance();
        $config->setValue('debug', true);
    }

    public function tearDown(){
        parent::tearDown();
    }

    /**
     * Test constructor
     */
    public function testConstructor() {
        $controller = new TestAuthController(true);
        $this->assertTrue(isset($controller), 'constructor test');
    }

    /**
     * Test controller for non-logged in user
     * @TODO Possibly load the resulting markup as a DOM object and test various children in it;
     * this would enforce valid markup
     */
    public function testControlNotLoggedIn() {
        $config = Config::getInstance();
        $controller = new TestAuthController(true);
        $results = $controller->go();

        $this->assertEqual($results, 'You must be logged in to do this', "not logged in, auth controller output");
    }

    /**
     * Test controller for logged-in user
     * @TODO Possibly load the resulting markup as a DOM object and test various children in it;
     * this would enforce valid markup
     */
    public function testIsLoggedIn() {
        $_SESSION['user'] = 'me@example.com';
        $config = Config::getInstance();
        $config->setValue('site_root_path', '/my/path/to/thinkup/');

        $controller = new TestAuthController(true);
        $results = $controller->go();

        //test if view variables were set correctly
        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('test'), 'Testing, testing, 123');
        $this->assertEqual($v_mgr->getTemplateDataItem('app_title'), 'ThinkUp');

        $this->assertEqual($results,
        '<a href="/my/path/to/thinkup/index.php">ThinkUp</a>: Testing, testing, 123 | Logged in as me@example.com', 
        "auth controller output when logged in");
    }

    /**
     * Test cache key logged in, no params
     */
    public function testCacheKeyLoggedIn() {
        $_SESSION['user'] = 'me@example.com';

        $config = Config::getInstance();
        $config->setValue('cache_pages', true);
        $controller = new TestAuthController(true);
        $results = $controller->go();

        $this->assertEqual($controller->getCacheKeyString(), 'testme.tpl-me@example.com');
    }
}
