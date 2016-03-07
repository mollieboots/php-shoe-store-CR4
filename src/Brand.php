<?php
    class Brand
    {
        private $brand_name;
        private $price_range;
        private $id;

        function __construct($brand_name, $price_range, $id = null)
        {
            $this->brand_name = $brand_name;
            $this->price_range = $price_range;
            $this->id = $id;
        }

        function setBrandName($brand_name)
        {
            $this->brand_name = $brand_name;
        }

        function getBrandName()
        {
            return $this->brand_name;
        }

        function setPriceRange($price_range)
        {
            $this->price_range = $price_range;
        }

        function getPriceRange()
        {
            return $this->price_range;
        }

        function setId($id)
        {
            $this->id = $id;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO brands (brand_name, price_range) VALUES ('{$this->getBrandName()}', '{$this->getPriceRange()}');");
              $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function deleteAll()
        {
          $GLOBALS['DB']->exec("DELETE FROM brands;");
        }

        static function getAll()
        {
            $returned_brands = $GLOBALS['DB']->query("SELECT * FROM brands;");
            $brands = [];
            foreach($returned_brands as $brand) {
                $brand_name = $brand['brand_name'];
                $price_range = $brand['price_range'];
                $id = $brand['id'];
                $new_brand = new Brand($brand_name, $price_range, $id);
                array_push($brands, $new_brand);
            }
            return $brands;
        }

        function updateBrandName($new_name)
        {
            $GLOBALS['DB']->exec("UPDATE brands SET brand_name = '{$new_name}' WHERE id = {$this->getId()};");
            $this->setBrandName($new_name);
        }

        function updatePriceRange($new_price_range)
        {
            $GLOBALS['DB']->exec("UPDATE brands SET price_range = '{$new_price_range}' WHERE id = {$this->getId()};");
            $this->setPriceRange($new_price_range);
        }

        static function find($search_id)
        {
            $found_brand = null;
            $brands = Brand::getAll();
            foreach($brands as $brand) {
                $brand_id = $brand->getId();
                if ($brand_id == $search_id) {
                    $found_brand = $brand;
                }
            }
            return $found_brand;
        }

        function addStore($store)
        {
            $GLOBALS['DB']->exec("INSERT INTO stores_brands (store_id, brand_id) VALUES ({$store->getId()}, {$this->getId()});");
        }

        function getStores()
        {
            $query = $GLOBALS['DB']->query("SELECT stores.* FROM brands JOIN stores_brands ON (brands.id = stores_brands.brand_id) JOIN stores ON (stores_brands.store_id = stores.id) WHERE brands.id = {$this->getId()}; ");
			$returned_stores = $query->fetchAll(PDO::FETCH_ASSOC);
			$stores = array();
			foreach($returned_stores as $store){
				$store_name = $store['store_name'];
                $location = $store['location'];
				$id = $store['id'];
				$new_store = new Store($store_name, $location, $id);
				array_push($stores, $new_store);
			}
			return $stores;
        }

        function deleteBrand()
        {
            $GLOBALS['DB']->exec("DELETE FROM brands WHERE id = {$this->getId()};");
            $GLOBALS['DB']->exec("DELETE FROM brands_brands WHERE brand_id = {$this->getId()};");
        }
    }
 ?>
