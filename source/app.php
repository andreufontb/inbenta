<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Questions to ask (by order)
 */
$app['dataStructure'] = array (
    'hello' => array (
        'question' => 'Hello! I will ask you some questions ok?',
        'value' => null,
        //'response' => '',
        'error' => '',
        'validation' => 'askThis'
    ),
    'name'  => array (
        'question' => 'What is your name?',
        'value' => null,
        //'response' => 'Your name is {$name}',
        'error' => '',
        'validation' => ''
    ),
    'email' => array (
        'question' => 'What is your email?',
        'value' => null,
        //'response' => 'you are {$age} old',
        'error' => 'Sorry, I could not understand your email address',
        'validation' => 'email'
    ),
    'age'   => array (
        'question' => 'How old are you?',
        'value' => null,
        //'response' => 'I can contact you on {$email}',
        'error' => 'Sorry, I could not understand your age',
        'validation' => 'numeric'
    )
);
$app['index'] = ['hello','name','email','age']; //Index of questions

/**
 * Middleware to process json received data
 */
$app->before(function (Request $request) use ($app){
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

/**
 * ////////// Routes and controllers to present the web, send and receive chat messages ///////////
 */

/**
 * Main route
 */
$app->get('/', function (Application $app) {
    return $app['twig']->render('index.html', array(
        'title' => 'Bot exercise',
        'name' => 'Paloma'
    ));
});

/**
 * Route to get chat history in JSON
 */
$app->get('/chat/history', function (Application $app, Request $request) {
    return $app->json($app['getHistory']);
});

/**
 * Route where receive user messages
 * 
 * It returns the response of the bot in JSON
 */
$app->post('/chat', function(Application $app, Request $request){
    $history = $app['getHistory'];
    $data    = $app['getData'];
    //1. Tag and save the received message to history
    $message = array(
        'type'      => 'sent',
        'content'   => $request->request->get('message')
    );
    $history[] = $message;

    $response = [];

    if ($app['pendingQuestions']($data)  > 0 ){
        $currentIndex = $app['currentQuestion']($data);
        $currentQuestionName = $app['index'][$currentIndex];
        $errors = null;
        
        switch ($data[$currentQuestionName]['validation']){
            case 'askThis': // Send current question and set it as answered
                $question = $data[$currentQuestionName]['question'];
                $response[] = $app['generateSendMessage']($question);
                break;
            case 'email': //Run email test validation
                $errors = $app['validator']->validate($message['content'], new Assert\Email());
                break;
            case 'numeric': //Run numeric test validation
                $errors = $app['validator']->validate($message['content'], new Assert\Range(array(
                    'min'        => 0,
                    'max'        => 110
                )));
                break;
        }

        if (count($errors) > 0){
            //Send an error message
            $errorMessage = $data[$currentQuestionName]['error'];
            $response[] = $app['generateSendMessage']($errorMessage);
            //re-send the current question
            $question = $data[$currentQuestionName]['question'];
            $response[] = $app['generateSendMessage']($question);
        } else {
            //save the message as answer for the current question
            $data[$currentQuestionName]['value'] = $message['content'];

            //Send the next question
            if ($app['pendingQuestions']($data)>0) {
                $nextQuestionName = $app['index'][$currentIndex+1];
                $question = $data[$nextQuestionName]['question'];
                $response[] = $app['generateSendMessage']($question);
            } else {
                $response[] = $app['generateEndMessage']($data);
            }
            
        }

        $app['storeData']($data);
    } else {
        //$response = "Thanks! Now I know you better. Your name is {$data['name']}, you are {$data['age']} old and I can contact you on {$data['email']}";
        $response[] = $app['generateEndMessage']($data);
    }
    

    // Return the response
    foreach($response as $message){
        $history[] = $message;
    }
    $app['storeHistory']($history);
    return $app->json($response);
});


/**
 * ////////// Internal functions to process data between requests ///////////
 */


 /**
  * Load the current history log
  */
$app['getHistory'] = function($app) {
    $history = $app['session']->get('history');
    if ($history == null){
        $history = [];
    }
    return $history;
};

/**
 * Store history log
 */
$app['storeHistory'] = $app->protect( function($history) use ($app) {
    $app['session']->set('history', $history);
});

/**
 * Loads the user data, if doesn't exists, creates an empty set from template
 */
$app['getData'] = function ($app){
    $data = $app['session']->get('data');
    if ($data == null){
        $data = $app['dataStructure'];
    }
    return $data;
};

/**
 * Save data of current user
 */
$app['storeData'] = $app->protect( function($data) use ($app) {
    $app['session']->set('data', $data);
});

/**
 * Return the number of pending questions
 */
$app['pendingQuestions'] = $app->protect( function($data) {
    $i = 0;
    foreach($data as $question){
        if ($question['value'] == null){
            $i++;
        }
    };
    return $i;
});

/**
 * Return the index of current question
 */
$app['currentQuestion'] = $app->protect( function($data) use ($app) {
    $pending = $app['pendingQuestions']($data);
    $total = count($data);
    return $total-$pending;
});

/**
 * Generates a message to sended by bot (received by user)
 */
$app['generateSendMessage'] = $app->protect(function($message){
    return array(
        'type' => 'received',
        'content' => $message
    );
});

/**
 * Generates the end message, when bot hava all data
 */
$app['generateEndMessage'] = $app->protect(function($data) use ($app){
    
    return $app['generateSendMessage']("Thanks! Now I know you better. Your name is {$data['name']['value']}, you are {$data['age']['value']} old and I can contact you on {$data['email']['value']}");
    
});