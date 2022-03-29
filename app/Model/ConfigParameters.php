<?php

namespace App\Helpers;

class ConfigParameters
{

    public $debugMode = false;
    public $wwwDir = false;
    public $historyItemPerPage;

    public function __construct($params)
    {
        $this->debugMode = $params['debugMode'];
        $this->wwwDir = $params['wwwDir'];
        $this->historyItemPerPage = $params['history-item-per-page'];
    }
}