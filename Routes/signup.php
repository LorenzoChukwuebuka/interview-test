<?php

include_once '../Controller/Authcontroller.php';
include_once '../Controller/Util.php';

Util::CORS();
$user = new Authentication();

$api = $_SERVER['REQUEST_METHOD'];
$id = intval($_GET['id'] ?? "");

if ($api === "POST") {
    $obj = Util::input();

    $fname = @$user->sanitize($obj->fname);
    $lname = @$user->sanitize($obj->lname);
    $username = @$user->sanitize($obj->username);
    $password = @$user->sanitize(md5($obj->password));

    echo $user->create_user($fname,$lname,$username,$password);
}
