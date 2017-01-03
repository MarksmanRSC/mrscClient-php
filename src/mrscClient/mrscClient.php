<?php

namespace mrscClient;

/**
 * Base client library for interacting with the Marksman RSC API
 */
class mrscClient {
    public $endpoint = 'http://localhost:8081/api.php';
    public $mrscAccessCode = '99999';
    public $secretKey = 'f12202faa74a985ada0e7fe1a3376f10bbebe3c875261d5007a18c298dc5ae8b';
    
    public $uri = null;
    public $body = array();
    
    public function sign_request($url = null)
    {
        if ($url == null) { $url = $this->endpoint. $this->uri; }
    
        $url = explode("/api.php", $url, 2);
    
        $uri = '/api.php'. $url[1]. "&mrscAccessCode=". $this->mrscAccessCode;
        $url = $url[0];
 
        $sig = hash_hmac("sha256", $uri, $this->secretKey);    
        return $url . $uri. "&signature=$sig";    
    }
    
    public function send()
    {
        $url = $this->sign_request();
        
        // return file_get_contents($url);
        
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true            
        ));
        
        if (count($this->body) > 0) {
            
            $field_string = '';
            foreach ($this->body as $key=>$value) {
                $field_string .= $key . '=' . json_encode($value). '&';
            } rtrim($field_string, '&');
            
            curl_setopt($ch, CURLOPT_POST, count($this->body));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
        }
        
        $response = json_decode(curl_exec($ch));
        
        print_r($response);
    }

    /**
     * Loads a class that extends from inventoryMain
     * @param string $class_name
     */    
    static function autoload($class_name)
    {
        echo "Looking for $class_name\n";
        $parts = str_replace('\\', '/', $class_name);
        
        $dir = dirname(__FILE__). '/';
        
        echo "Looking for ". $dir . $parts . '.php'. "\n";
        
        if (is_file($dir . $parts . '.php')) {
            include_once $dir . $parts . '.php';
        }
    }
}

// spl_autoload_register("mrscClient::autoload");
