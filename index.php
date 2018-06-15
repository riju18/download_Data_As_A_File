<?php
  include_once 'php/database.php';
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>data download</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script src="libraries/fileSaver.js"></script>
  <script src="libraries/xlsx.full.min.js"></script>

  <style>
  #myform{
    margin-top: 20%;
  }
  #downloadFileOption{
      margin-top: 40%;
      margin-left: 20%;
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col-md-5">
        <div id="myform">
          <!-- for php -->
          <?php
            if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
              if ( isset($_POST['sub']) ) {
                 $uname = $_POST['uname'];
                 $mail  = $_POST['mail'];

                 $insert = "insert into information(name,mail) values('$uname','$mail')";
                 if ( mysqli_query( $connect , $insert ) ) {
                   echo '<div class="alert alert-success" role="alert">
                      <a href="" class="close" data-dismiss="alert">&check;</a>
                      <p class="alert-heading">data saved</p>
                    </div>';
                 }
              }
            }
           ?>
          <form class="" action="" method="post">

            <div class="form-group">
              <label for="uname">Name:</label>
              <input type="text" name="uname" id="uname" class="form-control" placeholder="" aria-describedby="helpId">
            </div>

            <div class="form-group">
              <label for="mail">Email:</label>
              <input type="email" name="mail" id="mail" class="form-control" placeholder="" aria-describedby="helpId">
            </div>

            <button type="reset" class="btn btn-sm btn-danger">clear</button> <button type="submit" name="sub" class="btn btn-sm btn-success">proceed</button>

            </form>
        </div>
      </div>
      <div class="col-md-4">
        <div class="btn-group btn-group-sm" id="downloadFileOption">
            <button type="button" class="btn btn-dark">download information as</button>
                <button class="btn btn-dark dropdown-toggle" type="button" id="triggerId" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                </button>
                <ul id="format" class="dropdown-menu" aria-labelledby="triggerId">
                    <a name="" id="csv_format" class="btn btn-primary dropdown-item" href="#" role="button">csv</a>
                    <a name="" id="excel_format" class="btn btn-primary dropdown-item" href="#" role="button">excel</a>
                    <a name="" id="pdf_format" class="btn btn-primary dropdown-item" href="#" role="button">pdf <span class="badge badge-danger">coming</span></a>
                </ul>
          </div>
      </div>
        <div class="col-md-12 col-sm-12" style="margin-top:20px;">

          <table class="table table-bordered" id="tbl_data">
            <thead>
              <tr>
                <th>name</th>
                <th>email</th>
              </tr>
            </thead>
          </table>
        </div>
    </div>
  </div>
  <script>
    $(document).ready(function () {

      // to show data in index page

      var xhr = new XMLHttpRequest();
      xhr.open('get', 'php/data_collection_from_database_as_json.php', true);
          xhr.onload = function () {
            if ( this.status == 200 ) {
                var jsonData = JSON.parse( this.responseText );

                var output = "";
                for (const i in jsonData) {

                    // display data
                    output += "<tbody><tr><td scope='row'>"+ jsonData[i].name +"</td><td>"+ jsonData[i].mail+"</td></tr></tbody>";
                }
                $("#tbl_data").append(output);

                //conditions to download as excel file
                
                var wb    = XLSX.utils.table_to_book( document.getElementById('tbl_data'), {sheet: "sheet js"} );
                var wbout = XLSX.write( wb, {
                    bookType : 'xlsx',
                    bookSST  : true,
                    type     : 'binary'
                } );
                function s2bb(s) {
                var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
                var view = new Uint8Array(buf);  //create uint8array as viewer
                for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
                return buf;
                }


                $("#excel_format").click(function () {
                    saveAs(new Blob([s2bb(wbout)],{type:"application/octet-stream"}), 'testing.xlsx');
                });

            }
         }
      xhr.send();

      // for download as csv file

      $.ajax({
        type: "GET",
        url: "php/data_collection_from_database_as_json.php",
        data: "data",
        dataType: "json",
        success: function ( data ) {

          var jsonStr = JSON.stringify( data );  //convert the json data into string
          var jsonPar = JSON.parse( jsonStr );

          var wsJson  = XLSX.utils.json_to_sheet( jsonPar );  //for json
            var csv = XLSX.utils.sheet_to_csv ( wsJson );

            function s2ab(s) {
            var buf = new ArrayBuffer(s.length); //convert s to arrayBuffer
            var view = new Uint8Array(buf);  //create uint8array as viewer
            for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; //convert to octet
            return buf;
        }


        $("#csv_format").click(function () {
            saveAs(new Blob([s2ab(csv)],{type:"application/octet-stream"}), 'testing.csv');
        });  //for json format & csv type

        }
      });
    });
  </script>
</body>
</html>
