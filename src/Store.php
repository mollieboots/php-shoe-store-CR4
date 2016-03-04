<?php
    class Store
    {
        private $store_name;
        private $location;
        private $id;

        function __construct($store_name, $location, $id = null)
        {
            $this->store_name = $store_name;
            $this->location = $location;
            $this->id = $id;
        }

        function setStoreName($store_name)
        {
            $this->store_name = $store_name;
        }

        function getStoreName()
        {
            return $this->store_name;
        }

        function setLocation($location)
        {
            $this->location = $location;
        }

        function getLocation()
        {
            return $this->location;
        }

        function setId($store_name)
        {
            $this->id = $id;
        }

        function getId()
        {
            return $this->id;
        }

        static function deleteAll()
        {
          $GLOBALS['DB']->exec("DELETE FROM stores;");
        }

        static function getAll()
        {
            $returned_stores = $GLOBALS['DB']->query("SELECT * FROM stores;");
            $stores = [];
            foreach($returned_stores as $store) {
                $store_name = $store['store_name'];
                $location = $store['location'];
                $id = $store['id'];
                $new_store = new Store($store_name, $location, $id);
                array_push($stores, $new_store);
            }
            return $stores;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO stores (store_name, location) VALUES ('{$this->getStoreName()}', '{$this->getLocation()}');");
              $this->id = $GLOBALS['DB']->lastInsertId();
        }

        function updateStoreName($new_name)
        {
            $GLOBALS['DB']->exec("UPDATE stores SET store_name = '{$new_name}' WHERE id = {$this->getId()};");
            $this->setStoreName($new_name);
        }

        function updateLocation($new_location)
        {
            $GLOBALS['DB']->exec("UPDATE stores SET location = '{$new_location}' WHERE id = {$this->getId()};");
            $this->setLocation($new_location);
        }

        static function find($search_id)
        {
            $found_store = null;
            $stores = Store::getAll();
            foreach($stores as $store) {
                $store_id = $store->getId();
                if ($store_id = $search_id) {
                    $found_store = $store;
                }
            }
            return $found_store;
        }

        function addBrand($brand)
        {
            $GLOBALS['DB']->exec("INSERT INTO stores_brands (store_id, brand_id) VALUES ({$this->getId()}, {$brand->getId()});");
        }

        function getBrands()
        {
            $brands = $GLOBALS['DB']->query("SELECT brands.* FROM stores JOIN stores_brands ON (stores.id = stores_brands.store_id) JOIN brands ON (brands.id = stores_brands.brand_id) WHERE stores.id = {$this->getId()};");

            $returned_brands = [];
            foreach($brands as $brand) {
                $brand_name = $brand['brand_name'];
                $price_range = $brand['price_range'];
                $id = $brand['id'];
                $new_brand = new Brand($brand_name, $price_range, $id);
                array_push($returned_brands, $new_brand);
            }
            return $returned_brands;
        }

        function deleteStore()
        {
            $GLOBALS['DB']->exec("DELETE FROM stores WHERE id = {$this->getId()};");
            $GLOBALS['DB']->exec("DELETE FROM stores_brands WHERE store_id = {$this->getId()};");
        }
    }
 ?>
