<?php
class ProductsController {
        public $error;
        private $code;
        private $products = array();
        private $filterList = array('all', 'cart');
        private $api;
        private $phone = 79963814070; //Не забыть убрать, dev-значение

        function __construct() {
            require '../dialove/apiClasses/productsApi.class.php';
            $this->api = new ProductsApi();
        }

        function getProductCard($product) {
            $html = '<div class="productListItem" onclick="addProductForm(\'' . $product->getId() . '\')">
                    <div class="productListItemImg">
                    </div>
                    <div class="productListItemBio">
                        <div class="productListItemBioName">' . $product->getName() . '</div>
                        <div class="productListItemBioCat">Категория</div>
                        <div class="productListItemBioStat">'
                            . (($product->getLen()) ? '<div class="productListItemBioStatItem productListItemBioStatLen"><span></span>' . $product->getLen() . '</div>' : '')
                            . (($product->getRes()) ? '<div class="productListItemBioStatItem productListItemBioStatRes"><span></span>' . $product->getRes() . '</div>' : '')
                            . '<div class="productListItemBioStatItem productListItemBioStatTran"><span></span>' . $product->getTran() . '</div>'
                            . (($product->getTranRes()) ? '<div class="productListItemBioStatItem productListItemBioStatTranRes"><span></span>' . $product->getTranRes() . '</div>' : '')
                        . '</div>
                    </div>'
                    . (($product->getQuantity()) ? ('
                        <div class="productListItemCount">
                            <div class="productListItemCountTotal">' . $product->getQuantity()  . '</div>
                            <div class="productListItemCountTotalTran">' . ($product->getQuantity() * $product->getTran() / 100)  . '</div>
                            <div class="productListItemCountTran">' . ($product->getQuantity() * $product->getTran() / 1250) . '</div>
                        </div>') : '')
                    . '<div class="productListItemButtons">
                        <div class="productListItemButtonsLike">
                            +
                        </div>
                        <div class="productListItemButtonsEdit" onclick="editProductForm(event, \'' . $product->getId() . '\')">
                            ?
                        </div>

                        <div class="productListItemButtonsDelete" onclick="deleteProductConfirm(event, \'' . $product->getId() . '\')">
                            -
                        </div>
                    </div>
                </div>';
                return $html;
        }


        function getProducts() {
            $html = '';
            foreach ($this->filterList as $filter) {
                $html .= '<div class="' . $filter . 'ProductList">';
                $products = $this->api->getProductList($filter, 'data');
                if (!$products) {
                    $html .= 'Пусто';
                }
                else {
                    foreach ($products as $product) {
                        $html .= $this->getProductCard($product);
                    }
                }
                $html .= '</div>';
            }
            return $html;
        }


        function editProductForm($productId) {
            $product = $this->api->getProduct($productId, 'data');
            $html = '
            <input type="text" placeholder="Название товара" id="editProductName" value="' . $product->getName() . '">
            <input type="number" placeholder="Транзит" id="editProductTran" value="' . $product->getTran() . '">
            <input type="number" placeholder="Резерв транзита" id="editProductTranRes" value="' . $product->getTranRes() . '">
            <div class="menuRow">
                <div class="button addButton" onclick="toggleOverlay()">Назад</div>
                <div class="button addButton" onclick="editProduct(\'' . $product->getId() . '\')">Готово</div>
            </div>';
            return $html;
        }

        function addProductForm($productId) {
            $product = $this->api->getProduct($productId, 'data');
            $html = '
            <div>Введите количество</div>
            <input type="number" id="addProductQuantity">
            <div class="menuRow">
                <div class="button addButton" onclick="toggleOverlay()">Назад</div>
                <div class="button addButton" onclick="addProduct(\'' . $product->getId() . '\')">Готово</div>
            </div>';
            return $html;
        }



}
?>