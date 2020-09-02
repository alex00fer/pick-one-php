<?php
// This php is separated from index.php to avoid duplicated
// submits. See pattern https://en.wikipedia.org/wiki/Post/Redirect/Get

// Needed for the session id
session_start();

require './utils/config_ini.php';
$conf = get_config_ini();

require './db/dbConnection.php';
$conn = stablish_connection_db(
  $conf['mysql_host'],
  $conf['mysql_user'],
  $conf['mysql_password'],
  $conf['mysql_db']
);

// Check if there is a response submitted
if (!empty($_POST["selected"]) && !empty($_POST["rejected"])) {

  // prepare needed data
  $selected = mysqli_real_escape_string($conn, $_POST['selected']);
  $rejected = mysqli_real_escape_string($conn, $_POST['rejected']);
  $session_id = session_id();

  // prepare and bind prepared statement
  $stmt = $conn->prepare("INSERT INTO responses (selected_img, rejected_img, session_id) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $selected, $rejected, $session_id);

  // execute and close everything
  $stmt->execute();
  $stmt->close();
  $conn->close();
}

// Redirect to home page
header('Location: ./index.php');
