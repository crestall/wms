<?php

/**
 * Session Class
 *
 
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class Session{

    /**
     * constructor for Session Object.
     *
     * @access private
     */
    private function __construct(){}

    /**
     * Starts the session if not started yet.
     *
     * @access public
     *
     */
    public static function init(){

        if (session_status() == PHP_SESSION_NONE) {     // if (session_id() == '')
            session_start();
        }
    }

    /**
     * Checks if session data exists and valid or not.
     *
     * @access public
     * @static static method
     * @param  string $ip
     * @param  string $userAgent
     * @return boolean
     *
     */
    public static function isSessionValid($ip, $userAgent){

        $isLoggedIn  = self::getIsLoggedIn();
        $userId      = self::getUserId();
        $userRole    = self::getUserRole();

        // 1. check if there is any data in session
        if(empty($isLoggedIn) || empty($userId) || empty($userRole)){
            return false;
        }

        /*if(!self::isConcurrentSessionExists()){
            self::remove();
            return false;
        }*/

        // 2. then check ip address and user agent
        if(!self::validateIPAddress($ip) || !self::validateUserAgent($userAgent)) {
            Logger::log("SESSION", "current session is invalid", __FILE__, __LINE__);
            self::remove();
            return false;
        }

        // 3. check if session is expired
        if(!self::validateSessionExpiry()){
            self::remove();
            return false;
        }

        return true;
    }

    /**
     * Get IsLoggedIn value(boolean)
     *
     * @access public
     * @static static method
     * @return boolean
     *
     */
    public static function getIsLoggedIn(){
        return empty($_SESSION["is_logged_in"]) || !is_bool($_SESSION["is_logged_in"]) ? false : $_SESSION["is_logged_in"];
    }

    /**
     * Get User ID.
     *
     * @access public
     * @static static method
     * @return string|null
     *
     */
    public static function getUserId(){
        return empty($_SESSION["user_id"]) ? null : (int)$_SESSION["user_id"];
    }

    public static function isAdminUser()
    {
        return empty($_SESSION["is_admin_user"]) || !is_bool($_SESSION["is_admin_user"]) ? false : $_SESSION["is_admin_user"];
    }

    public static function isWarehouseUser()
    {
        return empty($_SESSION["is_warehouse_user"]) || !is_bool($_SESSION["is_warehouse_user"]) ? false : $_SESSION["is_warehouse_user"];
    }

    public static function isProductionUser()
    {
        return empty($_SESSION["is_production_user"]) || !is_bool($_SESSION["is_production_user"]) ? false : $_SESSION["is_production_user"];
    }

    public static function isDeliveryClientUser()
    {
        return empty($_SESSION["is_delivery_client"]) || !is_bool($_SESSION["is_delivery_client"]) ? false : $_SESSION["is_delivery_client"];

    }

    /**
     * Get User Name.
     *
     * @access public
     * @static static method
     * @return string|null
     *
     */
    public static function getUsersName(){
        return empty($_SESSION["users_name"]) ? null : $_SESSION["users_name"];
    }

    /**
     * Get User Role
     *
     * @access public
     * @static static method
     * @return string|null
     *
     */
    public static function getUserRole(){
        return empty($_SESSION["role"]) ? null : $_SESSION["role"];
    }

    public static function getUserClientId(){
        return empty($_SESSION["client_id"]) ? 0 : $_SESSION["client_id"];
    }

    /**
     * Get CSRF Token
     *
     * @access public
     * @static static method
     * @return string|null
     *
     */
    public static function getCsrfToken(){
        return empty($_SESSION["csrf_token"]) ? null : $_SESSION["csrf_token"];
    }

    /**
     * Get CSRF Token generated time
     *
     * @access public
     * @static static method
     * @return string|null
     *
     */
    public static function getCsrfTokenTime(){
        return empty($_SESSION["csrf_token_time"]) ? null : $_SESSION["csrf_token_time"];
    }

    /**
     * set session key and value
     *
     * @access public
     * @static static method
     * @param $key
     * @param $value
     *
     */
    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }

    /**
     * get session value by $key
     *
     * @access public
     * @static static method
     * @param  $key
     * @return mixed
     *
     */
    public static function get($key){
        return array_key_exists($key, $_SESSION)? $_SESSION[$key]: null;
    }

    /**
     * get session value by $key and destroy it
     *
     * @access public
     * @static static method
     * @param  $key
     * @return mixed
     *
     */
    public static function getAndDestroy($key){

        if(array_key_exists($key, $_SESSION)){

            $value = $_SESSION[$key];
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);

            return $value;
        }

        return null;
    }

    public static function destroy($key){

        if(array_key_exists($key, $_SESSION)){
            $_SESSION[$key] = null;
            unset($_SESSION[$key]);
        }
        return null;
    }

    /**
     * matches current IP Address with the one stored in the session
     *
     * @access public
     * @static static method
     * @param  string $ip
     * @return bool
     *
     */
    private static function validateIPAddress($ip){

        if(!isset($_SESSION['ip']) || !isset($ip)) {
            return false;
        }

        return $_SESSION['ip'] === $ip;
    }

    /**
     * matches current user agent with the one stored in the session
     *
     * @access public
     * @static static method
     * @param  string $userAgent
     * @return bool
     *
     */
    private static function validateUserAgent($userAgent){

        if(!isset($_SESSION['user_agent']) || !isset($userAgent)) {
            return false;
        }

        return $_SESSION['user_agent'] === $userAgent;
    }

    /**
     * checks if session has been expired
     *
     * @access public
     * @static static method
     * @return bool
     *
     */
    private static function validateSessionExpiry(){

        $max_time = 60 * 60 * 24; // 1 day

        if(!isset($_SESSION['generated_time'])) {
            return false;
        }

        return ($_SESSION['generated_time'] + $max_time) > time();
    }

    /**
     * checks for session concurrency
     *
     * This is done as the following:
     * UserA logs in with his session id('123') and it will be stored in the database.
     * Then, UserB logs in also using the same email and password of UserA from another PC,
     * and also store the session id('456') in the database
     *
     * Now, Whenever UserA performs any action,
     * You then check the session_id() against the last one stored in the database('456'),
     * If they don't match then log both of them out.
     *
     * @access public
     * @static static method
     * @return bool
     * @see Session::updateSessionId()
     * @see http://stackoverflow.com/questions/6126285/php-stop-concurrent-user-logins
     */
    public static function isConcurrentSessionExists(){

        $session_id = session_id();
        $userId  = self::getUserId();

        if(isset($userId) && isset($session_id)){

            $db = Database::openConnection();
            return !$db->fieldValueTaken('users', $session_id, 'session_id') ;
        }

        return false;
    }

    /**
     * get CSRF token and generate a new one if expired
     *
     * @access public
     * @static static method
     * @return string
     *
     */
    public static function generateCsrfToken(){

        $max_time = 60 * 60 * 24; // 1 day
        $stored_time = self::getCsrfTokenTime();
        $csrf_token  = self::getCsrfToken();

        if($max_time + $stored_time <= time() || empty($csrf_token)){
            //$token = md5(uniqid(rand(), true));  not so secure
            $token = Encryption::getRandomToken(16);
            $_SESSION["csrf_token"] = $token;
            $_SESSION["csrf_token_time"] = time();
        }

        return self::getCsrfToken();
    }

    /**
     * reset session id, delete session file on server, and re-assign the values.
     *
     * @access public
     * @static static method
     * @param  array  $data
     * @return string
     *
     */
    public static function reset($data){

        // remove old and regenerate session ID.
        session_regenerate_id(true);
        $_SESSION = array();

        $_SESSION["is_logged_in"]        = true;
        $_SESSION["user_id"]             = (int)$data["user_id"];
        $_SESSION["role"]                = $data["role"];
        $_SESSION['users_name']          = $data['users_name'];
        $_SESSION['client_id']           = $data['client_id'];
        $_SESSION['is_admin_user']       = $data['is_admin_user'];
        $_SESSION['is_production_user']  = $data['is_production_user'];
        $_SESSION['is_warehouse_user']   = $data['is_warehouse_user'];
        //extra client data
        $db = Database::openConnection();
        $_SESSION['is_delivery_client'] = ($db->queryValue('clients', array('id' => $data['client_id']), 'delivery_client') > 0);

        // save these values in the session,
        // they are needed to avoid session hijacking and fixation
        $_SESSION['ip']             = $data["ip"];
        $_SESSION['user_agent']     = $data["user_agent"];
        $_SESSION['generated_time'] = time();

        // update session id in database
        self::updateSessionId($data["user_id"], session_id());

        // set session cookie setting manually,
        // Why? because you need to explicitly set session expiry, path, domain, secure, and HTTP.
        // @see https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#Cookies
        setcookie(session_name(), session_id(), time() + Config::get('SESSION_COOKIE_EXPIRY') /*a week*/, Config::get('COOKIE_PATH'), Config::get('COOKIE_DOMAIN'), Config::get('COOKIE_SECURE'), Config::get('COOKIE_HTTP'));
    }

    /**
     * update session id in database
     *
     * @access public
     * @static static method
     * @param  string $userId
     * @param  string $sessionId
     * @return string
     *
     */
    private static function updateSessionId($userId, $sessionId = null){

        $db = Database::openConnection();
        $db->updateDatabaseField('users', 'session_id', $sessionId, $userId);

    }

    /**
     * Remove the session
     * Delete session completely from the browser cookies and destroy it's file on the server
     *
     * @access public
     * @static static method
     */
    public static function remove(){

        // update session in database
        $userId = self::getUserId();
        if(!empty($userId)){
            self::updateSessionId(self::getUserId());
        }

        // clear session data
        $_SESSION = array();

        // remove session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // destroy session file on server(if not already)
        if(session_status() === PHP_SESSION_ACTIVE){
            session_destroy();
        }
    }

}
