<?php

spl_autoload_register(
    function ($class_name) {
        if(file_exists( MODULES_DIR.$class_name . '.php' )) {
            if( DEBUG ) {
                echo "trying to create ".$class_name.', require '.MODULES_DIR.$class_name.'.php<br>'.PHP_EOL;
            }
            require MODULES_DIR.$class_name . '.php';
        }
    }
);