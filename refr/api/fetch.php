<?php
// Get database
include('db.php');

$query_params = null;

if ($_GET['t'] == 'a') {

  // Create query
  $query = "SELECT * FROM files ORDER BY file_id";

} else if ($_GET['t'] == 'v') {
  $query = "SELECT * FROM file_versions WHERE file_id = :fid";
  $query_params = Array(":fid" => $_GET['fid']);
}

$statement = $connect->prepare($query);

if($statement->execute($query_params))
{
 while($row = $statement->fetch(PDO::FETCH_ASSOC))
 {
  $data[] = $row;
 }

  echo json_encode($data);
}

?>
