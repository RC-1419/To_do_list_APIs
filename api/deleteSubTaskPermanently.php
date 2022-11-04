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
    $val = $item->deleteSubTaskPermanently();
    if(strcmp($val, 'nothing') == 0){
        http_response_code(404);
        echo json_encode("No sub tasks are present which were soft deleted before a month ago.");
    }
    else if($val){
        echo json_encode("All sub tasks which were soft deleted before a month ago are permanently deleted.");
    } 
    else{
        http_response_code(404);
        echo json_encode("Soft deleted sub tasks were not deleted permanently");
    }
?>