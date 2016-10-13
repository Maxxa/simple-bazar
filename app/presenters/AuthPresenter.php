<?php

namespace App\Presenters;


use App\AdminModule\Forms\ILoginForm;
use Nette\Application\UI\Presenter;

class AuthPresenter extends Presenter
{

    /** @var ILoginForm @inject */
    public $loginForm;


    public function createComponentLoginForm()
    {
        return $this->loginForm->create();
    }

    public function actionLogout(){
        $this->user->logout();
        $this->redirect("default");
    }


}