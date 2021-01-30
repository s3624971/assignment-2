<?php
$string = $_SESSION['id'];
$string = str_replace(' ','',$string);
?>
<html>
<head>
  <title>Change Profile Picture</title>
  <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
  <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<h1>Change Profile Picture</h1>
<hr>
<?php include 'random_headers_2.php'; ?>
<?php
echo "<p>Username: $_SESSION[id]</p>"; ?>
<form action="" method="POST" enctype="multipart/form-data">
         <input type="file" name="image" />
         <input type="submit"/>
</form>
<img class='userpic' src="https://storage.googleapis.com/36249713375912-userpics/<?php echo $string;?>.jpg" alt="profile">
<p>
  <a href="/">Back to Home Page</a>
</p>
</body>
</html>

<?php

require_once 'vendor/autoload.php';

//define("PROJECT_ID", [[task1-s3375912]]);
//define("BUCKET", [[trybucket-1]]);

use Google\Cloud\Storage\StorageClient;

$storage = new StorageClient([
    'projectId' => $projectId
]);


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
$source = $_FILES['image']['tmp_name'];
$file = fopen($source, 'r');
$bucket = $storage->bucket('36249713375912-userpics');
$object = $bucket->upload($file, [
        'name' => $string.".jpg",
		'metadata' => [
          'cacheControl' => 'public, max-age=15, no-transform',
        ]
    ]);

$acl = $object->acl();
$acl->update('allUsers', 'READER');

//$object->setCacheControl('public, max-age=15, no-transform');
//$object->reload();
}
?>