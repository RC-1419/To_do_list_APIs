<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    include_once '../config/database.php';
    include_once '../class/subtasks.php';
    $database = new Database();
    $db = $database->getConnection();
    $item = new subTasks($db);
    $data = json_decode(file_get_contents("php://input"));
    $item->subTaskId = 'STsk' . time() . (string) random_int(1, 1000);
    $item->taskId = $data->taskId;
    $item->title = $data->title;
    $item->createdDate = date("Y-m-d");
    $item->createdTime = date("h:i:s");
    $item->dueDate = $data->dueDate;
    $item->state = 'Pending';
    
    if(isset($item->taskId)){
        $val = $item->createSubTask();
        if(strcmp($val, "task does not exist") == 0){
            http_response_code(404);
            echo 'Task is not created. Create a task first and then add sub tasks to it.';
        }
        else if(strcmp($val, "title exists") == 0){
            http_response_code(409);
            echo 'A Sub task with the same name already exists in the To do list. Hence this one cannot be added.';
        }
        else{
            if($val){
                echo 'Sub task created successfully in the To do list.';
            } 
            else{
                http_response_code(404);
                echo 'Sub task could not be created.';
            }
        }
    }
    else{
        http_response_code(400);
        echo 'Missing Task Id.';
    }
    
?>