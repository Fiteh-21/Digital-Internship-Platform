<?php
$file = '../uploads/test.txt';
if (file_put_contents($file, 'test')) {
    echo "Success: Created " . realpath($file);
} else {
    echo "Failure: Could not create file in uploads/";
}
?>
