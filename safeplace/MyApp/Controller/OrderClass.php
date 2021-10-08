<?php
namespace MyApp\Controller;
use MyApp\Controller\DBClass;
use mysqli;

class OrderClass extends DBClass{

    private $order_column = ['order_num'=>'i', 'order_date'=>'s', 'client_id'=>'i'];

    public function listorders(array $get){
        $query = "SELECT `order`.`oid`, `order`.`order_num`, `order`.`order_date`, 
                  CONCAT(`client`.`lastname`, ' ', `client`.`firstname`) as `client`,
                  SUM(`order_item_rel`.`quantity`) as `quantity`,
                  SUM(`order_item_rel`.`quantity`*`catalog_item`.`price`) as `price`
                  FROM `order` 
                  LEFT JOIN `client` ON `order`.`client_id`=`client`.`ID` 
                  LEFT JOIN `order_item_rel` ON `order`.`oid`=`order_item_rel`.`oid`
                  LEFT JOIN `catalog_item` ON `catalog_item`.`item_id`=`order_item_rel`.`item_id`
                  WHERE 1";
        $values = array();
        $query_where = "";
        $type = "";
        $message = '';
        $prov = true;
        if(array_key_exists('order_num',$get) and ($get['order_num'])){
            $prov = $prov && is_numeric($get['order_num']);
            $query_where .= ' AND `order`.`order_num` =?';
            $values[] = $get['order_num'];
            $type .= 'i';
        }
        if(array_key_exists('order_date',$get) and ($get['order_date'])){
            $prov = $prov && $this->check_date($get['order_date']);
            $query_where .= ' AND `order`.`order_date` =?';
            $values[] = $get['order_date'];
            $type .= 's';
        }
        if(array_key_exists('client_id',$get) and ($get['client_id'])){
            $prov = $prov && $this->check_id($get['client_id'],'ID','client');
            $query_where .= ' AND `order`.`client_id` =?';
            $values[] = $get['client_id'];
            $type .= 'i';
        }
        $values[]=0;
        if((array_key_exists('limit', $get))){
            if(is_numeric($get['limit']) and ($get['limit'] > 1)) $values[count($values)-1] = ((int)$get['limit'] - 1) * 10;
        }
        $type .= 'i';
        if(!$prov) {
            $message = 'Некорректно введены данные';
            $result = $this->query($query." GROUP BY `order`.`oid` LIMIT 10 OFFSET 0");
        }
        else{
            $query .= $query_where." GROUP BY `order`.`oid` LIMIT 10 OFFSET ?";
            $result = $this->bin_param_query($query,$type,$values);
        }
        return ['message' => $message, 'order_list' => $result];
    }

    public function orderinfo(array $get){
        if(!array_key_exists('oid',$get)) return [['message' => 'Запись не найдена']];
        if(!$this->check_id($get['oid'],'oid','order')) return [['message' => 'Запись не найдена']];
        $query = "SELECT `order`.`oid`,`order`.`order_num`,`order`.`order_date`,
                  CONCAT(`client`.`lastname`,' ', `client`.`firstname`) as `client`,
                  SUM(`order_item_rel`.`quantity`) as `quantity`, 
                  SUM(`order_item_rel`.`quantity`*`catalog_item`.`price`) as `price`
                  FROM `order` 
                  LEFT JOIN `client` ON `order`.`client_id`=`client`.`ID` 
                  LEFT JOIN `order_item_rel` ON `order`.`oid`=`order_item_rel`.`oid`
                  LEFT JOIN `catalog_item` ON `order_item_rel`.`item_id`=`catalog_item`.`item_id`
                  WHERE `order`.`oid` =?";
        $result = $this->bin_param_query($query,'i',[$get['oid']]);

        $query = "SELECT `catalog_item`.`item_id`, `catalog_item`.`itemname`, `order_item_rel`.`quantity`, `catalog_item`.`description`, `catalog_item`.`price`
                  FROM `order_item_rel`
                  LEFT JOIN `catalog_item` ON `order_item_rel`.`item_id` = `catalog_item`.`item_id`
                  WHERE `order_item_rel`.`oid` =?";
        $result1 = $this->bin_param_query($query,'i',[$get['oid']]);
        $result = array_merge($result, $result1);
        if(!$result[0]['quantity']) $result[0]['quantity'] = 0;
        if(!$result[0]['price']) $result[0]['price'] = 0;
        return $result;
    }

    public function addorder(array $get){
        $query = "INSERT INTO `order` (`order`.`oid`,`order`.`order_num`, `order`.`order_date`,`order`.`client_id`) 
                  VALUES (NULL, ?,?,?)";
        $values = array();
        $type = "";
        foreach ($this->order_column as $key => $value){
            if ((array_key_exists($key, $get)) and ($get[$key])){
                $values[$key] = $get[$key];
                $type .= $value;
            }
        }
        if(count($values)==0){
            return [];
        }
        if(count($values) < 3){
            return ['message' => 'Заполните все поля','values' => $values];
        }
        $prov = is_numeric($values['order_num']) &&
                $this->check_date($values['order_date']) &&
                $this->check_id($values['client_id'],'ID','client');
        if (!$prov){
            return ['message' => 'Некорректно введены данные','values' => $values];
        }
        $this->bin_param_query($query,'isi',[$values['order_num'], $values['order_date'], $values['client_id']]);
        return ['ok' => true,'oid' => $this->db->insert_id];
    }

    public function editorder(array $get,array $post){
        if(!array_key_exists('oid',$get)) return [['message' => 'Запись не найдена']];
        if(!$this->check_id($get['oid'],'oid','order')) return [['message' => 'Запись не найдена']];
        $query = "UPDATE `order` SET";
        $values = array();
        $type = "";
        $message = '';
        $ok = false;
        foreach ($this->order_column as $key => $value){
            if (array_key_exists($key, $post) and ($post[$key])){
                $query .= '`'.$key.'` = ?, ';
                $values[] = $post[$key];
                $type .= $this->order_column[$key];
            }
        }
        if(count($values) > 0) {
            $values[] = $get['oid'];
            $query = substr($query, 0, -2) . ' WHERE `order`.`oid` = ?';
            $prov = true;
            if(array_key_exists('order_num',$post)) $prov = $prov && is_numeric($post['order_num']);
            if(array_key_exists('order_date',$post)) $prov = $prov && $this->check_date($post['order_date']);
            if(array_key_exists('client_id',$post)) $prov = $prov && $this->check_id($post['client_id'],'ID','client');
            if($prov){
                $this->bin_param_query($query,$type.'i',$values);
                $ok = true;
            }
            else $message = 'Некорректно введены данные';
        }
        $query = "SELECT `order`.`oid`, `order`.`order_num`, `order`.`order_date`, `order`.`client_id`,
                  CONCAT(`client`.`lastname`, ' ', `client`.`firstname`) as `client`
                  FROM `order` LEFT JOIN `client` ON `order`.`client_id`=`client`.`ID`
                  WHERE `order`.`oid` = ?";
        $result = $this->bin_param_query($query,'i',[$get['oid']]);
        $result[0]['message'] = $message;
        if($ok) $result[0]['ok'] = true;
        return $result;
    }

    public function deleteorder(array $get){
        if(!array_key_exists('oid',$get)) return ['message' => 'Запись не найдена'];
        if(!$this->check_id($get['oid'],'oid','order')) return ['message' => 'Запись не найдена'];
        $query = "SELECT COUNT(`order_item_rel`.`quantity`) as `count` FROM `order_item_rel` WHERE `order_item_rel`.`oid`= ?";
        $result = $this->bin_param_query($query,'i',[$get['oid']]);
        $count = $result[0]['count'];
        if($count > 0) return ['message' => 'В заказе есть товары, удаление невозможно'];
        $query = "DELETE FROM `order` WHERE `order`.`oid` = ?";
        $this->bin_param_query($query,'i',[$get['oid']]);
        return ['ok' => true];
    }
}
