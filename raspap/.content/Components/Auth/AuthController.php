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
        $cache = Framework::getInstance()->cache;
        $config = Framework::getInstance()->config;

        // user does not exists - this prevents cache from overloading (DoS attacks)
        if (!$this->userExists($user))
        {
            sleep(2.8);

            if (Framework::getInstance()->isDeveloperMode())
            {
                throw new PantheraFrameworkException('@debug: User does not exists', 'USER_DOES_NOT_EXISTS');
            }

            return false;
        }

        $retries = (int)$cache->get('login.retry.' . $user);

        if ($retries >= $config->get('LoginRetries', 10))
        {
           throw new PantheraFrameworkException('Login retries exhausted', 'LOGIN_RETRIES_TOO_MANY');
        }

        $cache->set('login.retry.' . $user, $retries + 1, $config->get('LoginRetriesBlockTime', 300));

        if (!in_array($user, $config->get('SudoUsers', ['root', 'raspap-admin'])))
        {
            throw new PantheraFrameworkException('User is not in "SudoUsers" list, please add user to app.php - key: "SudoUsers", see manual on github.com/webnull/raspap-webgui', 'AUTH_NO_USER_IN_SUDOERS');
        }

        return pam_auth_py($user, $password);
    }
}

/**
 * @param string $user
 * @param string $password
 *
 * @return string
 */
function pam_auth_py($user, $password)
{
    $descriptorSpec = [
        0 => [ "pipe", "r" ],
        1 => [ "pipe", "w" ],
        2 => [ "pipe", "w" ]
    ];

    $raspapdPam = is_file('/usr/bin/raspapd-pam.py') ? '/usr/bin/raspapd-pam.py' : '/usr/local/bin/raspapd-pam.py';

    // development version
    if (is_file(Framework::getInstance()->appPath . '/../raspapd/raspapd-pam.py') && getcwd() === dirname(realpath(Framework::getInstance()->appPath . '/../')))
    {
        $raspapdPam = Framework::getInstance()->appPath . '/../raspapd/raspapd-pam.py';
    }

    $raspapdPam = realpath($raspapdPam);
    $command    = 'sudo -n ' . $raspapdPam;

    $process = proc_open($command, $descriptorSpec, $pipes, getcwd());

    if (is_resource($process))
    {
        fwrite($pipes[0], $user . "\n" . $password);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        $stderr  = stream_get_contents($pipes[2]);
        fclose($pipes[1]);

        if (strpos($stderr, 'Traceback (most recent call last)') !== false)
        {
            throw new PantheraFrameworkException('Python exception: ' . nl2br($stdout . $stderr), 'SUDO_ERROR');
        }

        if (strpos($stderr, 'sudo:') !== false)
        {
            throw new PantheraFrameworkException('Sudo returned error: "' . $stderr . '", this means not properly configured /etc/sudoers file for raspap user, get back to the README.md, command: ' . $command . ', whoami: ' . shell_exec('whoami'), 'SUDO_ERROR');
        }

        $returnValue = proc_close($process);
        return trim($stdout) === 'Success' && !$returnValue;
    }

    return false;
}