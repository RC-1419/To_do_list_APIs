<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: DELETE");
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
    $item->deleted_at = date("Y-m-d h:i:s");
    
    if(isset($item->taskId) && isset($item->title)){
        $val = $item->deleteTask();
        if(strcmp($val, 'task does not exist') == 0){
            http_response_code(404);
            echo 'Task is not present. Hence it cannot be deleted.';
        }
        else if(strcmp($val, 'already deleted') == 0){
            http_response_code(409);
            echo 'The task is already deleted.';
        }
        else{
            if($val){
                echo 'Task is deleted successfully in the To do list.';
            } 
            else{
                http_response_code(404);
                echo 'Task could not be deleted.';
            }
        }
    }
    else{
        http_response_code(400);
        echo 'Missing Task Id or Task title.';
    }
    
?>