<?php
namespace App\Components;

use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;

/**
 * Description of BaseComponent
 *
 * @author Max-xa - Vojtěch Müller
 * @email muller.voj@gmail.com
 */
abstract class BaseComponent extends Control {

    public function render() {
        $this->template->render();
    }

    public function createTemplate(): ITemplate
    {
        $template = parent::createTemplate();
        $template->setFile($this->getTemplateFilePath());
        return $template;
    }

    protected function getTemplateFilePath() {
        $reflection = $this->getReflection();
        $dir = dirname($reflection->getFileName());
        $filename = lcfirst($reflection->getShortName() . '.latte');
        return $dir . \DIRECTORY_SEPARATOR . $filename;
    }

}