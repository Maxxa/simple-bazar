<?php
namespace App\Model;

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

    public function __construct($mailFrom, $mailAdmin, $subject, Context $database)
    {
        $this->database = $database;
        $this->mailFrom = $mailFrom;
        $this->mailAdmin = $mailAdmin;
        $this->subject = $subject;
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
            "id" => base64_encode($row['id'])
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
                ->addTo($this->mailAdmin)
                ->setSubject("Nový inzerát!")
                ->setHtmlBody($body);
            $mailer = new SendmailMailer;
            $mailer->send($mail);



        } catch (Exception $ex) {
        } catch (InvalidArgumentException $ex) {


        }
    }

}