<?php
 
namespace App\Controllers;
 
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;
 
class Login extends BaseController
{
    use ResponseTrait;
     
    public function index()
    {
        $userModel = new UserModel();
        
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');
          
        $user = $userModel->where('email', $email)->first();
  
        if(is_null($user)) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }
  
        $pwd_verify = password_verify($password, $user['password']);
  
        if(!$pwd_verify) {
            return $this->respond(['error' => 'Invalid username or password.'], 401);
        }
 
        $key = getenv('JWT_SECRET');
        $iat = time();
        $nbf = $iat + 10;
        $exp = $iat + 3600;
 
        $payload = array(
            "iss" => "The_claim",
            "aud" => "The_Aud",
            "iat" => $iat,
            "nbf" => $nbf,
            "exp" => $exp,
            "email" => $user['email'],
        );
        //HS256
        $token = JWT::encode($payload, $key, 'HS256');
 
        $response = [
            'message' => 'Login Succesful',
            'token' => $token
        ];
         
        return $this->respond($response, 200);
    }
 
}