<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

require '../includes/DbConnect.php';

require '../includes/DbOperations.php';

$app = new \Slim\App([
   'settings'=>[
       'displayErrorDetails'=>true
   ]
]);

//Registrazione
$app->post('/createuser', function(Request $request, Response $response){
   
    if(!haveEmptyParameters(array('email','password','name','username'),$request, $response)){

      $request_data = $request->getParsedBody();

      $email = $request_data['email'];
      $password = $request_data['password'];
      $name = $request_data['name'];
      $username = $request_data['username'];

      $hash_password = password_hash($password, PASSWORD_DEFAULT);

      $db = new DbOperations;

      $result = $db->createUser($email, $hash_password, $name, $username);

      if($result == USER_CREATED){

         $message = array();
         $message['error'] = false;
         $message['message'] = 'User created succesfully';

         $response->write(json_encode($message));

         return $response
                   ->withHeader('Content-type', 'application/json')
                   ->withStatus(201);


      } else if ($result == USER_FAILURE){

        $message = array();
        $message['error'] = true;
        $message['message'] = 'Some error occured';

        $response->write(json_encode($message));

        return $response
                  ->withHeader('Content-type', 'application/json')
                  ->withStatus(422);


         }else if ($result == USER_EXISTS){

            $message = array();
            $message['error'] = true;
            $message['message'] = 'User already exists';
   
            $response->write(json_encode($message));
   
            return $response
                      ->withHeader('Content-type', 'application/json')
                      ->withStatus(422);
      }

    }  

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);

});

//Login
  $app->post('/userlogin', function(Request $request, Response $response){

    if(!haveEmptyParameters(array('email', 'password'),$request, $response)){

      $request_data = $request->getParsedBody();
      $email = $request_data['email'];
      $password = $request_data['password'];

      $db = new DbOperations;

      $result = $db->userLogin($email, $password);
      
      if($result == USER_AUTHENTICATED){

       $user = $db->getUserByEmail($email);
       $response_data = array();
       $response_data['error'] = false;
       $response_data['message'] = 'Login Successful';
       $response_data['user'] = $user;

       $response -> write(json_encode($response_data));

       return $response
         ->withHeader('Content-type', 'application/json')
         ->withStatus(200);

      }else if ($result == USER_NOT_FOUND){

         $response_data = array();

         $response_data['error'] = true;
         $response_data['message'] = 'User not exist';
        
         $response -> write(json_encode($response_data));
  
         return $response
           ->withHeader('Content-type', 'application/json')
           ->withStatus(200);
  
      }else if($result == USER_PASSWORD_DO_NOT_MATCH){

         $response_data = array();

         $response_data['error'] = true;
         $response_data['message'] = 'Invalid credential';
        
         $response -> write(json_encode($response_data));
  
         return $response
           ->withHeader('Content-type', 'application/json')
           ->withStatus(200);
   }


}

    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(422);

 });

 //Mostra tutti gli utenti
 $app->get('/allusers', function(Request $request, Response $response){

    $db = new DbOperations;
    $users = $db->getAllUsers();
    $response_data = array();
    $response_data['error'] = false;
    $response_data['users'] = $users;
    $response->write(json_encode($response_data));
    
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);

});

//Aggiona utente tramite id
 $app->put('/updateuser/{id}', function(Request $request, Response $response, array $args){

     $id = $args['id'];

    if(!haveEmptyParameters(array('email', 'name', 'username'),$request, $response)){

     $request_data = $request->getParsedBody();
     $email = $request_data['email'];
     $name = $request_data['name'];
     $username = $request_data['username'];
     
     $db = new DbOperations;

    if($db->updateUser($email, $name, $username, $id)) {

      $response_data = array();
      $response_data['error'] = false;
      $response_data['message'] = 'User Update Successfully';
      $user = $db->getUserByEmail($email);
      $response_data['user'] = $user;

      $response->write(json_encode($request_data));

      return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(200);

      }else {

      $response_data = array();
      $response_data['error'] = true;
      $response_data['message'] = 'Please try again';
      $user = $db->getUserByEmail($email);
      $response_data['user'] = $user;

      $response->write(json_encode($request_data));

      return $response
      ->withHeader('Content-type', 'application/json')
      ->withStatus(200);

       }

    }
   
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);
});

//Aggiorna password
$app->put('/updatepassword', function(Request $request, Response $response){
  
   if(!haveEmptyParameters(array('currentpassword', 'newpassword', 'email'), $request, $response)){
       
       $request_data = $request->getParsedBody(); 
       $currentpassword = $request_data['currentpassword'];
       $newpassword = $request_data['newpassword'];
       $email = $request_data['email']; 
       $db = new DbOperations; 
       $result = $db->updatePassword($currentpassword, $newpassword, $email);

       if($result == PASSWORD_CHANGED){
         $response_data = array(); 
         $response_data['error'] = false;
         $response_data['message'] = 'Password Changed';
         $response->write(json_encode($response_data));
         return $response->withHeader('Content-type', 'application/json')
                         ->withStatus(200);
     }else if($result == PASSWORD_DO_NOT_MATCH){
         $response_data = array(); 
         $response_data['error'] = true;
         $response_data['message'] = 'You have given wrong password';
         $response->write(json_encode($response_data));
         return $response->withHeader('Content-type', 'application/json')
                         ->withStatus(200);
     }else if($result == PASSWORD_NOT_CHANGED){
         $response_data = array(); 
         $response_data['error'] = true;
         $response_data['message'] = 'Some error occurred';
         $response->write(json_encode($response_data));
         return $response->withHeader('Content-type', 'application/json')
                         ->withStatus(200);
     }
 }
 return $response
     ->withHeader('Content-type', 'application/json')
     ->withStatus(422);  
});

//Elimina untente tramite id
$app->delete('/deleteuser/{id}', function(Request $request, Response $response, array $args){

   $id = $args['id'];
   
   $db = new DbOperations;

   $response_data = array();

   if($db->deleteUser($id)){

    $response_data['error'] = false;
    $response_data['message'] ='User has been deleted';
      
      }else{

         $response_data['error'] = true;
         $response_data['message'] ='Try again later';

   }

   $response->write(json_encode($response_data));

   return $response
   ->withHeader('Content-type', 'application/json')
   ->withStatus(200);  

});

function haveEmptyParameters($required_params, $request, $response){

       $error = false;
       $error_params = '';
       $request_params = $request->getParsedBody();

    foreach($required_params as $param){

        if(!isset($request_params[$param]) || strlen($request_params[$param])<=0 ){
           $error = true;
           $error_params .= $param . ', ';}
        }

        if($error){

           $error_detail = array();
           $error_detail['error'] = true;
           $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
           $response->write(json_encode($error_detail));
        }

        return $error;

   }


 $app->run();
