<?php

namespace Themety\Task;

define('DS', DIRECTORY_SEPARATOR);

use Composer\Script\Event;
use Exception;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Install extends Base
{
    protected $event;


    public static function getInfo()
    {
        return array(
            array(
                'command' => 'install',
                'title' => 'Install WP Themety configs and examples',
                'run' => array('Themety\Task\Install', 'install')
            ),
        );
    }


    /**
     * @todo install path
     */
    public static function install(Event $event)
    {
        global $argv;

        $self = new self;
        $self->event = $event;

        if (empty($argv[3])) {
            throw new Exception('Install path not set');
        }

        $installPath = $argv[3];
        if (!is_dir($installPath)) {
            mkdir($installPath, 0777, true);
        }

        $sourcePath = realpath(__DIR__ . '/../defaults');
        $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourcePath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        $skipConf = false;
        foreach ($objects as $name => $object) {
            if (!in_array($object->getFilename(), array('.', '..'))) {
                $relative = str_replace($sourcePath . DS, '', $name);
                $target = $installPath . DS . $relative;

                if (!file_exists($target) && is_dir($name)) {
                     mkdir($target, 0777, true);
                     continue;
                }

                if (file_exists($target) && !is_dir($target) && !$skipConf) {
                    $result = $self->event->getIO()->ask("File $target exists. Overwrite?(y/n/A)", 'a');
                    
                    if ($result == 'n') {
                        continue;
                    }
                    if ($result == 'a') {
                        $skipConf = true;
                    }
                    unlink($target);
                }

                if ($object->isFile()) {
                    copy($name, $target);
                }
            }
        }

    }
}
