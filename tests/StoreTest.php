<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Store.php";
    require_once "src/Brand.php";

    $server = 'mysql:host=localhost;dbname=shoes_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class StoreTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Store::deleteAll();
            Brand::deleteAll();
        }

        function testGetInfo()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $id = 1;
            $test_store = new Store($store_name, $location, $id);

            $result = $test_store->getStoreName();
            $result2 = $test_store->getLocation();
            $result3 = $test_store->getId();

            $this->assertEquals($store_name, $result);
            $this->assertEquals($location, $result2);
            $this->assertEquals($id, $result3);
        }

        function testSave()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $result = Store::getAll();

            $this->assertEquals($test_store, $result[0]);
        }

        function testUpdates()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $new_name = "Nordys";
            $new_location = "Washington Square";

            $test_store->updateStoreName($new_name);
            $test_store->updateLocation($new_location);

            $this->assertEquals("Nordys", $test_store->getStoreName());
            $this->assertEquals("Washington Square", $test_store->getLocation());
        }

        function testFind()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $store_name2 = "Macys";
            $location2 = "Washington Square";
            $test_store2 = new Store($store_name2, $location2);
            $test_store2->save();

            $result = Store::find($test_store2->getId());

            $this->assertEquals($test_store2, $result);
        }

        function testAddBrand()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $brand_name = "Louie Vuitton";
            $price_range = "$$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $test_store->addBrand($test_brand);

            $this->assertEquals($test_store->getBrands(), [$test_brand]);
        }

        function testGetBrands()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $brand_name = "Louie Vuitton";
            $price_range = "$$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $brand_name2 = "Birkenstocks";
            $price_range2 = "$$";
            $test_brand2 = new Brand($brand_name2, $price_range2);
            $test_brand2->save();

            $test_store->addBrand($test_brand);
            $test_store->addBrand($test_brand2);

            $this->assertEquals($test_store->getBrands(), [$test_brand, $test_brand2]);
        }

        function testDeleteStore()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $brand_name = "Birkenstocks";
            $price_range = "$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $test_store->addBrand($test_brand);
            $test_store->deleteStore();

            $this->assertEquals([], $test_brand->getStores());
        }
    }
 ?>
