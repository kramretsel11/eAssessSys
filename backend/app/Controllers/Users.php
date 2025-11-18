<?php namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use App\Models\UsersModel;
use App\Models\AuthModel;
use App\Models\MiscModel;
use \Firebase\JWT\JWT;
use App\Libraries\AuditLogger;
use Config\Database;

class Users extends BaseController
{
    /**
     * @var \App\Models\AuthModel
     */
    protected $authModel;

    /**
     * @var \App\Models\MiscModel
     */
    protected $miscModel;

    /**
     * @var \App\Models\UsersModel
     */
    protected $userModel;

    /**
     * @var \App\Libraries\AuditLogger
     */
    protected $auditLogger;
    
    /**
     * @var \CodeIgniter\Database\BaseConnection
     */
    protected $db;
    
    public function __construct(){
        //Database
        $this->db = Database::connect();
        
        //Models
        $this->userModel = new UsersModel();
        $this->authModel = new AuthModel();
        $this->miscModel = new MiscModel();
        $this->auditLogger = new AuditLogger();
    }

    public function getUserDetails(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 

        //Select Query for finding User Information
        $user = $this->authModel->where(['id' => $data->id])->get()->getRow();
        
        //Set Api Response return to the FE
        if($user){

            if($user->status == 1){
                $user->userType = $this->miscModel->getUserType($user->userType);
                $user->branch = $this->miscModel->getBranch($user->branchId);
             
          
                return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode($user));
            } else {
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
            $response = [
                'title' => 'Please contact admin',
                'message' => 'Please check your data.'
            ];

            return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }


        // print_r(json_encode($data));
        
    }

    public function registerUser(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON(); 
        $data->password = sha1($data->password);
        
        $query = $this->userModel->insert($data);

        if($query){
            // Log successful user registration
            $this->auditLogger->log(
                'User Registration',
                'User registered successfully',
                null, // No specific user ID for new registration
                [
                    'new_user_id' => $query,
                    'username' => $data->username ?? 'N/A',
                    'email' => $data->email ?? 'N/A'
                ]
            );

            $response = [
                'title' => 'Registration Complete',
                'message' => 'User data has been successfully submitted.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            // Log failed user registration
            $this->auditLogger->log(
                'User Registration Failed',
                'User registration failed',
                null,
                [
                    'username' => $data->username ?? 'N/A',
                    'email' => $data->email ?? 'N/A',
                    'reason' => 'Database insertion failed'
                ]
            );

            $response = [
                'title' => 'Registration Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r($data);
        // exit();
        
    }
    
    public function updateUser(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        $where = [
            "id" => $data->uid
        ];
        $setData = [
            "firstName" => $data->firstName,
            "lastName" => $data->lastName,
            "middleName" => $data->middleName,
            "suffix" => $data->suffix,
            "email" => $data->email,
            "contact" => $data->contact,
            "userType" => $data->userType,
        ];
        
        $query = $this->userModel->updateUserInfo($where, $setData);

        if($query){

            $response = [
                'title' => 'Update Information',
                'message' => 'User data has been successfully updated.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            $response = [
                'title' => 'Registration Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r($data);
        // exit();
        
    }
    public function ChangePassword(){
        
        //Get API Request Data from NuxtJs
        $payload = $this->request->getJSON();
        $payload->newPassword = sha1($payload->newPassword);

        $where = ['id' => $payload->id];
        $updateData = ['password' => $payload->newPassword];

        $updatePassword =  $this->userModel->updatePassword($where, $updateData);

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
    public function updateUserStatus(){
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();
        
        // Get current user info for audit logging
        $currentUser = $this->userModel->where(['id' => $data->uid])->first();
        
        $where = [
            "id" => $data->uid
        ];
        $setData = [
            "status" => $data->status,
        ];
        
        $query = $this->userModel->updateUserInfo($where, $setData);

        if($query){
            // Log user status update
            $this->auditLogger->log(
                'User Status Update',
                'User status changed from ' . ($currentUser['status'] ?? 'unknown') . ' to ' . $data->status,
                $data->uid,
                [
                    'previous_status' => $currentUser['status'] ?? 'unknown',
                    'new_status' => $data->status,
                    'username' => $currentUser['username'] ?? 'N/A'
                ]
            );

            $response = [
                'title' => 'Update Information',
                'message' => 'User data has been successfully updated.'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
            
        } else {
            // Log failed user status update
            $this->auditLogger->log(
                'User Status Update Failed',
                'Failed to update user status',
                $data->uid,
                [
                    'attempted_status' => $data->status,
                    'username' => $currentUser['username'] ?? 'N/A',
                    'reason' => 'Database update failed'
                ]
            );

            $response = [
                'title' => 'Registration Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
        // print_r($data);
        // exit();
        
    }
    
    public function getAllUserList(){

        // $header = $this->request->getHeader("");
        
        $list = [];
        $list['list'] = $this->userModel->getAllUserInfo();

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }


    public function getAllInactiveUserList(){
        
        $list = [];
        $list['list'] = $this->userModel->getAllUserInfo(['userType' => 1, 'status' => 0]);

        if($list){
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($list));
        } else {
            $response = [
                'title' => 'Error',
                'message' => 'No Data Found'
            ];

            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    } 

    public function updateTenantStatus(){
        
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = ['id' => $data->userId];
        
        if($data->action == 'ACTIVE'){
            $setData = [
                'status' => 1,
                'reasonVacancy' => $data->reason,
            ];
        } else if ($data->action == 'DEACTIVE') {
            $setData = [
                'status' => 0,
                'reasonVacancy' => $data->reason,
            ];
        } else if ($data->action == 'VACATE') {
            $setData = [
                'status' => 2,
                'isFirstLogin' => 0,
                'premise' => '',
                'password' => '',
                'reasonVacancy' => $data->reason
            ];
        } else if($data->action == 'RENEW'){
            $setData = [
                'status' => 1,
                'startDate' =>  date('j-M-y', strtotime($data->startDate)),
                'endDate' => date('j-M-y', strtotime($data->endDate)),
                'reasonVacancy' => $data->reason
            ];
        } else if($data->action == 'RESET'){
            $setData = [
                'status' => 1,
                'isFirstLogin' => 0,
                'password' => 'password',
                'reasonVacancy' => $data->reason
            ];
        } else {
            $setData = [
                'reasonVacancy' => 'Something went wrong',
            ];
        }

        $update = $this->userModel->updateTenantInfo($where, $setData);

        if($update){
            if($data->action == 'VACATE'){
                $whereU = ['id' => $data->buildingId];
                $updateData = ['userId' => 0];
                $this->buildingModel->updateBuildingInfo($whereU, $updateData);
            }
            $response = [
                'title' => 'Tenant Information',
                'message' => 'Tenant details successfully updated'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Update Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    public function assignExistingTenant(){
        
        //Get API Request Data from NuxtJs
        $data = $this->request->getJSON();

        $where = ['id' => $data->userId];
        
        $setData = [
            'status' => 1,
            'isFirstLogin' => 0,
            'premise' => $data->premise,
            'password' => 'password',
            'startDate' => $data->startDate,
            'endDate' => $data->endDate,
        ];

        $update = $this->userModel->updateTenantInfo($where, $setData);

        if($update){
            if($data->action == 'VACANT'){
                $whereU = ['id' => $data->buildingId];
                $updateData = ['userId' => $data->userId];
                $this->buildingModel->updateBuildingInfo($whereU, $updateData);
            }
            $response = [
                'title' => 'Tenant Information',
                'message' => 'Tenant details successfully updated'
            ];
 
            return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        } else {
            $response = [
                'title' => 'Update Failed!',
                'message' => 'Please check your data.'
            ];
 
            return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode($response));
        }
    }

    // User Management API Endpoints

    /**
     * Get all users with filtering options for the admin panel
     */
    public function getUsersForAdmin(){
        try {
            $request = $this->request->getJSON();
            $municipality = $request->municipality ?? null;
            $role = $request->role ?? null;

            // Build query
            $builder = $this->db->table('tblusers u')
                ->select('u.*, ut.description as roleDescription')
                ->join('tblusertypes ut', 'u.userType = ut.id', 'left')
                ->where('u.isDeleted', 0);

            // Apply filters
            if ($municipality && $municipality !== 'all') {
                $builder->where('u.municipality', $municipality);
            }

            if ($role && $role !== 'all') {
                $builder->where('u.userType', $role);
            }

            $users = $builder->get()->getResult();

            // Log audit
            $this->auditLogger->log(
                'View Users List',
                'Admin viewed users list with filters',
                $this->getCurrentUserId(),
                ['municipality' => $municipality, 'role' => $role]
            );

            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'success',
                    'data' => $users
                ]));

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to fetch users: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Create a new user from admin panel
     */
    public function createUserForAdmin(){
        try {
            $data = $this->request->getJSON();

            // Validate required fields
            $required = ['username', 'password', 'firstName', 'lastName', 'email', 'userType', 'municipality'];
            foreach ($required as $field) {
                if (!isset($data->$field) || empty($data->$field)) {
                    return $this->response
                        ->setStatusCode(400)
                        ->setContentType('application/json')
                        ->setBody(json_encode([
                            'status' => 'error',
                            'message' => "Field '$field' is required"
                        ]));
                }
            }

            // Check if username or email already exists
            $existingUser = $this->userModel
                ->where('username', $data->username)
                ->orWhere('email', $data->email)
                ->where('isDeleted', 0)
                ->first();

            if ($existingUser) {
                return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'error',
                        'message' => 'Username or email already exists'
                    ]));
            }

            // Prepare user data
            $userData = [
                'username' => $data->username,
                'password' => sha1($data->password), // Using same hash as existing system
                'firstName' => $data->firstName,
                'lastName' => $data->lastName,
                'middleName' => $data->middleName ?? '',
                'suffix' => $data->suffix ?? '',
                'sex' => $data->sex ?? 'Not Specified',
                'email' => $data->email,
                'contact' => $data->contact ?? '',
                'address' => $data->address ?? '',
                'userType' => $data->userType,
                'municipality' => $data->municipality,
                'status' => 1,
                'isDeleted' => 0
            ];

            // Insert user
            $userId = $this->userModel->insert($userData);

            if ($userId) {
                // Log audit
                $this->auditLogger->log(
                    'User Created',
                    'Admin created new user',
                    $this->getCurrentUserId(),
                    [
                        'new_user_id' => $userId,
                        'username' => $data->username,
                        'municipality' => $data->municipality
                    ]
                );

                return $this->response
                    ->setStatusCode(201)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'success',
                        'message' => 'User created successfully',
                        'data' => ['id' => $userId]
                    ]));
            }

            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to create user'
                ]));

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to create user: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Update an existing user from admin panel
     */
    public function updateUserForAdmin(){
        try {
            $data = $this->request->getJSON();

            if (!isset($data->id) || empty($data->id)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'error',
                        'message' => 'User ID is required'
                    ]));
            }

            // Check if user exists
            $user = $this->userModel->where('id', $data->id)->where('isDeleted', 0)->first();
            if (!$user) {
                return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'error',
                        'message' => 'User not found'
                    ]));
            }

            // Prepare update data
            $updateData = [];
            $allowedFields = ['firstName', 'lastName', 'middleName', 'suffix', 'sex', 'email', 'contact', 'address', 'userType', 'municipality', 'status'];
            
            foreach ($allowedFields as $field) {
                if (isset($data->$field)) {
                    $updateData[$field] = $data->$field;
                }
            }

            // Handle password update separately
            if (isset($data->password) && !empty($data->password)) {
                $updateData['password'] = sha1($data->password);
            }

            // Check for username/email conflicts
            if (isset($data->username) && $data->username !== $user['username']) {
                $existingUser = $this->userModel
                    ->where('username', $data->username)
                    ->where('id !=', $data->id)
                    ->where('isDeleted', 0)
                    ->first();
                if ($existingUser) {
                    return $this->response
                        ->setStatusCode(400)
                        ->setContentType('application/json')
                        ->setBody(json_encode([
                            'status' => 'error',
                            'message' => 'Username already exists'
                        ]));
                }
                $updateData['username'] = $data->username;
            }

            if (isset($data->email) && $data->email !== $user['email']) {
                $existingUser = $this->userModel
                    ->where('email', $data->email)
                    ->where('id !=', $data->id)
                    ->where('isDeleted', 0)
                    ->first();
                if ($existingUser) {
                    return $this->response
                        ->setStatusCode(400)
                        ->setContentType('application/json')
                        ->setBody(json_encode([
                            'status' => 'error',
                            'message' => 'Email already exists'
                        ]));
                }
                $updateData['email'] = $data->email;
            }

            // Update user
            if (!empty($updateData)) {
                $result = $this->userModel->update($data->id, $updateData);

                if ($result) {
                    // Log audit
                    $this->auditLogger->log(
                        'User Updated',
                        'Admin updated user information',
                        $this->getCurrentUserId(),
                        [
                            'updated_user_id' => $data->id,
                            'changes' => array_keys($updateData)
                        ]
                    );

                    return $this->response
                        ->setStatusCode(200)
                        ->setContentType('application/json')
                        ->setBody(json_encode([
                            'status' => 'success',
                            'message' => 'User updated successfully'
                        ]));
                }
            }

            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update user'
                ]));

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update user: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Delete a user (soft delete)
     */
    public function deleteUserForAdmin(){
        try {
            $data = $this->request->getJSON();

            if (!isset($data->id) || empty($data->id)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'error',
                        'message' => 'User ID is required'
                    ]));
            }

            // Check if user exists
            $user = $this->userModel->where('id', $data->id)->where('isDeleted', 0)->first();
            if (!$user) {
                return $this->response
                    ->setStatusCode(404)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'error',
                        'message' => 'User not found'
                    ]));
            }

            // Prevent deletion of super admin
            if ($user['userType'] == 1) {
                return $this->response
                    ->setStatusCode(403)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'error',
                        'message' => 'Cannot delete super admin user'
                    ]));
            }

            // Soft delete user
            $result = $this->userModel->update($data->id, ['isDeleted' => 1]);

            if ($result) {
                // Log audit
                $this->auditLogger->log(
                    'User Deleted',
                    'Admin deleted user',
                    $this->getCurrentUserId(),
                    [
                        'deleted_user_id' => $data->id,
                        'username' => $user['username']
                    ]
                );

                return $this->response
                    ->setStatusCode(200)
                    ->setContentType('application/json')
                    ->setBody(json_encode([
                        'status' => 'success',
                        'message' => 'User deleted successfully'
                    ]));
            }

            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete user'
                ]));

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete user: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Get user types for dropdown
     */
    public function getUserTypes(){
        try {
            $userTypes = $this->db->table('tblusertypes')->get()->getResult();

            return $this->response
                ->setStatusCode(200)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'success',
                    'data' => $userTypes
                ]));

        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(500)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to fetch user types: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * Helper method to get current user ID from JWT token
     */
    private function getCurrentUserId(){
        try {
            $authHeader = $this->request->getHeaderLine('Authorization');
            if ($authHeader && preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
                $decoded = JWT::decode($token, new \Firebase\JWT\Key(getenv('JWT_SECRET'), 'HS256'));
                return $decoded->uid ?? null;
            }
        } catch (\Exception $e) {
            // Return null if token is invalid
        }
        return null;
    }

}