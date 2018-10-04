<?php


class QueryBuilder{

    protected $pdo;

    public function __construct(PDO $pdo){
        $this->pdo=$pdo;
        $this->pdo->beginTransaction();
    }

    public function getPdo(){
        return $this->pdo;
    }

    public function save(){
        return $this->pdo->commit();
    }

    public function cancel(){
        return $this->pdo->rollBack();
    }

    public function getAllData($table, $forClass){
        $statement=$this->pdo->prepare("SELECT * FROM {$table}");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, $forClass);       
    }

    public function getSpecificData($table, $parameters, $where, $operator, $forClass){

        $toSearch=implode(',',$parameters);
        $whereClause='';

        $keys=array_keys($where);
        for($i=0;$i<count($keys);$i++){            
            if($i==count($keys)-1){
                $operator='';
            }
            $whereClause.=$keys[$i]."=:".$keys[$i]." $operator ";
        }

        $sql=sprintf('select %s from %s where %s', $toSearch, $table, $whereClause);
        
        $statement=$this->pdo->prepare($sql);
        $statement->execute($where);
        return $statement->fetchAll(PDO::FETCH_CLASS, $forClass);
        
    }

    public function insert($table, $parameters){
        $keys=array_keys($parameters);
        $sql=sprintf('insert into %s(%s) values(%s)',
            $table,
            implode(',',$keys),
            ':'.implode(',:',$keys)
        );
       
        try{
            $statement=$this->pdo->prepare($sql);
            
            $statement->execute($parameters);

            return true;
        }catch(PDOException $e){
            $_SESSION['messages']=[["terdapat kesalahan atau duplicate data.",0]]; 

            $this->cancel();

            return false;          
        }
    }
    
    public function update($table, $toUpdate, $where, $operator, $forClass){
        $keys=array_keys($toUpdate);
        $update='';
       
        for($i=0;$i<count($keys);$i++){
            $update.=$keys[$i]."=:".$keys[$i];
            if($i<count($keys)-1){
                $update.=",";
            }
        }

        $keys=array_keys($where);
        $whereClause="";

        for($i=0; $i<count($keys); $i++){
            $whereClause.=$keys[$i]."=:".$keys[$i];
            if($i<count($keys)-1){
                $whereClause.=" $operator ";
            }
        }

        $sql=sprintf("update %s set %s where %s",
            $table,
            $update, 
            $whereClause);
        
        try{
            $statement=$this->pdo->prepare($sql);
        
            foreach($toUpdate as $u => $val){
                $statement->bindValue(':'.$u , $val);
                
            }
            
            foreach($where as $w => $val){
                $statement->bindValue(':'.$w , $val);
            }
            
            $statement->execute();

            $affected_rows = $statement->rowCount();

            //$_SESSION['messages']=[["Sukses update data. Terdapat $affected_rows baris data terpengaruh", 1]]; 
            
            return true;
        
        }catch(PDOException $e){
            
            $_SESSION['messages']=[["terdapat kesalahan atau duplicate data.",0]]; 
            
            $this->cancel();

            return false;          
        
        }

    }

    public function delete($table, $where, $operator, $forClass){

        $w=array_keys($where);
        $whereClause='';
        for($i=0;$i<count($w);$i++){
            $whereClause.=$w[$i]."=:".$w[$i];
            if($i<count($w)-1){
                $whereClause.=" $operator ";
            }
        }

        $sql=sprintf('delete from %s where %s', $table, $whereClause);
        //dd($sql);
        try{  
            $statement=$this->pdo->prepare($sql);
            $statement->execute($where);
            return true;
        }
        catch(PDOException $e){
            $_SESSION['messages']=[["terdapat kesalahan atau duplicate data.",0]]; 
            $this->cancel();
            return false;   
        }

    }

    public function custom($sql, $forClass){
        
        try{
            $statement=$this->pdo->prepare($sql);
            
            $statement->execute();

            return $statement->fetchAll(PDO::FETCH_CLASS, $forClass);
        }catch(PDOException $e){
            $_SESSION['messages']=[["terdapat kesalahan atau duplicate data.",0]]; 

            $this->cancel();

            return false;          
        }
    }
    
}