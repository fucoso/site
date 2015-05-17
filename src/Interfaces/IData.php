<?php

namespace Fucoso\Site\Interfaces\IData;

interface IData
{

    /**
     *
     * @return IData
     */
    public static function getSharedInstance();

    public function load($connection = 'default');

    public function isReady();

    public function query($sql);

    public function result($query = false);

    public function row($query = false);

    public function count($key = 'total', $query = false);

    public function resultColumn($key, $query = false);

    public function resultColumnAsString($key, $query = false);

    public function resultMapByColumn($key, $query = false);

    public function upsertSucceeded();

    public function lastInsertId();
}
