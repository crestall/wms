<?php

/**
 * Help Centre controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class HelpcentreController extends Controller
{
    /**
     * show coming soon page
     *
     */
    public function index()
    {
        //set the page name for menu display
        Config::setJsConfig('curPage', 'help-centre-index');
        parent::displayIndex(get_class());
    }

}