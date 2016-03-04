<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once "src/Brand.php";
    require_once "src/Store.php";

    $server = 'mysql:host=localhost;dbname=shoes_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BrandTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Brand::deleteAll();
            Store::deleteAll();
        }

        function testGetInfo()
        {
            $brand_name = "Louie Vuitton";
            $price_range = "$$$";
            $id = 1;
            $test_brand = new Brand($brand_name, $price_range, $id);

            $result = $test_brand->getBrandName();
            $result2 = $test_brand->getPriceRange();
            $result3 = $test_brand->getId();

            $this->assertEquals($brand_name, $result);
            $this->assertEquals($price_range, $result2);
            $this->assertEquals($id, $result3);
        }

        function testSave()
        {
            $brand_name = "Louie Vuitton";
            $price_range = "$$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $result = Brand::getAll();

            $this->assertEquals($test_brand, $result[0]);
        }

        function testUpdates()
        {
            $brand_name = "Louie Vuitton";
            $price_range = "$$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $new_name = "Louie V";
            $new_price_range = "$$";

            $test_brand->updateBrandName($new_name);
            $test_brand->updatePriceRange($new_price_range);

            $this->assertEquals("Louie V", $test_brand->getBrandName());
            $this->assertEquals("$$", $test_brand->getPriceRange());
        }

        function testFind()
        {
            $brand_name = "Louie Vuitton";
            $price_range = "$$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $brand_name2 = "Birkenstocks";
            $price_range2 = "$$";
            $test_brand2 = new Brand($brand_name2, $price_range2);
            $test_brand2->save();

            $result = Brand::find($test_brand2->getId());

            $this->assertEquals($test_brand2, $result);
        }

        function testAddStore()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $brand_name = "Birkenstocks";
            $price_range = "$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $test_brand->addStore($test_store);

            $this->assertEquals([$test_store], $test_brand->getStores());
        }

        function testGetStores()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $store_name2 = "Louie Vuitton";
            $location2 = "place";
            $test_store2 = new Store($store_name2, $location2);
            $test_store2->save();

            $brand_name = "Birkenstocks";
            $price_range = "$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            //Act
            $test_brand->addStore($test_store);
            $test_brand->addStore($test_store2);

            //Assert
            $this->assertEquals([$test_store, $test_store2], $test_brand->getStores());
        }

        function testDeleteBrand()
        {
            $store_name = "Nordstrom";
            $location = "123 Street";
            $test_store = new Store($store_name, $location);
            $test_store->save();

            $brand_name = "Birkenstocks";
            $price_range = "$$";
            $test_brand = new Brand($brand_name, $price_range);
            $test_brand->save();

            $test_brand->addStore($test_store);
            $test_brand->deleteBrand();

            $this->assertEquals([], $test_store->getBrands());
        }
    }
 ?>
