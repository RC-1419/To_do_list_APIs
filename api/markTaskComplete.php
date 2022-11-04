<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: PUT");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    include_once '../config/database.php';
    include_once '../class/tasks.php';
    $database = new Database();
    $db = $database->getConnection();
    $item = new Tasks($db);
    $data = json_decode(file_get_contents("php://input"));
    if(isset($data->taskId)){
        $item->taskId = $data->taskId;
    }
    if(isset($data->title)){
        $item->title = $data->title;
    }
    $item->state = 'Completed';
    
    if(isset($item->taskId) && isset($item->title)){
        $val = $item->markTaskWhenComplete();
        if(strcmp($val, 'task does not exist') == 0){
            http_response_code(404);
            echo 'Task is not present. Hence it cannot be marked.';
        }
        else if(strcmp($val, 'already marked') == 0){
            http_response_code(409);
            echo 'The task is already marked as completed.';
        }
        else if(strcmp($val, 'task marked but sub tasks were not marked') == 0){
            http_response_code(404);
            echo 'Task was marked as completed but sub tasks were not marked as completed due to an error.';
        }
        else{
            if($val){
                echo 'Task is marked as completed successfully in the To do list.';
            } 
            else{
                http_response_code(404);
                echo 'Task could not be marked as completed.';
            }
        }
    }
    else{
        http_response_code(400);
        echo 'Missing Task Id or Task title.';
    }
    
?>