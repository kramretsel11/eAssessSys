<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table      = 'tblusers';
    protected $typeTable  = 'tblusertypes';
    protected $branchTable= 'tblbranches';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'username', 
        'password', 
        'firstName', 
        'lastName', 
        'middleName', 
        'suffix',
        'sex',
        'address',
        'userType', 
        'email', 
        'contact',
        'status',
        'municipality',
        'isDeleted'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'createdDate';
    protected $updatedField  = 'updatedDate';
    // protected $deletedField  = 'deleted_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;


    public function getUserInfo($id){

        $query = $this->db->table($this->table)->where('id', $id)->get();
        $results = $query->getRow();
        return $results;
    }

    public function getAllUserInfo(){

        $query = $this->db->table($this->table)->whereIn('status', [1,0])->get();
        $results = $query->getResult('array');

        $all = array_map(function($el){
            foreach($el as $key => $val){
                $type = $this->db->table($this->typeTable)->where('id', $el['userType'])->get()->getRow();
                $el['userTypeDescription'] = $type->description;
            }
            return $el;
        }, $results);

        return $all;
    }

    public function updateUserInfo($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }

    public function updatePassword($where, $setData){

        $query = $this->db->table($this->table)->set($setData)->where($where)->update();
        return $query ? true : false;

    }


}