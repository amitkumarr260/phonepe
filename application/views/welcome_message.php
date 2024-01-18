<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<html>
  <head>
    <title>PhonePe Payment Form</title>
  </head>
  <body>
    <h2>PhonePe Payment Form</h2>
    <form action="<?=base_url('payment/process')?>" method="post">
      <label for="amount">Amount:</label>
      <input type="text" id="amount" name="amount" value="<?=$amount?>">
      <br><br>
      <input type="submit" value="Pay with PhonePe">
    </form>
  </body>
</html>