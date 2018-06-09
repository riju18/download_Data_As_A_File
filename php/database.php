<?php
define('HOST', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DATABASE', 'infodownload');

$connect = mysqli_connect( HOST, USERNAME, PASSWORD, DATABASE );
if ( !$connect ) {
  echo mysqli_error( $connect );
}
 ?>
