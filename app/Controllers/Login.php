<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class Login extends BaseController
{
    use ResponseTrait;

    public function updateUser()
    {
        $email = trim($this->request->getVar('email'));
        $fcmtoken = trim($this->request->getVar('fcmtoken'));

        if (is_null($email)) {
            return $this->respond(['error' => 'Invalid email or password.'], 401);
        }
        $db = db_connect();
        $sql = "UPDATE `users` SET `fcmtoken` = '" . $fcmtoken . "' WHERE `users`.`email` = '" . $email . "';";
        if ($query = $db->query($sql)) {
            return $this->respond(['message' => 'Update Successful'], 200);
        } else {
            return $this->respond(['error' => 'Invalid email or password.'], 401);
        }
    }

    public function refreshToken(){
        $email = $this->request->getVar('email');
        

       
        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
        $exp = $iat + 3600;
        //$exp = $iat + 60; // Test 1 minutes

        $payload = array(
            "iss" => "mediaoto",
            "aud" => "mobile",
            "sub" => "api",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "email" => $email,
        );

        $token = JWT::encode($payload, $key, 'HS256');

        $response = [
            'message' => 'Refresh Succesful',
            'token' => $token
        ];

        return $this->respond($response, 200); 


    }

    public function index()
    {
        $userModel = new UserModel();

        $email = trim($this->request->getVar('email'));
        $password = trim($this->request->getVar('password'));
        $fcmtoken = $this->request->getVar('fcmtoken');

        $user = $userModel->where('email', $email)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

        
        $pwd_verify = password_verify($password, $user['password']);

        if (!$pwd_verify) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }
        

        //update 
        $db = db_connect();
        $sql = "UPDATE `users` set `fcmtoken` = '".$fcmtoken."' WHERE `email` = '".$email."';";
        $query = $db->query($sql);

        $db->close();


        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
       $exp = $iat + 3600;
        //$exp = $iat + 60;

        $payload = array(
            "iss" => "mediaoto",
            "aud" => "mobile",
            "sub" => "api",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "email" => $user['email'],
        );

        $token = JWT::encode($payload, $key, 'HS256');

        $response = [
            'id' => $user['id'],
            'message' => 'Login Succesful',
            'token' => $token
        ];

        return $this->respond($response, 200);
    }

    public function loginProvider()
    {
        $userModel = new UserModel();

        $email = $this->request->getVar('email');
        $fcmtoken = $this->request->getVar('fcmtoken');

        $user = $userModel->where('email', $email)->first();

        if (is_null($user)) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

       
        if ($user['provider'] == 0) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }

        //update 
        $db = db_connect();
        $sql = "UPDATE `users` set `fcmtoken` = '".$fcmtoken."' WHERE `email` = '".$email."';";
        $query = $db->query($sql);

        $db->close();


        $key = getenv('JWT_SECRET');
        $iat = time(); // current timestamp value
       $exp = $iat + 3600;
        //$exp = $iat + 60;

        $payload = array(
            "iss" => "mediaoto",
            "aud" => "mobile",
            "sub" => "api",
            "iat" => $iat, //Time the JWT issued at
            "exp" => $exp, // Expiration time of token
            "email" => $user['email'],
        );

        $token = JWT::encode($payload, $key, 'HS256');

        $response = [
            'id' => $user['id'],
            'message' => 'Login Succesful',
            'token' => $token
        ];

        return $this->respond($response, 200);
    }

}