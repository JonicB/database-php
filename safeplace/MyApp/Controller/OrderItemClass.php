<?php

namespace MyApp\Controller;
use MyApp\Controller\DBClass;
use mysqli;

class OrderItemClass extends DBClass{

    private $orderitem_column = ['oid' => 'i', 'item_id' => 'i', 'quantity' => 'i'];

    public function addorderitem(array $get){
        if(!array_key_exists('oid',$get)) return ['message' => 'Запись не найдена'];
        if(!$this->check_id($get['oid'],'oid','order')) return ['message' => 'Запись не найдена'];
        $query = "INSERT INTO `order_item_rel` (`order_item_rel`.`oid`, `order_item_rel`.`item_id`, `order_item_rel`.`quantity`)
                  VALUES (?,?,?)";
        $values = array();
        $type = "";
        foreach ($this->orderitem_column as $key => $value){
            if ((array_key_exists($key, $get)) and ($get[$key])){
                $values[$key] = $get[$key];
                $type .= $value;
            }
        }
        if (count($values) == 1){
            return $values;
        }
        if(count($values) < 3){
            $values['message'] = 'Заполните все поля';
            return $values;
        }
        $prov = $this->check_id($values['oid'],'oid','order') &&
                $this->check_id($values['item_id'],'item_id','catalog_item') &&
                is_numeric($values['quantity']) && ($values['quantity'] > 0);
        if(!$prov) {
            $values['message'] = 'Некорректно введены данные';
            return $values;
        }
        $this->bin_param_query($query,'iii',[$values['oid'],$values['item_id'],(int)$values['quantity']]);
        $values['ok'] = true;
        return $values;
    }

    public function deleteorderitem(array $get){
        if(!((array_key_exists('oid',$get)) and (array_key_exists('item_id',$get)))) return ['message' => 'Запись не найдена'];
        if(!(($this->check_id($get['oid'],'oid','order')) and ($this->check_id($get['item_id'],'item_id','catalog_item'))))
            return ['message' => 'Запись не найдена'];
        $query = "SELECT COUNT(`order_item_rel`.`quantity`) as `count`
                  FROM `order_item_rel`
                  WHERE `order_item_rel`.`oid` =? AND `order_item_rel`.`item_id` =?";
        $result = $this->bin_param_query($query,'ii',[$get['oid'],$get['item_id']]);
        $count = $result[0]['count'];
        if($count == 0) return ['message' => 'Запись не найдена'];
        $guery = "DELETE FROM `order_item_rel` WHERE `order_item_rel`.`oid` =? AND `order_item_rel`.`item_id` =?";
        $this->bin_param_query($guery,'ii',[$get['oid'],$get['item_id']]);
        return ['ok' => true,'oid' => $get['oid']];
    }

    public function editorderitem(array $get,array $post){
        if(!((array_key_exists('oid',$get)) and (array_key_exists('item_id',$get)))) return ['message' => 'Запись не найдена'];
        if(!(($this->check_id($get['oid'],'oid','order')) and ($this->check_id($get['item_id'],'item_id','catalog_item'))))
            return ['message' => 'Запись не найдена'];
        $query = "SELECT COUNT(`order_item_rel`.`quantity`) as `count`
                  FROM `order_item_rel`
                  WHERE `order_item_rel`.`oid` =? AND `order_item_rel`.`item_id` =?";
        $result = $this->bin_param_query($query,'ii',[$get['oid'],$get['item_id']]);
        $count = $result[0]['count'];
        if($count == 0) return ['message' => 'Запись не найдена'];
        $query = "UPDATE `order_item_rel` 
                  SET `quantity` =?
                  WHERE `order_item_rel`.`oid` = ? AND `order_item_rel`.`item_id` =?";
        $message = '';
        $ok = false;
        if((array_key_exists('quantity',$post)) and ($post['quantity'])){
            if((is_numeric($post['quantity'])) and ($post['quantity'] > 0)){
                $this->bin_param_query($query,'iii',[$post['quantity'],$get['oid'],$get['item_id']]);
                $ok = true;
            }
            else $message = 'Некорректно введены данные';
        }
        $query = "SELECT `order_item_rel`.`oid`,`order_item_rel`.`item_id`,`order_item_rel`.`quantity`, `catalog_item`.`itemname`
                  FROM `order_item_rel`
                  LEFT JOIN `catalog_item` ON `order_item_rel`.`item_id` = `catalog_item`.`item_id`
                  WHERE `order_item_rel`.`oid` = ? AND `order_item_rel`.`item_id` =?";
        $result = $this->bin_param_query($query,'ii',[$get['oid'],(int)$get['item_id']]);
        $result = $result[0];
        $result['message'] = $message;
        if($ok) $result['ok'] = $ok;
        return $result;
}
}
