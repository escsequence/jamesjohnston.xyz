<?php
  require 'db.php';
  //
  // // These are the real answers for the questions - correct ones based on a qid.
  // $quiz_answers = array(
  //   "math-quiz-final" => array(45, 845618, 30, 74, 352, 331, 148, 32, 31, 6),
  //   "test-quiz" => array(0, 0, 3, 101, 2)
  // );
  //
  // // These are questions to be displayed based on a qid.
  // $quiz_questions = array(
  //   "math-quiz-final" => array(
  //     0 => "At one school, 85 percent of the students are taking mathematics courses, 55 percent of the students are taking computer science courses, and 5 percent of the students are taking neither mathematics nor computer science courses. Find the percent of the students who are taking both mathematics and computer science courses.",
  //     1 => "Let M and m be, respectively, the greatest and the least ten-digit numbers that are rearrangements of the digits 0 through 9 such that no two adjacent digits are consecutive. Find M − m.",
  //     2 => "John is five times as old as Sara was when John was Sara’s age. When Sara reaches John’s current age, the sum of their ages will be 72. Find John’s current age.",
  //     3 => "Find the least integer n > 60 so that when 3n is divided by 4, the remainder is 2 and when 4n is divided by 5, the remainder is 1.",
  //     4 => "Find the sum of all positive integers x such that there is a positive integer y satisfying 9x2 − 4y2 = 2021.",
  //     5 => "Find the minimum possible value of |m − n|, where m and n are integers satisfying m + n = mn − 2021.",
  //     6 => "A farmer wants to create a rectangular plot along the side of a barn where the barn forms one side of the rectangle and a fence forms the other three sides. The farmer will build the fence by fitting together 75 straight sections of fence which are each 4 feet long. The farmer will build the fence to maximize the area of the rectangular plot. Find the length in feet along the side of the barn of this rectangular plot.",
  //     7 => "In base ten, the number 100! = 100 · 99 · 98 · 97 · · · 2 · 1 has 158 digits, and the last 24 digits are all zeros. Find the number of zeros there are at the end of this same number when it is written in base 24.",
  //     8 => "Find the number of distinguishable groupings into which you can place 3 indistinguishable red balls and 3 indistinguishable blue balls. Here the groupings RR-BR-B-B and B-RB-B-RR are indistinguishable because the groupings are merely rearranged, but RRB-BR-B is distinguishable from RBB-BR-R.",
  //     9 => "Three red books, three white books, and three blue books are randomly stacked to form three piles of three books each. The probability that no book is the same color as the book immediately on top of it is m/n, where m and n are relatively prime numbers. Find m + n."
  //   ),
  //   "test-quiz" => array (
  //     0 => "Question #1 is true.",
  //     1 => "Question #2 is false.",
  //     2 => "The right selection is 3.",
  //     3 => "The correct answer is `101`.",
  //     4 => "The correct answer is 1+1"
  //   )
  // );
  //
  // // Type of questions that will be asked - there are only 3 implemented: 'multiple', 't/f', and 'input'
  // $quiz_answer_selection = array(
  //   "math-quiz-final" => array(
  //     0 => "multiple",
  //     1 => "input",
  //     2 => "multiple",
  //     3 => "t/f",
  //     4 => "input",
  //     5 => "input",
  //     6 => "multiple",
  //     7 => "t/f",
  //     8 => "t/f",
  //     9 => "multiple"
  //   ),
  //   "test-quiz" => array(
  //     0 => "t/f",
  //     1 => "t/f",
  //     2 => "multiple",
  //     3 => "input",
  //     4 => "input"
  //   )
  // );
  //
  // // The answer options that will show for the user. Use 'null' if the option input, a single value for t/f, and an array for a multiple selection.
  // $quiz_answers_options = array(
  //   "math-quiz-final" => array(
  //     0 => array(40, 45, 50, 55, 60),
  //     1 => null,
  //     2 => array(10, 20, 30, 35, 40),
  //     3 => 75, // false
  //     4 => null,
  //     5 => null,
  //     6 => array(100, 140, 148, 149, 150),
  //     7 => 32, // true
  //     8 => 64, //false,
  //     9 => array(1, 3, 5, 6, 8)
  //   ),
  //   "test-quiz" => array(
  //     0 => 0,
  //     1 => 1,
  //     2 => array(1, 2, 3, 4, 5, 6),
  //     3 => null,
  //     4 => null
  //   )
  // );

  function getAll($sql, $db, $parameterValues = null) {

    // Prepare the SQL with the statement provided
    $statement = $db->prepare($sql);

    // Execute the query
    $statement->execute($parameterValues);

    // Return the fetchAll statement passed in.
    return $statement->fetchAll(PDO::FETCH_ASSOC);
  }

  if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
  }

  function QM_VERIFY($qms) {
    //echo $qms;
    $qms_spl_eol = explode(PHP_EOL, $qms);
    $parsed_content = array();
    $set_qc = false;
    $set_an = false;
    $set_qi = false;
    $set_qt = false;
    $set_qa = false;
    for($i = 0; $i < count($qms_spl_eol); $i++) {
      $qms_spl_ei = explode("=", $qms_spl_eol[$i]);
      for ($ix = 0; $ix < count($qms_spl_ei); $ix++) {
        switch($qms_spl_ei[$ix]) {
          case "question_count":
            if (!$set_qc) {
              $set_qc = true;
              $parsed_content["qc"] = $qms_spl_ei[$ix+1];
            }
            break;
          case "question_info":
            if (!$set_qi) {
              $qi_list_raw = $qms_spl_ei[$ix+1];
              $qi_list_raw_stripped = str_replace("(", "", $qi_list_raw);
              $qi_list_raw_stripped = str_replace(")", "", $qi_list_raw_stripped);
              $qi_list = explode(",", $qi_list_raw_stripped);
              $parsed_content["qi"] = array();
              for ($ia = 0; $ia < count($qi_list); $ia++) {
                $qi_itm = $qi_list[$ia];
                array_push($parsed_content["qi"], $qi_itm);
              }
              $set_qi = true;
            }
            break;
          case "question_type":
            if (!$set_qt) {
              $qt_list_raw = $qms_spl_ei[$ix+1];
              $qt_list_raw_stripped = str_replace("(", "", $qt_list_raw);
              $qt_list_raw_stripped = str_replace(")", "", $qt_list_raw_stripped);
              $qt_list = explode(",", $qt_list_raw_stripped);
              $parsed_content["qt"] = array();
              for ($ia = 0; $ia < count($qt_list); $ia++) {
                $qt_itm = $qt_list[$ia];
                array_push($parsed_content["qt"], $qt_itm);
              }
              $set_qt = true;
            }
            break;
          case "question_answers":
            if (!$set_qa) {
              $qa_list_raw = $qms_spl_ei[$ix+1];
              $qa_list = explode(",", $qa_list_raw);
              $parsed_content["qa"] = array();
              for ($ia = 0; $ia < count($qa_list); $ia++) {
                $qa_itm = $qa_list[$ia];
                if (str_contains($qa_itm, "(") && str_contains($qa_itm, ")")) {
                  $qa_itm = str_replace("(", "", $qa_itm);
                  $qa_itm = str_replace(")", "", $qa_itm);
                  $qa_x_list = explode(":", $qa_itm);
                  $parsed_content["qa"][$ia] = array();
                  for ($iax = 0; $iax < count($qa_x_list); $iax++) {
                    array_push($parsed_content["qa"][$ia], $qa_x_list[$iax]);
                  }
                } else {
                  $qa_itm = str_replace("(", "", $qa_itm);
                  $qa_itm = str_replace(")", "", $qa_itm);
                  array_push($parsed_content["qa"], $qa_itm);
                }

              }
              $set_qa = true;
            }
            break;
          case "answers":
            if (!$set_an) {
              $answers_list_raw = $qms_spl_ei[$ix+1];
              $answers_list_raw_stripped = str_replace("(", "", $answers_list_raw);
              $answers_list_raw_stripped = str_replace(")", "", $answers_list_raw_stripped);
              $answers_list = explode(",", $answers_list_raw_stripped);
              $parsed_content["an"] = array();
              for ($ia = 0; $ia < count($answers_list); $ia++) {
                array_push($parsed_content["an"], $answers_list[$ia]);
              }
              $set_an = true;
            }
            break;
        }
      }
    }
    return ($set_an && $set_qa && $set_qc && $set_qi && $set_qt && $set_qa);
  }

  function QM_CONVERT($qms) {
    //echo $qms;
    $qms_spl_eol = explode(PHP_EOL, $qms);
    $parsed_content = array();
    $set_qc = false;
    $set_an = false;
    $set_qi = false;
    $set_qt = false;
    $set_qa = false;
    for($i = 0; $i < count($qms_spl_eol); $i++) {
      $qms_spl_ei = explode("=", $qms_spl_eol[$i]);
      for ($ix = 0; $ix < count($qms_spl_ei); $ix++) {
        switch($qms_spl_ei[$ix]) {
          case "question_count":
            if (!$set_qc) {
              $set_qc = true;
              $parsed_content["qc"] = $qms_spl_ei[$ix+1];
            }
            break;
          case "question_info":
            if (!$set_qi) {
              $qi_list_raw = $qms_spl_ei[$ix+1];
              $qi_list_raw_stripped = str_replace("(", "", $qi_list_raw);
              $qi_list_raw_stripped = str_replace(")", "", $qi_list_raw_stripped);
              $qi_list = explode(",", $qi_list_raw_stripped);
              $parsed_content["qi"] = array();
              for ($ia = 0; $ia < count($qi_list); $ia++) {
                $qi_itm = $qi_list[$ia];
                array_push($parsed_content["qi"], $qi_itm);
              }
              $set_qi = true;
            }
            break;
          case "question_type":
            if (!$set_qt) {
              $qt_list_raw = $qms_spl_ei[$ix+1];
              $qt_list_raw_stripped = str_replace("(", "", $qt_list_raw);
              $qt_list_raw_stripped = str_replace(")", "", $qt_list_raw_stripped);
              $qt_list = explode(",", $qt_list_raw_stripped);
              $parsed_content["qt"] = array();
              for ($ia = 0; $ia < count($qt_list); $ia++) {
                $qt_itm = $qt_list[$ia];
                array_push($parsed_content["qt"], $qt_itm);
              }
              $set_qt = true;
            }
            break;
          case "question_answers":
            if (!$set_qa) {
              $qa_list_raw = $qms_spl_ei[$ix+1];
              $qa_list = explode(",", $qa_list_raw);
              $parsed_content["qa"] = array();
              for ($ia = 0; $ia < count($qa_list); $ia++) {
                $qa_itm = $qa_list[$ia];
                if (str_contains($qa_itm, "(") && str_contains($qa_itm, ")")) {
                  $qa_itm = str_replace("(", "", $qa_itm);
                  $qa_itm = str_replace(")", "", $qa_itm);
                  $qa_x_list = explode(":", $qa_itm);
                  $parsed_content["qa"][$ia] = array();
                  for ($iax = 0; $iax < count($qa_x_list); $iax++) {
                    array_push($parsed_content["qa"][$ia], $qa_x_list[$iax]);
                  }
                } else {
                  $qa_itm = str_replace("(", "", $qa_itm);
                  $qa_itm = str_replace(")", "", $qa_itm);
                  array_push($parsed_content["qa"], $qa_itm);
                }

              }
              $set_qa = true;
            }
            break;
          case "answers":
            if (!$set_an) {
              $answers_list_raw = $qms_spl_ei[$ix+1];
              $answers_list_raw_stripped = str_replace("(", "", $answers_list_raw);
              $answers_list_raw_stripped = str_replace(")", "", $answers_list_raw_stripped);
              $answers_list = explode(",", $answers_list_raw_stripped);
              $parsed_content["an"] = array();
              for ($ia = 0; $ia < count($answers_list); $ia++) {
                array_push($parsed_content["an"], $answers_list[$ia]);
              }
              $set_an = true;
            }
            break;
        }
      }
    }
    return $parsed_content;
  }

  // Returns the QID
  function QM_INSERT($db, $qms) {
    $qid = generate_qid();
    $query = "INSERT INTO entries (qid, qms) VALUES (:qid, :qms);";
    $query_params = Array(":qid" => $qid, ":qms" => $qms);
    $statement = $db->prepare($query);
    $statement->execute($query_params);
    return $qid;
  }

  function QM_PULL($db, $qid) {
    $query = "SELECT qms FROM entries WHERE qid = :qid;";
    $query_params = Array("qid" => $qid);
    return getAll($query, $db, $query_params);
  }

  function QM_A($count, $qid, $answers, $qinfo, $qtype, $qanswers) {
    $answers_arr[$qid] = $answers;
    $qinfo_arr[$qid] = $qinfo;
    $qtype_arr[$qid] = $qtype;
    $qanswers_arr[$qid] = $qanswers;
  }

  function generate_qid($len = 12, $del = "-", $del_spc = 5) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $ret = '';
    for ($i = 0; $i < $len+3; $i++) {
      if (($i != 0) && ($i % $del_spc == 0)) {
        $ret .= $del;
      } else {
        $ret .= $chars[rand(0, $len -1)];
      }
    }
    return $ret;
  }


  // Checks a 2-d array with another 2-d array.
  function verify_array($arr, $answer_arr, $id) {
    //print_r($arr);
    return $arr[$id] == $answer_arr[$id];
  }

  // Generates a div based on the arrays passed in
  function generate_question($arr, $qid, $id, $type_arr, $options_arr) {
    // Create some variables to hold the data
    $question = $arr[$id];
    $type = $type_arr[$id];
    $options = $options_arr[$id];

    $type = trim(str_replace("\"", "", $type));
    $question = trim(str_replace("\"", "", $question));

    // Output the card style for athestic
    echo "<div id='qq-" . $id . "' class='qq card mb-3' type='" . $type . "'>";
    echo "<div class='card-header bg-secondary text-white'>Question #" . ($id+1) . "</div>";
    echo "<div class='card-body'>";

    // Determine what type of question is being output!
    //echo $type;
    switch($type) {
      case "input":
        echo "<div class='form-group'>";
        echo "<label for='question-" . $id . "' class='mb-3'>" . $question . "</label>";
        echo "<input id='question-" . $id . "' type='text' class='form-control' name='q" . $id . "'>";
        echo "</div>";
        break;
      case "multiple":
        echo "<div class='mb-3'>" . $question . "</div>";
        echo "The answer is: <select name='q" . $id . "' class='form-select form-select-md mb-3'>";
        echo "<option value='' selected>-- Please choose an option --</option>";
        for ($ix = 0; $ix < count($options); ++$ix) {
            echo "<option value='" . $options[$ix] . "'>" . $options[$ix] . "</option>";
        }
        echo "</select>";
      break;
      case "t/f":
        echo "<div>" . $question . "</div>";
        echo "<div class='fw-bold mb-3'>The answer is = " . $options ."</div>";

        echo "<div class='form-check'>";
        echo "<input class='form-check-input' type='radio' name='q" . $id . "' id='question-" . $id . "-radio-T' value='T'>";
        echo "<label class='form-check-label' for='question-" . $id . "-radio-T'>True</label>";
        echo "</div>";

        echo "<div class='form-check'>";
        echo "<input class='form-check-input' type='radio' name='q" . $id . "' id='question-" . $id . "-radio-F' value='F'>";
        echo "<label class='form-check-label' for='question-" . $id . "-radio-F'>False</label>";
        echo "</div>";
        break;
    }
    echo "</div></div>";
  }
?>
