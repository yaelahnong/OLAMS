<?php
function generateToken(){
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
} 

function isCsrfTokenValid($token){
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if(!isset($_SESSION['csrf_token'])){
    generateToken();
}

?>