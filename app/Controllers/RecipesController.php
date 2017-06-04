<?php

namespace App\Controllers;

use \Respect\Validation\Validator as v;

class RecipesController extends BaseController
{
    private $newFile = __DIR__ . '/../../public/recipes.csv';
    private $filename = __DIR__ . '/../../public/recipes.csv';

    /**
     * Paginate-able list of recipes
     *
     * @param  object $request
     * @param  object $response
     * @return string
     */
    public function getAll($request, $response)
    {
        // data filter
        $cuisine = !empty($request->getParam('recipe_cuisine')) ? $request->getParam('recipe_cuisine') : '';

        // pagination filter
        $currentPage    = !empty($request->getParam('current_page')) ? $request->getParam('current_page') : 1;
        $perPage        = !empty($request->getParam('per_page')) ? $request->getParam('per_page') : 5;
        // total number of recipes stored
        $totalCount     = count($this->Data);
        // number of recipes found
        $count          = 0;

        $result = [];

        if ($cuisine) {
            foreach ($this->Data as $recipe) {
                if ($recipe['recipe_cuisine'] == $cuisine) {
                    $result[] = $recipe;
                    $count++;
                }
            }

            $result = array_slice($result, (($currentPage - 1) * $perPage), $perPage);
        } else {
            $count = count($this->Data);

            $result = array_slice($this->Data, (($currentPage - 1) * $perPage), $perPage);
        }

        $body = [
            'status'        => 'ok',
            'pagination'    => [
                'total_count'   => $totalCount,
                'count'         => $count,
                'per_page'      => $perPage,
                'total_pages'   => ceil($count / $perPage),
                'current_page' => $currentPage
            ],
            'data'          => $result
        ];

        return $response->withJson($body, 200);
    }

    /**
     * Get a recipe
     *
     * @param  object $request
     * @param  object $response
     * @param  array  $args
     * @return string
     */
    public function get($request, $response, $args)
    {
        $foundRecipe = [];

        // loop over all recipes to get the one we are looking for by id
        foreach ($this->Data as $recipe) {
            if ($recipe['id'] == $args['id']) {
                $foundRecipe = $recipe;
            }
        }

        $body = [
            'status'    => 'ok',
            'data'      => $foundRecipe
        ];

        return $response->withJson($body, 200);
    }

    /**
     * Create / add / store a new recipe
     *
     * @param  object $request
     * @param  object $response
     * @return string JSON
     */
    public function create($request, $response)
    {
        // get POST body
        $input = (object) $request->getParsedBody();

        $inputValidator = v::attribute('box_type', v::notEmpty())
                        ->attribute('title', v::notEmpty())
                        ->attribute('marketing_description', v::notEmpty())
                        ->attribute('calories_kcal', v::notEmpty())
                        ->attribute('protein_grams', v::intVal()->notEmpty())
                        ->attribute('fat_grams', v::notEmpty())
                        ->attribute('carbs_grams', v::length(1, null))
                        ->attribute('recipe_diet_type_id', v::notEmpty())
                        ->attribute('season', v::notEmpty())
                        ->attribute('protein_source', v::notEmpty())
                        ->attribute('preparation_time_minutes', v::notEmpty())
                        ->attribute('shelf_life_days', v::notEmpty())
                        ->attribute('equipment_needed', v::notEmpty())
                        ->attribute('origin_country', v::notEmpty())
                        ->attribute('recipe_cuisine', v::notEmpty())
                        ->attribute('gousto_reference', v::notEmpty());

        // try validating
        try {
            $inputValidator->assert($input);
        } catch(\Respect\Validation\Exceptions\NestedValidationException $exception) {
           $body = [
                'status'  => 'error',
                'message' => $exception->getMessages()
           ];

           return $response->withJson($body, 200);
        }

        $newRecipe = [];

        // additional data just to make sure that we are writing to csv file
        // in the correct / original column order
        $newRecipe['id']                        = (count($this->Data));
        $newRecipe['id']                        = $newRecipe['id'] + 1;
        $newRecipe['created_at']                = date('d/m/Y H:i:s');
        $newRecipe['updated_at']                = '00/00/0000 00:00:00';
        $newRecipe['box_type']                  = $input->box_type;
        $newRecipe['title']                     = $input->title;
        $newRecipe['slug']                      = str_replace(' ', '-', strtolower($input->title));
        $newRecipe['short_title']               = isset($input->short_title) ? $input->short_title : '';
        $newRecipe['marketing_description']     = $input->marketing_description;
        $newRecipe['calories_kcal']             = $input->calories_kcal;
        $newRecipe['protein_grams']             = $input->protein_grams;
        $newRecipe['fat_grams']                 = $input->fat_grams;
        $newRecipe['carbs_grams']               = $input->carbs_grams;
        $newRecipe['bulletpoint1']              = isset($input->bulletpoint1) ? $input->bulletpoint1 : '';
        $newRecipe['bulletpoint2']              = isset($input->bulletpoint2) ? $input->bulletpoint2 : '';
        $newRecipe['bulletpoint3']              = isset($input->bulletpoint3) ? $input->bulletpoint3 : '';
        $newRecipe['recipe_diet_type_id']       = $input->recipe_diet_type_id;
        $newRecipe['season']                    = $input->season;
        $newRecipe['base']                      = isset($input->base) ? $input->base : '';
        $newRecipe['protein_source']            = $input->protein_source;
        $newRecipe['preparation_time_minutes']  = $input->preparation_time_minutes;
        $newRecipe['shelf_life_days']           = $input->shelf_life_days;
        $newRecipe['equipment_needed']          = $input->equipment_needed;
        $newRecipe['origin_country']            = $input->origin_country;
        $newRecipe['recipe_cuisine']            = $input->recipe_cuisine;
        $newRecipe['in_your_box']               = isset($input->in_your_box) ? $input->in_your_box : '';
        $newRecipe['gousto_reference']          = $input->gousto_reference;
        $newRecipe['rating']                    = isset($input->base) ? $input->base : '';

        $file = fopen($this->newFile,"a");
        fputcsv($file, $newRecipe);
        fclose($file);

        $body = [
            'status'    => 'ok',
            'message'   => 'new recipe added'
        ];

        return $response->withJson($body, 200);
    }

    /**
     * Update an existing recipe
     *
     * @param  object $request
     * @param  object $response
     * @param  array  $args
     * @return string
     */
    public function update($request, $response, $args)
    {
        $id = $args['id'];

        $data = $this->Data;

        $inputValidator = v::attribute('id', v::stringType()->length(1, null));

        // try validating
        try {
            $inputValidator->assert($id);
        } catch(\Respect\Validation\Exceptions\NestedValidationException $exception) {
            $body = [
                'status'  => 'error',
                'message' => $exception->getMessages()
            ];

           return $response->withJson($body, $body);
        }

        foreach ($data as $num => $recipe) {
            if (array_search($id, $recipe)) {
                // get data to update
                $input = $request->getParsedBody();

                // update
                foreach ($input as $key => $value) {
                    $data[$num][$key] = $value;
                }

                // write to file
                $file = fopen($this->filename,"w");
                foreach ($data as $singleData) {
                    fputcsv($file, $singleData);
                }

                fclose($file);

                $body = [
                    'status'        => 'ok',
                    'message'   => 'Recipe updated'
                ];

                return $response->withJson($body, 200);
            }
        }

        $body = [
            'status'  => 'ok',
            'message' => 'Recipe not found'
        ];

        return $response->withJson($body, 200);
    }

    /**
     * Rate a recipe
     *
     * @param  object $request
     * @param  object $response
     * @param  array  $args
     * @return string
     */
    public function rate($request, $response, $args)
    {
        $id = $args['id'];
        $rating = $request->getParam('rating');

        $data = $this->Data;

        $inputValidator = v::attribute('id', v::stringType()->length(1, null))
                            ->attribute('rating', v::intVal()->between(1, 5));

        $validate = (object) array('id' => $id, 'rating' => $rating);

        // try validating
        try {
            $inputValidator->assert($validate);
        } catch(\Respect\Validation\Exceptions\NestedValidationException $exception) {
            $body = [
                'status'  => 'error',
                'message' => $exception->getMessages()
            ];

           return $response->withJson($body, 200);
        }

        foreach ($data as $num => $recipe) {
            if (array_search($id, $recipe)) {
                $data[$num]['rating'] = $rating;

                $file = fopen($this->newFile,"w");
                foreach ($data as $singleData) {
                    fputcsv($file, $singleData);
                }

                fclose($file);

                $body = [
                    'status'  => 'ok',
                    'message' => 'Recipe rating updated'
                ];

                return $response->withJson($body, 200);
            }
        }

        $body = [
            'status'  => 'error',
            'message' => 'Recipe not found'
        ];

        return $response->writeJson($body, 200);
    }
}
