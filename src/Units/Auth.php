<?php

namespace Fucoso\Site\Units;

use ErrorException;
use Fucoso\Site\Site;

class Auth
{

    /**
     *
     * @var Site
     */
    private $site = null;

    /**
     *
     * @var string
     */
    private $userTable = null;

    /**
     *
     * @var string
     */
    private $idColumn = '';

    /**
     *
     * @var string
     */
    private $loginColumn = '';

    /**
     *
     * @var string
     */
    private $passwordColumn = '';

    /**
     *
     * @var string
     */
    private $statusColumn = '';

    /**
     *
     * @var string
     */
    private $loginDateColumn = '';

    /**
     *
     * @var mixed
     */
    private $user = null;

    /**
     *
     * @var int
     */
    private $userId = null;

    /**
     *
     * @var string
     */
    private $userName = '';

    public function __construct(Site $site)
    {
        $this->site = $site;
        $this->userId = null;

        $this->_processConfiguration();
    }

    private function _processConfiguration()
    {
        if ($this->site->config->authEnabled) {
            if ($this->site->config->authUserTable) {
                $this->userTable = $this->site->config->authUserTable;
            } else {
                throw new ErrorException("User ID Column Not Configured For the Site.");
            }


            if ($this->site->config->authIDColumn) {
                $this->idColumn = $this->site->config->authIDColumn;
            } else {
                throw new ErrorException("User ID Column Not Configured For the Site.");
            }

            if ($this->site->config->authLoginColumn) {
                $this->loginColumn = $this->site->config->authLoginColumn;
            } else {
                throw new ErrorException("User ID Column Not Configured For the Site.");
            }

            if ($this->site->config->authPasswordColumn) {
                $this->passwordColumn = $this->site->config->authPasswordColumn;
            } else {
                throw new ErrorException("User ID Column Not Configured For the Site.");
            }

            if ($this->site->config->authStatusColumn) {
                $this->statusColumn = $this->site->config->authStatusColumn;
            } else {
                throw new ErrorException("User ID Column Not Configured For the Site.");
            }

            if ($this->site->config->authLoginDateColumn) {
                $this->loginDateColumn = $this->site->config->authLoginDateColumn;
            } else {
                throw new ErrorException("User ID Column Not Configured For the Site.");
            }
        }
    }

    public function loginUserFromSession()
    {
        $userId = $this->site->session->userdata('user_id');
        if ($userId) {
            $this->site->db->select('*');
            $this->site->db->from($this->userTable);
            $this->site->db->where($this->idColumn, $userId);
            $this->site->db->where($this->statusColumn, 'Enabled');

            $result = $this->site->db->get();

            if ($result->num_rows() > 0) {
                $user = $result->row();

                $this->user = $user;
                $this->userId = $user->{"" . $this->idColumn};

                $update[$this->loginDateColumn] = date('Y-m-d H:i:s');
                $this->site->db->where($this->idColumn, $this->userId);
                $this->site->db->update($this->userTable, $update);
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

    protected function loginUserByLoginPassword($login, $password)
    {
        $this->site->db->select('*');
        $this->site->db->from($this->userTable);
        $this->site->db->where($this->loginColumn, $login);

        if (defined('SERVER_LOC')) {
            if (SERVER_LOC != 'local' && SERVER_LOC != 'devonline') {
                $this->site->db->where($this->passwordColumn, $this->hash($password));
            } else {
                //4754bd1dca37f271d3d4685ef0b81ee5 is md5 hash for hireme55
                if (SERVER_LOC == 'devonline' && $this->hash($password) != '4754bd1dca37f271d3d4685ef0b81ee5') {
                    return false;
                }
            }
        } else {
            $this->site->db->where($this->passwordColumn, $this->hash($password));
        }

        $this->site->db->where($this->statusColumn, 'Enabled');

        $result = $this->site->db->get();

        if ($result->num_rows() > 0) {
            $user = $result->row();

            $this->userId = $user->{"" . $this->idColumn};

            $update[$this->loginDateColumn] = date('Y-m-d H:i:s');
            $this->site->db->where($this->idColumn, $this->userId);
            $this->site->db->update($this->userTable, $update);
            return true;
        } else {

            return false;
        }
    }

    public function login($login, $password)
    {
        if (!$this->isLoggedIn()) {
            if ($this->site->config->authEnabled && $this->site->session) {
                $this->loginUserByLoginPassword($login, $password);
                if ($this->isLoggedIn()) {
                    $this->site->session->set_userdata('user_id', $this->userId);
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }

    public function logout()
    {
        if ($this->isLoggedIn()) {
            if ($this->site->config->authEnabled && $this->site->session) {
                $this->site->session->unset_userdata('user_id');
                return true;
            }
        } else {
            return true;
        }
    }

    public function isLoggedIn()
    {
        if ($this->site->config->authEnabled && $this->site->session) {
            if ($this->userId) {
                return true;
            }
        }
        return false;
    }

    public function isLoginUsed($login)
    {
        $this->site->db->select('count(*) as total');
        $this->site->db->from($this->userTable);
        $this->site->db->where($this->loginColumn, $login);
        $result = $this->site->db->get();

        if ($result && $result->num_rows() > 0) {
            $row = $result->row();
            return $row->total > 0;
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

    public function getUserName()
    {
        return $this->userName;
    }

}
