<?php
class Product {
    private $id;
    private $name;
    private $owner;
    public function __construct($id, $name, $owner) {
        $this->setId($id);
        $this->setName($name);
        $this->setOwner($owner);
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
    public function setId($id) {
        $this->id = $id;
    }
    public function setName($name) {
        $this->name = $name;
    }
    public function setOwner($owner) {
        $this->owner = $owner;
    }
    public function expose() {
        return get_object_vars($this);
    }

}
?>