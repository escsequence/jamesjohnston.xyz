<?php
  function getAll($sql, $db, $parameterValues = null) {

    // Prepare the SQL with the statement provided
    $statement = $db->prepare($sql);

    // Execute the query
    $statement->execute($parameterValues);

    // Return the fetchAll statement passed in.
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  function checkIP($db, $ip) {
    // Get info based on IP
    $query = "SELECT ip, lastdt, count FROM iplog WHERE ip = :ip";
    $query_params = Array(":ip" => $ip);
    $ret = getAll($query, $db, $query_params);

    if (count($ret) > 0) {

      // Get the curent date according to mysql
      $querycd = "SELECT DATE(NOW()) AS 'cd';";
      $ret2 = getAll($querycd, $db, NULL);
      $current_date = $ret2[0]["cd"];

      $count = intval($ret[0]["count"]);
      $lastdt = $ret[0]["lastdt"];

      if ($current_date == $lastdt) {
        // Dates match, update the count
        $count = $count + 1;
        //echo $count;
      } else {
        // Dates do not match, do not update the count - but update the date
        $count = 1;
        $lastdt = $current_date;
      }


      //echo
      if ($count > 10) {
        // no moe.
        return false;
      } else {
        // moe.
        // Log this.
        $query = "UPDATE iplog SET lastdt = DATE(:dt), count = :count WHERE ip = :ip;";
        $query_params = Array(":ip" => $ip, ":dt" => $lastdt, ":count" => $count);
        $statement = $db->prepare($query);
        $statement->execute($query_params);

        return true;
      }

    } else {
      //echo "No user, user has been logged.";
      $query = "INSERT INTO iplog (ip, lastdt, count) VALUES (:ip, NOW(), 1);";
      $statement = $db->prepare($query);
      $statement->execute($query_params);
      return true;
    }
  }

  function generateRandomName($amt) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $ret = '';
    for ($i = 0; $i < $amt; $i++) {
      $ret .= $chars[rand(0, strlen($chars)-1)];
    }
    return $ret;
  }

?>
