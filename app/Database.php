<?php


namespace App;


use Mysqli;

class Database extends Mysqli
{
    public function __construct($host = null, $username = null, $passwd = null, $dbname = null, $port = null, $socket = null)
    {
        parent::__construct($host, $username, $passwd, $dbname, $port, $socket);

        $this->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);
    }
}