<?php

namespace MyApp\Controller;
use MyApp\Controller\DBClass;
use mysqli;

class CatalogClass extends DBClass{

    private $catalog_columns = ['itemname' => 's', 'description' => 's', 'price' => 'd'];

    public function catalog(array $get){
        $query = "SELECT `catalog_item`.`item_id`, `catalog_item`.`itemname`, `catalog_item`.`description`, `catalog_item`.`price`, 
                  COUNT(`order_item_rel`.`item_id`) as `times`
                  FROM `catalog_item`
                  LEFT JOIN `order_item_rel` ON `catalog_item`.`item_id` = `order_item_rel`.`item_id`
                  WHERE 1";
        $values = array();
        $query_where = "";
        $type = "";
        $prov = true;
        $message = '';
        if ((array_key_exists('itemname', $get))and ($get['itemname'])){
            $query_where .= " AND `catalog_item`.`itemname` LIKE ?";
            $values[] = "%".$get['itemname']."%";
            $type .= 's';
        }
        if ((array_key_exists('description', $get))and ($get['description'])){
            $query_where .= " AND `catalog_item`.`description` LIKE ?";
            $values[] = "%".$get['description']."%";
            $type .= 's';
        }
        if ((array_key_exists('up_price', $get))and ($get['up_price'])){
            $prov = $prov && is_numeric($get['up_price']) && ($get['up_price'] >= 0);
            $query_where .= " AND `catalog_item`.`price` <=?";
            $values[] = $get['up_price'];
            $type .= 'd';
        }
        if ((array_key_exists('down_price', $get))and ($get['down_price'])){
            $prov = $prov && is_numeric($get['down_price']) && ($get['down_price'] >= 0);
            $query_where .= " AND `catalog_item`.`price` >=?";
            $values[] = $get['down_price'];
            $type .= 'd';
        }
        $values[] = 0;
        if((array_key_exists('limit', $get)) and ($get['limit'] > 1)){
            if(is_numeric($get['limit']) and ($get['limit'] > 1)) $values[count($values) - 1] = ($get['limit'] - 1) * 10;
        }
        $type .= 'i';
        if(!$prov){
            $message = 'Некорректно введены данные';
            $result = $this->query($query." GROUP BY `catalog_item`.`item_id` LIMIT 10 OFFSET 0");
        }
        else{
            $query .= $query_where." GROUP BY `catalog_item`.`item_id` LIMIT 10 OFFSET ?";
            $result = $this->bin_param_query($query,$type,$values);
        }
        return ['message' => $message, 'goods' => $result];
    }

    public function minicatalog(){
        $query = "SELECT `catalog_item`.`item_id`, `catalog_item`.`itemname`
                  FROM `catalog_item`
                  WHERE 1";
        $result = $this->query($query);
        return $result;
    }

    public function addcatalogitem(array $get){
        $guery = "INSERT INTO `catalog_item` (`catalog_item`.`item_id`, `catalog_item`.`itemname`, `catalog_item`.`description`,`catalog_item`.`price`)
                  VALUES (NULL,?,?,?)";
        $values = array();
        $type = '';
        foreach($this->catalog_columns as $key => $value){
            if((array_key_exists($key,$get)) and ($get[$key])){
                $values[$key] = $get[$key];
                $type .= $value;
            }
        }
        if(count($values) == 0) return [];
        if(count($values) < 3) return ['message' => 'Заполните все поля','values' => $values];
        $prov = is_numeric($values['price']) && ($values['price'] > 0);
        if(!$prov) return ['message' => 'Некорректно введены данные','values' => $values];
        $this->bin_param_query($guery,'ssd',[$get['itemname'],$get['description'],$get['price']]);
        return ['ok' => true];
    }

    public function deletecatalogitem(array $get){
        if(!array_key_exists('item_id',$get)) return ['message' => 'Запись не найдена'];
        if(!$this->check_id($get['item_id'],'item_id','catalog_item')) return ['message' => 'Запись не найдена'];
        $query = "SELECT COUNT(`order_item_rel`.`item_id`) as `count` 
                  FROM `order_item_rel` 
                  WHERE `order_item_rel`.`item_id` = ?";
        $result = $this->bin_param_query($query,'i',[$get['item_id']]);
        $count = $result[0]['count'];
        if($count > 0) return ['message' => 'Товар уже заказан, удаление невозможно'];
        $query = "DELETE FROM `catalog_item` WHERE `catalog_item`.`item_id` =?";
        $this->bin_param_query($query,'i',[$get['item_id']]);
        return ['ok' => true];
    }

    public function editcatalogitem(array $get,array $post){
        if(!array_key_exists('item_id',$get)) return [['message' => 'Запись не найдена']];
        if(!$this->check_id($get['item_id'],'item_id','catalog_item')) return [['message' => 'Запись не найдена']];
        $query = "UPDATE `catalog_item` SET";
        $values = array();
        $type = "";
        $message = '';
        $ok = false;
        foreach ($this->catalog_columns as $key => $value){
            if (array_key_exists($key, $post) and ($post[$key])){
                $query .= '`'.$key.'` = ?, ';
                $values[] = $post[$key];
                $type .= $this->catalog_columns[$key];
            }
        }
        if(count($values) > 0) {
            if(array_key_exists('price',$post)){
                $prove = is_numeric($post['price']) && ($post['price'] > 0);
            }
            if($prove){
                $ok = true;
                $values[] = $get['item_id'];
                $query = substr($query, 0, -2) . ' WHERE `catalog_item`.`item_id` = ?';
                $this->bin_param_query($query,$type.'i',$values);
            }
            else $message = 'Некорректно введены данные';
        }
        $query = "SELECT `catalog_item`.`item_id`, `catalog_item`.`itemname`, `catalog_item`.`description`,`catalog_item`.`price`
                  FROM `catalog_item`
                  WHERE `catalog_item`.`item_id` = ?";
        $result = $this->bin_param_query($query,'i',[$get['item_id']]);
        $result[0]['message'] = $message;
        if($ok) $result[0]['ok'] = true;
        return $result;
    }
}
