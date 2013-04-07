<?php

namespace EverYum;

/**
 * This is the everyum application
 *
 * @package EverYum
 * @author Armin Hackmann
 */
class Application {

    public $config;

    /**
     * Initializes the application.
     *
     * This class should receive an array with configuration data.
     * Sample configuration can be found in the conf/ directory.
     *
     * @param array $config
     */
    public function __construct(array $config) {

        foreach($config as $key=>$value) {
            $this->config[$key] = $value;
        }

        $this->initServices();

    }

    /**
     * Initializes services
     *
     * @return void
     */
    protected function initServices() {

        $this->service['yummly'] = new \EverYum\Service\Yummly($this->config);
        $this->service['evernote'] = new \EverYum\Service\Evernote($this->config);
        $this->service['tropo'] = new \EverYum\Service\Tropo($this->config);

    }

    public function getBestMatches(array $myItems, \stdClass $recipes) {

        $data = array();
        $myItemsCount = count($myItems);

        foreach ($recipes->matches as $recipe) {

            $score = 0;
            $ingredients = implode(",", $recipe->ingredients);
            $tobuy = $ingredients;

            // score number of matches between my fridge/grocery list and recipe ingredients
            // and either delete the ingredient in the tobuy-list or mark it as questionable
            for ($n=0; $n<$myItemsCount; $n++) {
                $item_compare = trim($myItems[$n]);

                if (stripos($tobuy, "," . $item_compare . ",") !== false) {
                    //echo "item deleted : $item_have<br>";
                    $tobuy = str_ireplace($item_compare . ",", "", $tobuy);
                    $score++;
                } elseif (stripos($tobuy, $item_compare) !== false) {
                    // echo "item deleted : $item_have<br>";
                    $tobuy = str_ireplace($item_compare, "<font color=\"red\">*$item_compare*</font>", $tobuy);
                    $score++;
                }
            }

            $data[] = array(
                'id'          => $recipe->id,
                'name'        => $recipe->recipeName,
                'ingredients' => $ingredients,
                'rating'      => $recipe->rating,
                'score'       => $score,
                'toBuy'       => $tobuy,
            );
        }

        // sort $data by score - highest first
        // in a second step, maybe also sort by rating
        $scores = array();
        foreach ($data as $key => $row) {
            $scores[$key] = $row['score'];
        }

        array_multisort($scores, SORT_DESC, $data);

        // take only top 3 high scoring recipes
        $data = array_slice($data, 0, 3, true);

        return $data;

    }

}
