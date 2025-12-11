<?php 
  function save_state($file, $data) {
    file_put_contents($file, json_encode($data));
  }
?>