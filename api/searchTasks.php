<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    include_once '../config/database.php';
    include_once '../class/tasks.php';
    $database = new Database();
    $db = $database->getConnection();
    $items = new Tasks($db);
    $data = json_decode(file_get_contents("php://input"));
    if(isset($data->search_keyword)){
        $items->title = $data->search_keyword;
        $stmt = $items->searchTasks();
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
                    "Task Id" => $taskId,
                    "Title" => $title,
                    "Created Date" => $createdDate,
                    "Created Time" => $createdTime,
                    "Due Date" => $dueDate,
                    "State" => $state,
                    "Sub Tasks" => $subTasks,
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
    }
    else{
        http_response_code(400);
        echo 'Title needed for searching. Please provide a keyword from title';
    }
?>