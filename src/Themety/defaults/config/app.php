<?php

return array(

    'debug' => true,

    'timezone' => 'UTC',

    'providers' => [
        'Illuminate\Filesystem\FilesystemServiceProvider',

        'Themety\Theme\ThemeServiceProvider',
        'Themety\Model\ModelServiceProvider',
        'Themety\Content\ContentServiceProvider',
        'Themety\Widget\WidgetsServiceProvider',
        'Themety\Routes\RoutesServiceProvider',
        'Themety\Shortcodes\ShortcodeServiceProvider',
    ],
);
