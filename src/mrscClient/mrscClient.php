<?php

namespace mrscClient;

/**
 * Base client library for interacting with the Marksman RSC API
 */
class mrscClient {
    /**
     * URL of the api endpoint you are using
     * Example: https://app.marksmanrsc.com/api.php
     */
    public $endpoint;
    
    /**
     * Set to true to enable test mode
     * @todo This is not yet implemented.
     * 
     * For testing purposes please use testing.marksmanrsc.com
     * It is a testing instance of our application
     *
     * 
     * @var bool
     */
    public $testMode = false;
    
    /**
     * Your mrscAccessCode, which tells us which account you are connecting as
     * Unless configured otherwise this is the same as your user account id
     * 
     * @var string
     */
    public $mrscAccessCode;
    
    /**
     * Your secret key used to sign your API requests
     * You can find this by visiting the API setup page on our website
     * 
     * DO NOT SHARE YOUR SECRET KEY WITH ANYONE ELSE
     * @var string
     */
    public $secretKey;
    
    /**
     * The full request uri
     * @var string
     */
    public $uri = null;
    
    public $debug = true;
    
    public function __construct()
    {
        if (is_file(MRSC_CLIENT_CONFIG)) {
            $config = parse_ini_file(MRSC_CLIENT_CONFIG);
            
            $requiredSettings = array(
                'endpoint',
                'mrscAccessCode',
                'secretKey'
            );
            
            foreach ($requiredSettings as $setting) {
                if (!isset($config[$setting])) {
                    throw new \Exception("Required setting '$setting' missing from CONFIG.ini");
                } else {
                    $this->$setting = $config[$setting];
                }
            }
            
            
        } else {
            throw new \Exception("Config file missing: ". MRSC_CLIENT_CONFIG);
        }
    }
    
    public function sign_request($url = null)
    {
        if ($url == null) { $url = $this->endpoint . $this->uri; }
    
        // echo "Presign: $url\n";
    
        $url = explode("/api.php", $url, 2);
        
        if (strpos($url[1], '?') !== 1) {
            $separator = '?';
        } else {
            $separator = '&';
        }
    
        $uri = '/api.php'. $url[1]. $separator . "mrscAccessCode=". $this->mrscAccessCode. '&timestamp=' . time() ;
        $url = $url[0];
 
        $sig = hash_hmac("sha256", $uri, $this->secretKey);    
        return $url . $uri. "&signature=$sig";
    }
    
    /**
     * Returns json object that would be sent for this request
     * This is meant for testing and debugging
     * @return json
     */
    public function mockSend()
    {        
        $url = $this->sign_request();
                   
        $body = $this;
        unset($body->secretKey, $body->endpoint);
        // print_r($body); exit;
        $body = json_encode($body);
        
        return $body;        
    }
    
    /**
     * Sends a prepared request to the API endpoint
     * @return object Decoded json response object
     */
    public function send($json_response = true)
    {
               
        
        $url = $this->sign_request();
        
        // echo "Url: $url\n";
        
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true            
        ));
                   
        $body = $this;
        unset($body->secretKey, $body->endpoint);
        // print_r($body); exit;

        $body = json_encode($body);

        if ($this->debug) {
            echo "\n\n$url\n\n". json_encode( json_decode($body), JSON_PRETTY_PRINT). "\n\n";
        }

        // echo $body; exit;

        /*
         * This is neccessary to prevent curl from making the content
         * type of the form www-urlencoded-form
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, 
            array(
                "Content-Type: application/json",
                "Content-Length: ". strlen($body)                
            )
            );
        
        $response = curl_exec($ch);
        
        if ($this->debug) {
            
            if ($json_response) {
                $debugDecode = json_decode($response);
                
                echo "\n\n". json_encode($debugDecode, JSON_PRETTY_PRINT). "\n\n";
                
            } else {
                echo "\n\n". $response. "\n\n";    
            } 
                
            
        }
        
        if ($json_response) {
            $response = json_decode($response);    
        }
        
        
        return $response;
    }

    /**
     * Loads a class that extends from inventoryMain
     * @param string $class_name
     */    
    static function autoload($class_name)
    {
        // echo "Looking for $class_name\n";
        $parts = str_replace('\\', '/', $class_name);
        
        $dir = dirname(__FILE__). '/';
        
        // echo "Looking for ". $dir . $parts . '.php'. "\n";
        
        if (is_file($dir . $parts . '.php')) {
            include_once $dir . $parts . '.php';
        }
    }
    
    public static function getFile($file_id)
    {
        $client = new self;
        $client->uri = "/?section=user&action=getFile&file_id=$file_id";
        $file = $client->send(false);
        
        return $file;
    }
}
