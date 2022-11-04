<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    include_once '../config/database.php';
    include_once '../class/subtasks.php';
    $database = new Database();
    $db = $database->getConnection();
    $item = new subTasks($db);
    $data = json_decode(file_get_contents("php://input"));
    if(isset($data->subTaskId)){
        $item->subTaskId = $data->subTaskId;
    }
    if(isset($data->title)){
        $item->title = $data->title;
    }
    $item->deleted_at = date("Y-m-d h:i:s");
    
    if(isset($item->subTaskId) && isset($item->title)){
        $val = $item->deleteSubTask();
        if(strcmp($val, 'sub task does not exist') == 0){
            http_response_code(404);
            echo 'Sub task is not present. Hence it cannot be deleted.';
        }
        else if(strcmp($val, 'already deleted') == 0){
            http_response_code(409);
            echo 'The sub task is already deleted.';
        }
        else{
            if($val){
                echo 'Sub task is deleted successfully in the To do list.';
            } 
            else{
                http_response_code(404);
                echo 'Sub Task could not be deleted.';
            }
        }
    }
    else{
        http_response_code(400);
        echo 'Missing Sub Task Id or Sub Task title.';
    }
    
?>