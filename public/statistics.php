<?php

require './utils/config_ini.php';
$conf = get_config_ini();

require './db/dbConnection.php';
$conn = stablish_connection_db(
  $conf['mysql_host'],
  $conf['mysql_user'],
  $conf['mysql_password'],
  $conf['mysql_db']
);

// Apply filters

$max_results = 30;
if (isset($_GET["max-results"])) {
  // custom max results number set
  $temp_max_results = filter_var($_GET["max-results"], FILTER_SANITIZE_NUMBER_INT); // sanitize
  if ($temp_max_results != false)
    $max_results = $temp_max_results;
}

$min_votes = 1;
if (isset($_GET["min-votes"])) {
  // custom min votes number set
  $temp_min_votes = filter_var($_GET["min-votes"], FILTER_SANITIZE_NUMBER_INT); // sanitize
  if ($temp_min_votes != false)
    $min_votes = $temp_min_votes;
}

$order_by = "DESC";
if (isset($_GET["order-by"])) {
  // custom order set
  if ($_GET["order-by"] === "worst")
    $order_by = "ASC";
}



// This query got a little out of hands but was the only one I managed
// that worked perfectly on every scenario (and should be performant enough). 
// It may be possible to simplify it.
//
// Results are ordered from the most picked images to the less picked ones
//
// The query returns the following columns:
// - img -> path of the image
// - times_selected, times_rejected -> times the image was selected/rejected
// - times_total -> total times the image appeared (same as times_selected+times_rejected)
// - ratio_selected and percentage_selected -> ratio/percentage of times the image was picked over other
// Note: ^^^^^^ rato_selected may return null (because of divide by zero in case the image never was rejected)
$query = "
SELECT *, 
(times_selected + times_rejected) as times_total,
(times_selected / times_rejected) as ratio_selected,
(times_selected / (times_rejected + times_selected) * 100) as percentage_selected
FROM (
SELECT img, 
(SELECT COUNT(selected_img) FROM responses WHERE selected_img = img) as times_selected,
(SELECT COUNT(rejected_img) FROM responses WHERE rejected_img = img) as times_rejected
FROM (SELECT selected_img AS img FROM responses UNION SELECT rejected_img FROM responses) AS all_images) AS images_times
WHERE (times_selected + times_rejected) >= $min_votes
ORDER BY percentage_selected $order_by
LIMIT $max_results";

// execute the query and save the result for later
$result = $conn->query($query);
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <title>PickOne Example - Statistics</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <style>
    body {
      background-color: rgb(37, 37, 37);
      color: white;
    }

    h1 {
      color: white;
      margin: 0;
    }

    img {
      cursor: pointer;
    }

    .header {
      background-color: #66395c;
      padding: 2em;
    }

    .img-row {
      background-color: rgb(40, 40, 40);
      padding: 2em;
      border-top: 1px dotted white;
    }

    .btn-primary,
    .btn-primary:hover,
    .btn-primary:active,
    .btn-primary:visited {
      background-color: #66395c !important;
      border-color: white;
    }
  </style>

</head>

<body>

  <div class="container">
    <div class="row header align-items-center">
      <div class="col-12 text-center">
        <h1>Image statistics</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-12 p-5">
        <form class="form-inline" method="get">
          <div class="form-group m-2">
            <label for="inputMaxResults" class="mr-2">Max results</label>
            <input type="number" name="max-results" class="form-control" id="inputMaxResults" value="<?php echo $max_results ?>">
          </div>
          <div class="form-group m-2">
            <label for="inputMinVotes" class="mr-2">Min votes</label>
            <input type="number" name="min-votes" class="form-control" id="inputMinVotes" value="<?php echo $min_votes ?>">
          </div>
          <div class="form-group m-2">
            <label for="inputOrderBy" class="mr-2">Order by</label>
            <select name="order-by" class="form-control" id="inputOrderBy">
              <option value="best">Most voted</option>
              <option value="worst">Less voted</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary m-2">Apply filters</button>
        </form>
      </div>
    </div>

    <?php

    // check if the query failed
    if (!isset($result) || !$result) {
      echo "Query failed";
    }

    // Print rows for each image from results
    if ($result->num_rows > 0) {
      $position = 1;
      while ($row = $result->fetch_assoc()) {
        printImageRow($position, $row);
        $position++;
      }
    } else {
      // 0 results
      echo "<div class='text-center'>There is no data yet.</div>";
    }

    function printImageRow($position, $row)
    {
      $img = $row['img'];
      $perc = number_format($row['percentage_selected'], 1, '.', '');
      $select = $row['times_selected'];
      $total = $row['times_total'];
      
      echo "<div class='row align-items-center text-center img-row'>";
      echo "<div class='col-2'><h2>$position</h2></div>";
      echo "<div class='col-4'><img onclick='window.open(this.src)' class='img-fluid' src='$img'></div>";
      echo "<div class='col-3'><h3>$perc%</h3></div>";
      echo "<div class='col-3'>$select of $total</div>";
      echo "</div>";
    }

    ?>

  </div>

</body>

</html>