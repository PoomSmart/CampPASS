<?php

return array(
    'pdf' => array(
        'enabled' => true,
        'binary'  => base_path('vendor/wemersonjanuario/wkhtmltopdf-windows/bin/64bit/wkhtmltopdf'),
        'timeout' => 3600,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary'  => base_path('vendor/wemersonjanuario/wkhtmltopdf-windows/bin/64bit/wkhtmltoimage'),
        'timeout' => 3600,
        'options' => array(),
        'env'     => array(),
    ),
);
