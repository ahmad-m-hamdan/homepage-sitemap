<?php

spl_autoload_register(function ($class) {
    // Define the base directory where your plugin's classes are stored.
    $baseDir = __DIR__; // Use the appropriate base directory.

    // Remove the initial part of the namespace.
    $class = preg_replace('/^HomepageSitemap\\\/i', '', $class);

    // Convert PascalCase to param-case, also known as kabab-case :)
    $class = strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $class));

    // Convert the class namespace to a file path.
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Define the full path to the class file.
    $file = "{$baseDir}" . DIRECTORY_SEPARATOR . "{$class}.php";


    // Check if the class file exists and require it.
    if (file_exists($file)) {
        require $file;
    }
});
