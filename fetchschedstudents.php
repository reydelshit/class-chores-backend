<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":


        $sql = "SELECT sched_id AS id, sched_title AS title, start, end, allDay FROM schedule WHERE group_name = :group_name";

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':group_name', $_GET['group_name']);


            $stmt->execute();
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($appointments);
        }


        break;
}
