<?php
    class Tasks{
        private $conn;
        private $db_table = "tasks";
        public $Id;
        public $taskId;
        public $title;
        public $createdDate;
        public $createdTime;
        public $dueDate;
        public $state;
        public $subTasks;
        public $deleted_at;
        public $filterTerm;
        
        public function __construct($db){
            $this->conn = $db;
        }  
        
        public function createTask(){
            $sqlQuery = "SELECT * FROM ". $this->db_table . " WHERE title='" . $this->title . "'";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!is_array($fetch)){
                $sqlQuery = "INSERT INTO
                        ". $this->db_table ."
                    SET
                        taskId = :taskId, 
                        title = :title, 
                        createdDate = :createdDate,
                        createdTime = :createdTime, 
                        dueDate = :dueDate, 
                        state = :state,
                        subTasks = :subTasks";
            
                $stmt = $this->conn->prepare($sqlQuery);
            
                // sanitize
                $this->taskId=htmlspecialchars(strip_tags($this->taskId));
                $this->title=htmlspecialchars(strip_tags($this->title));
                $this->createdDate=htmlspecialchars(strip_tags($this->createdDate));
                $this->createdTime=htmlspecialchars(strip_tags($this->createdTime));
                $this->dueDate=htmlspecialchars(strip_tags($this->dueDate));
                $this->state=htmlspecialchars(strip_tags($this->state));
                $this->subTasks=htmlspecialchars(strip_tags($this->subTasks));
            
                // bind data
                $stmt->bindParam(":taskId", $this->taskId);
                $stmt->bindParam(":title", $this->title);
                $stmt->bindParam(":createdDate", $this->createdDate);
                $stmt->bindParam(":createdTime", $this->createdTime);
                $stmt->bindParam(":dueDate", $this->dueDate);
                $stmt->bindParam(":state", $this->state);
                $stmt->bindParam(":subTasks", $this->subTasks);
            
                if($stmt->execute()){
                    return true;
                }
                return false;
            }
            return 'title exists';
        }
        
        function deleteTask(){
            $sqlQuery = "SELECT * FROM ". $this->db_table . 
            " WHERE taskId= '" . $this->taskId . 
            "' AND title='" . $this->title . "'";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(is_array($fetch)){
                $sqlQuery = "SELECT deleted_at FROM ". $this->db_table . 
                " WHERE taskId= '" . $this->taskId . 
                "' AND title='" . $this->title . 
                "' AND deleted_at IS NOT NULL";
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->execute();
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(!is_array($fetch)){
                    $sqlQuery = "UPDATE " . $this->db_table . " SET deleted_at='" . $this->deleted_at . "' WHERE taskId = '" . $this->taskId. "' AND title='" . $this->title . "'";
                    $stmt = $this->conn->prepare($sqlQuery);
                
                    if($stmt->execute()){
                        return true;
                    }
                    return false;   
                }
                return 'already deleted';
            }
            else{
                return 'task does not exist';
            }
        }

        function markTaskWhenComplete(){
            $sqlQuery = "SELECT * FROM ". $this->db_table . 
            " WHERE taskId= '" . $this->taskId . 
            "' AND title='" . $this->title . 
            "' AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(is_array($fetch)){
                $sqlQuery = "SELECT state FROM ". $this->db_table . 
                " WHERE taskId= '" . $this->taskId . 
                "' AND title='" . $this->title . 
                "' AND state='Completed' AND deleted_at IS NULL";
                $stmt = $this->conn->prepare($sqlQuery);
                $stmt->execute();
                $fetch = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if(!is_array($fetch)){
                    $sqlQuery = "UPDATE " . $this->db_table . 
                    " SET state='Completed', subTasks='0' WHERE taskId = '" . $this->taskId . 
                    "' AND title='" . $this->title . 
                    "' AND deleted_at IS NULL";
                    $stmt = $this->conn->prepare($sqlQuery);
                
                    if($stmt->execute()){
                        $sqlQuery = "SELECT subTasks FROM " . $this->db_table . 
                        " WHERE taskId= '" . $this->taskId . 
                        "' AND state='Pending' AND deleted_at IS NULL";
                        $stmt = $this->conn->prepare($sqlQuery);
                        $stmt->execute();
                        $fetch = $stmt->fetchColumn();
                        
                        if(isset($fetch)){
                            $sqlQuery = "UPDATE subtasks SET state='Completed' 
                            WHERE taskId = '" . $this->taskId . 
                            "' AND deleted_at IS NULL AND state='Pending'";
                            $stmt = $this->conn->prepare($sqlQuery);
                        
                            if($stmt->execute()){
                                return true;
                            }
                            return 'task marked but sub tasks were not marked';
                        }
                        return true;
                    }
                    return false;   
                }
                return 'already marked';
            }
            else{
                return 'task does not exist';
            }
        }

        public function getTasks(){
            $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE state='Pending' AND deleted_at IS NULL ORDER BY dueDate";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        public function searchTasks(){
            $search_term = $this->title;
            $sqlQuery = "SELECT * FROM " . $this->db_table . " WHERE title LIKE '%" . $search_term .
            "%' AND deleted_at IS NULL";
            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            return $stmt;
        }

        public function filterTasks(){
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

        public function deleteTaskPermanently(){
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