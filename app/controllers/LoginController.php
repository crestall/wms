<?php

/**
 * Login controller
 *
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class LoginController extends Controller {

    /**
     * Initialization method.
     * load components, and optionally assign their $config
     *
     */
    public function initialize(){

        $this->loadComponents([
            'Auth',
            'Security'
        ]);
    }

    public function beforeAction(){

        parent::beforeAction();


    }

    /**
     * login form
     *
     */
    public function index(){

        // check first if user is already logged in via session or cookie
        if($this->Auth->isLoggedIn()){
            //echo "going to dashboard";die();
            return $this->redirector->dashboard();

        } else {

            // Clearing the sesion won't allow user(un-trusted) to open more than one login form,
                // as every time the page loads, it generates a new CSRF Token.
            // Destroying the sesion won't allow accessing sesssion data (i.e. $_SESSION["csrf_token"]).
            //echo "going to login";die();
            // get redirect url if any
            $redirect = $this->request->query('redirect');

            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/login/", Config::get('LOGIN_PATH') . "index.php", ['redirect' => $redirect]);
        }
    }

    /**
     * get captcha image for registration form
     *
     * @return Gregwar\Captcha\CaptchaBuilder
     * @see views/login/index.php
     */
    public function getCaptcha(){

        // create a captcha with the Captcha library
        $captcha = new Gregwar\Captcha\CaptchaBuilder;
        $captcha->build();

        // save the captcha characters in session
        Session::set('captcha', $captcha->getPhrase());

        return $captcha;
    }

    /**
     * If password token valid, then show update password form
     *
     */
    public function resetPassword(){

        $userId  = $this->request->query("id");
        $userId  = empty($userId)? null: Encryption::decryptId($this->request->query("id"));
        $token   = $this->request->query("token");

        $result = $this->login->isForgottenPasswordTokenValid($userId, $token);

        if(!$result){
            //$this->resetPasswordToken($userId);
            return $this->error(404);
        }
        else
        {
            Session::set("user_id_reset_password", $userId);
            $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/login/", Config::get('LOGIN_PATH') . 'updatePassword.php');
        }
    }

    /**
     * logout
     *
     */
    public function logOut(){

        $this->login->logOut(Session::getUserId());
        return $this->redirector->login();
    }

}
