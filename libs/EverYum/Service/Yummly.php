<?php

namespace EverYum\Service;

/**
 * This class is responsible for handling Requests to Yummly
 * (http://www.yummly.com).
 *
 * @package EverYum
 * @subpackage Service
 * @author Armin Hackmann
 */
class Yummly extends Service {

    /**
     * @var string
     * @access protected
     */
    protected $applicationId;
    /**
     * @var string
     * @access protected
     */
    protected $applicationKey;
    /**
     * @var int
     * @access protected
     */
    protected $maxResult;

    /**
     * __construct
     *
     * Constructing the Service and setting the Yummly-specific
     * configuration
     *
     * @access public
     * @param \EverYum\Application $app
     * @param array $settings
     * @return void
     */
    public function __construct(\EverYum\Application $app, array $settings) {

        parent::__construct($app, $settings);

        $this->applicationId = $app->config['yummly.id'];
        $this->applicationKey = $app->config['yummly.key'];
        $this->maxResult = $app->config['yummly.maxResult'];

    }

    /**
     * yummlyRequest
     *
     * @access public
     * @param string $url
     * @param array $parameters (default: [])
     * @return void
     */
    public function yummlyRequest($url, array $parameters=array()) {

        // Build query string
        $query = http_build_query($parameters);

        // we need to get rid of the indizes in the url or yummly won't
        // use the ingredients searched for
        $query = preg_replace('/%5B[0-9]+%5D/simU', '%5B%5D', $query);

        // the actual request is done via our Service class
        $response = $this->request('GET', $url . (($query) ? '?' . $query : ''), '', array(
            'Accept' => 'application/json',
            'X-Yummly-App-ID' => $this->applicationId,
            'X-Yummly-App-Key' => $this->applicationKey,
        ));

        // decode the result
        $result = json_decode($response['body']);

        return $result;

    }

    /**
     * getRecipesByIngredients
     *
     * This function fetches recipes that match the ingredients, cuisine, diet
     * and course specified.
     * The possible values for $cuisine, $diet & $course can be found in the
     * documentation, located at /doc/yummly.md
     *
     * @access public
     * @param array $ingredients
     * @param array $cuisine (default: [])
     * @param array $diet (default: ['386^Vegan'])
     * @param array $course (default: ['course^course-Main Dishes'])
     * @return array $recipes|void
     */
    public function getRecipesByIngredients(array $ingredients, array $cuisine=array(), array $diet=array('386^Vegan'), array $course=array('course^course-Main Dishes')) {

        $url = 'recipes';

        // first, request recipes which feature EVERYTHING we have in our fridge
        $parameters = array(
            'start' => 0,
            'maxResult' => $this->maxResult,
            'allowedIngredient' => $ingredients,
            'allowedCuisine' => $cuisine,
            'allowedCourse' => $course,
            'allowedDiet' => $diet,
        );

        $recipes = $this->yummlyRequest($url, $parameters);

        // if that yielded no results, we trigger an 'OR'-search by exploiting
        // a flaw in the Yummly-API by adding a space to every ingredient
        // This might be fixed by Yummly in the future thus making this hack
        // unfeasible
        if ($recipes->totalMatchCount == 0) {

            array_walk($ingredients, function(&$value, $key) { $value .= ' '; });
            $parameters['allowedIngredient'] = $ingredients;

            $recipes = $this->yummlyRequest($url, $parameters);

        }

        return $recipes;

    }

    /**
     * getRecipe
     *
     * This function fetches full information for a recipe by its id.
     *
     * @access public
     * @param string $id
     * @return array $recipe
     */
    public function getRecipe($id) {

        $url = 'recipe/' . $id;

        try {

            $recipe = $this->yummlyRequest($url);
            return $recipe;

        } catch(\Exception $e) {

            switch($e->getCode()) {
                // Bad Request, can only mean, that the id doesn't exist
                case 400:
                    // for now, just do nothing
                    break;

                default:
                    throw $e;
                    break;
            }

        }

    }

}
