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
        $params = $this->context->getParameters();
        $this->paginator->setItemsPerPage($params['history-item-per-page']);
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
