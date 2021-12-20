<?php

/**
 * Contact controller
 *

 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class ContactController extends Controller
{
    /**
     * Generic Contact Us
     *
     */
    public function contactUs()
    {
        Config::setJsConfig('curPage', "contact-us");
        Config::set('curPage', "contact-us");
        $this->view->renderWithLayouts(Config::get('VIEWS_PATH') . "layout/contact/", Config::get('VIEWS_PATH') . 'contact/contactUs.php',[
            'pht'           =>  ": Contact us",
            'page_title'    =>  "Contact us"
        ]);
    }

    public function isAuthorized(){
        return true;
    }
}