<?php

/* This is a wrapper to:
   1. enable error display for debugging (A wrapper is needed to show syntax error.)
   2. show content of catalog.php 
 */
error_reporting(E_ALL);
ini_set("display_errors", 1);
require("catalog.php");
