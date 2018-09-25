<?php

namespace App\Core;

use App\Core\App;

class Role{
    
    protected $userRole=[];

    protected $allRoles=[];
    /*
    Add role to database
    */
    public function addRole(){

    }

    /*
    Give role to the user
    */
    public function giveUserRole($userId, $role){

    }

    /*
    Give permission to the role
    */
    public function giveRolePermission(){

    }

    public function getAllRole(){
        return $this->allRoles=App::get('builder')->getAllData('roles', 'Role');
    }

    //this should be getUserRole
    public function getRole($id){
        $forClass='Role';
        $sql=sprintf('SELECT b.name as role, d.name as permission FROM role_user as a inner join roles as b on a.role_id=b.id 
        inner join permission_role as c on b.id=c.role_id inner join permissions as d on d.id=c.permission_id where a.user_id=%s', ':user_id');
        $statement=App::get('builder')->getPdo()->prepare($sql);
        $statement->execute(['user_id'=>$id]);
        return $this->userRole=$statement->fetchAll(\PDO::FETCH_COLUMN|\PDO::FETCH_GROUP);
    }

    /*
        @description: function to get the role id of the specified role name
        @example: role with name 'admin' will return id '1'
        @return the id of the role
    
    public function getIdOfThisRole(){

        $parameters=[
            'id',
            'name'
        ];

        $where=[
            'name'=>$this->role,
        ];
        $result=App::get('builder')->getSpecificData('roles',$parameters,$where,'','Role');
        return $this->roleId=$result[0]->id;
    }

    
        @description: function to get the role id of the specified role name
        @example: role with name 'admin' will return id '1'
        @return the id of the role
    
    public function getIdOfThisPermission(){

        $parameters=[
            'id',
            'name'
        ];

        $where=[
            'name'=>$this->permission,
        ];
        $result=App::get('builder')->getSpecificData('permissions',$parameters,$where,'','Role');
        return $this->permissionId=$result[0]->id;
    }
    */

     /*
        @description:function that check is the user have the role as specified in argument of the function
        @param: the name of role. 
        @return: boolean. true if the user have the same role as specified in argument. otherwise false.
    */
    public function hasRole($role){
        foreach($this->userRole as $userRole=>$value){
            if($role==$userRole){
                return true;
            }
        }
        return false;
    }

    /*
        @description: the permissions/abilities of the user's role.
        @param: the name of permission.
        @example: 'admin' role CAN do 'create data' and 'delete data'
        @return: boolean. true if the role of the user has the same permission with the argument.
    */
    public function can($permission){

        foreach($this->userRole as $role =>$userPermission){
            for($i=0;$i<count($userPermission);$i++){
                if($permission==$userPermission[$i]){
                    return true;
                }
            }
        }
        return false;
    }

}

?>