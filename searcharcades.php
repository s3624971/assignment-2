<?php
require_once 'vendor/autoload.php';
use Google\Cloud\BigQuery\BigQueryClient;
?>
<html>
<head>
  <title>Search for Gaming Arcades in Melbourne</title>
  <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
</head>
<body>
<h1>Search for Gaming Arcades in Melbourne</h1>
<hr>
<?php $headers_json = json_decode($header_imgs,true);
foreach ($headers_json as $key => $value) {
  if ($key = 'cover') echo "<img src='".$value['cover']['url']."'>";
} ?>
<hr>
<a href="/main/">Main Page</a>
</br>
<form action="" method="post">
<div><label for="search">Query: </label><input type="text" name="search" id="search"><br><small><label for="search">An arcade name to search for.</small></label></div>
<div><input type="submit" value="Search"></div>
</form>

<div class='content'>
<?php
$bigQuery = new BigQueryClient([
    'projectId' => $projectId,
]);
$str = '';
$searchstr = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	$searchstr = $_POST['search'];
}else{
	$searchstr = '*';
}

$queryJobConfig = $bigQuery->query('SELECT * FROM `assignment-2-36249713375912.arcades.arcade_names` WHERE LOWER(NAME) LIKE \'%'.strtolower($searchstr).'%\' LIMIT 100');
$queryResults = $bigQuery->runQuery($queryJobConfig);	

if ($queryResults->isComplete()) {

		$str = "<table>".
		"<tr>" .
		"<th>Name</th>" .
		"<th>Address</th>" .
		"</tr>";
		$rows = $queryResults->rows();
		
		$i = 0;
		foreach ($rows as $row) {
            printf('--- Row %s ---' . PHP_EOL, ++$i);
            foreach ($row as $column => $value) {
                printf('%s: %s' . PHP_EOL, $column, $value);
            }
			echo "<br>";
        }
		
		foreach ($rows as $row)
		{
			$str .= "<tr>";

			foreach ($row as $field)
			{
				$str .= "<td>" . $field . "</td>";
			}
			$str .= "</tr>";
		}

		$str .= '</table></div>';

		echo $str;
}else {
    throw new Exception('The query failed to complete');
}
?>
</div>

</body>
</html>