<?php
  require 'db.php';
  require 'ipr_api.php';
  function get_client_ip() {
      $ipaddress = '';
      if (getenv('HTTP_CLIENT_IP'))
          $ipaddress = getenv('HTTP_CLIENT_IP');
      else if(getenv('HTTP_X_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
      else if(getenv('HTTP_X_FORWARDED'))
          $ipaddress = getenv('HTTP_X_FORWARDED');
      else if(getenv('HTTP_FORWARDED_FOR'))
          $ipaddress = getenv('HTTP_FORWARDED_FOR');
      else if(getenv('HTTP_FORWARDED'))
         $ipaddress = getenv('HTTP_FORWARDED');
      else if(getenv('REMOTE_ADDR'))
          $ipaddress = getenv('REMOTE_ADDR');
      else
          $ipaddress = 'UNKNOWN';
      return $ipaddress;
  }

  $ip = get_client_ip();


  if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
  }

  $added_new = false;
  $added_url = "";
  $added_name = "";
  $overthelimits = false;
  $doesnotexist = false;
  $showpolicy = false;
  try {
    if (isset($_GET['x'])) {
      if ($_GET['x'] == 'policy') {
        $showpolicy = true;
        throw new Exception("Policy shown");
      }
    }
    if (isset($_GET['u'])) {
      $u_value = $_GET['u'];
      $query = "SELECT url FROM lookup WHERE value = :value;";
      $query_params = Array(":value" => $u_value);
      $result = getAll($query, $db, $query_params);
      if (count($result) < 1) {
        $doesnotexist = true;
        throw new Exception("No items in database");
      }
      $url = $result[0]["url"];

      header("Location: " . $url);
      die();
    } else {

      if (isset($_POST['n']) && isset($_POST['a'])) {
        if (checkIP($db, $ip)) {
          $query = "INSERT INTO lookup VALUES (:url, :name);";
          $query_params = Array(":name" => $_POST['n'], ":url" => $_POST['a']);
          $statement = $db->prepare($query);
          $statement->execute($query_params);
          $added_url = $_POST['a'];
          $added_name = $_POST['n'];
          $added_new = true;
        } else {
          $overthelimits = true;
        }
      }

      throw new Exception("URL value not provided.");
    }

  } catch (Exception $e) {
    ?>
    <html>
    <head>
      <!-- Meta tags -->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
      <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
      <link href="quizem.css" rel="stylesheet">

      <!-- Page title -->
      <title>Redirector</title>
    </head>
    <body>
      <div class="container" style="max-width:680px">
        <h1 class="mt-5">Generate a shortened link.</h1>
        <p class="lead">Enter in the follow form to generate a shortened link.</p>
        <p>
          <form action="./" method="post">
            <input type="text" name="n" placeholder="name for link" />
            <input type="text" name="a" value="" placeholder="URL" />
            <input type="submit" value="Generate!">
          </form>
          <?php
            //echo "Hello $ip";
            if ($added_new) {
              echo "<hr />";
              echo "<p class='text-success'>Link generated!</p>";
              echo "<input type='text' value='https://jamesjohnston.xyz/r/$added_name'/>";
            } else if ($overthelimits) {
              echo "<hr />";
              echo "<p class='text-danger h5'>Unable to generate link, you've reached the daily limit.</p>";
              echo "<p>Please wait 24 hours to generate more.</p>";
              echo "<p>Questions? Concerns? Contact me <a href='mailto:admin@jamesjohnston.xyz'>here</a>, also check the <a href='?x=policy'>site policies here</a>.</p>";
            } else if ($doesnotexist) {
              echo "<hr />";
              echo "<p class='text-warning h5'>URL link not found. :(</p>";
              echo "<p>It could have expired, been deleted by the owner, or removed for breaking our <a href='?x=policy'>policy</a>.</p>";
            } else if ($showpolicy) {
              echo "<hr />";
              echo "<p class='h4'>Website Policies</p>";
              echo "<p>Breaking any of these policies will allow me to ban your from my site and throttle service uses on my site too.</p>";
              echo "<div>";
              echo "<ol>";
                echo "<li>Do not attempt to reverse engineer or bypass my code that is live - I appreciate feedback regarding mistakes in my code though. <a href='mailto:admin@jamesjohnston.xyz'>Email me here</a>.</li>";
                echo "<li>No inappropriate redirects - this includes pornography, spam, violence, viruses, hacks, exploits, etc.</li>";
                echo "<li>Every user is allowed to generate 10 redirects a day.</li>";
                echo "<li>Have fun. :)</li>";
              echo "</ol>";
              echo "</div>";
            }
          ?>
        </p>
      </div>
    </body>
    </html>
    <?php
  }
?>
