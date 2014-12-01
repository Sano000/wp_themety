<?php

namespace Themety\Traits;

use Exception;
use ReflectionClass;

trait View
{

//  protected $templatesPath;

    protected function view($template, $data)
    {
        if (file_exists($template)) {
            $filename = $template;
        } else {
            $reflector = new ReflectionClass(get_class($this));
            $classDir = $this->templatesBasePath ?: dirname($reflector->getFileName());
            $templatePath = isset($this->templatesPath) ? $this->templatesPath : 'templates';
            $filename = $classDir . '/' . $templatePath . '/' . $template . '.tpl.php';
        }

        $content = '';
        if (file_exists($filename)) {
            if (is_array($data)) {
                extract($data);
            }
            ob_start();
            include ( $filename );
            $content = ob_get_contents();
            ob_end_clean();
        } else {
            throw new Exception("Template file is not found: " . $filename);
        }
        return $content;
    }
}
