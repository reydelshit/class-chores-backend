<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        $sql = "SELECT sched_id AS id, sched_title AS title, start, end, allDay FROM schedule";

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($appointments);
        }


        break;

    case "POST":
        $appointment = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO schedule (sched_id, sched_title, start, end, allDay, group_name) 
                VALUES (null, :sched_title, :start, :end, :allDay, :group_name)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $status = "Pending";
        $stmt->bindParam(':sched_title', $appointment->sched_title);
        $stmt->bindParam(':start', $appointment->start);
        $stmt->bindParam(':end', $appointment->end);
        $stmt->bindParam(':allDay', $appointment->allDay);
        $stmt->bindParam(':group_name', $appointment->selectedGroup);



        // $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "Appointment created successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "Appointment creation failed"
            ];
        }

        echo json_encode($response);
        break;

    case "PUT":
        $appointment = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE schedule SET sched_title= :sched_title, start=:start, end=:end, allDay=:allDay 
                WHERE sched_id = :sched_id";
        $stmt = $conn->prepare($sql);
        $updated_at = date('Y-m-d');
        $stmt->bindParam(':sched_id', $appointment->sched_id);
        $stmt->bindParam(':sched_title', $appointment->sched_title);
        $stmt->bindParam(':start', $appointment->start);
        $stmt->bindParam(':end', $appointment->end);
        $stmt->bindParam(':allDay', $appointment->allDay);

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
        $sql = "DELETE FROM appointments WHERE sched_id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $path[3]);

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
