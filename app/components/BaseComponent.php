<?php
namespace App\Components;

use Nette\Application\UI\Control;

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

    public function getTemplate($class = NULL) {
        $template = parent::getTemplate($class);
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