<?php include('Views/_partials/header.php'); ?>


<?php
// Views/Foods.php

// 1) Autoload your API client
require __DIR__ . '/../Models/foodApiAccess.php';

// 2) Init variables
$error      = '';
$page       = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$query      = isset($_GET['query']) ? trim($_GET['query']) : '';
$foods      = [];
$totalPages = null;
$currentPage = $page;

// 3) Fetch from API
$client = new FoodApiAccess();
try {
    if ($query !== '') {
        // search returns a wrapper with 'foods'
        $response = $client->searchFood($query, 25, $page);
        $foods       = $response['foods'] ?? [];
        $totalPages  = $response['totalPages']  ?? null;
        // USDA returns currentPage zero‑based; convert to 1‑based
        $currentPage = isset($response['currentPage'])
            ? $response['currentPage'] + 1
            : $page;
    } else {
        // listFoods returns a flat array of food items
        $foods = $client->listFoods(25, $page);
    }
} catch (\Exception $e) {
    $error = $e->getMessage();
}
?>
<div class="w-full gap-16 items-center py-8 px-4 mx-auto">
    <form  method="get" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class=" max-w-md mx-auto">   
        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only ">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input  
                type="text"
                name="query"
                placeholder="Search foods…"
                value="<?= htmlspecialchars($query) ?>"
                id="default-search" 
                class=  "block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 
                        rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
                        required />
            <button 
                type="submit" 
                class="text-white absolute end-2.5 bottom-2.5 bg-blue-700 hover:bg-blue-800 focus:ring-4 
                    focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">Search</button>
        </div>
    </form>





    <!-- Error message -->
    <?php if ($error): ?>
        <p style="color: crimson;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

     <!-- Food list -->
     <div class=" grid grid-cols-8 gap-4">
    <?php if (empty($foods)): ?>
        <div class="card w-96 bg-base-100 card-xs shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-white">No Food :(</h2>
                    <div class="justify-end card-actions">
                    </div>
                </div>
            </div>
    <?php else: ?>
        <?php foreach ($foods as $food): ?>
            <div class="card w-50 bg-base-100 card-xs shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-white"><?= htmlspecialchars($food['description'] ?? 'No description') ?></h2>
                    <?php foreach ($food['foodNutrients'] as $Nutrient): ?>
                        <?php if ($Nutrient['number'] == 208): ?>
                            <p class="text-white"><?= htmlspecialchars( 'Calories: ' . $Nutrient['amount'] . ' ' . $Nutrient['unitName'] ) ?></p>
                        <?php endif; ?>

                    <?php endforeach; ?>
                    
                    <div class="justify-end card-actions">
                    <button class="btn btn-primary">Add</button>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
    </div>



  

    <!-- Pagination controls -->
    <div class="pagination max-w-md mx-auto">
    <div class="join">

        <?php
        // Prev arrow (only if we're past page 1)
        if ($currentPage > 1):
            $prevParams = ['page' => $currentPage - 1];
            if ($query !== '') { $prevParams['query'] = $query; }
        ?>
            <a href="?action=list& <?= http_build_query($prevParams) ?>" class="join-item btn">«</a>
        <?php endif; ?>
        <button class="join-item btn"><?php echo $currentPage?></button>

        <?php

        // Next arrow (if totalPages is known, don’t exceed it;
        // otherwise always show it to let user keep paging list)
        $showNext = $totalPages === null
            ? true
            : ($currentPage < $totalPages);

        if ($showNext):
            $nextParams = ['page' => $currentPage + 1];
            if ($query !== '') { $nextParams['query'] = $query; }
        ?>        
            <a href="?action=list&<?= http_build_query($nextParams) ?>" class="join-item btn">»</a>
        <?php endif; ?>
        </div>

    </div>


    </div>
    <?php include('Views/_partials/footer.php'); ?>
