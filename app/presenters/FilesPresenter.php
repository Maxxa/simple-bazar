<?php
namespace App\Presenters;

use App\Model\AdvertismentManager;
use Nette\Application\UI\Presenter;
use Nette\Utils\Image;
use Nette\Utils\ImageException;

class FilesPresenter extends Presenter
{

    /** @var AdvertismentManager @inject */
    public $manager;

    public function actionPhoto($id)
    {
        $row = $this->manager->findRowId($id);
        if ($row == null || $row == false) {
            Image::fromBlank(200, 200)->send();
        } else {
            try{
                $image = Image::fromString($row->image);
                $image->send();

            }catch (ImageException $exception){
                Image::fromBlank(200, 200)->send();
            }

        }
        $this->terminate();
    }


}