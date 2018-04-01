<?php
spl_autoload_register(function ($class_name) {
   $query_class_file = $_SERVER["DOCUMENT_ROOT"] . "/modules/query/" . $class_name . ".php";
   if (file_exists($query_class_file)) {
       //Ignore warning because of autoload function
       require_once $query_class_file;
       return true;
   }
   return false;
});
function query_exists($query) {
    return class_exists($query);
}