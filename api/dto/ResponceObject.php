<?php

class ResponseDto {

    const INVALID_USER = 'Wrong Username or Password';

    private $status;
    private $errorcode;
    private $errorMessage;
    private $text;
    private $target;
    private $firstName;
    private $lastName;
    private $role;
    private $gender;
    private $data;

    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    function getJsonData() {
        $var = get_object_vars($this);
        foreach ($var as &$value) {
            if (is_object($value) && method_exists($value, 'getJsonData')) {
                $value = $value->getJsonData();
            }
        }
        return $var;
    }

}

?>
