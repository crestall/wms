<?php

/**
 * The redirector class.
 *
 * Provides multiple options for redirection
 *
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class Redirector{

    /**
     * Constructor
     *
     */
    public function __construct(){
    }

    /**
     * Redirect to the given location
     *
     * @param string $location
     */
    public function to($location, $query = ""){

        if(!empty($query)){
            $query = '?' . http_build_query((array)$query, null, '&');
        }

        $response = new Response('', 302, ["Location" => $location . $query]);
        return $response;
    }

    /**
     * Redirect to the given location from the root
     *
     * @param string $location
     */
    public function root($location = "", $query = ""){
        return $this->to(PUBLIC_ROOT . $location, $query);
    }

    /**
     * Redirect to the dashboard
     */
    public function dashboard(){
        return $this->to(PUBLIC_ROOT . "dashboard");
    }

    /**
     * Redirect to the coming soon page
     */
    public function comingSoon(){
        return $this->to(PUBLIC_ROOT . "coming-soon");
    }

    /**
     * Redirect to the login page
     * $redirect_url is to send the user back to where he/she came from after login
     *
     * @param string|null $redirect_url
     */
    public function login($redirect_url = null){
        if(!empty($redirect_url)){
            return $this->to(PUBLIC_ROOT . "?redirect=" . urlencode($redirect_url));
        }else{
            return $this->to(PUBLIC_ROOT);
        }
    }

} 
