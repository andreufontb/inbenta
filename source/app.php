<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Questions to ask (by order)
 */
$app['questions'] = array(
    'What is your name?',
    'What is your email?',
    'How old are you?'
);



// Middleware to process json received data
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

// Main route
$app->get('/', function (Application $app) {
    return $app['twig']->render('index.html', array(
        'title' => 'Bot exercise',
        'name' => 'Paloma'
    ));
});

// Route to get chat history
$app->get('/chat/history', function (Application $app, Request $request) {
    return $app->json($app['getHistory']);
});

// Route to receive chat messages
$app->post('/chat', function(Application $app, Request $request){
    //1. Tag and save the received message to history
    $message = array(
        'type'      => 'sent',
        'content'   => $request->request->get('message')
    );
    $app['addToHistory']($message);
    
    return $app->json($message);
});


/**
 * Internal functions to process data between requests
 */


 /**
  * return the history chat log and if is empty, creates a new one.
  */
$app['getHistory'] = function($app) {
    $history = $app['readHistory'];
    if (count($history) == null){
        $history = array(
            array(
                'type'      => 'received',
                'content'   => 'Hello'
            )
        );
        $app['addToHistory']($history[0]);
    }
    
    return $history;
};

/**
 * Recovers the current history log
 */
$app['readHistory'] = function ($app) {
    return $app['session']->get('history');
};


/**
 * Add a new message tho the history log
 */
$app['addToHistory'] = $app->protect(function ($message) use ($app) {
    $history = $app['readHistory'];
    $history[] = $message;
    $app['session']->set('history', $history);
});

/**
 * Get the current user interaction data that bot know
 */
/* function getData(){

} */

/**
 * Save data of user to read later
 */
/* function saveData($data){

} */