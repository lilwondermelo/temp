<?php
class ProductsApi {
        public $error;
        private $code;
        private $filterQuery = '';
        private $products = array();
        private $phone = 79963814070; //Не забыть убрать, dev-значение



        function getProductList($filter = 'all', $type = 'json') {
            $this->products = array();
            if ($filter == 'all') {
                $this->filterQuery = ' on p.id = c.product_id where c.quantity is null';
            }
            else if ($filter == 'cart') {
                $this->filterQuery = ' on p.id = c.product_id where c.quantity';
            }
                require_once '../dialove/models/product.model.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataSource.class.php';
                $dataSource = new DataSource('select p.id, c.quantity, c.product_id as idInCart, p.product_name as name, p.owner_id as owner, p.category_id as category, p.len, p.res, p.tran, p.tran_res as tranRes, p.index_g as indexG from data_products p
                    left join data_cart c' . $this->filterQuery);
                $html = '';
                if (!$data = $dataSource->getData()) {
                        $this->error = $dataSource->error;
                        return $this->error;
                }
                foreach ($data as $key => $value) {
                    $product = new Product($value['id'], $value['name'], $value['owner'], $value['category'], $value['len'], $value['res'], $value['tran'], $value['tranRes'], $value['indexG']);
                    if ($filter == 'cart') {
                        $product->setQuantity($value['quantity']);
                    }
                    if ($type == 'json'){
                        $product = json_encode($product->expose());
                    }
                    $this->products[] = $product;
                }
                $this->products = ($type != 'json') ? $this->products : json_encode($this->products);
                return $this->products;
        }



        function editProduct($name, $id, $tran, $categoryId = '', $len = -1, $res = -1, $tranRes = -1, $indexG = 0) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/space/classes/application.class.php';
            $app = new Application();
            $ownerId = $app->getUserId();
            if (!$ownerId) {
                $this->error = $app->error;
                return false;
            }
            if ($id == 'new') {
                $id = $app->createUuid();
            }
            require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowUpdater.class.php';
            $updater = new DataRowUpdater('data_products');
            $updater->setKey('id', $id);
            $updater->setDataFields(array('product_name' => $name, 'owner_id' => $ownerId, 'category_id' => $categoryId, 'len' => $len, 'res' => $res, 'tran' => $tran, 'tran_res' => $tranRes, 'index_g' => $indexG));
            $result = $updater->update();
            if (!$result) {
                $this->error = $updater->error;
                return false;
            }
            return array('product_name' => $name, 'owner_id' => $ownerId, 'category_id' => $categoryId, 'len' => $len, 'res' => $res, 'tran' => $tran, 'tran_res' => $tranRes, 'index_g' => $indexG);
        }

        function addToCart($productId, $productQuantity, $id = '') {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/space/classes/application.class.php';
            $app = new Application();
            $userId = $app->getUserId();
            if (!$userId) {
                $this->error = $app->error;
                return false;
            }
            if ($id == '') {
               $id = $app->createUuid();
            }
            require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowUpdater.class.php';
            $updater = new DataRowUpdater('data_cart');
            $updater->setKey('id', $id);
            $updater->setDataFields(array('product_id' => $productId, 'user_id' => $userId, 'quantity' => $productQuantity));
            $result = $updater->update();
            if (!$result) {
                $this->error = $updater->error;
                return false;
            }
            return true;
        }


        function getProduct($productId, $type = 'json') {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowSource.class.php';
            $dataRow = new DataRowSource('select id, product_name name, owner_id owner, category_id category, len, res, tran, tran_res tranRes, index_g indexG from data_products where id = "' . $productId . '"');
            if (!$dataRow->getData()) {
                    $this->error = $dataRow->error;
                    return false;
            }
            require_once '../dialove/models/product.model.php';
            $product = new Product($dataRow->getValue('id'), $dataRow->getValue('name'), $dataRow->getValue('owner'), $dataRow->getValue('category'), $dataRow->getValue('len'), $dataRow->getValue('res'), $dataRow->getValue('tran'), $dataRow->getValue('tranRes'), $dataRow->getValue('indexG'));
            if ($type == 'json'){
                $product = $product->expose();
            }
            return ($type != 'json') ? $product : json_encode($product);
        }


        function deleteProduct($productId) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/space/classes/application.class.php';
            $app = new Application();
            $ownerId = $app->getUserId();
            if (!$ownerId) {
                $this->error = $app->error;
                return false;
            }
            require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowUpdater.class.php';
            $updater = new DataRowUpdater();
            $result = $updater->deleteRow('data_products', 'id', $productId);
            if (!$result) {
                $this->error = $updater->error;
                return false;
            }
            return true;
        }


        function clearCart() {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataConnector.class.php';
            $connector = new DataConnector();
            require_once $_SERVER['DOCUMENT_ROOT'] . '/space/classes/application.class.php';
            $app = new Application();
            $ownerId = $app->getUserId();
            $query = 'delete from data_cart where user_id = "' . $ownerId . '"';
            $result = $connector->sqlQuery($query);
            if (!$result) {
                $this->error = $connector->error;
                return false;
            }
            return true;
        }
}
?>