<?php


include 'DBconnect.php';
$objDB = new DbConnect();
$conn = $objDB->connect();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":

        if (isset($_GET['receiver_id'])) {
            $receiver_id = $_GET['receiver_id'];

            $sql = "SELECT * FROM notifications WHERE notifications.receiver_id = :receiver_id ORDER BY notifications.notification_id DESC";
        }

        if (isset($sql)) {
            $stmt = $conn->prepare($sql);

            if (isset($receiver_id)) {
                $stmt->bindParam(':receiver_id', $receiver_id);
            }

            $stmt->execute();
            $notification = $stmt->fetchAll(PDO::FETCH_ASSOC);


            echo json_encode($notification);
        }


        break;

    case "POST":
        $message = json_decode(file_get_contents('php://input'));
        $sql = "INSERT INTO notifications (sender_id, receiver_id, notification_message, created_at) VALUES (:sender_id, :receiver_id, :notification_message, :created_at)";
        $stmt = $conn->prepare($sql);
        $created_at = date('Y-m-d H:i:s');
        $stmt->bindParam(':sender_id', $message->sender_id);
        $stmt->bindParam(':receiver_id', $message->receiver_id);
        $stmt->bindParam(':notification_message', $message->notification_message);
        $stmt->bindParam(':created_at', $created_at);

        if ($stmt->execute()) {
            $response = [
                "status" => "success",
                "message" => "patient notification sent successfully"
            ];
        } else {
            $response = [
                "status" => "error",
                "message" => "patient notification sent failed"
            ];
        }

        echo json_encode($response);
        break;
}
