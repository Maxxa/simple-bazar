<?php

namespace App\Model;

use App\Components\MailingTemplate;
use App\Helpers\ConfigParameters;
use App\Security\CryptoService;
use Nette\InvalidArgumentException;
use Nette\Mail\Mailer;
use Nette\Mail\Message;
use Nette\Neon\Exception;

class MailManager
{


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
     * @var MailingTemplate
     */
    private $mailingTemplate;
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var ConfigParameters
     */
    private $configParameters;

    /**
     * MailManager constructor.
     * @param $mailFrom
     * @param $mailAdmin
     * @param $subject
     * @param CryptoService $cryptoHelper
     * @param MailingTemplate $mailingTemplate
     * @param Mailer $mailer
     */
    public function __construct($mailFrom, $mailAdmin, $subject, CryptoService $cryptoHelper, MailingTemplate $mailingTemplate, Mailer $mailer, ConfigParameters $configParameters)
    {
        $this->mailFrom = $mailFrom;
        $this->mailAdmin = $mailAdmin;
        $this->subject = $subject;
        $this->cryptoHelper = $cryptoHelper;
        $this->mailingTemplate = $mailingTemplate;
        $this->mailer = $mailer;
        $this->configParameters = $configParameters;
    }

    public function sendInsertMail($presenter, $row)
    {
        if ($this->configParameters->debugMode) {
            return;
        }

        $params = array(
            "data" => $row,
            "id" => $this->cryptoHelper->encrypt($row['id']),
            "text" => $row->text
        );

        $latteTemplate = $this->mailingTemplate->createTemplate(__DIR__ . '/../presenters/templates/email.latte');
        $latteTemplate->setParameters($params);

        try {
            $mail = new Message;
            $mail->setFrom($this->mailFrom)
                ->addTo($row['email'])
                ->setSubject($this->subject)
                ->setHtmlBody($latteTemplate);

            $this->mailer->send($mail);

            $params['email'] = $row->email;
            $params['name'] = $row->name;

            $latteTemplate = $this->mailingTemplate->createTemplate(__DIR__ . '/../presenters/templates/emailAdmin.latte');
            $latteTemplate->setParameters($params);

            $mail = new Message;
            $mail->setFrom($this->mailFrom)
                ->setSubject("Nový inzerát!")
                ->setHtmlBody($latteTemplate);
            if (is_array($this->mailAdmin)) {
                foreach ($this->mailAdmin as $mailAddress) {
                    $mail->addTo($mailAddress);
                }
            } else {
                $mail->addTo($this->mailAdmin);
            }
            $this->mailer->send($mail);


        } catch (Exception $ex) {
        } catch (InvalidArgumentException $ex) {
        }
    }

}