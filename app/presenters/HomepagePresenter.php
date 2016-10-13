<?php

namespace App\Presenters;

use App\Components\IAdvertisementForm;
use App\Components\IAdvertisementList;
use App\Model\AdvertismentManager;
use Nette;


class HomepagePresenter extends Nette\Application\UI\Presenter
{

    /** @var IAdvertisementForm @inject */
    public $formFactory;

    /** @var IAdvertisementList @inject */
    public $listFactory;

    /** @var string @persistent */
    public $filter;

    public function createComponentForm()
    {
        return $this->formFactory->create();
    }

    public function createComponentList()
    {
        return $this->listFactory->create();
    }

}
