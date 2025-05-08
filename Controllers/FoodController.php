<?php
// Controllers/FoodController.php



switch ($action) {

    /* -------------------------------------------------------------
     |  1) List foods from the USDA API
     ------------------------------------------------------------- */
    case 'listFoodAPI':
        // optional paging
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
        // fetch an array of foods
        $foods = $api->listFoods(
            pageSize:   25,
            pageNumber: $page
        );
        // render a view that loops $foods and shows "+" and "i" icons
        include 'Views/Food/List.php';
        exit();


    /* -------------------------------------------------------------
     |  2) Add (cache) a food item locally
     |     triggered by clicking the "+" next to a food in the API list
     ------------------------------------------------------------- */
    case 'addFoodItem':
        // fdcId comes via POST from the "+" form/button
        $fdcId = filter_input(INPUT_POST, 'fdcId', FILTER_VALIDATE_INT);
        if ($fdcId) {
            $db->createFoodItem((string)$fdcId);
        }
        // redirect back to the API list (or anywhere you like)
        header("Location: .?action=listFoodAPI");
        exit();


    /* -------------------------------------------------------------
     |  3) Show full details for a single food
     |     triggered by clicking the "i" icon next to a food
     ------------------------------------------------------------- */
    case 'viewFoodDetail':
        $fdcId = filter_input(INPUT_GET, 'fdcId', FILTER_VALIDATE_INT);
        if (!$fdcId) {
            die('Invalid food ID');
        }
        // pull full nutrient info from the API
        $food = $api->getFood($fdcId);
        include 'Views/Food/Detail.php';
        exit();


    /* -------------------------------------------------------------
     |  4) (Optional) List your locally cached food_items
     ------------------------------------------------------------- */
    case 'listCachedFood':
        // uses your existing dbAccess->displayRecords()
        $cached = $db->displayRecords('food_items');
        include 'Views/Food/CachedList.php';
        exit();



}
