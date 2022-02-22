<?php

include_once '../Controller/Authcontroller.php';
include_once '../Controller/Util.php';
require '../vendor/autoload.php';


Util::CORS();
$user = new Authentication();

$api = $_SERVER['REQUEST_METHOD'];
$id = intval($_GET['id'] ?? "");

if ($api === "POST") {
    $obj = Util::input();

    $username = $user->sanitize($obj->username);
    $password = $user->sanitize(md5($obj->password));

    echo $user->login($username, $password);
}
