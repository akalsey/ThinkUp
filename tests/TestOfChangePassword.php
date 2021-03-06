<?php
require_once dirname(__FILE__).'/config.tests.inc.php';
require_once $SOURCE_ROOT_PATH.'extlib/simpletest/autorun.php';
require_once $SOURCE_ROOT_PATH.'extlib/simpletest/web_tester.php';
ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.$INCLUDE_PATH);

require_once $SOURCE_ROOT_PATH.'tests/classes/class.ThinkUpWebTestCase.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.User.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.FollowMySQLDAO.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.Session.php';


class TestOfChangePassword extends ThinkUpWebTestCase {

    function setUp() {
        parent::setUp();

        //Add owner
        $session = new Session();
        $cryptpass = $session->pwdcrypt("secretpassword");
        $q = "INSERT INTO tu_owners (id, email, pwd, is_activated) VALUES (1, 'me@example.com', '".$cryptpass."', 1)";
        $this->db->exec($q);

        //Add instance
        $q = "INSERT INTO tu_instances (id, network_user_id, network_username, is_public) VALUES (1, 1234, 'thinkupapp', 1)";
        $this->db->exec($q);

        //Add instance_owner
        $q = "INSERT INTO tu_owner_instances (owner_id, instance_id) VALUES (1, 1)";
        $this->db->exec($q);
    }

    function tearDown() {
        parent::tearDown();
    }


    function testChangePasswordSuccess() {
        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me@example.com');
        $this->setField('pwd', 'secretpassword');

        $this->click("Log In");
        $this->assertTitle('Private Dashboard | ThinkUp');
        $this->assertText('Logged in as: me@example.com');

        $this->click("Configuration");
        $this->assertText('Your ThinkUp Password');
        $this->setField('oldpass', 'secretpassword');
        $this->setField('pass1', 'secretpassword1');
        $this->setField('pass2', 'secretpassword1');
        $this->click('Change password');
        $this->assertText('Your password has been updated.');

        $this->click("Log Out");
        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me@example.com');
        $this->setField('pwd', 'secretpassword1');

        $this->click("Log In");
        $this->assertTitle('Private Dashboard | ThinkUp');
        $this->assertText('Logged in as: me@example.com');
    }

    function testChangePasswordWrongExistingPassword() {
        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me@example.com');
        $this->setField('pwd', 'secretpassword');

        $this->click("Log In");
        $this->assertTitle('Private Dashboard | ThinkUp');
        $this->assertText('Logged in as: me@example.com');

        $this->click("Configuration");
        $this->assertText('Your ThinkUp Password');
        $this->setField('oldpass', 'secretpassworddd');
        $this->setField('pass1', 'secretpassword1');
        $this->setField('pass2', 'secretpassword1');
        $this->click('Change password');
        $this->assertText('Old password does not match or empty.');
    }

    function testChangePasswordEmptyExistingPassword() {
        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me@example.com');
        $this->setField('pwd', 'secretpassword');

        $this->click("Log In");
        $this->assertTitle('Private Dashboard | ThinkUp');
        $this->assertText('Logged in as: me@example.com');

        $this->click("Configuration");
        $this->assertText('Your ThinkUp Password');
        $this->setField('pass1', 'secretpassword1');
        $this->setField('pass2', 'secretpassword1');
        $this->click('Change password');
        $this->assertText('Old password does not match or empty.');
    }

    function testChangePasswordNewPasswordsDontMatch() {
        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me@example.com');
        $this->setField('pwd', 'secretpassword');

        $this->click("Log In");
        $this->assertTitle('Private Dashboard | ThinkUp');
        $this->assertText('Logged in as: me@example.com');

        $this->click("Configuration");
        $this->assertText('Your ThinkUp Password');
        $this->setField('oldpass', 'secretpassword');
        $this->setField('pass1', 'secretpassword1');
        $this->setField('pass2', 'secretpassword2');
        $this->click('Change password');
        $this->assertText('New passwords did not match. Your password has not been changed.');
    }

    function testChangePasswordNewPasswordsNotLongEnough() {
        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me@example.com');
        $this->setField('pwd', 'secretpassword');

        $this->click("Log In");
        $this->assertTitle('Private Dashboard | ThinkUp');
        $this->assertText('Logged in as: me@example.com');

        $this->click("Configuration");
        $this->assertText('Your ThinkUp Password');
        $this->setField('oldpass', 'secretpassword');
        $this->setField('pass1', 'dd');
        $this->setField('pass2', 'dd');
        $this->click('Change password');
        $this->assertText('New password must be at least 5 characters. Your password has not been changed.');
    }
}