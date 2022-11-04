<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    include_once '../config/database.php';
    include_once '../class/subtasks.php';
    $database = new Database();
    $db = $database->getConnection();
    $items = new subTasks($db);
    $stmt = $items->getSubTasks();
    $itemCount = $stmt->rowCount();

    // echo json_encode($itemCount);
    if($itemCount > 0){
        
        $taskArr = array();
        $taskArr["body"] = array();
        $taskArr["itemCount"] = $itemCount;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $e = array(
                "Id" => $Id,
                "Sub Task Id" => $subTaskId,
                "Task Id" => $taskId,
                "Title" => $title,
                "Created Date" => $createdDate,
                "Created Time" => $createdTime,
                "Due Date" => $dueDate,
                "State" => $state,
            );
            array_push($taskArr["body"], $e);
        }
        echo json_encode($taskArr);
    }
    else{
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }
?>