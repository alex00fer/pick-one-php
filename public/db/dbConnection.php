<?php

// Returns a MySQL (mysqli) connection. If the connection cannot be
// stablished ends the request
function stablish_connection_db($servername, $user, $password, $dbname)
{
       $conn = new mysqli($servername, $user, $password, $dbname);
       // Check connection
       if ($conn->connect_error) {
              $array_error = array(
                     "status" =>  "error",
                     "message" => "Connection error: " . $conn->connect_error
              );
              $json_error = json_encode($array_error, JSON_PRETTY_PRINT);
              echo $json_error;
              die();
       }
       // Force UTF-8 charset
       if (!$conn->set_charset("utf8")) {
              die("Utf8 charset could not be loaded");
       }

       return $conn;
}
