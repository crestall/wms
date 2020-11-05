<?php

/**
 * Comingsoon controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ComingsoonController extends DashboardController
{
    /**
     * show coming soon page
     *
     */
    public function index()
    {
        Config::setJsConfig('curPage', "comingsoon");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/dashboard/", Config::get('VIEWS_PATH') . 'dashboard/comingsoon.php',[

        ]);
    }

}