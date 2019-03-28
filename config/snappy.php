<?php

return array(
    'pdf' => array(
        'enabled' => true,
        'binary'  => base_path('bin/wkhtmltopdf.exe'),
        'timeout' => 3600,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => base_path('bin/wkhtmltoimage.exe'),
        'timeout' => 3600,
        'options' => array(),
        'env'     => array(),
    ),
);
