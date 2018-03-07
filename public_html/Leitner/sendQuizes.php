<?php
$connection = include 'Connection.php';

try{
    $connection->set_charset("utf8");
    $sql="SELECT * FROM quizes";
    $res = $connection->query($sql);
    if ($res->num_rows){
        $quizes=array();
        $i = 0;
        while ($row = $res->fetch_assoc()){
            $name=$row['name'];
            $date=$row['date'];
            $list=$row['list'];
            $enum=$row['enum'];
            $quizes[$i++]=new Quiz($name,$date,$list,$enum);
        }
        echo json_encode(array_values($quizes));
    }
}catch (Exception $e) {
    echo  $e->getMessage();
}

class Quiz
{
    var $name;
    var $date;
    var $list;
    var $enum;
    function __construct($name,$date,$list,$enum){
        $this->name=$name;
        $this->date=$date;
        $this->list=$list;
        $this->enum=$enum;
    }
}