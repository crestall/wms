<?php

/**
 * Help Centre controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class HelpCentreController extends Controller
{
    /**
     * show coming soon page
     *
     */
    public function index()
    {
        Config::setJsConfig('curPage', 'help-centre-index');
        Config::set('curPage', "help-centre-index");
        //return $this->comingSoon();
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/help-centre/", Config::get('VIEWS_PATH') . 'help-centre/index.php',[

        ]);
    }

    public function ordersHelp()
    {
        Config::setJsConfig('curPage', 'orders-help');
        //Config::set('curPage', "help-centre-index");
        return $this->comingSoon();
    }

    private function comingSoon()
    {
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dashboard/", Config::get('VIEWS_PATH') . 'dashboard/comingsoon.php',[]);
    }

    public function isAuthorized()
    {
        return true;
    }

}