<?php

namespace MyApp;
use MyApp\Controller\OrderClass;
use MyApp\Controller\OrderItemClass;
use MyApp\Controller\ClientClass;
use MyApp\Controller\CatalogClass;
use MyApp\Logger\LoggerIntarface;
use mysqli;

class EventHandler{

    private $db;
    protected $logger;

    public function __construct($dbsettings, $logger){
        $this->logger = $logger;
        $this->initDB($dbsettings);
    }

    private function initDB($dbsettings){
        $this->db = new mysqli($dbsettings['host'],$dbsettings['username'],$dbsettings['passwd'],$dbsettings['dbname']);
        $this->logger->logEvent('Connected to DB!', __FILE__, __LINE__, __FUNCTION__);
    }

    public function run(){
        try {
            $page = array_key_exists('page', $_GET) ? $_GET['page'] : 'default';

            switch ($page) {
                case 'listorders':
                    $order = new OrderClass($this->db,$this->logger);
                    $orderInfo = $order->listorders($_GET);
                    $order = new ClientClass($this->db,$this->logger);
                    $clients = $order->minilist();
                    return ['template' => 'listorders.twig.html', 'info' => ['orderList' => $orderInfo['order_list'], 'clients' => $clients, 'message' => $orderInfo['message']]];

                case 'orderinfo':
                    $order = new OrderClass($this->db,$this->logger);
                    $orderInfo = $order->orderinfo($_GET);
                    return ['template' => 'orderinfo.twig.html', 'info' => ['order_info' => $orderInfo[0], 'goods' => array_slice($orderInfo, 1, count($orderInfo) - 1)]];

                case 'addorder':
                    $order = new OrderClass($this->db,$this->logger);
                    $order_info = $order->addorder($_POST);
                    $client = new ClientClass($this->db,$this->logger);
                    $client_info = $client->minilist();
                    return ['template' => 'addorder.twig.html', 'info' => ['clients' => $client_info, 'info' => $order_info]];

                case 'editorder':
                    $order = new OrderClass($this->db,$this->logger);
                    $orderInfo = $order->editorder($_GET, $_POST);
                    $order = new ClientClass($this->db,$this->logger);
                    $clients = $order->minilist();
                    return ['template' => 'editorder.twig.html', 'info' => ['order_info' => $orderInfo[0], 'clients' => $clients]];

                case 'deleteorder':
                    $order = new OrderClass($this->db,$this->logger);
                    $info = $order->deleteorder($_GET);
                    return ['template' => 'deleteorder.twig.html', 'info' => ['info' => $info]];

                case 'listclients':
                    $client = new ClientClass($this->db,$this->logger);
                    $clients_info = $client->listclients($_GET);
                    return ['template' => 'listclients.twig.html', 'info' => ['clients' => $clients_info['client_list'], 'message' => $clients_info['message']]];

                case 'addclient':
                    $client = new ClientClass($this->db,$this->logger);
                    $info = $client->addclient($_POST);
                    return ['template' => 'addclient.twig.html', 'info' => ['info' => $info[0]]];

                case 'editclient':
                    $client = new ClientClass($this->db,$this->logger);
                    $info = $client->editclient($_GET, $_POST);
                    return ['template' => 'editclient.twig.html', 'info' => ['info' => $info[0]]];

                case 'deleteclient':
                    $client = new ClientClass($this->db,$this->logger);
                    $info = $client->deleteclient($_GET);
                    return ['template' => 'deleteclient.twig.html', 'info' => ['info' => $info]];

                case 'catalog':
                    $catalog = new CatalogClass($this->db,$this->logger);
                    $goods = $catalog->catalog($_GET);
                    return ['template' => 'catalog.twig.html', 'info' => ['goods' => $goods['goods'], 'message' => $goods['message']]];

                case 'addcatalogitem':
                    $catalog = new CatalogClass($this->db,$this->logger);
                    $info = $catalog->addcatalogitem($_POST);
                    return ['template' => 'addcatalogitem.twig.html', 'info' => ['info' => $info]];

                case 'deletecatalogitem':
                    $catalog = new CatalogClass($this->db,$this->logger);
                    $info = $catalog->deletecatalogitem($_GET);
                    return ['template' => 'deletecatalogitem.twig.html', 'info' => ['info' => $info]];

                case 'editcatalogitem':
                    $catalog = new CatalogClass($this->db,$this->logger);
                    $info = $catalog->editcatalogitem($_GET, $_POST);
                    return ['template' => 'editcatalogitem.twig.html', 'info' => ['info' => $info[0]]];

                case 'addorderitem':
                    $orderitem = new OrderItemClass($this->db,$this->logger);
                    $order_info = $orderitem->addorderitem($_POST);
                    $catalog = new CatalogClass($this->db,$this->logger);
                    $goods = $catalog->minicatalog();
                    return ['template' => 'addorderitem.twig.html', 'info' => ['goods' => $goods, 'order_info' => $order_info]];

                case 'deleteorderitem':
                    $orderitem = new OrderItemClass($this->db,$this->logger);
                    $info = $orderitem->deleteorderitem($_GET);
                    return ['template' => 'deleteorderitem.twig.html', 'info' => ['info' => $info]];

                case 'editorderitem':
                    $orderitem = new OrderItemClass($this->db,$this->logger);
                    $info = $orderitem->editorderitem($_GET, $_POST);
                    return ['template' => 'editorderitem.twig.html', 'info' => ['info' => $info]];

                default:
                    return ['template' => 'default.twig.html', 'info' => []];
            }
        }
        catch (Exception $e) {
            $this->logger->logEvent($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
            return ['template' => 'default.twig.html', 'info' => []];
        }
    }
}