<?php

use \Slim\App as Slim;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Respect\Validation\Validator as v;

// autoloader
require '../vendor/autoload.php';

// app settings
$settings = [
    'settings' => [
        'displayErrorDetails' => true,
    ]
];

// initialise Slim app
$app = new Slim($settings['settings']);

// get the container
$container = $app->getContainer();

// load recipes
$container['Data'] = function ($container) {
    // original file
    $filename = __DIR__ . '/../public/recipe-data.csv';
    // new file for recipes modifications (updating, rating, etc.)
    $newfile = __DIR__ . '/../public/recipes.csv';
    // column headers
    $headers = [];
    // loaded recipes
    $recipes = [];

    // get headers
    $handle = fopen($filename, 'r');
    $headers = fgetcsv($handle, 3000, ',');
    $headers[] = 'rating';
    fclose($handle);

    // check if the new file already exists
    if (!file_exists($newfile)) {
        $newfile = fopen($newfile, 'w');

        if (($handle = fopen($filename, 'r')) !== false) {
            while (($line = fgetcsv($handle, 3000, ',')) !== false) {
                // first column in recipe-data is for ids and is numberic,
                // so let's get the header before the actual ids
                if (!is_numeric($line[0])) {
                    continue;
                }

                // add an extra rating element
                $line['rating'] = '';
                $recipes[] = array_combine($headers, $line);
                fputcsv($newfile, $line);
            }

            fclose($newfile);
        }

        fclose($handle);
    } else {
        if (($handle = fopen($newfile, 'r')) !== false) {
            while (($line = fgetcsv($handle, 3000, ',')) !== false) {
                $recipes[] = array_combine($headers, $line);
            }

            fclose($handle);
        }
    }

    return $recipes;
};

// Recipes Controller
$container['RecipesController'] = function ($container) {
    return new \App\Controllers\RecipesController($container);
};

// require routes
require __DIR__ . '/../app/routes.php';
