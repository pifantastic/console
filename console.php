<?php

function error($number, $message, $file, $line) {
  echo "<strong>[ERROR]</strong> $message";
  return true;
}

set_error_handler("error");

// Clean up commands
$history = array();
foreach ($_REQUEST['history'] as $cmd) {
  $history[] = clean($cmd);
}

$result = array(
  "success" => FALSE,
  "result" => ""
);

if (!empty($history)) {
  // We only want the output from the last command
  $last_cmd = array_pop($history);
  
  ob_start();
  
  // Execute history and clear buffer
  eval(implode('', $history));
  ob_clean();
  
  // Get output for the last command
  eval(clean($last_cmd));
  $output = ob_get_clean();
  
  $result['success'] = TRUE;
  $result['result'] = $output;
}

// JSONify results
header("Content-Type: application/json");
echo json_encode($result);

function clean($cmd) {
  $cmd = trim($cmd);
  
  if (substr($cmd, -1) !== ";") {
    $cmd .= ';';
  }
  
  return $cmd;
}