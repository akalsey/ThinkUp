<?php
/**
 * ThinkUp Authorized Controller
 *
 * Parent controller for all logged-in user-only actions
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 *
 */
abstract class ThinkUpAuthController extends ThinkUpController {
    public function __construct($session_started=false) {
        parent::__construct($session_started);
    }

    public function control() {
        if ($this->isLoggedIn()) {
            return $this->authControl();
        } else {
            return $this->bounce();
        }
    }

    protected function bounce() {
        //@TODO bounce to sign in page and bounce back to original action once signed in
        if (get_class($this)=='PrivateDashboardController' || get_class($this)=='PostController') {
            $controller = new PublicTimelineController(true);
            return $controller->go();
        } else {
            return "You must be logged in to do this";
        }

    }
}