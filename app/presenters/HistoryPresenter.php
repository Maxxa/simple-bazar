<?php

namespace App\Presenters;

use App\Components\IAdvertisementForm;
use App\Components\IAdvertisementList;
use App\Model\AdvertismentManager;
use Nette;
use Nette\Utils\Paginator;


class HistoryPresenter extends BasePresenter
{

    /** @var IAdvertisementList @inject */
    public $listFactory;

    /** @var Paginator */
    private $paginator;

    public function actionDefault()
    {
        $this->paginator = new Paginator;
        $this->paginator->setItemsPerPage($this->configParams->historyItemPerPage);
        if ($this->page) {
            $this->paginator->setPage($this->page);
        }
    }

    public function createComponentList()
    {
        $comp = $this->listFactory->create();
        $comp->setPaginator($this->paginator);
        $comp->showAll();
        return $comp;
    }

}
