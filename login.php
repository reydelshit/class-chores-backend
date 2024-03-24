<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        $username = $_GET['username'];
        $password = $_GET['password'];

        $encrypted_username = md5($username);
        $encrypted_password = md5($password);

        $sql = "SELECT * FROM users WHERE username = :username AND password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $encrypted_username);
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


    case "POST":
        $account = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO users (user_id, username, password) 
            VALUES (null, :username, :password)";

        $stmt = $conn->prepare($sql);

        $encrypted_username = md5($account->username);
        $encrypted_password = md5($account->password);


        $stmt->bindParam(':username', $encrypted_username);
        $stmt->bindParam(':password', $encrypted_password);



        // $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Account created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Account creation failed"
            ];
        }

        echo json_encode($response);
        break;
}
