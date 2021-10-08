<?php

namespace MyApp\Controller;
use mysqli;
use mysqli_result;

abstract class DBClass{

    protected $db;
    protected $logger;

    public function __construct(mysqli $db, $logger){
        $this->db = $db;
        $this->logger = $logger;
    }

    protected function query(string $query){
        $this->logger->logEvent("query: ".$query, __FILE__, __LINE__, __FUNCTION__);
        $result = $this->db->query($query);
        $result = $result->fetch_all(MYSQLI_ASSOC);
        return $result;
    }

    protected function bin_param_query(string $query,string $type,array $values){
        $this->logger->logEvent("query: ".$query, __FILE__, __LINE__, __FUNCTION__);
        $this->logger->logEvent("params: ".var_export($values, true), __FILE__, __LINE__, __FUNCTION__);
        $stat = $this->db->prepare($query);
        $stat->bind_param($type,...$values);
        $stat->execute();
        $result = $stat->get_result();
        $stat->close();
        if (!is_bool($result)) $result = $result->fetch_all(MYSQLI_ASSOC);
        return $result;
    }

    protected function check_date(string $date){
        $d = date_create_from_format('Y-m-d', $date);
        return $d && ($d->format('Y-m-d') == $date);
    }

    protected function check_id($id,string $table_id,string $table){
        if (!is_numeric($id)) return false;
        $query = "SELECT COUNT(`".$table."`.`".$table_id."`) as `count`
                  FROM `".$table."`
                  WHERE `".$table."`.`".$table_id."` =?";
        $result = $this->bin_param_query($query,'i',[$id]);
        return $result[0]['count'] > 0;
    }
}
