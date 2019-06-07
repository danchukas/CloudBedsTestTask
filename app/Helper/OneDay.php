<?php

namespace App\Helper;

use DateInterval;

class OneDay extends DateInterval
{
    public function __construct()
    {
        parent::__construct('P1D');
    }
}