<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Create Router instance */
$router = new \Bramus\Router\Router();

/* Set credentials for authentication */
$cred = set_cred('ddwt18', 'ddwt18');
$router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
    check_cred($cred);
    if (! check_cred($cred)){
        echo "You're not allowed to perform this action";
        exit();
    }
});

/* Getting api mount */
$router->mount('/api', function() use ($router){
    /* Connect to the db */
    $db = connect_db('localhost', 'ddwt18_week3', 'ddwt18', 'ddwt18');

    /* Setting content type to json */
    http_content_type("application/json");

    /* Check if url is valid */
    $router->set404(function() {
        header("HTTP/1.1 404 Not Found");
        echo "This page doesn't exist, check if the url you filled in is correct";
    });

    /* Get the info of all the series */
    $router->get('/series', function() use ($db) {
        $series = json_encode(get_series($db));
        echo $series;
    });

    /* Get the info of an individual serie */
    $router->get('/series/(\d+)', function($id) use ($db) {
        $serie_info = json_encode(get_serieinfo($db, $id));
        echo $serie_info;
    });

    /* Delete a series */
    $router->delete('/series/(\d+)', function($id) use ($db) {
        $delete_serie = json_encode(remove_serie($db, $id));
        echo $delete_serie;
    });

    /* Add a series */
    $router->post('/series', function() use ($db) {
        $add_serie = json_encode(add_serie($db, $_POST));
        echo $add_serie;
    });


    /* Update a series */
    $router->put('/series/(\d+)', function($id) use ($db) {
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        $edit_serie = json_encode(update_serie($db, $serie_info));
        echo $edit_serie;
    });


});


/* Run the router */
$router->run();
