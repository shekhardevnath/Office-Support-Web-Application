<?php

   session_start();
   $_SESSION['nav_item'] = 'home';

   header('Location: /nm_tx/standard/user_home/user_home.php');

?>