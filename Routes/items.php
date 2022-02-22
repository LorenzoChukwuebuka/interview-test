<?php

include_once '../Controller/Authcontroller.php';
include_once '../Controller/Util.php';
require '../vendor/autoload.php';

$secret_key = '1234567890';
$jwt = null;
$data = json_decode(file_get_contents("php://input"));
$authHeader = @$_SERVER['HTTP_AUTHORIZATION'];

// checks for token in header
if (!preg_match('/Bearer\s(\S+)/', @$_SERVER['HTTP_AUTHORIZATION'], $matches)) {
    header('HTTP/1.0 400 Bad Request');
    echo 'Authorization failed';
    exit;
}
$jwt = $matches[1];

//if token exists

if ($jwt) {

    //continue with the script

    Util::validateToken();
    Util::CORS();
    $item = new Authentication();

    $api = $_SERVER['REQUEST_METHOD'];
    $id = intval($_GET['id'] ?? "");

    if ($api === 'POST') {
        $obj = Util::input();

        $userId = $item->sanitize($obj->userid);
        $title = $item->sanitize($obj->title);
        $description = $item->sanitize($obj->description);
        $amount = $item->sanitize($obj->amount);

        echo $item->create_product($userId, $title, $description, $amount);

    }

    if ($api === 'GET') {
        echo $item->read_products($id);
    }

    if ($api === "DELETE") {
        echo $item->delete_item($id);
    }

    if ($api === "PUT") {
        $obj = Util::input();
        // $userId = $item->sanitize($obj->userid);
        $title = $item->sanitize($obj->title);
        $description = $item->sanitize($obj->description);
        $amount = $item->sanitize($obj->amount);

        echo $item->update_item($id, $title, $description, $amount);

    }

} else {
// No token was able to be extracted from the authorization header
    return header('HTTP/1.0 400 Bad Request');
    exit;
}
