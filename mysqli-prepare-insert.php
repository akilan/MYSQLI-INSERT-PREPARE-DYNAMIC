<?php
/**
 * 
 * DYNAMIC DB INSERTION FOR MYSQLI PREPARE
 * 
 * $fielddata - Array contains Field Names
 * $fieldvalue - Array contains Field Data
 * $tableName - Variable contains table name
 *
 */
 $fieldvalue_join=implode(',', array_map('addquote', $fieldvalue));
 
 $fieldvalue=explode(",",$fieldvalue_join);
$value_count=count($fieldvalue);
$question_mark=array();
for($i=0;$i<$value_count;$i++){
    $question_mark[]='?';
} 
$join_question_mark=implode(",",$question_mark);
$types = '';                        
foreach($fieldvalue as $param) {        
    if(is_int($param)) {
        $types .= 'i';              //integer
    } elseif (is_float($param)) {
        $types .= 'd';              //double
    } elseif (is_string($param)) {
        $types .= 's';              //string
    } else {
        $types .= 'b';              //blob and unknown
    }
}
$mysqli_test=new mysqli(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
$stmt=$mysqli_test->stmt_init();
$stmt->prepare("INSERT INTO ".$tableName."(".implode(",",$fielddata).") VALUES (".$join_question_mark.")");
$bind_names[] = $types;
for ($i=0; $i<count($fieldvalue);$i++) {
    $bind_name = 'bind' . $i;       
    $$bind_name = $fieldvalue[$i];      
    $bind_names[] = &$$bind_name;   
}
call_user_func_array(array($stmt,'bind_param'),$bind_names);
    if($stmt->execute())
    { 
        echo "INSERT ID ----->  ".$stmt->insert_id."<br />";
        $insert_id=$stmt->insert_id;
         $stmt->close();
         return $insert_id;
    }
 
 
 
 function addquote($str){
    if($str[0]=="'" || $str[0]=='"' && $str[strlen($str)-1]=="'" || $str[strlen($str)-1]=="'" ){
        $str=substr($str,1);
        $str=substr($str,0,-1);
    }
    return sprintf("%s", $str);
}