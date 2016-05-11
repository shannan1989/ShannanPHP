<?php

function _autoload($class) {
    
}

function load() {
    $args = func_get_args();
    $rc = new ReflectionClass(array_shift($args));
    return $rc->newInstanceArgs($args);
}

function _shutdown() {
    
}

spl_autoload_register('_autoload');

register_shutdown_function('_shutdown');