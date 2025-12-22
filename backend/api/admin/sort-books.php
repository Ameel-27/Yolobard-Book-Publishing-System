<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["user"]["Role"] !== 'admin') die("Unauthorized access");


?>