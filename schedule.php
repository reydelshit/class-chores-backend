<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":



        if (isset($_GET['patient_id'])) {
            $patient_id_specific = $_GET['patient_id'];
            $sql = "SELECT *
            FROM appointments
            WHERE patient_id = :patient_id
              AND CURDATE() >= appointments.start
              AND CURDATE() <= appointments.end";
        }

        if (isset($_GET['patient_id']) && isset($_GET['next_appointment'])) {
            $patient_id_next_appointment = $_GET['patient_id'];
            $sql = "SELECT *
            FROM appointments
            WHERE patient_id = :patient_id
              AND appointments.start >= CURRENT_TIMESTAMP
            ORDER BY appointments.start
            LIMIT 1";
        }

        if (isset($_GET['patient_id']) && isset($_GET['all_appointments'])) {
            $patient_id_all_appointment = $_GET['patient_id'];
            $sql = "SELECT sched_id AS id, sched_title AS title, start, end, allDay, sched_status FROM appointments WHERE patient_id = :patient_id";
        }

        if (!isset($_GET['patient_id'])) {
            $sql = "SELECT sched_id AS id, sched_title AS title, start, end, allDay FROM schedule";
        }
        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($patient_id_specific)) {
                $stmt->bindParam(':patient_id', $patient_id_specific);
            }

            if (isset($patient_id_next_appointment)) {
                $stmt->bindParam(':patient_id', $patient_id_next_appointment);
            }

            if (isset($patient_id_all_appointment)) {
                $stmt->bindParam(':patient_id', $patient_id_all_appointment);
            }



            $stmt->execute();
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($appointments);
        }


        break;

    case "POST":
        $appointment = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO schedule (sched_id, sched_title, start, end, allDay, patient_id, sched_status) 
                VALUES (null, :sched_title, :start, :end, :allDay, :patient_id, :sched_status)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $status = "Pending";
        $stmt->bindParam(':sched_title', $appointment->sched_title);
        $stmt->bindParam(':start', $appointment->start);
        $stmt->bindParam(':end', $appointment->end);
        $stmt->bindParam(':allDay', $appointment->allDay);
        $stmt->bindParam(':patient_id', $appointment->patient_id);
        $stmt->bindParam(':sched_status', $status);


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
