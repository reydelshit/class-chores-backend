<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $user_id = $_GET['user_id'];
        $password = $_GET['password'];

        // $encrypted_user_id = md5($user_id);
        $encrypted_password = md5($password);

        $sql = "SELECT * FROM users WHERE user_id = :user_id AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':password', $encrypted_password);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {

            $response = [
                "status" => "success",
                "message" => "User login successful"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Failed to login "
            ];
        }


        echo json_encode($users);

        break;
}
