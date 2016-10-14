<?php

namespace App\Presenters;

use App\Components\IAdvertisementForm;
use App\Components\IAdvertisementList;
use App\Model\AdvertismentManager;
use Nette;


class HomepagePresenter extends BasePresenter
{

    /** @var IAdvertisementForm @inject */
    public $formFactory;

    /** @var IAdvertisementList @inject */
    public $listFactory;

    public function createComponentForm()
    {
        return $this->formFactory->create();
    }

    public function createComponentList()
    {
        return $this->listFactory->create();
    }

}
