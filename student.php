<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['student_id'])) {
            $student_id_specific = $_GET['student_id'];
            $sql = "SELECT *
            FROM students
            WHERE student_id = :student_id";
        }

        if (!isset($_GET['student_id'])) {
            $sql = "SELECT * FROM students ORDER BY student_id DESC";
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($student_id_specific)) {
                $stmt->bindParam(':student_id', $student_id_specific);
            }

            $stmt->execute();
            $stud = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($stud);
        }



        break;

    case "POST":
        $stud = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO students (student_id, studentFirst, studentLast, image, groupAssigned) 
        VALUES (null,  :studentFirst, :studentLast, :image, :groupAssigned)";


        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':studentFirst', $stud->studentFirst);
        $stmt->bindParam(':studentLast', $stud->studentLast);
        $stmt->bindParam(':image', $stud->image);
        $stmt->bindParam(':groupAssigned', $stud->groupAssigned);


        $encrypted_username = md5($stud->username);
        $encrypted_password = md5($stud->password);


        if ($stmt->execute()) {

            $sql2 = "INSERT INTO users (user_id, username, password, type) 
            VALUES (null, :username, :password, :type)";

            $stmt2 = $conn->prepare($sql2);
            $stmt2->bindParam(':username', $encrypted_username);
            $stmt2->bindParam(':password', $encrypted_password);
            $stmt2->bindParam(':type', $stud->type);

            $stmt2->execute();


            $response = [
                "status" => "success",
                "message" => "Patient created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Patient creation failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $stud = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE students 
        SET studentFirst = :studentFirst, 
            studentLast = :studentLast, 
            image = :image, 
            groupAssigned = :groupAssigned, 
            type = :type
     
        WHERE student_id = :student_id";

        $stmt = $conn->prepare($sql);


        $stmt->bindParam(':studentFirst', $stud->studentFirst);
        $stmt->bindParam(':studentLast', $stud->studentLast);
        $stmt->bindParam(':image', $stud->image);
        $stmt->bindParam(':groupAssigned', $stud->groupAssigned);
        $stmt->bindParam(':type', $stud->type);
        $stmt->bindParam(':student_id', $stud->student_id);


        if ($stmt->execute()) {

            $response = [
                "status" => "success",
                "message" => "User updated successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "User update failed"
            ];
        }

        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM students WHERE student_id = :id";
        $stud = json_decode(file_get_contents('php://input'));


        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $stud->id);

        if ($stmt->execute()) {
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
}
