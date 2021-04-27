<?php
class Product {
    private $id;
    private $name;
    private $owner;
    private $category;
    private $len;
    private $res;
    private $tran;
    private $tranRes;
    private $indexG;
    private $quantity;
    public function __construct($id, $name, $owner, $category, $len, $res, $tran, $tranRes, $indexG, $quantity = '') {
        $this->setId($id);
        $this->setName($name);
        $this->setOwner($owner);
        $this->setCategory($category);
        $this->setLen($len);
        $this->setRes($res);
        $this->setTran($tran);
        $this->setTranRes($tranRes);
        $this->setIndexG($indexG);
        $this->setQuantity($quantity);
    }
    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getOwner() {
        return $this->owner;
    }
    public function getCategory() {
        return $this->category;
    }
    public function getLen() {
        return $this->len;
    }
    public function getRes() {
        return $this->res;
    }
    public function getTran() {
        return $this->tran;
    }
    public function getTranRes() {
        return $this->tranRes;
    }
    public function getIndexG() {
        return $this->indexG;
    }
    public function getQuantity() {
        return $this->quantity;
    }
    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setOwner($owner) {
        $this->owner = $owner;
    }
    public function setCategory($category) {
        $this->category = $category;
    }
    public function setLen($len) {
        $this->len = ($len == -1) ? '' : $len;
    }
    public function setRes($res) {
        $this->res = ($res == -1) ? '' : $res;
    }
    public function setTran($tran) {
        $this->tran = $tran;
    }
    public function setTranRes($tranRes) {
        $this->tranRes = ($tranRes == -1) ? '' : $tranRes;
    }
    public function setIndexG($indexG) {
        $this->indexG = $indexG;
    }
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
    public function expose() {
        return get_object_vars($this);
    }

}
?>