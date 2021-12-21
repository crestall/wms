<?php

/**
 * Errors controller
 *
 * Used for Non 200 Code Http responses
 *
 * Errors controller can be only accessed from within the application itself,
 * So, any request that has errors as controller will be considered as invalid
 *
 * @see App::isControllerValid()
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ErrorsController extends Controller{

    /**
     * Initialization method.
     *
     */
    public function initialize(){ 
    }

    public function NotFound(){
        Config::setJsConfig('curPage', "error-404");
        Config::set('curPage', "error-404");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "404.php",[
            'pht'           => ": Page Not Found",
            'error_code'    => 404
        ]);
    }

    public function Unauthenticated(){
        Config::setJsConfig('curPage', "error-401");
        Config::set('curPage', "error-401");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "401.php",[
            'pht'           => ": Unauthorized",
            'error_code'    => 401
        ]);
    }

    public function Unauthorized(){
        Config::setJsConfig('curPage', "error-403");
        Config::set('curPage', "error-403");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "403.php",[
            'pht'           => ": Forbidden",
            'error_code'    => 403
        ]);
    }

    public function BadRequest(){
        Config::setJsConfig('curPage', "error-400");
        Config::set('curPage', "error-400");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "400.php",[
            'pht'           => ": Bad Request",
            'error_code'    => 400
        ]);
    }

    public function System(){
        Config::setJsConfig('curPage', "error-500");
        Config::set('curPage', "error-500");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/errors/", Config::get('ERRORS_PATH') . "500.php",[
            'pht'           => ": Internal Server Error",
            'error_code'    => 500
        ]);
    }
}
