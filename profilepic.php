<?php
session_start();
echo "Username: " . $_SESSION['username'];
$string = $_SESSION['username'];
$string = str_replace(' ','',$string);
?>
<html>
<body>

<a href="/main/">Main Page</a>
</br>
<form action="" method="POST" enctype="multipart/form-data">
         <input type="file" name="image" />
         <input type="submit"/>
</form>
<br>
<img src="https://storage.googleapis.com/trybucket-1/<?php echo $string?>.jpg" alt="profile">

</body>
</html>

<?php

require_once 'vendor/autoload.php';

//define("PROJECT_ID", [[task1-s3375912]]);
//define("BUCKET", [[trybucket-1]]);

use Google\Cloud\Storage\StorageClient;

$projectId = 'task1-s3375912';

$storage = new StorageClient([
    'projectId' => $projectId
]);

$source = $_FILES['image']['tmp_name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
$file = fopen($source, 'r');
$bucket = $storage->bucket('trybucket-1');
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