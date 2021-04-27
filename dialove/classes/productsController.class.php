<?php
class ProductsController {
        public $error;
        private $code;
        private $products = array();
        private $phone = 79963814070; //Не забыть убрать, dev-значение

        function getProducts($filter) {
            $html = '';
            $products = $this->getProductList($filter);
            foreach ($products as $product) {
                 $html .= '<div class="productListItem">
                    <div class="productListItemImg">

                    </div>
                    <div class="productListItemBio">
                        <div class="productListItemBioName">' . $product->getName() . '</div>
                        <div class="productListItemBioCat">Категория</div>

                    </div>
                    <div class="productListItemButtons">
                        <div class="productListItemButtonsLike">
                            +
                        </div>
                        <div class="productListItemButtonsEdit" onclick="editProductForm(\'' . $product->getId() . '\')">
                            ?
                        </div>

                        <div class="productListItemButtonsDelete" onclick="deleteProductConfirm(\'' . $product->getId() . '\')">
                            -
                        </div>
                    </div>
                </div>';
            }
            return $html;
        }

        function getProductList($filter = 'all') {
                //$this->phone = $phone;
                require '../dialove/models/product.model.php';
                require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataSource.class.php';
                $dataSource = new DataSource('select id, product_name name, owner_id owner from data_products');
                $html = '';
                if (!$data = $dataSource->getData()) {
                        $this->error = $dataSource->error;
                        return $this->error;
                }
                foreach ($data as $key => $value) {
                      $this->products[] = new Product($value['id'], $value['name'], $value['owner']);
                }
                return $this->products;
        }

        function editProduct($name, $id) {
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
            $updater->setDataFields(array('product_name' => $name, 'owner_id' => $ownerId));
            $result = $updater->update();
            if (!$result) {
                $this->error = $updater->error;
                return false;
            }
            return $result;
        }


        function getProduct($productId) {
            require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataRowSource.class.php';
            $dataRow = new DataRowSource('select id, product_name name, owner_id owner from data_products where id = "' . $productId . '"');
            if (!$dataRow->getData()) {
                    $this->error = $dataRow->error;
                    return false;
            }
            require_once '../dialove/models/product.model.php';
            $product = new Product($dataRow->getValue('id'), $dataRow->getValue('name'), $dataRow->getValue('owner'));
            return json_encode($product->expose());
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
            return $result;
        }


}
?>