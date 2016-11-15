<?php
namespace App\Model;

use App\Collegas\Security\CryptoService;
use App\Components\BaseComponent;
use Latte\Engine;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Database\Context;
use Nette\InvalidArgumentException;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Neon\Exception;

class MailManager
{

    /** @var Context */
    private $database;

    /**
     * @var
     */
    private $mailFrom;
    /**
     * @var
     */
    private $subject;
    /**
     * @var CryptoService
     */
    private $cryptoHelper;

    /**
     * MailManager constructor.
     * @param $mailFrom
     * @param $mailAdmin
     * @param $subject
     * @param Context $database
     * @param CryptoService $cryptoHelper
     */
    public function __construct($mailFrom, $mailAdmin, $subject, Context $database, CryptoService $cryptoHelper)
    {
        $this->database = $database;
        $this->mailFrom = $mailFrom;
        $this->mailAdmin = $mailAdmin;
        $this->subject = $subject;
        $this->cryptoHelper = $cryptoHelper;
    }

    public function sendInsertMail($presenter, $row)
    {
        $params = $presenter->context->getParameters();
        if ($params['debugMode']) {
            return;
        }

        $message = new Engine();
        $params = array(
            "_presenter" => $presenter,
            "data" => $row,
            "id" => $this->cryptoHelper->encrypt($row['id'])
        );
        UIMacros::install($message->getCompiler());
        $body = $message->renderToString(__DIR__ . '/../presenters/templates/email.latte', $params);
        try {
            $mail = new Message;
            $mail->setFrom($this->mailFrom)
                ->addTo($row['email'])
                ->setSubject($this->subject)
                ->setHtmlBody($body);
            $mailer = new SendmailMailer;
            $mailer->send($mail);

            $params['email'] = $row->email;
            $params['text'] = $row->text;
            $params['name'] = $row->name;

            $body = $message->renderToString(__DIR__ . '/../presenters/templates/emailAdmin.latte', $params);
            $mail = new Message;
            $mail->setFrom($this->mailFrom)
                ->setSubject("Nový inzerát!")
                ->setHtmlBody($body);
            $mailer = new SendmailMailer;
            if (is_array($this->mailAdmin)) {
                foreach ($this->mailAdmin as $mailAddress) {
                    $mail->addTo($mailAddress);
                }
            } else {
                $mail->addTo($this->mailAdmin);
            }
            $mailer->send($mail);


        } catch (Exception $ex) {
        } catch (InvalidArgumentException $ex) {


        }
    }

}