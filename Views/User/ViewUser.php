
<?php
include('Views/_partials/header.php');

date_default_timezone_set('America/Chicago');

$today = date('l');

$caloriesToday = $dailyTotals[$today]['Energy'] ?? 0;
$ProteinToday = $dailyTotals[$today]['Protein'] ?? 0;

?>



<div>
    
<div class=" mx-5 w-full bg-white rounded-lg shadow-sm dark:bg-gray-800 p-4 md:p-6">
  <div class="flex justify-between pb-4 mb-4 border-b border-gray-200 dark:border-gray-700">

    <div>

    </div>
  </div>

  <div class="grid grid-cols-2">
    <dl class="flex items-center">
        <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal me-1">Colories today:</dt>
        <dd class="text-gray-900 text-sm dark:text-white font-semibold"><?= number_format($caloriesToday) ?> kcal</dd>
    </dl>
    <dl class="flex items-center justify-end">
        <dt class="text-gray-500 dark:text-gray-400 text-sm font-normal me-1">Protein intake:</dt>
        <dd class="text-gray-900 text-sm dark:text-white font-semibold"><?= number_format($ProteinToday)?> G</dd>
    </dl>
  </div>

  <div id="column-chart"></div>
    <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between">
      <div class="flex justify-between items-center pt-5">

      </div>
    </div>
    
</div>




<div class="w-full gap-16 items-center py-8 px-4 mx-10" data-theme="light">
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100 w-full">
  <table class="table">
        <caption class="text-lg font-bold ">EATEN TODAY</caption>

    <!-- head -->
    <thead>
      <tr>
        <th>Name</th>
        <th>Protein</th>
        <th>Fat</th>
        <th>Carbs</th>

      </tr>
    </thead>
    <tbody>
    <?php if (empty($logsWithFood)): ?>
      <tr>
        <td colspan="5">No Data</td>


      </tr>
    <?php else: ?>

        <?php foreach ($logsWithFood as $entry): ?>
        <tr>

            <td><?= htmlspecialchars($entry['food']['description'] ?? 'No description') ?></td>
            
                    <?php
                    $nutrients = $entry['food']['foodNutrients'] ?? [];

                    foreach ($nutrients  as $Nutrient): ?>
                            <td><?= htmlspecialchars( $Nutrient['amount'] . ' ' . $Nutrient['unitName'] ) ?></td>
                    <?php endforeach; ?>
          
            <td>
                <form class="remove-food-form" method="post">
                    <input type="hidden" name="logID"      value="<?=  htmlspecialchars($entry['logID']) ?>">
                    <button type="submit" class="btn btn-primary">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>

    <?php endif; ?>
    </tbody>
  </table>
</div>
    
</div>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<script>
$(document).on('submit', '.remove-food-form', function(event) {
  event.preventDefault();
  const $form = $(this);
  const $row  = $form.closest('tr');
  const payload = $form.serialize();

  $.ajax({
    type: 'POST',
    url: 'Models/ajax_deleteFoodLog.php',
    data: payload,
    dataType: 'json'
  })
  .done(function(response) {
    if (response.success) {
      // 1) Remove the row
      alert("It's gone :(");
      $row.fadeOut(200, () => $row.remove());

      // 2) Refresh the chart data
      $.getJSON('Models/ajax_getDailyTotals.php', {
        userId: <?= json_encode($_SESSION['UserId'], JSON_NUMERIC_CHECK) ?>
      })
      .done(function(dailyTotals) {
        const shortDay = d => d.slice(0,3);
        const nutrients = Object.keys(
          dailyTotals[Object.keys(dailyTotals)[0]] || {}
        );
        const newSeries = nutrients.map(nutrient => ({
          name: nutrient,
          data: Object.entries(dailyTotals).map(([day, vals]) => ({
            x: shortDay(day),
            y: vals[nutrient] || 0
          }))
        }));
        chart.updateSeries(newSeries, true);
      })
      .fail(function(_, status, err) {
        console.error('Failed to reload totals:', status, err);
      });

    } // <-- close the if block here

  }) // end .done()
  .fail(function(jqXHR, status, error) {
    alert('Delete request failed: ' + error);
  });
});
</script>


<script>
      const dailyTotals = <?= json_encode($dailyTotals, JSON_HEX_TAG) ?>;
      const shortDay = day => day.slice(0,3);
      const nutrients = Object.keys(
        dailyTotals[Object.keys(dailyTotals)[0]] || {}
      );

      const series = nutrients.map(nutrient => ({
        name: nutrient,
        data: Object.entries(dailyTotals).map(([day, vals]) => ({
          x: shortDay(day),
          y: vals[nutrient] || 0
        }))
      }));


const options = {
colors: [
  "#1A56DB", // Blue
  "#FDBA8C", // Peach
  "#34D399", // Mint
  "#F47174", // Coral
  "#A78BFA", // Lavender
  "#FDE047", // Yellow
  "#F43F5E", // Pink
  "#3B82F6", // Light Blue
  "#10B981"  // Emerald
],  series,
  chart: {
    type: "bar",
    height: "320px",
    fontFamily: "Inter, sans-serif",
    toolbar: {
      show: false,
    },
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: "70%",
      borderRadiusApplication: "end",
      borderRadius: 8,
    },
  },
  tooltip: {
    shared: true,
    intersect: false,
    style: {
      fontFamily: "Inter, sans-serif",
    },
  },
  states: {
    hover: {
      filter: {
        type: "darken",
        value: 1,
      },
    },
  },
  stroke: {
    show: true,
    width: 0,
    colors: ["transparent"],
  },
  grid: {
    show: false,
    strokeDashArray: 4,
    padding: {
      left: 2,
      right: 2,
      top: -14
    },
  },
  dataLabels: {
    enabled: false,
  },
  legend: {
    show: false,
  },
  xaxis: {
    floating: false,
    labels: {
      show: true,
      style: {
        fontFamily: "Inter, sans-serif",
        cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
      }
    },
    axisBorder: {
      show: false,
    },
    axisTicks: {
      show: false,
    },
  },
  yaxis: {
    show: false,
  },
  fill: {
    opacity: 1,
  },
}

if(document.getElementById("column-chart") && typeof ApexCharts !== 'undefined') {
  const chart = new ApexCharts(document.getElementById("column-chart"), options);
  chart.render();
}

</script>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<script>

$('#target').submit(function(event) {

	event.preventDefault();
	
	$.ajax({
		type: 'POST',
		url: 'http://localhost:8000/CIS266/RESTful/MurachDB_Sample4/ServiceProvider/MDB_Tables_API.php',
		data: 'NA', 
		dataType: 'json',
		encode: true
	})
	.done(function(data) {
		$('#aMessage').text(data.tablesList);
	})
	.fail(function() {
		$('#aMessage').text('An error occurred. Please try again.');
	});

	});

</script>

</div>


<?php include('Views/_partials/footer.php'); ?>