<?php

namespace mrscClient\Shipping;

class Package
{
    /**
     * Length of package
     * @var float
     */
    public $length;
    
    /**
     * Height of package
     * @var float
     */
    public $height;
    
    /**
     * Width of package
     * @var float
     */
    public $width;
    
    /**
     * Weight of package
     * @var float
     */
    public $weight;
    
    /**
     * Unit of measurement for height, length, and width. Defaults to inches
     * 
     * Valid values:
     *  in - inches
     *  cm
     *  ft
     *  mm
     *  m
     *  yd
     * 
     * @var string
     */
    public $distance_unit = 'in';
    
    /**
     * Unit of measurement for weight. Defaults to lb
     * 
     * Valid values:
     *  lb
     *  g
     *  oz
     *  kg
     */
    public $mass_unit = 'lb';
    
    
    public function __construct(Array $package = null) {
        if ($package != null) {
            foreach ($package as $property => $value)
            {
                if (property_exists(get_class($this), $property)) {
     
                    if ($property == 'distance_unit') {
                        switch ($value) {
                            case 'in':
                            case 'cm':
                            case 'ft':
                            case 'mm':
                            case 'm':
                            case 'yd':
                                $this->$property = $value;
                                break;
                            default:
                                throw new Exception("Invalid distance_unit '$value'");
                        }
                    } else if ($property == 'weight_unit') {
                        switch ($value) {
                            case 'lb':
                            case 'g':
                            case 'oz':
                            case 'kg':
                                    $this->$property = $value;
                                break;
                            default:
                               throw new Exception("Invalid weight_unit '$value'");
                        }
                        
                    } else {
                        $this->$property = $value;
                    }               
                } else {
                    throw new Exception("Invalid property '$property'");
                }
            }
        }
    }
}
