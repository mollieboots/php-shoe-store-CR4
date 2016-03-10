<?php
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Store.php";
    require_once __DIR__."/../src/Brand.php";

    $app = new Silex\Application();

    $server = 'mysql:host=localhost;dbname=shoes';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    $app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/../views'));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    $app['debug'] = true;

    //homepage
    $app->get("/", function() use ($app) {
        return $app['twig']->render('index.html.twig');
    });

    //list of all stores, get here from the homepage
    $app->get("/stores", function() use ($app) {
        return $app['twig']->render('stores.html.twig', array('stores' => Store::getAll()));
    });

    //list of all stores after a new store has been added
    $app->post("/stores", function() use ($app) {
        $store = new Store($_POST['store_name'], $_POST['location']);
        $store->save();
        return $app['twig']->render('stores.html.twig', array('stores' => Store::getAll()));
    });

    //individual store view, get here from the list of all stores page
    $app->get("/store/{id}", function($id) use ($app) {
        var_dump($id);
        $store = Store::find($id);
        var_dump($store);
        return $app['twig']->render('store.html.twig', array('stores' => Store::getAll(), 'store' => $store, 'brands' => $store->getBrands(), 'all_brands' => Brand::getAll()));
    });

    //individual store edit page where the data to update will be inputted, but no changes will be made
    $app->get("/store/{id}/edit", function($id) use ($app) {
        $store = Store::find($id);
        return $app['twig']->render('store_edit.html.twig', array('store' => $store));
    });

    //sends you back to individual store view and implements changes
    $app->patch("/store/{id}", function($id) use ($app) {
        $new_name = $_POST['new_name'];
        $new_location = $_POST['new_location'];
        $store = Store::find($id);
        $store->updateStoreName($new_name);
        $store->updateLocation($new_location);
        return $app['twig']->render('store.html.twig', array('store' => $store, 'all_brands' => Brand::getAll(), 'brands' => $store->getBrands()));
    });

    //deletes individual store from database
    $app->delete("/store/{id}/delete", function($id) use ($app) {
        $store = Store::find($id);
        $store->deleteStore();
        return $app['twig']->render('store_delete.html.twig', array('store' => $store));
    });

    //adds brand to a store
    $app->post("/store/{id}/add_brand", function($id) use ($app) {
        $store = Store::find($id);
        $brand = Brand::find($_POST['brand_id']);
        $store->addBrand($brand);
        return $app['twig']->render('store.html.twig', array('store' => $store, 'stores' => Store::getAll(), 'brands' => $store->getBrands(), 'all_brands' => Brand::getAll()));
    });

    //list of all brands, get here from the homepage
    $app->get("/brands", function() use ($app) {
        return $app['twig']->render('brands.html.twig', array('all_brands' => Brand::getAll(), 'stores' => Store::getAll()));
    });

    //list of all brands after a new brand has been added
    $app->post("/brands", function() use ($app) {
        $brand = new Brand($_POST['brand_name'], $_POST['price_range']);
        $brand->save();
        return $app['twig']->render('brands.html.twig', array('all_brands' => Brand::getAll()));
    });

    //individual brand view, get here from the list of all brands page
    $app->get("/brand/{id}", function($id) use ($app) {
        $brand = Brand::find($id);
        // $store = Store::find($_POST['store_id']);
        // $brand->addStore($store);
        return $app['twig']->render('brand.html.twig', array('all_brands' => Brand::getAll(), 'brand' => $brand, 'all_stores' => Store::getAll()));
    });

    //individual store edit page where the data to update will be inputted, but no changes will be made
    $app->get("/brand/{id}/edit", function($id) use ($app) {
        $brand = Brand::find($id);
        return $app['twig']->render('brand_edit.html.twig', array('brand' => $brand));
    });

    //sends you back to individual brand view and implements changes
    $app->patch("/brand/{id}", function($id) use ($app) {
        $new_name = $_POST['new_name'];
        $new_price_range = $_POST['new_price_range'];
        $brand = Brand::find($id);
        $brand->updateBrandName($new_name);
        $brand->updatePriceRange($new_price_range);
        return $app['twig']->render('brand.html.twig', array('brand' => $brand, 'all_stores' => Store::getAll()));
    });

    //deletes individual brand from database
    $app->delete("/brand/{id}/delete", function($id) use ($app) {
        $brand = Brand::find($id);
        $brand->deleteBrand();
        return $app['twig']->render('brand_delete.html.twig', array('brand' => $brand));
    });

    $app->post("/brand/{id}/add_store", function($id) use ($app) {
      $brand = Brand::find($id);
      $store = Store::find($_POST['store_id']);
      $brand->addStore($store);
      return $app['twig']->render('brand.html.twig', array('brand' => $brand, 'stores' => $brand->getStores(), 'store' => $store, 'all_stores' => Store::getAll()));
  });

    return $app;

 ?>
