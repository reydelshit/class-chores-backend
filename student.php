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
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':studentFirst', $stud->studentFirst);
        $stmt->bindParam(':studentLast', $stud->studentLast);
        $stmt->bindParam(':image', $stud->image);
        $stmt->bindParam(':groupAssigned', $stud->groupAssigned);


        if ($stmt->execute()) {


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
        $user = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE users SET name= :name, email=:email, gender=:gender, profile_picture=:profile_picture, address=:address, profile_description=:profile_description, updated_at=:updated_at WHERE user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':user_id', $user->user_id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':profile_picture', $user->profile_picture);
        $stmt->bindParam(':address', $user->address);
        $stmt->bindParam(':gender', $user->gender);
        $stmt->bindParam(':profile_description', $user->profile_description);
        $stmt->bindParam(':updated_at', $updated_at);

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
        $sql = "DELETE FROM users WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[2]);

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
