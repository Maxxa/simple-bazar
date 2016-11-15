<?php
namespace App\Components;

use App\Collegas\Security\CryptoService;
use App\Helpers\ImageUtil;
use App\Model\AdvertismentManager;
use Nette\Application\UI\Form;
use Nette\Utils\Image;
use Nette\Utils\Paginator;
use Nette\Utils\Strings;

class AdvertisementList extends BaseComponent
{
    /**
     * @var AdvertismentManager
     */
    private $manager;
    private $showAll = false;

    /**
     * @var Paginator
     */
    private $paginator;
    /**
     * @var CryptoService
     */
    private $cryptoHelper;

    /**
     * AdvertisementForm constructor.
     * @param AdvertismentManager $manager
     * @param CryptoService $cryptoHelper
     */
    public function __construct(AdvertismentManager $manager, CryptoService $cryptoHelper)
    {
        $this->manager = $manager;
        $this->cryptoHelper = $cryptoHelper;
    }

    public function render()
    {
        if ($this->showAll) {
            $data = $this->manager->dataAll();
            $this->paginator->setItemCount($data == FALSE ? 0 : $data->count());
            $data = $data->limit($this->paginator->getLength(), $this->paginator->getOffset());
        } else {
            $data = $this->manager->data($this->presenter->filter);
        }

        $this->template->history = $this->showAll;
        $this->template->rows = $data;
        $this->template->paginator = $this->paginator;
        parent::render();
    }

    public function createComponentFiltr()
    {
        $form = new Form();

        $form->addCheckbox(AdvertismentManager::SALE, "Prodám");
        $form->addCheckbox(AdvertismentManager::PURCHASE, "Koupím");

        $type = $this->presenter->filter;
        $values = array(
            AdvertismentManager::SALE => true,
            AdvertismentManager::PURCHASE => true
        );
        if ($type == AdvertismentManager::SALE || $type == "none") {
            $values[AdvertismentManager::PURCHASE] = false;
        }
        if ($type == AdvertismentManager::PURCHASE || $type == "none") {
            $values[AdvertismentManager::SALE] = false;
        }

        $form->setValues($values);

        $form->addSubmit("onFilter", "Filtrovat");
        $form->onSuccess[] = array($this, "changeFilter");
        return $form;
    }

    public function changeFilter(Form $form, $values)
    {
        $sale = $values[AdvertismentManager::SALE];
        $purchase = $values[AdvertismentManager::PURCHASE];

        if ($sale && $purchase) {
            $this->presenter->filter = null;
        } else if ($sale) {
            $this->presenter->filter = AdvertismentManager::SALE;
        } else if ($purchase) {
            $this->presenter->filter = AdvertismentManager::PURCHASE;
        } else {
            $this->presenter->filter = null;
            $this->flashAndRedirect("Jeden z filtrů musí být aktivní!", "danger");
            return;
        }
        $this->flashAndRedirect("Filtr byl úspěšně upraven", "success");
    }


    private function flashAndRedirect($msg, $type)
    {
        $this->presenter->flashMessage($msg, $type);
        if ($this->presenter->isAjax()) {
            $this->presenter->redrawControl();
        } else {
            $this->redirect('this');
        }
    }

    public function buildText($text)
    {
        $text = Strings::replace($text, "/$/m", " <br>");
        // The Regular Expression filter
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        if (preg_match($reg_exUrl, $text, $url)) {
            $url = $url[0];
            $text = preg_replace($reg_exUrl, "<a href=\"{$url}\">{$url}</a> ", $text);
        }
        return $text;
    }

    public function cryptId($id)
    {
        return  $this->cryptoHelper->encrypt($id);
    }

    public function buildImage($image)
    {
        $image = Image::fromString($image);
        return ImageUtil::resizePhoto($image, 100, 100);
    }

    public function buildEmail($email)
    {
        return Strings::replace($email, "/\@/", "{{at}}");
    }

    public function showAll()
    {
        $this->showAll = true;
    }

    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }


}