<?php

namespace App\Presenters;


use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{

    /** @var int @persistent */
    public $page;

    /** @var string @persistent */
    public $filter;


}