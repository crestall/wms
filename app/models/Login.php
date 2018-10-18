<?php

/**
 * Login Class
 *
 
 * @author     Mark Solly <mark.solly@3plplus.com.au>
 */

class Login extends Model{

    /**
     * Checks if forgotten password token is valid or not.
     *
     * @access public
     * @param  integer  $userId
     * @param  string   $passwordToken
     * @return boolean
     */
    public function isForgottenPasswordTokenValid($userId, $passwordToken){

        if (empty($userId) || empty($passwordToken))
        {
            return false;
        }

        $db = Database::openConnection();
        $array = array(
            'user_id'           =>  $userId,
            'password_token'    =>  $passwordToken
        );
        $forgottenPassword = $db->queryRow("SELECT * FROM forgotten_passwords WHERE user_id = :user_id AND password_token = :password_token LIMIT 1", $array);



        if(!empty($forgottenPassword))
        {
            $expiry_time = (24 * 60 * 60);
            $time_elapsed = time() - $forgottenPassword['password_last_reset'];
            return ($time_elapsed <= $expiry_time);
        }
        else
        {
            $this->resetPasswordToken($userId);
            Logger::log("PASSWORD TOKEN", "User ID ". $userId . " is trying to reset password using invalid token: " . $passwordToken, __FILE__, __LINE__);
            return false;
        }
    }

    /**
     * Logout by removing the Session and Cookies.
     *
     * @access public
     * @param  integer $userId
     *
     */
    public function logOut($userId){

        Session::remove();
        Cookie::remove($userId);
    }

    /**
     * Reset forgotten password token
     *
     * @access private
     * @param  integer   $userId
     * @throws Exception  If couldn't reset password token
     */
    public function resetPasswordToken($userId){

        $db = Database::openConnection();
        $query = "UPDATE forgotten_passwords SET password_token = NULL, " .
                 "password_last_reset = NULL, forgotten_password_attempts = 0 ".
                 "WHERE user_id = $userId LIMIT 1";
        $db->query($query);
    }

    public function generateForgottenPasswordToken($userId, $forgottenPassword){

        $db = Database::openConnection();

        if(!empty($forgottenPassword)){
            $query = "UPDATE forgotten_passwords SET password_token = :password_token, " .
                     "password_last_reset = :password_last_reset, forgotten_password_attempts = forgotten_password_attempts+1 ".
                     "WHERE user_id = :user_id";
        }else{
            $query = "INSERT INTO forgotten_passwords (user_id, password_token, password_last_reset, forgotten_password_attempts) ".
                     "VALUES (:user_id, :password_token, :password_last_reset, 1)";
        }

        // generate random hash for email verification (40 char string)
        $passwordToken = sha1(uniqid(mt_rand(), true));

        $array = array(
            'password_token'         =>  $passwordToken,
            'password_last_reset'   =>  time(),
            'user_id'               =>  $userId
        );
        $db->query($query, $array);

        return $passwordToken;
    }

    public function updatePassword($hashedPassword, $userId)
    {
        $db = Database::openConnection();
        $db->updateDatabaseField('users', 'hashed_password', $hashedPassword, $userId);
    }

    public function isIpBlocked($userIp){
        $db = Database::openConnection();
        return $db->queryValue('blocked_ips', array('ip' => $userIp), 'ip');
    }

    public function isLoginAttemptAllowed($email)
    {
        $db = Database::openConnection();
        $failedLogin = $db->queryRow("SELECT * FROM failed_logins WHERE user_email = :email", array("email" => $email));
        $last_time   = isset($failedLogin["last_failed_login"])? $failedLogin["last_failed_login"]: null;
        $count       = isset($failedLogin["failed_login_attempts"])? $failedLogin["failed_login_attempts"]: null;
        $block_time = (10 * 60);
        $time_elapsed = time() - $last_time;
        if ($count >= 5 && $time_elapsed < $block_time)
            return false;
        return true;
    }

    public function getMinutesBeforeLogin($email)
    {
        $db = Database::openConnection();
        $failedLogin = $db->queryRow("SELECT * FROM failed_logins WHERE user_email = :email", array("email" => $email));
        $last_time   = isset($failedLogin["last_failed_login"])? $failedLogin["last_failed_login"]: 0;
        $block_time = (10 * 60);
        $time_elapsed = time() - $last_time;
        return (date("i",$block_time - $time_elapsed));
    }

    /**
     * Adds a new record(if not exists) to ip_failed_logins table,
     * Also block the IP Address if number of attempts exceeded
     *
     * @access private
     * @param  string   $userIp
     * @param  string   $email
     */
    public function handleIpFailedLogin($userIp, $email)
    {
        $db = Database::openConnection();
        if(!in_array($userIp, $this->ignored_ips))
        {
            //increment failed logins
            $failedLogin = $db->queryRow("SELECT * FROM failed_logins WHERE user_email = :email", array("email" => $email));
            if(!empty($failedLogin))
            {
                $query = "UPDATE failed_logins SET last_failed_login = :last_failed_login, " .
                         "failed_login_attempts = failed_login_attempts+1 WHERE user_email = :user_email";
            }
            else
            {
                $query = "INSERT INTO failed_logins (user_email, last_failed_login, failed_login_attempts) ".
                         "VALUES (:user_email, :last_failed_login, 1)";
            }
            $array = array(
                'last_failed_login' =>  time(),
                'user_email'        =>  $email
            );
            $db->query($query, $array);
            $blocked_ips = $db->queryData("SELECT ip, user_email FROM ip_failed_logins WHERE ip = :ip", array('ip' => $userIp));
            $count = count($blocked_ips);
            // block IP if there were failed login attempts using different emails(>= 10) from the same IP address
            if($count >= 10)
            {
                $this->blockIp($userIp);
            }
            else
            {
                // check if ip_failed_logins already has a record with current ip + email
                // if not, then insert it
                if(!$db->queryValue('ip_failed_logins', array('ip' => $userIp, 'user_email' => $email), 'ip'))
                {
                    $vals = array(
                        'user_email'    =>  $email,
                        'ip'            =>  $userIp
                    );
                    $db->insertQuery('ip_failed_logins', $vals);
                }
            }
        }
    }

    public function blockIp($userIp){
        $db = Database::openConnection();
        $vals = array(
            'ip'    =>  $userIp
        );
        $db->insertQuery('blocked_ips', $vals);
    }

    public function resetFailedLogins($email){

        $db = Database::openConnection();
        $query = "UPDATE failed_logins SET last_failed_login = NULL, " .
                 "failed_login_attempts = 0 WHERE user_email = :user_email";

        $db->query($query, array('user_email' => $email));
    }
}