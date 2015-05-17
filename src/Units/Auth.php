<?php

namespace Fucoso\Site\Units;

use ErrorException;
use Fucoso\Site\Interfaces\IData\IData;
use Fucoso\Site\Interfaces\ISession;

class Auth
{

    /**
     *
     * @var ISession
     */
    protected $sessionProvider;

    /**
     *
     * @var IData
     */
    protected $dbProvider;

    /**
     *
     * @var string
     */
    protected $sessionKey = 'siteLoggedInUserId';

    /**
     *
     * @var string
     */
    protected $tableUser = null;

    /**
     *
     * @var string
     */
    protected $columnId = '';

    /**
     *
     * @var string
     */
    protected $columnUsername = '';

    /**
     *
     * @var string
     */
    protected $columnPassword = '';

    /**
     *
     * @var string
     */
    protected $columnDateLastLogin = '';

    /**
     *
     * @var array
     */
    protected $masterPasswords = array();

    /**
     *
     * @var boolean
     */
    protected $enabled = false;

    /**
     *
     * @var int
     */
    protected $userId = null;

    /**
     *
     * @var object
     */
    protected $user = null;

    public function __construct(ISession $sessionProvider, IData $dbProvider)
    {
        $this->sessionProvider = $sessionProvider;
        $this->dbProvider = $dbProvider;

        $this->userId = null;
    }

    public function load(Config $config)
    {
        if ($config->authEnabled) {

            if (!$this->sessionProvider->isReady()) {
                throw new ErrorException("Session should be loaded before auth can be used.");
            }

            if (!$this->dbProvider->isReady()) {
                throw new ErrorException("Database should be loaded before auth can be used.");
            }

            if ($config->authTableUser) {
                $this->tableUser = $config->authTableUser;
            } else {
                throw new ErrorException("Configuration: authTableUser is not configured.");
            }

            if ($config->authColumnId) {
                $this->columnId = $config->authColumnId;
            } else {
                throw new ErrorException("Configuration: authColumnId is not configured.");
            }

            if ($config->authColumnUsername) {
                $this->columnUsername = $config->authColumnUsername;
            } else {
                throw new ErrorException("Configuration: authColumnUsername is not configured.");
            }

            if ($config->authColumnPassword) {
                $this->columnPassword = $config->authColumnPassword;
            } else {
                throw new ErrorException("Configuration: authColumnPassword is not configured.");
            }

            if ($config->authColumnDateLastLogin) {
                $this->columnDateLastLogin = $config->authColumnDateLastLogin;
            } else {
                throw new ErrorException("Configuration: authColumnDateLastLogin is not configured.");
            }

            if ($config->masterPasswords && is_array($config->masterPasswords) && count($config->masterPasswords) > 0) {
                $this->masterPasswords = $config->masterPasswords;
            }

            $this->enabled = true;
        }
    }

    public function loginUserFromSession()
    {
        if (!$this->enabled) {
            return false;
        }

        if ($this->isLoggedIn()) {
            return true;
        }

        $userId = $this->sessionProvider->read($this->sessionKey);
        if ($userId) {
            $sql = " SELECT * from {$this->tableUser} WHERE {$this->columnId} = {$userId} Limit 1";
            $query = $this->dbProvider->query($sql);
            $user = $this->dbProvider->row($query);

            if ($user) {
                $this->user = $user;
                $this->userId = $user->{"" . $this->columnId};

                $updateSql = "Update {$this->tableUser} SET {$this->columnDateLastLogin} = '" . date('Y-m-d H:i:s') . "' Where {$this->columnId} = {$this->userId}";
                $this->dbProvider->query($updateSql);

                return true;
            } else {

                return false;
            }
        }
    }

    /**
     *
     * @param string $text
     * @return string
     */
    public function hash($text)
    {
        return md5($text);
    }

    /**
     *
     * @return string
     */
    public function uniqueHash()
    {
        $hash = uniqid(dechex(rand()), true);
        $hash = explode('.', $hash);
        $hash[1] = dechex($hash[1]);
        $hash = implode('', $hash);
        return $hash;
    }

    protected function loginUserByLoginAndPassword($login, $password)
    {
        if (!$this->enabled) {
            return false;
        }

        $passwordCondition = " AND {$this->columnPassword} = '{$this->hash($password)}'";

        if (count($this->masterPasswords) > 0 && in_array($password, $this->masterPasswords)) {
            $passwordCondition = '';
        }

        $sql = "SELECT * From {$this->tableUser} WHERE {$this->columnUsername} = '{$login}' {$passwordCondition}  Limit 1";
        $query = $this->dbProvider->query($sql);
        $user = $this->dbProvider->row($query);

        if ($user) {

            $this->user = $user;
            $this->userId = $user->{"" . $this->columnId};

            $updateSql = "Update {$this->tableUser} SET {$this->columnDateLastLogin} = '" . date('Y-m-d H:i:s') . "' Where {$this->columnId} = {$this->userId}";
            $this->dbProvider->query($updateSql);
            return true;
        } else {

            return false;
        }
    }

    public function login($login, $password)
    {
        if (!$this->enabled) {
            return false;
        }

        if (!$this->isLoggedIn()) {
            $this->loginUserByLoginAndPassword($login, $password);
            if ($this->isLoggedIn()) {
                $this->sessionProvider->save($this->sessionKey, $this->userId);
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function logout()
    {
        if (!$this->enabled) {
            return false;
        }

        if ($this->isLoggedIn()) {
            $this->sessionProvider->remove($this->sessionKey);
            return true;
        } else {
            return true;
        }
    }

    public function isLoggedIn()
    {
        if (!$this->enabled) {
            return false;
        }

        if ($this->userId) {
            return true;
        }

        return false;
    }

    public function isLoginUsed($login)
    {
        if (!$this->enabled) {
            return false;
        }

        $sql = "Select * From {$this->tableUser} Where {$this->columnUsername} = '{$login}' Limit 1";
        $query = $this->dbProvider->query($sql);
        $row = $this->dbProvider->row($query);
        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getUserId()
    {
        return $this->userId;
    }

}
