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
        $store = Store::find($id);
        return $app['twig']->render('store.html.twig', array('stores' => Store::getAll(), 'store' => $store));
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
        return $app['twig']->render('store.html.twig', array('store' => $store));
    });

    //deletes individual store from database
    $app->delete("/store/{id}/delete", function($id) use ($app) {
        $store = Store::find($id);
        $store->deleteStore();
        return $app['twig']->render('store_delete.html.twig', array('store' => $store));
    });

    //list of all brands, get here from the homepage
    $app->get("/brands", function() use ($app) {
        return $app['twig']->render('brands.html.twig', array('brands' => Brand::getAll()));
    });


    return $app;

 ?>
