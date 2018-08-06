<?php

class DBSettings{

    protected const DB_ERROR_PARAM_MUST_BE_ARRAY        = "ERROR 001: Parameter must be an array";
    protected const DB_ERROR_TABLE_DOES_NOT_EXIST       = "ERROR 002: The table name is blank.";
    protected const DB_ERROR_INSERT_VALUES_NOT_ARRAY    = "ERROR 003: Insert Values need to be in the form of an array.";
    protected const DB_TABLE_ALREADY_EXISTS             = "ERROR 004: The table already exists.";
    

     # query type flag
    protected const SELECT = 0;
    protected const INSERT = 1;
    protected const UPDATE = 2;
    protected const CREATE = 3;


    private const settings = array
    (
        "host"=>"localhost", 
        "database"=>"test", 
        "username"=>"", 
        "password"=>""
    );


    public static function getSettings(){

        return self::settings;
    }

}

?>