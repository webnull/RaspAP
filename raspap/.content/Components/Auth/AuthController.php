<?php
namespace RaspAP\Components\Auth;

use Panthera\Classes\BaseExceptions\PantheraFrameworkException;
use Panthera\Components\Kernel\Framework;

/**
 * RaspAP
 * --
 * AuthController
 *
 * @package RaspAP\Components\Auth
 */
class AuthController
{
    /**
     * @return bool
     */
    public function isAuthorized()
    {
        $session = Framework::getInstance()->session;
        return $session->get('UserUID') !== null && $session->get('UserIsSUDO') === true;
    }

    /**
     * @param int $userID
     * @param bool $sudo
     *
     * @return $this
     */
    public function grantAccess($userID, $sudo = false)
    {
        $session = Framework::getInstance()->session;

        if ($sudo)
        {
            $session->set('UserIsSUDO', true);
        }

        $session->set('UserUID', $userID);
        return $this;
    }

    /**
     * @param string $login
     * @return bool
     */
    public function userExists($login)
    {
        $users = explode("\n", file_get_contents('/etc/passwd'));

        foreach ($users as $user)
        {
            $parts = explode(':', $user);

            if ($parts[0] == $login)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $user
     * @param string $password
     * @throws PantheraFrameworkException
     * @return bool
     */
    public function checkAccess($user, $password)
    {
        // user does not exists - this prevents cache from overloading (DoS attacks)
        if (!$this->userExists($user))
        {
            sleep(2.8);
            return false;
        }

        $retries = (int)Framework::getInstance()->cache->get('login.retry.' . $user);

        if ($retries >= Framework::getInstance()->config->get('LoginRetries', 10))
        {
           throw new PantheraFrameworkException('Login retries exhausted', 'LOGIN_RETRIES_TOO_MANY');
        }

        Framework::getInstance()->cache->set('login.retry.' . $user, $retries + 1, Framework::getInstance()->config->get('LoginRetriesBlockTime', 300));

        if (!in_array($user, Framework::getInstance()->config->get('SudoUsers', ['root', 'raspap-admin'])))
        {
            throw new PantheraFrameworkException('User is not in "SudoUsers" list, please add user to app.php - key: "SudoUsers", see manual on github.com/webnull/raspap-webgui', 'AUTH_NO_USER_IN_SUDOERS');
        }

        return pam_auth($user, $password);
    }
}

if (!function_exists('pam_auth'))
{
    function pam_auth($user, $password)
    {
        throw new \Exception('Please install pam extension. `sudo pecl install pam`');
        return false;
    }
}