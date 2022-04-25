<?php

namespace App\Presenters;


use App\Helpers\ConfigParameters;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{

    /** @var int @persistent */
    public $page;

    /** @var string @persistent */
    public $filter;

    /** @var ConfigParameters @inject */
    public $configParams;

    protected function startup()
    {
        parent::startup();

        if (class_exists('GeoIp2\Database\Reader', true) && file_exists("/usr/share/GeoIP/GeoIP.dat")) {
            $reader = new \GeoIp2\Database\Reader('/usr/share/GeoIP/GeoLite2-Country.mmdb');
            $record = $reader->country($_SERVER['REMOTE_ADDR']);

            if (!in_array($record->country->isoCode, ['CZ','SK','GR'])) {
                $this->error("",444);
            }

        }


    }


}