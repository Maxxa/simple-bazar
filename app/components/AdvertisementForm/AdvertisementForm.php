<?php
namespace App\Components;

use App\Model\AdvertismentManager;
use App\Model\MailManager;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Http\Request;
use Nette\Neon\Exception;
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
     * @var HttpRequest
     */
    private $request;

    private $maxWith = 600;
    private $maxHeight = 600;


    /**
     * AdvertisementForm constructor.
     * @param AdvertismentManager $manager
     * @param MailManager $mailManager
     * @param Request $request
     */
    public function __construct(AdvertismentManager $manager, MailManager $mailManager, Request $request)
    {
        $this->manager = $manager;
        $this->mailManager = $mailManager;
        $this->request = $request;
    }

    public function createComponentForm()
    {
        $form = new Form();

        $form->addText("name", "Kontaktní jméno")
            ->addRule(Form::FILLED, "Položka musí být vyplněna")
            ->setRequired("Položka musí být vyplněna");
        $form->addText("email", "E-mailová adresa")
            ->setType("email")
            ->addRule(Form::FILLED, "Položka musí být vyplněna")
            ->addRule(Form::EMAIL, "Zadaný e-mail není validní.")
            ->setRequired("Položka musí být vyplněna");

        $form->addSelect("type", "Typ inzerátu", AdvertismentManager::types());
        $form->addTextArea("text", "Inzerát")
            ->setRequired("Inzerát musí být vyplněn");

        $upload = $form->addUpload("image", "");
        $upload->addConditionOn($upload, Form::FILLED)
//            ->setRequired("")
            ->addRule(Form::IMAGE, 'Vložený soubor musí být obrázek')//            ->addRule(Form::MAX_LENGTH, 'Maximálně je povoleno nahrád 10 obrázků!', 10)
        ;

//        $c = $form->addReCaptcha('captcha', NULL, "Please prove you're not a robot.");

        $form->addSubmit("save", "Přidat");

        $form->onSuccess[] = array($this, 'onSave');
        return $form;
    }

    public function onSave(Form $form, $values)
    {
        try {
            $values['ip_address'] = $this->request->getRemoteAddress();
            $values['image'] = $this->buildImg($values['image']);

            $row = $this->manager->insert($values);
            $this->mailManager->sendInsertMail($this->presenter, $row);
            $this->flashAndRedirect("Inzerát byl úspěšně vložen", "success");
        } catch (Exception$ex) {
            $this->flashAndRedirect("Při vkládání inzerátu nastala chyba!", "danger");
        }
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