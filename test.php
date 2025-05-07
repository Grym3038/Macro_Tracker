<?php

$foodId = '534358';
$apiKey = 'sQgXROR2bo2d0seQZuplJdxcHsLXrwkgGysR5afW';



$nutrientIds = [
    208, // Energy (kcal)
    203, // Protein (g)
    204, // Total lipid (fat) (g)
    606, // Fatty acids, total saturated (g)
    605, // Fatty acids, total trans (g)
    601, // Cholesterol (mg)
    205, // Carbohydrate, by difference (g)
    291, // Fiber, total dietary (g)
    269, // Sugars, total (g)
    539, // Sugars, added (g)
    307, // Sodium, Na (mg)
    306, // Potassium, K (mg)
    301, // Calcium, Ca (mg)
    303, // Iron, Fe (mg)
    304, // Magnesium (mg)
    305, // Phosphorus, P (mg)
    320, // Vitamin A, RAE (µg)
    401, // Vitamin C, total ascorbic acid (mg)
    404, // Thiamin (mg)
    405, // Riboflavin (mg)
    406, // Niacin (mg)
    415, // Vitamin B-6 (mg)
    417, // Folate, total (µg)
    418, // Vitamin B-12 (µg)
    430  // Vitamin K (phylloquinone) (µg)
];

$nutrientsParam = implode(',', $nutrientIds);


// build the URL
$url = "https://api.nal.usda.gov/fdc/v1/food/{$foodId}?format=full&nutrients={$nutrientsParam}&api_key={$apiKey}";

// initialize cURL
$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,           // return response as a string
    CURLOPT_FOLLOWLOCATION => true,           // follow redirects
    CURLOPT_TIMEOUT        => 30,             // timeout after 10 seconds
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
    ],
]);

// execute and check for errors
$response = curl_exec($ch);
if ($response === false) {
    die('cURL error: ' . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    die("API returned HTTP status {$httpCode}: {$response}");
}

// decode JSON
$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die('JSON decode error: ' . json_last_error_msg());
}

// use the data
echo 'Name: ' . htmlspecialchars($data['description'] ?? 'N/A') . "\n";
foreach ($data['foodNutrients'] as $nutrient) {

        echo 
            '<br>'
            . $nutrient['nutrient']['name']    // e.g. “Total lipid (fat)”
            . ': '
            . $nutrient['amount']              // e.g. “32.14”
            . ' '
            . $nutrient['nutrient']['unitName'] // e.g. “g”
            . '<br>';
    
}
