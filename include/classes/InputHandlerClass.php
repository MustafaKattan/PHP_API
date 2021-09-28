<?php
class  InputHandler{
        static function getInput($name){
            $result = false;
            if(isset($_POST[$name])){
                $result = $_POST[$name];
            } elseif(isset($_GET[$name])){
                $result = $_GET[$name];
            }
            return $result;
        }
    }
?>