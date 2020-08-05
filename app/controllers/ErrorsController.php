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
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "404.php",['pht' => ": Page Not Found"]);
    }

    public function Unauthenticated(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "401.php",['pht' => ": Unauthorized"]);
    }

    public function Unauthorized(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "403.php",['pht' => ": Forbidden"]);
    }

    public function BadRequest(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "400.php",['pht' => ": Bad Request"]);
    }

    public function System(){
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "500.php",['pht' => ": Internal Server Error"]);
    }
}
