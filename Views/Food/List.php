<?php include('Views/_partials/header.php'); ?>

<?php
// Views/Foods.php

// 1) Autoload your API client
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

        <!--<?php foreach ($foods as $food): ?>-->
                    <!--<h3 class="card-title text-blac"><?= htmlspecialchars($food['fdcId'] ?? 'No fdcId') ?></h3>-->

        <!--<?php endforeach; ?>-->


<div class="w-full gap-16 items-center py-8 px-4 mx-auto">
    <form  method="get" action="?action=listFoodAPI" class=" max-w-md mx-auto">   
      <input type="hidden" name="action" value="listFoodAPI">

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
        <div class="card min-w-50 w-96 bg-base-100 card-xs shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-white">No Food :(</h2>
                    <div class="justify-end card-actions">
                    </div>
                </div>
            </div>
    <?php else: ?>
        
    
        <?php foreach ($foods as $food): ?>
            <div class="card w-60 bg-base-100 card-xs shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-white"><?= htmlspecialchars($food['description'] ?? 'No description') ?></h2>
                    <h3 class="card-title text-white"><?= htmlspecialchars($food['fdcId'] ?? 'No fdcId') ?></h3>

                    <?php foreach ($food['foodNutrients'] as $Nutrient): ?>
                        <?php if ($query == ''): ?>

                        <?php if ($Nutrient['number'] == 208): ?>
                            <p class="text-white"><?= htmlspecialchars( 'Calories: ' . $Nutrient['amount'] . ' ' . $Nutrient['unitName'] ) ?></p>
                        <?php endif; ?>
                        <?php else: ?>
                             <?php if ($Nutrient['nutrientNumber'] == 208): ?>
                            <p class="text-white"><?= htmlspecialchars( 'Calories: ' . $Nutrient['value'] . ' ' . $Nutrient['unitName'] ) ?></p>
                        <?php endif; ?>
                        <?php endif; ?>

                    <?php endforeach; ?>
                    
                    <div class="justify-end card-actions">
                    <form class="add-food-form" method="post">
                      <input type="hidden" name="user_id"      value="<?= $_SESSION['UserId'] ?>">
                      <input type="hidden" name="food_item_id" value="<?= $food['fdcId'] ?>">
                      <label class="text-white" for="quantity">Choose quantity:</label>
                      <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        required
                        placeholder="999"
                        class="…"
                      />
                      <button type="submit" class="btn btn-primary">Add</button>
                    </form>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>
    </div>


<dialog id="my_modal_1" class="modal">
  <div class="modal-box">
    <h3 class="text-lg font-bold">Hello!</h3>
    <p class="py-4">Press ESC key or click the button below to close</p>
    <div class="modal-action">
      <form method="dialog">
        <!-- if there is a button in form, it will close the modal -->
        <button class="btn">Close</button>
      </form>
    </div>
  </div>
</dialog>

  

    <!-- Pagination controls -->
    <div class="pagination max-w-md mx-auto">
    <div class="join">

        <?php
        // Prev arrow (only if we're past page 1)
        if ($currentPage > 1):
            $prevParams = ['page' => $currentPage - 1];
            if ($query !== '') { $prevParams['query'] = $query; }
        ?>
            <a href="?action=listFoodAPI& <?= http_build_query($prevParams) ?>" class="join-item btn">«</a>
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
            <a href="?action=listFoodAPI&<?= http_build_query($nextParams) ?>" class="join-item btn">»</a>
        <?php endif; ?>
        </div>

    </div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<script>
$(document).on('submit', '.add-food-form', function(event) {
  event.preventDefault();

  var $form   = $(this);
  var payload = $form.serialize();

  $.ajax({
    type: 'POST',
    url: 'Models/ajax_createFoodLog.php',
    data: payload,
    dataType: 'json'
  })
  .done(function(response) {
    if (response.success) {
      // you could update the UI inline instead of alert…
      alert('Logged! New ID: ' + response.log_id);
    } else {
      alert('Error: ' + response.error);
    }
  })
  .fail(function(jqXHR, status, error) {
    alert('Request failed: ' + error);
  });
});
</script>





    </div>
    <?php include('Views/_partials/footer.php'); ?>
