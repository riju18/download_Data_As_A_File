<?php
  include_once 'database.php';
  $select = "select * from information";
  $result = mysqli_query( $connect, $select );
  $output = [];
  if ( $result ) {
    if ( mysqli_num_rows( $result ) > 0 ) {
      while ( $rows = mysqli_fetch_assoc( $result ) ) {
        $output[] = $rows;
      }
    }
    $jsonFormat = json_encode( $output );
    echo $jsonFormat;
  }
 ?>
