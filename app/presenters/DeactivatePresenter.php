<?php

namespace App\Presenters;

use App\Components\IAdvertisementForm;
use App\Components\IAdvertisementList;
use App\Model\AdvertismentManager;
use Nette;


class DeactivatePresenter extends Nette\Application\UI\Presenter
{

    /** @var AdvertismentManager @inject */
    public $manager;

    public function actionDefault($id, $history)
    {
        if ($id != null) {
            $id = base64_decode($id);
            if ($this->manager->deactivate($id))
                $this->flashMessage("Inzerát byl úspěšně deaktivován!", "success");
        }
        if ($history) {
            $this->redirect("History:");
            return;
        }
        $this->redirectHome();
    }

    private function redirectHome()
    {
        $this->redirect("Homepage:");
    }

}
