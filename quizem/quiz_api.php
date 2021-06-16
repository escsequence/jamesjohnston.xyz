<?php
  require 'db.php';

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
