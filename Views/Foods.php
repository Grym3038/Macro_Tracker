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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Food Catalog</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 2rem auto; }
        form { margin-bottom: 1.5rem; }
        ul { list-style: none; padding: 0; }
        li { padding: .5rem 0; border-bottom: 1px solid #eee; }
        .pagination a { margin: 0 .5rem; text-decoration: none; font-size: 1.2rem; }
    </style>
</head>
<body>
    <h1>Food Catalog</h1>

    <!-- Search form -->
    <form method="get" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input
            type="text"
            name="query"
            placeholder="Search foods…"
            value="<?= htmlspecialchars($query) ?>"
        >
        <button type="submit">🔍</button>
    </form>

    <!-- Error message -->
    <?php if ($error): ?>
        <p style="color: crimson;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- Food list -->
    <ul>
    <?php if (empty($foods)): ?>
        <li><em>No items found.</em></li>
    <?php else: ?>
        <?php foreach ($foods as $food): ?>
            <li>
                <strong><?= htmlspecialchars($food['description'] ?? 'No description') ?></strong>
                (FDC ID: <?= htmlspecialchars($food['fdcId'] ?? '') ?>)
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
    </ul>

    <!-- Pagination controls -->
    <div class="pagination">
        <?php
        // Prev arrow (only if we're past page 1)
        if ($currentPage > 1):
            $prevParams = ['page' => $currentPage - 1];
            if ($query !== '') { $prevParams['query'] = $query; }
        ?>
            <a href="?<?= http_build_query($prevParams) ?>">&laquo; Prev</a>
        <?php endif; ?>

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
            <a href="?<?= http_build_query($nextParams) ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</body>
</html>
