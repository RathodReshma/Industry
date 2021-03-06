<?php
class Industry {
    private static $m_pInstance;
    public static function getInstance() {
        if (!self::$m_pInstance) {
            self::$m_pInstance = new Industry();
        }
        return self::$m_pInstance;
    }
    function __construct(){
        $this->db = new mysqli(SERVER, USERNAME, PASSWORD,DATABASE);
        if ($this->db-> connect_errno) {
            echo "Failed to connect to MySQL: " .$this->db -> connect_error;
            exit();
        }
    }
    function insertDept($data) {
        //http://localhost/index.php?action=addDept&DeptName=res&ws=1
        $dept_query = "INSERT INTO `department` (`dept_name`) VALUES ('{$data['DeptName']}')";
        $this->db -> query($dept_query);
        $message ="Department {$data['DeptName']} Added Successfully";
        $msg['Status'] = "Success";        
        $msg['MessageInfo'] = $message;
        if(isset($data['ws']) && $data['ws'] == '1'){
            echo json_encode($msg);
            return;
        }
        return json_encode($msg);
    }
    function listDepartment($data='') {
       // http://localhost/index.php?action=listDept&ws=1
        $seldep_query = "SELECT * FROM `department`";
        if(isset($data['DeptId']) && $data['DeptId'] !=''){
            $seldep_query .= " AND id = '{$data['DeptId']}'";
        }
        $result = $this->db->query($seldep_query);
        $dept =  array();
        if ($result->num_rows > 0) {
            $i=0;
            while($row = $result->fetch_assoc()) {               

                $dept[$i]['dep_id'] =$row["id"];
                $dept[$i]['dep_name'] =$row["dept_name"];
                $i++;                         
            }
         } else {
             return json_encode("No Record Found");
         }
         if(isset($data['ws']) && $data['ws'] == '1'){
            echo json_encode($dept);
            return;
         }
        return json_encode($dept);
    }
    function addEmployee() {
       $listDept = Industry::getInstance()->listDepartment();
       $dept =json_decode($listDept,true);      
       include_once("addEmp.html");
    }
    function insertEmployee($empData) {
       // http://localhost/index.php?action=insertEmp&EmpName=res&DeptId=9&EmpPhone=523452&EmpAddress=afasdfadfa
        $emp_query = "INSERT INTO `employee` (`emp_name`, `dept_id`) VALUES ('{$empData['EmpName']}', '{$empData['DeptId']}')";
        $this->db -> query($emp_query);
        $emp_id = $this->db -> insert_id;
        $emp_det_query = "INSERT INTO `emp_details` (`emp_id`, `entity`, `value`) VALUES ";
        $emp_det_query .= "('{$emp_id}', 'phone', '{$empData['EmpPhone']}'),";
        $emp_det_query .= "('{$emp_id}', 'address', '{$empData['EmpAddress']}')";
        $this->db -> query($emp_det_query);

        $message ="Employee {$empData['EmpName']} Added Successfully";
        $msg['Status'] = "Success";        
        $msg['MessageInfo'] = $message;
        if(isset($empData['ws']) && $empData['ws'] == '1'){
            echo json_encode($msg);
            return;
        }       
        header("Location:index.php");
    }
    function updateEmployee($empData) {
       // http://localhost/index.php?action=editEmp&EmpName=resupdatechk&DeptId=9&EmpPhone=523452&EmpAddress=afasdfadfa&EmpId=4&ws=1
        
        $emp_query = "UPDATE `employee` SET `emp_name` = '{$empData['EmpName']}'";
        if(isset($empData['DeptId']) && $empData['DeptId'] !=''){
            $emp_query .= " ,dept_id = '{$empData['DeptId']}'";
        }        
        $emp_query .= " WHERE `emp_id` = {$empData['EmpId']}";
        $this->db -> query($emp_query);
        
        $emp_det_phone_query = "UPDATE `emp_details` SET `value` = '{$empData['EmpPhone']}' ";
        $emp_det_phone_query .= " WHERE entity = 'phone' AND emp_id = '{$empData['EmpId']}'";
        $this->db -> query($emp_det_phone_query);

        $emp_det_address_query = "UPDATE `emp_details` SET `value` = '{$empData['EmpAddress']}' ";
        $emp_det_address_query .= " WHERE entity = 'address' AND emp_id = '{$empData['EmpId']}'";
        $this->db -> query($emp_det_address_query);

        $message ="Employee {$empData['EmpName']} updated Successfully";
        $msg['Status'] = "Success";        
        $msg['MessageInfo'] = $message;
        if(isset($empData['ws']) && $empData['ws'] == '1'){
            echo json_encode($msg);
            return;
        }  
        header("Location:index.php");
    }
    function listEmployee($data='') {
       // http://localhost/index.php?action=listEmp&EmpName=resupdatechk&ws=1
        
        $selemp_query = "SELECT `emp_id`,`emp_name`,d.dept_name as dept_name FROM employee e, department d WHERE e.dept_id = d.id";
        if(isset($data['DeptId']) && $data['DeptId'] !=''){
            $selemp_query .= " AND d.id = '{$data['DeptId']}'";
        }  
        if(isset($data['EmpName']) && $data['EmpName'] !=''){
            $selemp_query .= " AND emp_name = '{$data['EmpName']}'";
        } 
        $result = $this->db->query($selemp_query);
        $empl =  array();
        if ($result->num_rows > 0) {
            $i=0;
            while($row = $result->fetch_assoc()) {
                $selphone_query = "SELECT value FROM `emp_details` WHERE  `emp_id` ='{$row["emp_id"]}' and `entity` = 'phone'";                
                $resultPhone = $this->db->query($selphone_query);
                $phone = array();
                if ($resultPhone->num_rows > 0) {
                     while($phone_arr = $resultPhone->fetch_assoc()) {
                         $phone[] =$phone_arr["value"];
                     }
                }


                $seladdr_query = "SELECT value FROM `emp_details` WHERE  `emp_id` ='{$row["emp_id"]}' and `entity` = 'address'";
                $resultAddress = $this->db->query($seladdr_query);
                $address = array();
                if ($resultAddress->num_rows > 0) {
                     while($addr_arr = $resultAddress->fetch_assoc()) {
                         $address[] =$addr_arr["value"];
                     }
                }

                $empl[$i]['emp_id'] =$row["emp_id"];
                $empl[$i]['emp_name'] =$row["emp_name"];
                $empl[$i]['dept_name'] =$row["dept_name"];   
                $empl[$i]['phone'] =$phone;
                $empl[$i]['address'] =$address;
                 $i++;                         
            }
         } else {
            $empl['error'] = "No Record Found";
         }         
         if(isset($data['ws']) && $data['ws'] == '1'){
            echo json_encode($empl);
            return;
         }
        return json_encode($empl);
    }        
}
?>