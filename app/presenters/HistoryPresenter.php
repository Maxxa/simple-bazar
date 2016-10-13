<?php

namespace App\Presenters;

use App\Components\IAdvertisementForm;
use App\Components\IAdvertisementList;
use App\Model\AdvertismentManager;
use Nette;
use Nette\Utils\Paginator;


class HistoryPresenter extends Nette\Application\UI\Presenter
{

    /** @var IAdvertisementList @inject */
    public $listFactory;

    /** @var int @persistent */
    public $page;

    /** @var Paginator */
    private $paginator;

    /** @var string @persistent */
    public $filter;

    public function actionDefault()
    {
        $this->paginator = new Paginator;
        $params = $this->context->getParameters();
        $this->paginator->setItemsPerPage($params['history-item-per-page']);
        $this->paginator->setPage($this->page);
    }

    public function createComponentList()
    {
        $comp = $this->listFactory->create();
        $comp->setPaginator($this->paginator);
        $comp->showAll();
        return $comp;
    }

}
