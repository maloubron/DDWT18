<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

// Set credentials for authentication
$cred = set_cred('ddwt18', 'ddwt18');

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week3', 'ddwt18', 'ddwt18');

/* Create Router instance */
$router = new \Bramus\Router\Router();

// Add routes here

$router->mount('/api', function() use ($router, $db, $cred) {

    http_content_type('application/json');

    $router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){ // Validate authentication
        if (!check_cred($cred)){
            $feedback = [
                'type' => 'danger',
                'message' => 'Authentication failed. Please check the credentials.'
            ];
            echo json_encode($feedback);
            exit();
        }
    });

    $router->set404(function() {
        header('HTTP/1.1 404 Not Found');
        echo 'error 404 page nog found';
    });

     $router->get('/series', function() use ($db){
         $series = get_series($db);
         echo json_encode($series);
     });

     /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) { // Retrieve and output information
        $series_info = get_serieinfo($db, $id);
        echo json_encode($series_info);
        });

    /* Route for deleting individual series */
    $router->delete('/series/(\d+)', function($id) use($db) { // Retrieve and output information
        $feedback = remove_serie($db, $id);
        echo json_encode($feedback);
    });

    /* POST route to create series */
    $router->post('/series', function() use ($db){
        $newseries = add_series($db, $_POST);
        echo json_encode($newseries);

    });

    $router->put('/series/(\d+)', function($id) use ($db){
        $_PUT = array(); parse_str(file_get_contents('php://input'), $_PUT);
        $serie_info = $_PUT + ["serie_id" => $id];
        $newserie = update_series($db, $serie_info);
        echo json_encode($newserie);
    });


});



    /* Run the router */
$router->run();
