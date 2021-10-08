<?php

namespace MyApp\Controller;
use MyApp\Controller\DBClass;
use mysqli;

class ClientClass extends DBClass{

    private $client_column = ['lastname' => 's', 'firstname' => 's', 'birthdate' => 's', 'address' => 's'];

    public function listclients(array $get){
        $query = "SELECT `client`.`ID`, `client`.`lastname`, `client`.`firstname`, `client`.`birthdate`, `client`.`address`, 
                  COUNT(DISTINCT `order`.`oid`) as `total_order`,
                  SUM(`order_item_rel`.`quantity`*`catalog_item`.`price`) as `total_spent`
                  FROM `client`
                  LEFT JOIN `order` ON `client`.`ID` = `order`.`client_id`
                  LEFT JOIN `order_item_rel` ON `order`.`oid` = `order_item_rel`.`oid`
                  LEFT JOIN `catalog_item` ON `order_item_rel`.`item_id` = `catalog_item`.`item_id`
                  WHERE 1";
        $values = array();
        $query_where = "";
        $type = "";
        $prov = true;
        $message = '';
        if (array_key_exists('lastname', $get) and ($get['lastname'])){
            $query_where .= " AND `client`.`lastname` =?";
            $values[] = $get['lastname'];
            $type .= 's';
        }
        if (array_key_exists('birthyear',$get) and ($get['birthyear'])){
            $prov = $prov && is_numeric($get['birthyear']);
            $query_where .= " AND YEAR(`client`.`birthdate`) =?";
            $values[] = $get['birthyear'];
            $type .= 'i';
        }
        $values[] = 0;
        if((array_key_exists('limit', $get))){
            if(is_numeric($get['limit']) and ($get['limit'] > 1)) $values[count($values)-1] = ((int)$get['limit'] - 1) * 10;
        }
        $type .= 'i';
        if(!$prov) {
            $message = 'Некорректно введены данные';
            $result = $this->query($query." GROUP BY `client`.`ID` LIMIT 10 OFFSET 0");
        }
        else{
            $query .= $query_where." GROUP BY `client`.`ID` LIMIT 10 OFFSET ?";
            $result = $this->bin_param_query($query,$type,$values);
        }
        return ['message' => $message, 'client_list' => $result];
    }

    public function minilist(){
        $query = "SELECT `client`.`ID`,
                  CONCAT(`client`.`lastname`,' ',`client`.`firstname`) as name
                  FROM `client`
                  WHERE 1";
        $result = $this->query($query);
        return $result;
    }

    public function addclient(array $get){
        $query = "INSERT INTO `client` (`client`.`ID`,`client`.`lastname`,`client`.`firstname`,`client`.`birthdate`";
        $values = array();
        $max = 3;
        foreach ($this->client_column as $key => $value){
            if(array_key_exists($key,$get) and ($get[$key])){
                $values[$key] = $get[$key];
            }
        }
        if(array_key_exists('address',$values)){
            $query .=",`client`.`address`) VALUES (NULL,?,?,?,?)";
            $max = 4;
        }
        else $query .= ") VALUES (NULL,?,?,?)";
        if(count($values) == 0) return [[]];
        if(count($values) < $max) return [['message' => 'Заполните все поля', 'values' => $values]];
        if(!$this->check_date($get['birthdate'])) return [['message' => 'Некорректно введены данные', 'values' => $values]];
        if($max == 4) $this->bin_param_query($query,'ssss',[$values['lastname'],$values['firstname'],$values['birthdate'],$values['address']]);
        else $this->bin_param_query($query,'sss',[$values['lastname'],$values['firstname'],$values['birthdate']]);
        return [['ok' => true]];
    }

    public function editclient(array $get,array $post){
        if(!array_key_exists('ID',$get)) return [['message' => 'Запись не найдена']];
        if(!$this->check_id($get['ID'],'ID','client')) return [['message' => 'Запись не найдена']];
        $query = "UPDATE `client` SET";
        $values = array();
        $type = "";
        $message = '';
        $ok = false;
        foreach ($this->client_column as $key => $value){
            if (array_key_exists($key, $post) and ($post[$key])){
                $query .= '`'.$key.'` = ?, ';
                $values[] = $post[$key];
                $type .= $value;
            }
        }
        if(count($values) > 0) {
            $values[] = $get['ID'];
            $query = substr($query, 0, -2) . ' WHERE `client`.`ID` = ?';
            if($this->check_date($post['birthdate'])) {
                $this->bin_param_query($query, $type . 'i', $values);
                $ok = true;
            }
            else $message = 'Некорректно введены данные';
        }
        $query = "SELECT `client`.`ID`,`client`.`lastname`,`client`.`firstname`,`client`.`birthdate`,`client`.`address`
                  FROM `client`
                  WHERE `client`.`ID` = ?";
        $result = $this->bin_param_query($query,'i',[$get['ID']]);
        $result[0]['message'] = $message;
        if($ok) $result[0]['ok'] = true;
        return $result;
    }

    public function deleteclient(array $get){
        if(!array_key_exists('ID',$get)) return ['message' => 'Запись не найдена'];
        if(!$this->check_id($get['ID'],'ID','client')) return ['message' => 'Запись не найдена'];
        $query = "SELECT COUNT(`order`.`client_id`) as `count` FROM `order` WHERE `order`.`client_id` =?";
        $result = $this->bin_param_query($query,'i',[$get['ID']]);
        $count = $result[0]['count'];
        if($count > 0) return ['message' => 'У клиента есть заказы, удаление невозможно'];
        $query = "DELETE FROM `client` WHERE `client`.`ID` =?";
        $this->bin_param_query($query,'i',[$get['ID']]);
        return ['ok' => true];
    }
}
