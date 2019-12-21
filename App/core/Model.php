<?php

namespace App\core;

use App\core\handler\Error;
use PDO;

abstract class Model
{
    private $connection;
    private $query;
    private $params;
    protected $table;
    protected $fields=[];
    protected $relations=[];
    protected $flag;
    public function __construct()
    {
        $this->connection = new PDO("mysql:host=localhost;dbname=" . DBNAME, DBUSER, DBPASS);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function __set($name, $value)
    {
        $this->{$name}=$value;
    }

    public function __get($name)
    {
        return $this->{$name};
    }
    protected function select($cols="*")
    {
        $this->flag=true;
        $this->query = "select $cols from $this->table (relation) ";
        return $this;
    }

    public function where($cond)
    {
        $cond=explode("=",$cond);
        $this->query .= " where $cond[0]=:cond";
        $this->params[":cond"] = $cond[1];
        return $this;
    }

    public function execute($relation=true)
    {
        $this->flag=$relation;
        $this->relate();
        $stmt = $this->connection->prepare($this->query);
        $result = $stmt->execute($this->params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        //TODO: fill fileds
    }

    public function exec($query=null){
        if($query!=null)
            $this->query=$query;
        $stmt = $this->connection->prepare($this->query);
        return $stmt->execute($this->params);
    }

    private function relate(){
        if(!empty($this->relations)&&$this->flag){
            $rq="";
            foreach($this->relations as $key=>$relation){
                $rq.="$key on $relation ";
            }
            $this->query=str_replace("(relation)",$rq,$this->query);
        }else
        $this->query=str_replace("(relation)","",$this->query);
    }

    protected function insert(){
        $this->query="insert into $this->table(:C) values(:V);";
        $cols=implode(",",$this->fields);
        foreach($this->fields as $f){
            $p= ":" . $f;
            $this->params[$p]=$this->{$f};
        }
        $values=implode(",",array_keys($this->params));
        $this->query=str_replace(":C",$cols, $this->query);
        $this->query=str_replace(":V",$values, $this->query);

        $stmt=$this->connection->prepare($this->query);
        $stmt->execute($this->params);
        return $this->connection->lastInsertId();
    }
    //TODO
    public function upsert(){
        $this->query = "insert into $this->table(:C) values(:V) on duplicate key 
        update ";
        
        $this->query.=implode(",",array_map(function($entry){
            return "$entry='{$this->$entry}'";
        },$this->fields));

        $cols = implode(",", $this->fields);
        foreach ($this->fields as $f) {
                $p = ":" . $f;
                $this->params[$p] = $this->{$f};
        }
        $values = implode(",", array_keys($this->params));
        $this->query = str_replace(":C", $cols, $this->query);
        $this->query = str_replace(":V", $values, $this->query);

        $stmt = $this->connection->prepare($this->query);
        var_dump($this->params);
        $stmt->execute($this->params);
        return $this->connection->lastInsertId();
    }

    public function update(){
        $this->query="update $this->table set ";
        $keys=[];
        foreach($this->fields as $field){
            if(isset($this->$field)){
                $p=":".$field;
                array_push($keys,$field."=:$field");
                $this->params[$p]=$this->$field;
            }
        }
        $this->query.=implode(",",$keys);
        return $this;
    }

    public function delete(){
        $this->query="delete from $this->table where id=$this->id";
        return $this->connection->exec($this->query);
    }

    public function count()
    {
        return $this->select("count(id) as count")->execute(false)[0]['count'];
    }

    public function limit(int $limit,int $offset){
        $this->query.=" limit $limit,$offset";
        return $this;
    }
}
