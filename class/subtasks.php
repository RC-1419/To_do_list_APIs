<?php
    class subTasks{
        private $conn;
        private $db_table = "subtasks";
        public $id;
        public $subTaskId;
        public $taskId;
        public $title;
        public $createdDate;
        public $createdTime;
        public $dueDate;
        public $state;
        
        public function __construct($db){
            $this->conn = $db;
        }
        
        public function createSubTask(){
            $sqlQuery = "SELECT * FROM tasks WHERE taskId='" . $this->taskId . "'";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(is_array($fetch)){
                $sqlQuery = "SELECT * FROM ". $this->db_table . " WHERE title='" . $this->title . "'";
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->execute();
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(is_array($fetch)){
                    return 'title exists';
                }
                else{
                    $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        subTaskId = :subTaskId,
                        taskId = :taskId, 
                        title = :title, 
                        createdDate = :createdDate,
                        createdTime = :createdTime, 
                        dueDate = :dueDate, 
                        state = :state";
            
                    $stmt = $this->conn->prepare($sqlQuery);
                
                    $this->subTaskId=htmlspecialchars(strip_tags($this->subTaskId));
                    $this->taskId=htmlspecialchars(strip_tags($this->taskId));
                    $this->title=htmlspecialchars(strip_tags($this->title));
                    $this->createdDate=htmlspecialchars(strip_tags($this->createdDate));
                    $this->createdTime=htmlspecialchars(strip_tags($this->createdTime));
                    $this->dueDate=htmlspecialchars(strip_tags($this->dueDate));
                    $this->state=htmlspecialchars(strip_tags($this->state));
                
                    $stmt->bindParam(":subTaskId", $this->subTaskId);
                    $stmt->bindParam(":taskId", $this->taskId);
                    $stmt->bindParam(":title", $this->title);
                    $stmt->bindParam(":createdDate", $this->createdDate);
                    $stmt->bindParam(":createdTime", $this->createdTime);
                    $stmt->bindParam(":dueDate", $this->dueDate);
                    $stmt->bindParam(":state", $this->state);
                
                    if($stmt->execute()){
                        $sqlQuery = "SELECT COUNT(*) FROM ". $this->db_table . 
                        " WHERE taskId='" . $this->taskId .
                        "' AND deleted_at IS NULL";
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->execute();
                        $fetch = $stmt->fetchColumn();
                        
                        if($fetch){
                            $sqlQuery = "UPDATE tasks SET subTasks='" . (string)$fetch . 
                            "' WHERE taskId = '" . $this->taskId . 
                            "' AND deleted_at IS NULL";
                            $stmt = $this->conn->prepare($sqlQuery);
                        
                            if($stmt->execute()){
                                return true;
                            }
                        }
                        return true;
                    }
                    return false;
                }
            }
            else{
                return 'task does not exist';
            }
        }

        function deleteSubTask(){
            $sqlQuery = "SELECT * FROM ". $this->db_table . 
            " WHERE subTaskId= '" . $this->subTaskId . 
            "' AND title='" . $this->title . "'";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(is_array($fetch)){
                $sqlQuery = "SELECT deleted_at FROM ". $this->db_table . 
                " WHERE subTaskId= '" . $this->subTaskId . 
                "' AND title='" . $this->title . 
                "' AND deleted_at IS NOT NULL";
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->execute();
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(!is_array($fetch)){
                    $sqlQuery = "UPDATE " . $this->db_table . 
                    " SET deleted_at='" . $this->deleted_at . 
                    "' WHERE subTaskId = '" . $this->subTaskId. 
                    "' AND title='" . $this->title . 
                    "' AND deleted_at IS NULL";
                    $stmt = $this->conn->prepare($sqlQuery);
                
                    if($stmt->execute()){
                        $sqlQuery = "SELECT taskId FROM ". $this->db_table . 
                        " WHERE subTaskId= '" . $this->subTaskId . "'";
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->execute();
                        $this->taskId = $stmt->fetchColumn();
                        
                        if(isset($this->taskId)){
                            $sqlQuery = "SELECT COUNT(*) FROM ". $this->db_table . 
                            " WHERE taskId='" . $this->taskId . 
                            "' AND deleted_at IS NULL";
                            $stmt = $this->conn->prepare($sqlQuery);
                            $stmt->execute();
                            $fetch = $stmt->fetchColumn();
                            
                            if($fetch){
                                $sqlQuery = "UPDATE tasks SET subTasks='" . (string)$fetch .  
                                "' WHERE deleted_at IS NULL" . 
                                " AND taskId = '" . $this->taskId . "'";
                                $stmt = $this->conn->prepare($sqlQuery);
                            
                                if($stmt->execute()){
                                    return true;
                                }
                            }
                        }
                    }
                    return false;   
                }
                return 'already deleted';
            }
            else{
                return 'sub task does not exist';
            }
        }

        function markSubTaskWhenComplete(){
            $sqlQuery = "SELECT * FROM ". $this->db_table . 
            " WHERE subTaskId= '" . $this->subTaskId . 
            "' AND title='" . $this->title . 
            "' AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(is_array($fetch)){
                $sqlQuery = "SELECT state FROM ". $this->db_table . 
                " WHERE subTaskId= '" . $this->subTaskId . 
                "' AND title='" . $this->title . 
                "' AND state='Completed' AND deleted_at IS NULL";
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->execute();
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(!is_array($fetch)){
                    $sqlQuery = "UPDATE " . $this->db_table . 
                    " SET state='Completed' WHERE subTaskId = '" . $this->subTaskId . 
                    "' AND title='" . $this->title . 
                    "' AND deleted_at IS NULL";
                    $stmt = $this->conn->prepare($sqlQuery);
                
                    if($stmt->execute()){
                        $sqlQuery = "SELECT taskId FROM ". $this->db_table . 
                        " WHERE subTaskId= '" . $this->subTaskId . "'";
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->execute();
                        $this->taskId = $stmt->fetchColumn();
                        
                        if(isset($this->taskId)){
                            $sqlQuery = "SELECT subTasks FROM tasks" . 
                            " WHERE taskId= '" . $this->taskId . 
                            "' AND state='Pending' AND deleted_at IS NULL";
                            $stmt = $this->conn->prepare($sqlQuery);
                            $stmt->execute();
                            $fetch = $stmt->fetchColumn();
                            
                            if(isset($fetch)){
                                $fetch -= 1;
                                $sqlQuery = "UPDATE tasks SET subTasks='" . (string)$fetch .  
                                "' WHERE deleted_at IS NULL" . 
                                " AND taskId = '" . $this->taskId . "'";
                                $stmt = $this->conn->prepare($sqlQuery);
                            
                                if($stmt->execute()){
                                    return true;
                                }
                            }
                        }
                    }
                    return false;   
                }
                return 'already marked';
            }
            return 'sub task does not exist';
        }

        public function getSubTasks(){
            $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE state='Pending' AND deleted_at IS NULL ORDER BY dueDate";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        public function searchSubTasks(){
            $search_term = $this->title;
            $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE title LIKE '%" . $search_term .
            "%' AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        public function filterSubTasks(){
            $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE state='Pending' AND ";
            $remQuery = '';
            $curr_date = date("Y-m-d");

            if(strcmp($this->filterTerm, 'Today') == 0){
                $remQuery = "DATE(NOW()) = DATE(dueDate)";    
            }
            else if(strcmp($this->filterTerm, 'This Week') == 0){
                $next_date = date("Y-m-d", strtotime($curr_date. ' + 7 days'));
                $remQuery = "dueDate >= '" . $curr_date . "' and dueDate <= '" . $next_date . "'";
            }
            else if(strcmp($this->filterTerm, 'Next Week') == 0){
                $prev_date = date("Y-m-d", strtotime($curr_date. ' + 7 days'));
                $next_date = date("Y-m-d", strtotime($curr_date. ' + 14 days'));
                $remQuery = "dueDate > '" . $prev_date . "' and dueDate <= '" . $next_date . "'";                
            }
            else if(strcmp($this->filterTerm, 'Overdue') == 0){
                $remQuery = "DATE(dueDate) < CURDATE()";    
            }
            
            $stmt = $this->conn->prepare($sqlQuery . $remQuery . " AND deleted_at IS NULL");
            $stmt->execute();
            return $stmt;
        }

        public function deleteSubTaskPermanently(){
            $prev_date = date("Y-m-d", strtotime(date("Y-m-d") . ' - 1 month'));
            $end_query = $this->db_table . " WHERE deleted_at IS NOT NULL AND DATE(dueDate) < " . $prev_date;
            $sqlQuery = "SELECT * FROM " . $end_query;
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(is_array($fetch)){
                $sqlQuery = "DELETE FROM " . $end_query;
                $stmt = $this->conn->prepare($sqlQuery);

                if($stmt->execute()){
                    return true;
                }
                return false;
            }
            return 'nothing';
        }
    }
?>