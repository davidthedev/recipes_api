<?php

// GET / list all recipes for a specific cuisine
$app->get('/recipes', 'RecipesController:getAll');

// GET / get a recipe
$app->get('/recipes/{id}', 'RecipesController:get');

// POST / create a recipe
$app->post('/recipes', 'RecipesController:create');

// PUT / update a recipe
$app->put('/recipes{id}', 'RecipesController:update');

// PUT / rate a recipe
$app->put('/recipes/{id}/rating', 'RecipesController:rate');
