<?php

namespace Themety;

use Composer\Script\Event;
use Exception;

use Themety\Themety;

class Tasks {

    protected $tasks = array();
    protected $event;

    public static function manager(Event $event)
    {
        global $argv;

        $tasks = new self;
        $tasks->event = $event;
        $tasks->collectInfo();

        if (empty($argv[2])) {
            $tasks->showHelp();
            return;
        }

        $task = $argv[2];
        $tasks->run($task);
    }


    /**
     * Run a task
     */
    protected function run($task)
    {
        if (empty($this->tasks[$task])) {
            throw new Exception('Task not found: ' . $task);
        }

        call_user_func($this->tasks[$task]['run'], $this->event);
    }


    /**
     *  Show tasks list
     */
    protected function showHelp()
    {
        $this->event->getIO()->write('');
        foreach ($this->tasks as $key => $value) {
            $this->event->getIO()->write($key . "\t\t" . $value['title']);
            $this->event->getIO()->write('');
        }
        $this->event->getIO()->write('');
    }



    /**
     * Collect tasks info
     */
    public function collectInfo()
    {
        $path = __DIR__ . '/Task/';
        $files = glob($path . '*.php');

        foreach ($files as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $class = 'Themety\\Task\\' . $className;

            if ($className === 'Base') {
                continue;
            }

            $data = call_user_func(array($class, 'getInfo'));
            foreach ($data as $item) {
              $this->addCommand($item);
            }
        }

    }


    /**
     * Add single themety task
     *
     * @param task data $command
     * @throws Exception
     */
    public function addCommand($command)
    {
        $data = array_merge(array(
            'command' => '',
            'title' => '',
            'run' => null,
        ), $command);

        if (empty($data['command'])) {
            throw new Exception('Command not set');
        }

        if (!is_callable($data['run'])) {
            throw new Exception('Task should have callable function');
        }

        $this->tasks[$data['command']] = $data;
    }
}
