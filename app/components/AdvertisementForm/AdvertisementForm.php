<?php

namespace App\Components;

use App\Model\AdvertismentManager;
use App\Model\BanIPModel;
use App\Model\MailManager;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Http\Request;
use Nette\Utils\Image;

class AdvertisementForm extends BaseComponent
{
    /**
     * @var AdvertismentManager
     */
    private $manager;
    /**
     * @var MailManager
     */
    private $mailManager;
    /**
     * @var Request
     */
    private $request;

    private $maxWith = 600;
    private $maxHeight = 600;
    private $wwwDir;
    /**
     * @var BanIPModel
     */
    private $banModel;


    /**
     * AdvertisementForm constructor.
     * @param AdvertismentManager $manager
     * @param MailManager $mailManager
     * @param Request $request
     * @param BanIPModel $benModel
     */
    public function __construct($wwwDir, AdvertismentManager $manager, MailManager $mailManager, Request $request, BanIPModel $benModel)
    {
        $this->manager = $manager;
        $this->mailManager = $mailManager;
        $this->request = $request;
        $this->banModel = $benModel;
        $this->wwwDir = $wwwDir;
    }

    public function createComponentForm()
    {
        $form = new Form();

        $form->addText("name", "Kontaktní jméno")
            ->addRule(Form::FILLED, "Položka musí být vyplněna")
            ->setRequired("Položka musí být vyplněna");
        $form->addText("email", "E-mailová adresa")
            ->setHtmlType("email")
            ->addRule(Form::FILLED, "Položka musí být vyplněna")
            ->addRule(Form::EMAIL, "Zadaný e-mail není validní.")
            ->setRequired("Položka musí být vyplněna");

        $form->addSelect("type", "Typ inzerátu", AdvertismentManager::types());
        $form->addTextArea("text", "Inzerát")
            ->setRequired("Inzerát musí být vyplněn");

        $upload = $form->addUpload("image", "");
        $upload->addConditionOn($upload, Form::FILLED)
//            ->setRequired("")
            ->addRule(Form::IMAGE, 'Vložený soubor musí být obrázek')
            ->addRule(Form::MAX_FILE_SIZE, 'Maximální velikost souboru je 2 MB.', 2000 * 1024 /* v bytech */)//            ->addRule(Form::MAX_LENGTH, 'Maximálně je povoleno nahrád 10 obrázků!', 10)
        ;
        $form->addCheckbox("confirmTermsAndCondition", "Souhlasím s obchodními podmínkami")
            ->setRequired("Pro přidání inzerátu musíte souhlasit s obchondními podmínkami");

        $form->addInvisibleReCaptcha('captcha', TRUE, 'Are you a bot?');

        $form->addSubmit("save", "Přidat");

        $form->onSuccess[] = array($this, 'onSave');
        return $form;
    }

    public function render()
    {
        $this->template->wwwDir = $this->wwwDir;
        parent::render();
    }

    public function onSave(Form $form, $values)
    {
        try {
            $ip = $this->request->getRemoteAddress();
            $values['ip_address'] = $ip;
            unset($values['confirmTermsAndCondition']);
            if ($this->banModel->isBan($ip)) {
                throw new \Exception();
            } else {
                $values['image'] = $this->buildImg($values['image']);
                $row = $this->manager->insert($values);
                $this->mailManager->sendInsertMail($this->presenter, $row);
                $this->flashAndRedirect("Inzerát byl úspěšně vložen.", "success");
            }
        } catch (\Exception $ex) {
            if ($ex instanceof AbortException) {
                throw $ex;
            } else {
                $this->flashAndRedirect("Při vkládání inzerátu nastala chyba! ", "danger");
                return;
            }
        }

    }

    public function handleRefresh()
    {
        if ($this->presenter->isAjax()) {
            if ($this->request->isMethod('POST') == false) {
                $this->presenter->payload->postGet = TRUE;
                $this->presenter->payload->url = $this->presenter->link('this');
                $this->redrawControl();
            }
        } else {
            $this->presenter->redirect('this');
        }
    }

    private function flashAndRedirect($msg, $type)
    {
        $this->presenter->flashMessage($msg, $type);
        if ($this->presenter->isAjax()) {
            $this->presenter->redrawControl();
        } else {
            $this->presenter->redirect('this');
        }
    }

    public function buildImg(FileUpload $img)
    {
        $image = null;
        if ($img != null && $img->isOk() && $img->isImage()) {
            $image = Image::fromFile($img->getTemporaryFile());
            $this->resizeImage($image);
        }
        return $image;

    }

    public function resizeImage(Image &$image)
    {
        //obrazek je vetsi v jednom z rozeměrů.
        if ($image->width > $this->maxWith || $image->height > $this->maxHeight) {
            if ($image->width > $image->height) {
                $image->resize($this->maxWith, NULL);
            } else {
                $image->resize(NULL, $this->maxHeight);
            }
        }
    }


}