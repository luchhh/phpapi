<?php
    
    
    define('PHPAPI_DIR', __DIR__);
    $config = include(PHPAPI_DIR."/config.php");
    // Your custom class dir
    //define('CLASS_DIR', 'model');
    //$include_path = __DIR__.DIRECTORY_SEPARATOR.CLASS_DIR;

    define('PHPAPI_URL', $config["app"]["root"]);

    //echo $include_path;
    // Add your class dir to include path
    set_include_path(get_include_path().PATH_SEPARATOR.PHPAPI_DIR);

    // You can use this trick to make autoloader look for commonly used "My.class.php" type filenames
    //spl_autoload_extensions('.php');

    // Use default autoload implementation
    spl_autoload_register();
