<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\AuthModel;
use App\Models\MiscModel;
use App\Models\UsersModel;
use App\Libraries\AuditLogger;

use CodeIgniter\RESTful\ResourceController;
use \Firebase\JWT\JWT;

class Auth extends BaseController
{
    /**
     * @var \App\Models\AuthModel
     */
    protected $authModel; // <--- ADD THIS LINE!

    /**
     * @var \App\Models\MiscModel
     */
    protected $miscModel; // <--- ADD THIS LINE!

    /**
     * @var \App\Models\UsersModel
     */
    protected $userModel; // <--- ADD THIS LINE!
    
    /**
     * @var \App\Libraries\AuditLogger
     */
    protected $auditLogger;
    
    public function __construct(){
        //Models
        $this->authModel = new AuthModel();
        $this->miscModel = new MiscModel();
        $this->userModel = new UsersModel();
        
        // Initialize audit logger
        $this->auditLogger = new AuditLogger();
    }

    public function privateKey(){
        $privateKey = <<<EOD
        -----BEGIN RSA PRIVATE KEY-----
        MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
        vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
        5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
        AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
        bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
        Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
        cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
        5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
        ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
        k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
        qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
        eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
        B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
        -----END RSA PRIVATE KEY-----
        EOD;

        return $privateKey;
    }

    public function login(){
        // Set proper content type header
        $this->response->setContentType('application/json');
        
        try {
            //Get API Request Data from NuxtJs
            $data = $this->request->getJSON(); 
            
            if (!$data || !isset($data->username) || !isset($data->password)) {
                $response = [
                    'error' => 400,
                    'title' => 'Invalid Request',
                    'message' => 'Username and password are required'
                ];
                
                return $this->response
                        ->setStatusCode(400)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
            
            $hasPass = sha1($data->password);

            //Select Query for finding User Information
            $user = $this->authModel->where(['username' => $data->username, 'password' => $hasPass])->get()->getRow();
        
        //Set Api Response return to the FE
        if($user){

            if($user->status == 1){
                // $userModules = $user[0]['userType'] == 0 ? ['101', '103', '104', '105'] : ['101', '102'];

                $user->userType = $this->miscModel->getUserType($user->userType);
                // $userModules =;
                // $user[0]['usersMod'] = $userModules;
                // print_r($user);

                // //Set JWT Authorization
                $secretKey = $this->privateKey();
                $issueTimeClaim = time();
                $notBeforeClaim = $issueTimeClaim + 10;
                $expiryClaim = $issueTimeClaim + 3600;

                // //Generate Token
                $token = [
                    "fullName" => $user->firstName .' '. $user->lastName,
                    "iss" => $user->email,
                    "aud" => $user->username,
                    "iat" => $issueTimeClaim,
                    "nbf" => $notBeforeClaim,
                    "exp" => $expiryClaim,
                    "userId" => $user->id,
                    "modules" =>  $user->userType->modules
                ];

                $jwt = JWT::encode($token, $secretKey, 'RS256');

                // Log successful login
                $this->auditLogger->logAuth(
                    $user->id,
                    'Login',
                    'User logged in successfully',
                    $this->request
                );

                // Determine user role based on userType ID
                $role = 'user'; // default role
                if ($user->userType->id == 1) { // Super Admin
                    $role = 'admin';
                } elseif ($user->userType->id == 2) { // Coordinator
                    $role = 'coordinator';
                }

                $result = [
                    "fullName" => $user->firstName .' '. $user->lastName,
                    "userId" => $user->id,
                    "userType" => $user->userType,
                    "role" => $role,
                    "jwt" => $jwt,
                    // "userData" => $user
                ];


                return $this->response 
                        ->setHeader('jwt', $jwt)
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($result));
            } else {
                // Log failed login attempt for deactivated account
                if ($user) {
                    $this->auditLogger->logAuth(
                        $user->id,
                        'Login Failed',
                        'Login attempt with deactivated account',
                        $this->request
                    );
                }
                
                $response = [
                    'title' => 'Account Deactivated',
                    'message' => 'Please contact your adminitrator for more information'
                ];
    
                return $this->response
                        ->setStatusCode(404)
                        ->setContentType('application/json')
                        ->setBody(json_encode($response));
            }
            
        } else {
            // Log failed login attempt
            $this->auditLogger->log(
                0, // No user ID for failed login
                'Login Failed',
                'Authentication',
                null,
                'Failed login attempt for username: ' . ($data->username ?? 'unknown'),
                $this->request
            );
            
            $response = [
                'error' => 404,
                'title' => 'Invalid Credentials',
                'message' => 'Please check your username or password'
            ];

            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Login error: ' . $e->getMessage());
            
            $response = [
                'error' => 500,
                'title' => 'Server Error',
                'message' => 'An internal server error occurred'
            ];

            return $this->response
                    ->setStatusCode(500)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }  

    public function firstLoginChangePassword(){

        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();

        $where = ['id' => $payload->id];
        $updateData = ['password' => $payload->password, 'isFirstLogin' => 1];

        $updatePassword =  $this->authModel->updateBuildingInfo($where, $updateData);

        if($updatePassword){
            $response = [
                'title' => 'Change Password',
                'message' => 'Your successfully change password.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Change Password Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    /**
     * Get current user information based on JWT token
     */
    public function getCurrentUser(){
        $headers = $this->request->getHeaders();
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $token = str_replace('Bearer ', '', $authHeader[0]);
            
            try {
                // For newer JWT library versions, need to create Key object
                $key = new \Firebase\JWT\Key($this->privateKey(), 'RS256');
                $decoded = JWT::decode($token, $key);
                $userId = $decoded->userId;
                
                // Get user data
                $user = $this->authModel->find($userId);
                if ($user) {
                    $user['userType'] = $this->miscModel->getUserType($user['userType']);
                    
                    // Determine role based on userType ID
                    $role = 'user'; // default role
                    if ($user['userType']->id == 1) { // Super Admin
                        $role = 'admin';
                    } elseif ($user['userType']->id == 2) { // Coordinator
                        $role = 'coordinator';
                    }
                    
                    $userData = [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'firstName' => $user['firstName'],
                        'lastName' => $user['lastName'],
                        'email' => $user['email'],
                        'userType' => $user['userType'],
                        'role' => $role,
                        'fullName' => $user['firstName'] . ' ' . $user['lastName']
                    ];
                    
                    return $this->response
                            ->setStatusCode(200)
                            ->setContentType('application/json')
                            ->setBody(json_encode($userData));
                }
            } catch (\Exception $e) {
                return $this->response
                        ->setStatusCode(401)
                        ->setContentType('application/json')
                        ->setBody(json_encode(['error' => 'Invalid token']));
            }
        }
        
        return $this->response
                ->setStatusCode(401)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'No authorization token provided']));
    }

    /**
     * Check if current user is admin
     */
    public function isAdmin(){
        $headers = $this->request->getHeaders();
        
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            $token = str_replace('Bearer ', '', $authHeader[0]);
            
            try {
                // For newer JWT library versions, need to create Key object
                $key = new \Firebase\JWT\Key($this->privateKey(), 'RS256');
                $decoded = JWT::decode($token, $key);
                $userId = $decoded->userId;
                
                // Get user data
                $user = $this->authModel->find($userId);
                if ($user) {
                    $userType = $this->miscModel->getUserType($user['userType']);
                    $isAdmin = ($userType->id == 1); // Super Admin ID
                    
                    return $this->response
                            ->setStatusCode(200)
                            ->setContentType('application/json')
                            ->setBody(json_encode(['isAdmin' => $isAdmin, 'role' => $isAdmin ? 'admin' : 'user']));
                }
            } catch (\Exception $e) {
                return $this->response
                        ->setStatusCode(401)
                        ->setContentType('application/json')
                        ->setBody(json_encode(['error' => 'Invalid token']));
            }
        }
        
        return $this->response
                ->setStatusCode(401)
                ->setContentType('application/json')
                ->setBody(json_encode(['error' => 'No authorization token provided']));
    }

}