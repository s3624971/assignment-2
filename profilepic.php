<?php
echo "Username: $_SESSION[id]<br>";
$string = $_SESSION['id'];
$string = str_replace(' ','',$string);
?>
<html>
<head>
  <title>Change Profile Picture</title>
  <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
</head>
<body>
<h1>Change Profile Picture</h1>
<hr>
<?php $headers_json = json_decode($header_imgs,true);
    foreach ($headers_json as $key => $value) {
      if ($key = 'cover') echo "<img src='".$value['cover']['url']."'>";
    } ?>
<hr>
<a href="/main/">Main Page</a>
</br>
<form action="" method="POST" enctype="multipart/form-data">
         <input type="file" name="image" />
         <input type="submit"/>
</form>
<br>
<img src="https://storage.googleapis.com/36249713375912-userpics/<?php echo $string;?>.jpg" alt="profile">

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