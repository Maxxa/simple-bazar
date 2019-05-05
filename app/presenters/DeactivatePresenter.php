<?php

namespace App\Presenters;

use App\Components\IAdvertisementForm;
use App\Components\IAdvertisementList;
use App\Model\AdvertismentManager;
use App\Model\BanIPModel;
use Nette;


class DeactivatePresenter extends BasePresenter
{

    /** @var AdvertismentManager @inject */
    public $manager;

    /** @var BanIPModel @inject */
    public $banModel;

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

    public function actionBan($id)
    {
        if ($id != null) {
            $row = $this->manager->findRowId($id);
            if ($row) {
                $ip = $row['ip_address'];
                $this->banModel->ban($ip);
                $this->manager->deactivateIP($ip);

                $this->flashMessage("IP adresa ($ip) dostala ban a všechny inzeraty s danou IP jsou deaktivovány!", "success");
            }
        }
        $this->redirect("History:");
    }

    private function redirectHome()
    {
        $this->redirect("Homepage:");
    }

}
