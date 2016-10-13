<?php
namespace App\Presenters;

use App\Model\AdvertismentManager;
use Nette\Application\UI\Presenter;
use Nette\Utils\Image;

class FilesPresenter extends Presenter
{

    /** @var AdvertismentManager @inject */
    public $manager;

    public function actionPhoto($id)
    {
        $id = base64_decode($id);
        $row = $this->manager->row($id);
        if ($row == null || $row == false) {
            Image::fromBlank(200, 200)->send();
        } else {
            $image = Image::fromString($row->image);
            $image->send();
        }
        $this->terminate();
    }


}