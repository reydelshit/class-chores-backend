<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['user_id'])) {
            $user_id = $_GET['user_id'];
            $sql = "SELECT *
            FROM seedphrase
            WHERE user_id = :user_id";
        }


        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($user_id)) {
                $stmt->bindParam(':user_id', $user_id);
            }

            $stmt->execute();
            $stud = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($stud);
        }



        break;

    case "POST":
        $stud = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO seedphrase (seed_id, seed_phrase, user_id) 
        VALUES (null, :seed_phrase, :user_id)";


        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':seed_phrase', $stud->seed_phrase);
        $stmt->bindParam(':user_id', $stud->user_id);


        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "seed created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "seed creation failed"
            ];
        }

        echo json_encode($response);
        break;


    case "DELETE":
        $sql = "DELETE FROM seedphrase WHERE user_id = :user_id AND seed_phrase = :seed_phrase";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $stud = json_decode(file_get_contents('php://input'));

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $stud->user_id);
        $stmt->bindParam(':seed_phrase', $stud->seed_phrase);


        if ($stmt->execute()) {

            $sql = "DELETE FROM users WHERE user_id = :user_id";
            $stud = json_decode(file_get_contents('php://input'));

            $stmts = $conn->prepare($sql);
            $stmts->bindParam(':user_id', $stud->user_id);

            $stmts->execute();

            $response = [
                "status" => "success",
                "message" => "User deleted successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User deletion failed"
            ];
        }

        echo json_encode($response);
}
