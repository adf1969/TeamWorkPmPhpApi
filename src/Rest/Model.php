<?php namespace TeamWorkPm\Rest;

abstract class Model
{
    /**
     * Maneja las instancias de clases
     * creanda en el projecto
     * @var array
     */
    private static $instances = [];

    /**
     * Es una instancia a la clase que maneja
     * las conexiones del api con curl
     * @var \TeamWorkPm\Rest
     */
    protected $rest = null;
    /**
     * Es el elemento padre que contiene
     * los demas elementos xml o json de los paramentros
     * del put y del post
     * @var string
     */
    protected $parent = null;
    /**
     * Es el comnun recurso que se debe ejecutar
     * @var string
     */
    protected $action = null;
    /**
     * Almacena los campos del objeto
     * @var array
     */
    protected $fields = [];
    
    /**
     * Array for storing all fields. Used by __get() / __set()
     * 
     */
    protected $data = [];
    
    /**
     * Headers of the query that resulted in this data
     */
    protected $headers = [];
    
    /**
     *
     * @var string
     */
    private $hash = null;

    final private function  __construct($url, $key, $class, $hash, $data = [], $headers = [])
    {
        $this->rest   = new \TeamWorkPm\Rest($url, $key);
        $this->hash   = $hash;
        $this->parent = strtolower(str_replace(
          ['TeamWorkPm\\', '\\'],
          ['', '-'],
          $class
        ));
        if (method_exists($this, 'init')) {
            $this->init();
        }
        if (null === $this->action) {
            $this->action = str_replace('-', '_', $this->parent);
            // pluralize
            if (substr($this->action, -1) === 'y') {
                $this->action = substr($this->action, 0, -1) . 'ies';
            } else {
                $this->action .= 's';
            }
        }
        //configure request para put y post fields
        $this->rest->getRequest()
                    ->setParent($this->parent)
                    ->setFields($this->fields);

        // Set internal field/header values
        $this->data = $data;
        $this->headers = $headers;
    }

    /**
     * @codeCoverageIgnore
     */
    final public function  __destruct()
    {
        unset (self::$instances[$this->hash]);
    }

    /**
     * @codeCoverageIgnore
     */
    final protected function __clone ()
    {

    }

    /**
     *
     * @param string $company
     * @param string $key
     * @return TeamWorkPm\Model
     */
    final public static function getInstance($url, $key, $data = [], $headers = [])
    {
        $class = get_called_class();
        $hash = md5($class . '-' . $url . '-' . $key);
        if (!isset(self::$instances[$hash]) || $data) {  //TODO This is a kluge. Can I do something else than just check if $data exists? Perhaps add something from data to the $hash?
            self::$instances[$hash] = new $class($url, $key, $class, $hash, $data, $headers);
        }

        return self::$instances[$hash];
    }
    
    
// <editor-fold defaultstate="collapsed" desc="Property Methods"> ------------------\\

    public function toArray()
    {
      return $this->data;
    }

    public function getHeaders()
    {
      return $this->headers;
    }
    
    public function __get($name)
    {
      return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
      $this->data[$name] = $value;
    }

    public function __isset($name)
    {
      return isset($this->data[$name]);
    }

    public function __unset($name)
    {
      unset($this->data[$name]);
    }
    
// </editor-fold> Property Methods -------------------------------------------------\\

}