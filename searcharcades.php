<?php
session_start();
require_once 'vendor/autoload.php';
use Google\Cloud\BigQuery\BigQueryClient;
?>
<html>
<body>
<a href="/main/">Main Page</a>
</br>
<form action="" method="post">
<div>Search<textarea name="search" rows="1" cols="60"></textarea></div>
<div><input type="submit" value="submit"></div>
</form>

<div class='content'>
<?php
$projectId = 'task1-s3375912';
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

$queryJobConfig = $bigQuery->query("SELECT * FROM `task1-s3375912.arcades.arcade_names` LIMIT 100");
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

			foreach ($row['f'] as $field)
			{
				$str .= "<td>" . $field['v'] . "</td>";
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