<?php

/**
 * Errors controller
 *
 * Errors controller can be only accessed from within the application itself,
 * So, any request that has errors as controller will be considered as invalid
 *
 * @see App::isControllerValid()
 *

 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class ErrorsController extends Controller{

    /**
     * Initialization method.
     *
     */
    public function initialize(){ 
    }

    public function NotFound(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "404.php");
    }

    public function Unauthenticated(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "401.php");
    }

    public function Unauthorized(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "403.php");
    }

    public function BadRequest(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "400.php");
    }

    public function System(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "500.php");
    }
}
