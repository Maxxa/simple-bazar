<?php

namespace App\Presenters;

use App\Components\IAdvertisementForm;
use App\Components\IAdvertisementList;
use App\Model\AdvertismentManager;
use Nette;


class DeactivatePresenter extends BasePresenter
{

    /** @var AdvertismentManager @inject */
    public $manager;

    public function actionDefault($id, $history)
    {
        if ($id != null) {
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
