<?php


class Db{
    /*
     * Rules: to work with this quicky ORM all tables must have a Primary key named "id", and a "created" and an "updated" datetime column
     * This allows for a simpler usage
     */

    private $host;
    private $database;
    private $username;
    private $password;
    private $connection;

    function __construct(){
        $this->host = DB_HOST;
        $this->database = DB_NAME;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
    }

    function connect(){
        try {
            $dsn = 'mysql:host='.$this->host.';dbname='.$this->database.';charset=utf8mb4';
            $options = array(
                //PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            $this->connection = new PDO( $dsn, $this->username, $this->password, $options );
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);

        } catch ( Exception $e ) {
            die($e->getMessage());
        }
    }

    function disconnect(){
        $this->connection=null;
    }

    /*
     * data array is like this: array('col1'=>'value1','col2'=>'value2')
     */
    function insert($table_name,$data_array){
        $columns=array();
        $values=array();
        $params=array();
        foreach($data_array as $key=>$value){
            $columns[]=$this->sanitize_column_name($key);
            $values[]='?';
            $params[]=$value;
        }
        $query = "INSERT INTO ".$this->sanitize_table_name($table_name)." (".implode(',',$columns).",created,updated) values (".implode(',',$values).",NOW(),NOW())";
        $result = $this->query($query,$params);

        if($result==1){
            //recuperer id de image
            $query = "SELECT LAST_INSERT_ID() AS id";
            $result = $this->query($query);
            foreach($result['request_result'] as $key=>$row)
                $id=$row['id'];
            $this->free_resources($result['stmt']);
            return  $id;
        }
        return false;
    }

    function update($table_name,$id,$data_array){
        return $this->update_by($table_name,'id',$id,$data_array);
    }
    function update_by($table_name,$column,$id,$data_array){
        $updates=array();
        $params=array();
        foreach($data_array as $key=>$value){
            $updates[]=$this->sanitize_column_name($key).'=?';
            $params[]=$value;
        }
        $params[]=$id;
        $updates_string='';
        if(count($updates)>0){
            $updates_string=implode(',',$updates).',';
        }
        $query="UPDATE ".$this->sanitize_table_name($table_name)." SET ".$updates_string."updated=NOW() WHERE ".$this->sanitize_column_name($column)."= ?";
        $result = $this->query($query,$params);
        return $result;
    }

    function delete($table_name,$id){
        return $this->delete_by($table_name,'id',$id);
    }
    function delete_by($table_name,$column,$id){
        $query = "DELETE FROM ".$this->sanitize_table_name($table_name)." WHERE ".$this->sanitize_column_name($column)."=?";
        $params=array($id);
        $result = $this->query($query,$params);
        return $result;
    }

    function load($table_name,$id){
        return $this->load_by($table_name,'id',$id);
    }
    function load_by($table_name,$column,$value){
        $query = "SELECT * FROM ".$this->sanitize_table_name($table_name)." WHERE ".$this->sanitize_column_name($column)."=?";
        $params=array($value);
        $result = $this->query($query,$params);
        $this->free_resources($result['stmt']);
        if(count($result['request_result'])){
            return $result['request_result'][0];
        }else{
            return false;
        }
    }

    function sanitize_table_name($table_name){
        return preg_replace('#[^a-zA-Z0-9_]#', '', $table_name);
    }

    function sanitize_column_name($column_name){
        return preg_replace('#[^a-zA-Z0-9_]#', '', $column_name);
    }

    function query($query,$params=array()){
        try {
            $stmt = $this->connection->prepare($query);

            $i = 1;
            foreach ($params as $key=>$p) {

                // default to string datatype
                $parameterType = PDO::PARAM_STR;
                // now let's see if there is something more appropriate
                if (is_bool($p)) {
                    $parameterType = PDO::PARAM_BOOL;
                } elseif (is_null($p)) {
                    $parameterType = PDO::PARAM_NULL;
                } elseif (is_int($p)) {
                    $parameterType = PDO::PARAM_INT;
                }
                //echo "param: ".$p." ".$parameterType."\n";
                // parameters passed to execute() are input-only parameters, so use
                // bindValue()

                if(substr($key,0,1)==':'){
                    $bindname=$key;
                }else{
                    $bindname=$i;
                }
                $stmt->bindValue($bindname, $p, $parameterType);
                $i++;
            }
            $stmt->execute($params);
            //$stmt->debugDumpParams();

            $colcount = $stmt->columnCount();
            if($colcount>0){
                $request_result=array();
                while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
                    $request_result[]=$row;
                }
                $result['stmt']=$stmt;
                $result['request_result']=$request_result;
                return $result;
            }else{
                return $stmt->rowCount();
            }
        } catch(Exception $e){
            echo 'Exception -> '; var_dump($e->getMessage());
            $stmt->debugDumpParams();
            echo 'Params: '; var_dump($params);
            $this->lastErrorMessage=$e->getMessage();
            return false;
        }
    }
    function free_resources($res){
        if($res){$res->closeCursor();}
    }

}