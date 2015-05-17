<?php

namespace Fucoso\Site\CI;

use CI_DB_active_record;
use Fucoso\Site\Interfaces\IData\IData;

class CIData implements IData
{

    /**
     *
     * @var CIData
     */
    private static $sharedInstance = null;

    /**
     *
     * @var CI_DB_active_record
     */
    public $db = null;

    private function __construct()
    {

    }

    public function load($connection = 'default')
    {
        $ci = get_instance();
        if (isset($ci->db) && $ci->db) {
            $this->db = $ci->db;
        } else {
            $this->db = $ci->load->database($connection, true);
        }
    }

    /**
     *
     * @return CIData
     */
    public static function getSharedInstance()
    {
        if (!self::$sharedInstance) {
            self::$sharedInstance = new CIData();
        }
        return self::$sharedInstance;
    }

    public function isReady()
    {
        if ($this->db) {
            return true;
        }
        return false;
    }

    public function query($sql)
    {
        return $this->db->query($sql);
    }

    public function result($query = false)
    {
        if ($query === false) {
            $query = $this->db->get();
        }
        if ($query && $query->num_rows() > 0) {
            return $query->result();
        }
        return false;
    }

    public function row($query = false)
    {
        if ($query === false) {
            $query = $this->db->get();
        }
        if ($query && $query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }

    public function count($key = 'total', $query = false)
    {
        $row = $this->row($query);
        if ($row) {
            if (isset($row->$key)) {
                return $row->$key;
            }
        }
        return 0;
    }

    public function resultColumn($key, $query = false)
    {
        $rows = $this->result($query);
        if ($rows) {
            $columnResult = array();
            foreach ($rows as $row) {
                $columnResult[] = $row->$key;
            }
            return $columnResult;
        }
        return false;
    }

    public function resultColumnAsString($key, $query = false)
    {
        $rows = $this->result($query);
        if ($rows) {
            $columnResult = array();
            foreach ($rows as $row) {
                $columnResult[] = '' . $row->$key;
            }
            return $columnResult;
        }
        return false;
    }

    public function resultMapByColumn($key, $query = false)
    {
        $rows = $this->result($query);
        if ($rows) {
            $rowMap = array();
            foreach ($rows as $row) {
                $rowMap[$row->$key] = $row;
            }
            return $rowMap;
        }
        return false;
    }

    public function upsertSucceeded()
    {
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function lastInsertId()
    {
        return $this->db->insert_id();
    }

    public function date()
    {
        return date('Y-m-d H:i:s');
    }

}
