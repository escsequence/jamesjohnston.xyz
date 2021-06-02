<?php
  require 'quiz_api.php';


  // Attempt to get the quiz id if we triggered the start/review button previously.
  $qid = isset($_POST['qid']) ? $_POST['qid'] : null;

  // Action - s = start, r = review, (default or nothing) = show an option for what quiz to start.
  $action = isset($_POST['a']) ? $_POST['a'] : null;


  if (isset($_GET['qid'])) {
    if ($action == null) {
      $qid = $_GET['qid'];
      $action = "start";
    }
  }

  $error = null;

  // Super-duper safe check just in-case someone got here by manipulating data
  if ($action == "start" || $action == "results") {
    if ($qid != null) {
      $qms_arr = QM_PULL($db, $qid);
      if (empty($qms_arr)) {
        $action = "";
        $error = "invalid_qid";
      }
    } else {
      $action = "";
      $error = "no_qid";
    }
  }

?>

<!DOCTYPE html>
<html>
  <head>
    <!-- Meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="quizem.css" rel="stylesheet">

    <!-- Page title -->
    <title>QUIZEM</title>
  </head>
  <body>
    <!-- General navigation for the HTML page. -->
    <nav>
      <div class="nav-inne">
        <div class="nav navbar navbar-expand-lg navbar-dark bg-dark p-2 w-100">
          <a class="navbar-brand fw-bold" href="./">
            QUIZEM
          </a>
          <span class="text-white">Created by James Johnston</span>
        </div>
      </div>
    </nav>
    <!-- Main body of the HTML page. -->
    <?php
      switch ($action) {

        // Start is shown we are "starting" the quiz.
        case 'start':
        ?>
        <div class="content container-fluid mt-2">
          <div class="exam" id="final-exam-part-2">
            <form method="post" id="final-exam-form">
              <div id="qq-n" class="card mb-4">
                <div class="card-header bg-secondary text-white">
                  Name
                </div>
                <div class="card-body">
                  <div class="form-group">
                    <label for="name-input" class="mb-2">Name</label>
                    <input type="name" class="form-control" id="name-input" placeholder="Enter first name" name="name">
                  </div>
                  <div class="area-for-warning">

                  </div>
                  <div class="fw-bold mt-3">
                    Please note that ALL the fields below are required fields unless specified.
                  </div>
                </div>
              </div>
              <?php
                $qms_dt = $qms_arr[0]["qms"];

                if (QM_VERIFY($qms_dt)) {
                  $qms_cdt = QM_CONVERT($qms_dt);

                  for($i = 0; $i < $qms_cdt["qc"]; $i++) {
                    generate_question($qms_cdt["qi"], $qid, $i, $qms_cdt["qt"], $qms_cdt["qa"]);
                  }
                }
              ?>
              <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                  Submission
                </div>
                <div class="card-body">
                  <input type="hidden" name="qid" value="<?php echo $qid; ?>">
                  <input type="hidden" name="a" value="results">
                  <button id="submit-quiz-button" type="button" class="btn btn-primary">Submit</button>
                  <button id="reset-quiz-button" type="button" class="btn btn-secondary">Reset</button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <?php
        break;

        // Results are shown when the user is done with the quiz.
        case 'results':
          $qms_dt = $qms_arr[0]["qms"];

          if (QM_VERIFY($qms_dt)) {
            $qms_cdt = QM_CONVERT($qms_dt);

            $count = $qms_cdt["qc"];
            $user_answers = array();
            $correct_answers = 0;
            $name = $_POST['name'];

            $valid_data = true;

            for ($qix = 0; $qix < $count; ++$qix) {
              $qixt = 'q' . $qix;

              $valid_data = isset($_POST[$qixt]) ? $valid_data : false;
            }


            if ($valid_data) {
              $quiz_answers = $qms_cdt["an"];
              $quiz_answer_selection = $qms_cdt["qt"];
              $quiz_answers_options = $qms_cdt["qa"];

              echo "<div class='content container-fluid mt-2'>";
                echo "<div class='exam' id='final-exam-part-3'>";
                  echo "<div class='card'>";
                    echo "<div class='card-header bg-primary text-white'>Results</div>";
                    echo "<div class='card-body'>";
                      echo "<table class='table' id='table-results'>";
                        echo "<thead>";
                          echo "<tr>";
                            echo "<th scope='col'>Question #</th>";
                            echo "<th scope='col'>Your answer</th>";
                            echo "<th scope='col'>Correct answer</th>";
                            echo "<th scope='col'>Result</th>";
                          echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                          for($iv = 0; $iv < $count; ++$iv) {
                            $answer_is_correct = false;
                            $user_answer = $_POST['q' . $iv];
                            $correct_answer = $quiz_answers[$iv];

                            echo "<tr>";
                            echo "<td>" . ($iv+1) . "</td>";

                            $type = $quiz_answer_selection[$iv];
                            $type = trim(str_replace("\"", "", $type));

                            if ($type == "t/f") {
                              echo "<td>" . $user_answer . "</td>";

                              if (verify_array($quiz_answers_options, $quiz_answers, $iv)) {
                                echo "<td>T</td>";
                                if ($user_answer == "T") {
                                  $correct_answers++;
                                  $answer_is_correct = true;
                                }
                              } else {
                                echo "<td>F</td>";
                                if ($user_answer == "F") {
                                  $correct_answers++;
                                  $answer_is_correct = true;
                                }
                              }
                            } else {
                              echo "<td>" .  $user_answer . "</td>";
                              echo "<td>" . $correct_answer . "</td>";

                              if (strval($user_answer) == strval($correct_answer)) {
                                $correct_answers++;
                                $answer_is_correct = true;
                              }
                            }
                            if ($answer_is_correct) {
                              echo "<td class='text-success fw-bold'>Correct <i class='fa fa-check'></i></td>";
                            } else {
                              echo "<td class='text-danger fw-bold'>Incorrect <i class='fa fa-times'></i></td>";
                            }
                            echo "</tr>";
                          }
                        echo "</tbody>";
                      echo "</table>";
                      echo "<hr />";
                      echo "<div>";
                      $percent = round($correct_answers / $count * 100);
                      echo "<div><span class='fw-bold'>Name:</span> $name</div>";
                      echo "<div>You scored $correct_answers out of $count questions correct for a score of <span class='fw-bold'>$percent%</span>.</div>";
                      echo "</div>";
                    echo "</div>";
                  echo "</div>";
                echo "</div>";
              echo "</div>";
            }
          }
          break;

        case 'generate':
          $qms = isset($_POST['qms']) ? $_POST['qms'] : null;
          echo "<div class='content container-fluid mt-2'>";
            echo "<div class='exam' id='final-exam-part-3'>";
              echo "<div class='card'>";
                echo "<div class='card-header bg-secondary text-white'>Quizem generation status</div>";
                echo "<div class='card-body'>";

                // Try to save quiz in db here
                //echo $qms;
                //$generated_quiz = QM_VERIFY($qms);

                // $qms_spl_eol = explode(PHP_EOL, $qms);
                // $parsed_content = array();
                // $set_qc = false;
                // $set_an = false;
                // $set_qi = false;
                // $set_qt = false;
                // $set_qa = false;
                // $disabled = true;
                //
                // $size_mismatch = "";
                // $no_values = false;
                //
                // if (strlen($qms) > 1) {
                //   for($i = 0; $i < count($qms_spl_eol); $i++) {
                //     $qms_spl_ei = explode("=", $qms_spl_eol[$i]);
                //     for ($ix = 0; $ix < count($qms_spl_ei); $ix++) {
                //       switch($qms_spl_ei[$ix]) {
                //         case "question_count":
                //           if (!$set_qc) {
                //             $set_qc = true;
                //             $parsed_content["qc"] = $qms_spl_ei[$ix+1];
                //           }
                //           break;
                //         case "question_info":
                //           if (!$set_qi) {
                //             $qi_list_raw = $qms_spl_ei[$ix+1];
                //             $qi_list_raw_stripped = str_replace("(", "", $qi_list_raw);
                //             $qi_list_raw_stripped = str_replace(")", "", $qi_list_raw_stripped);
                //             $qi_list = explode(",", $qi_list_raw_stripped);
                //             $parsed_content["qi"] = array();
                //             for ($ia = 0; $ia < count($qi_list); $ia++) {
                //               $qi_itm = $qi_list[$ia];
                //               array_push($parsed_content["qi"], $qi_itm);
                //             }
                //             $set_qi = true;
                //           }
                //           break;
                //         case "question_type":
                //           if (!$set_qt) {
                //             $qt_list_raw = $qms_spl_ei[$ix+1];
                //             $qt_list_raw_stripped = str_replace("(", "", $qt_list_raw);
                //             $qt_list_raw_stripped = str_replace(")", "", $qt_list_raw_stripped);
                //             $qt_list = explode(",", $qt_list_raw_stripped);
                //             $parsed_content["qt"] = array();
                //             $invalid_qt = false;
                //             for ($ia = 0; $ia < count($qt_list); $ia++) {
                //               $qt_itm = trim(str_replace("\"", "", $qt_list[$ia]));
                //               if ($qt_itm == "t/f" || $qt_itm == "multiple" || $qt_itm == "input") {
                //                 array_push($parsed_content["qt"], $qt_itm);
                //               } else {
                //                 $invalid_qt = true;
                //               }
                //             }
                //
                //             if (!$invalid_qt)
                //               $set_qt = true;
                //           }
                //           break;
                //         case "question_answers":
                //           if (!$set_qa) {
                //             $qa_list_raw = $qms_spl_ei[$ix+1];
                //             $qa_list = explode(",", $qa_list_raw);
                //             $parsed_content["qa"] = array();
                //             for ($ia = 0; $ia < count($qa_list); $ia++) {
                //               $qa_itm = $qa_list[$ia];
                //               if (str_contains($qa_itm, "(") && str_contains($qa_itm, ")")) {
                //                 $qa_itm = str_replace("(", "", $qa_itm);
                //                 $qa_itm = str_replace(")", "", $qa_itm);
                //                 $qa_x_list = explode(":", $qa_itm);
                //                 $parsed_content["qa"][$ia] = array();
                //                 for ($iax = 0; $iax < count($qa_x_list); $iax++) {
                //                   array_push($parsed_content["qa"][$ia], $qa_x_list[$iax]);
                //                 }
                //               } else {
                //                 $qa_itm = str_replace("(", "", $qa_itm);
                //                 $qa_itm = str_replace(")", "", $qa_itm);
                //                 array_push($parsed_content["qa"], $qa_itm);
                //               }
                //
                //             }
                //             $set_qa = true;
                //           }
                //           break;
                //         case "answers":
                //           if (!$set_an) {
                //             $answers_list_raw = $qms_spl_ei[$ix+1];
                //             $answers_list_raw_stripped = str_replace("(", "", $answers_list_raw);
                //             $answers_list_raw_stripped = str_replace(")", "", $answers_list_raw_stripped);
                //             $answers_list = explode(",", $answers_list_raw_stripped);
                //             $parsed_content["an"] = array();
                //             for ($ia = 0; $ia < count($answers_list); $ia++) {
                //               array_push($parsed_content["an"], $answers_list[$ia]);
                //             }
                //             $set_an = true;
                //           }
                //           break;
                //       }
                //     }
                //   }
                //   $generated_quiz = ($set_an && $set_qa && $set_qc && $set_qi && $set_qt && $set_qa);
                // } else {
                //   $generated_quiz = false;
                //   $no_values = true;
                // }
                //
                //
                //
                // if ($generated_quiz) {
                //   if (count($parsed_content["an"]) > $parsed_content["qc"]) {
                //     $size_mismatch .= "answers, ";
                //     $generated_quiz = false;
                //   }
                //
                //   if (count($parsed_content["qa"]) > $parsed_content["qc"]) {
                //     $size_mismatch .= "question_answers, ";
                //     $generated_quiz = false;
                //   }
                //
                //   if (count($parsed_content["qt"]) > $parsed_content["qc"]) {
                //     $size_mismatch .= "question_type, ";
                //     $generated_quiz = false;
                //   }
                //
                //   if (count($parsed_content["qi"]) > $parsed_content["qc"]) {
                //     $size_mismatch .= "question_info, ";
                //     $generated_quiz = false;
                //   }
                //
                // }
                //
                // if ($generated_quiz) {
                //   $generated_quiz_id = QM_INSERT($db, $qms);
                //   echo "<div class='text-success'><i class='fa fa-check'></i> Quiz generated</div>";
                //   echo "<div><span class='fw-bold'>Quizem ID:</span> $generated_quiz_id<div>";
                //   echo "<hr />";
                //   echo "<a href='./?qid=$generated_quiz_id'>Link to start quiz</a>";
                // } else {
                //   echo "<div class='text-danger'><i class='fa fa-times'></i> Invalid data in QMS provided.</div>";
                //   echo "<div><span class='fw-bold'>See error information below, then go back and update the QMS entered. </span><div>";
                //   echo "<hr />";
                //   if (!$no_values) {
                //
                //     echo "<ul>";
                //     if (!$set_qc) {
                //       echo "<li>Missing or invalid <code>question_count</code> section.</li>";
                //     }
                //
                //     if (!$set_qi) {
                //       echo "<li>Missing or invalid <code>question_info</code> section.</li>";
                //     }
                //
                //     if (!$set_qa) {
                //       echo "<li>Missing or invalid <code>question_answers</code> section.</li>";
                //     }
                //
                //     if (!$set_qt) {
                //       echo "<li>Missing or invalid <code>question_type</code> section.</li>";
                //     }
                //
                //     if (!$set_an) {
                //       echo "<li>Missing or invalid <code>answers</code> section.</li>";
                //     }
                //
                //     if ($size_mismatch != "") {
                //       echo "<li>Array size mis-match of section(s): $size_mismatch</li>";
                //     }
                //
                //     echo "</ul>";
                //   } else {
                //       echo "<div>No value entered in QMS form.</div>";
                //   }
                //
                //
                //   //echo "Work in progress. :/";
                // }
                echo "Whoops. Generating a Quizem is currently disabled. Please check back another time.";
                //echo generate_qid();
                echo "</div>";
              echo "</div>";
            echo "</div>";
          echo "</div>";


          break;

        // This action is shown when the user is just coming to the site - or something else happened.
        default:

          echo <<< HEADER_START_QUIZ
            <div class="content container-fluid mt-2">
              <div class="exam" id="final-exam-part-1">
                <div class="card">
                  <div class="card-header bg-secondary text-white">
                    Start an existing quiz
                  </div>
          HEADER_START_QUIZ;

          if ($error == "invalid_qid") {
            echo "<div class='alert alert-danger m-2 p-2' role='alert'>Invalid Quizem ID, re-enter and try again.</div>";
          } else if ($error == "no_qid") {
            echo "<div class='alert alert-danger m-2 p-2' role='alert'>No Quizem ID entered, enter something and try again.</div>";
          }

          echo <<< BODY_START_QUIZ
                  <form class="submit-form" method="get">
                    <div class="card-body">
                      <div class="form-group">
                        <label for="quiz-id-input">Enter Quizem ID: </label>
                        <input type="text" class="form-control" id="quiz-id-input" placeholder="XXXX-XXXX-XXXX" name="qid">
                      </div>
                      <div class="div-hr">
                        <hr />
                      </div>
                      <div class="start-button">
                        <button type="submit" class="btn btn-primary">Start!</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
        BODY_START_QUIZ;

        echo <<< CREATE_NEW_QUIZ
          <div class="content container-fluid mt-2">
            <div class="exam" id="final-exam-part-1">
              <div class="card">
                <div class="card-header bg-secondary text-white panel-title" data-toggle="collapse" href="#generateQuizCollapse" role="button">
                  Create new quiz
                </div>
                <form class="submit-form collapse" method="post" id="generateQuizCollapse">
                  <div class="card-body">
                    <div class="form-group">
                      <label for="qms-form">Enter valid QM syntax: </label>
                      <textarea id="qms-form" style="display:block;width:100%;min-height:150px;" name="qms"></textarea>
                    </div>
                    <div class="div-hr">
                      <hr />
                    </div>
                    <div class="start-button">
                      <input type="hidden" name="a" value="generate">
                      <button type="submit" class="btn btn-primary">Generate!</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        CREATE_NEW_QUIZ;

        break;
      }
    ?>


    <!-- Script includes after the content -->
    <!-- Jquery CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <!-- Bootstrap CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <script type="text/javascript">

      // This is the action when a user goes to click on the submit button
      $("#submit-quiz-button").on("click", function() {

        // First remove all the required divs we created earlier (if they dont exist, it wont be an issue.)
        $(".required").empty();

        // Create some variables to hold data
        var msg = "";
        var errors = 0;

        // Lets check and see if the name field is okay.
        if ($("#name-input").val().length < 1) {

          // Add our custom error display
          $("#qq-n").find(".card-body .area-for-warning").append("<div class='required text-danger mt-2' id='required-n'>This field is required, please enter your name.</div>");

          // Add in some information for the user when we alert them.
          msg += "Name field is missing.\n"

          // Add to the error count
          errors++;

          // Add an event handler to remove the error message on screen if fixed.
          $("#name-input").change(function() {
            if ($(this).val().length > 0)
              resolve_question("n");
          });
        }

        // Loop through to find any questions that are missing data, these are all the non-static questions.
        $(".qq").each((i, e) => {

          // Set a variable to keep track of the element in the for loop
          $e = $(e);

          // See what type of "question" we are working with.
          switch($e.attr("type")) {
            // INPUT VALIDATION
            case "input":
              if ($e.find(".form-control").val().length < 1) {
                $e.find(".card-body").append("<div class='required text-danger mt-2' id='required-" + i + "'>This field is required, please enter a value.</div>")
                $e.find(".form-control").change(function() {
                  resolve_question(i);
                });
                msg += "Question #" + (i+1) + " is missing.\n";
                errors++;
              }
              break;
            // TRUE/FALSE VALIDATION
            case "t/f":
              var found_check = false;
              $e.find(".form-check-input").each((i, e) => {
                found_check = $(e).prop('checked') ? true : found_check;
              });

              if (!found_check) {
                $e.find(".card-body").append("<div class='required text-danger mt-2' id='required-" + i + "'>This field is required, please select either true or false.</div>")
                $e.find("input[type=radio]").change(function() {
                  resolve_question(i);
                });
                msg += "Question #" + (i+1) + " not checked.\n";
                errors++;
              }
              break;
            // MULTIPLE INPUT VALIDATION
            case "multiple":
              let current_selected = $e.find(":selected").val();
              if (current_selected.length < 1) {
                $e.find(".card-body").append("<div class='required text-danger' id='required-" + i + "'>This field is required, please select an option.</div>")
                $e.find(".form-select").change(function() {
                  if ($(this).find(":selected").val().length > 0) {
                    resolve_question(i);
                  }
                })
                msg += "Question #" + (i+1) + " not selected.\n";
                errors++;
              }
              break;
          }
        });


        // Create a function that gets triggered to resolve the question - aka delete the div we made.
        var resolve_question = function(i) {
          $("#required-" + i).empty();
        };


        // We now need to verify we dont have errors, if we do then show an error and stop from submission
        if (errors > 0) {
          // Alert the user there are errors.
          alert("There are " + errors + " errors in the form.\nPlease go back and fill in information for:\n\n" + msg);

        } else {

          // Submit the action since we are good to go.
          $("#final-exam-form").submit();
        }
      });

      // When the user clicks the reset button
      $("#reset-quiz-button").on("click", function() {

        // Remove "required" divs
        $(".required").empty();

        // Reset the form
        $("#final-exam-form")[0].reset();

        // Go back up to the top of the page
        document.location = "#";
      });

      // Drop down click - used for the first page when a user is picking a quiz.
      $(".dropdown-menu > .dropdown-item").on("click", function() {

        // id temp variable to hold
        let id = $(this).attr("id");


        // The button
        let $btn = $($(this).parent().siblings()[0]);

        // Updates the drop-down button for bootstrap
        let drop_down_text = $(this).text();
        $btn.text(drop_down_text);
        $btn.val(drop_down_text);

        // Update the hidden form value that gets sent here.
        $("#quiz-selection-id").val(id);
      });
    </script>


  </body>

  <!-- Footer W.I.P -->
  <footer>
    <div class="footer">

    </div>
  </footer>
</html>
