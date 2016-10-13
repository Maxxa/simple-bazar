<?php
namespace App\AdminModule\Forms;

use App\Components\BaseComponent;
use App\Model\Role;
use Nette\Application\UI\Form;
use Nette\Security\AuthenticationException;

class LoginForm extends BaseComponent
{

    public function createComponentForm()
    {
        $form = new Form();
        $form->addText("username")->setRequired("E-mail is required!");
        $form->addPassword("password")->setRequired("Password is required!");
        $form->addSubmit("login");
        $form->onSuccess[] = $this->login;
        return $form;
    }


    public function login(Form $form, $values)
    {
        try {
            $this->presenter->user->login($values["username"], $values["password"]);
            $this->presenter->redirect("this");
        } catch (AuthenticationException $ex) {
            $form->addError("Špatné přihlašovací údaje.");
        }
    }

}