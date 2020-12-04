<?php

namespace App\Components;


use Nette\Application\Application;
use Nette\Application\IPresenter;
use Nette\Application\LinkGenerator;
use Nette\Application\UI\ITemplateFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\UIMacros;

class MailingTemplate
{
    /** @var LinkGenerator */
    private $linkGenerator;

    /** @var ITemplateFactory */
    private $templateFactory;
    /**
     * @var Application
     */
    private $application;


    /**
     * MailingTemplate constructor.
     * @param LinkGenerator $generator
     * @param ITemplateFactory $factory
     * @param Application $application
     */
    public function __construct(LinkGenerator $generator, ITemplateFactory $factory, Application $application)
    {
        $this->linkGenerator = $generator;
        $this->templateFactory = $factory;
        $this->application = $application;
    }

    public function createTemplate($lattePath = null)
    {

        /** @var IPresenter $presenter */
        $presenter = $this->application->getPresenter();

        /** @var Template $template
         */
        $template = $this->templateFactory->createTemplate();

        $template->getLatte()->addProvider("uiControl", $presenter);
        $template->_control = $presenter;

        if ($lattePath != null) {
            $template->setFile($lattePath);
        }
        UIMacros::install($template->getLatte()->getCompiler());
        return $template;
    }

}