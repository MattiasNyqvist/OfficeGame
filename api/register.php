<?php
require_once __DIR__ . '/../models/User.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->username) && !empty($data->password) && !empty($data->email) && !empty($data->team_id)) {
    $user = new User();
    $result = $user->register($data->username, $data->password, $data->email, $data->team_id);

    if ($result === true) {
        http_response_code(201);
        echo json_encode(["message" => "User registered successfully"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => $result]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Incomplete data"]);
}
?>
