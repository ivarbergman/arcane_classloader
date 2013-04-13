<?php
namespace arcane\classloader;

class ClassLoader extends SplClassLoader
{

  private static $meta;

  private static $classes;
  private static $methods;
  private static $properties;
  private static $parameters;

  private $cacheFile = '/tmp/cacheFile.dat';


    /**
     * Creates a new <tt>SplClassLoader</tt> that loads classes of the
     * specified namespace.
     * 
     * @param string $ns The namespace to use.
     */
    public function __construct($ns = null, $includePath = null)
    {
      parent::__construct($ns, $includePath);
    }

    public static function meta($classname)
    {
      //echo "ClassLoader::meta($classname)".PHP_EOL;
      return isset(self::$meta[$classname]) ? self::$meta[$classname] : null;
    }

    public function addMetaClass($metaclass)
    {
      $n = $metaclass->getName();
      self::$classes[$n] = $metaclass;
    }

    public function addMetaMethod($classname, $metamethod)
    {
      $n = $metamethod->getName();
      self::$methods[$classname][$n] = $metamethod;
    }

    public function addMetaProperty($classname, $metaproperty)
    {
      $n = $metaproperty->getName();
      self::$properties[$classname][$n] = $metaproperty;
    }

    public function addMetaParameter($classname, $methodname, $metaproperty)
    {
      $n = $metaproperty->getName();
      self::$parameters[$classname][$methodname][$n] = $metaproperty;
    }

    public function getMetaClass($classname = null)
    {
      $c = & self::$classes;
      return $classname ? $c[$classname] : $c;
    }

    public function getMetaMethod($classname, $methodname = null)
    {
      $c = & self::$methods[$classname];
      return $methodname ? $c[$methodname] : $c;
    }

    public function getMetaProperty($classname, $propertyname = null)
    {
      $c = & self::$properties[$classname];
      return $propertyname ? $c[$propertyname] : $c;
    }

    public function getMetaParameter($classname, $methodname, $parametername = null)
    {
      $c = & self::$parameters[$classname][$methodname];
      return $parametername ? $c[$parametername] : $c;
    }

    public function register()
    {
      parent::register();
      return;
      self::$meta = []; 
      $it = $this->iterator();
      foreach ($it as $c)
	{
	  $this->addMetaClass($c);
	}
    }

    public function findInvalidCache()
    {
      $files = $this->findFiles();

      $cache = file_exists($this->cacheFile) ? unserialize(file_get_contents($this->cacheFile)) : []; 

      $diff = array_diff_assoc($files, $cache);
      return $diff;
    }

    public function cache()
    {
      $cache = $this->findFiles();
      file_put_contents($this->cacheFile, serialize($cache));
    }

    public function getImplementingClasses($interface)
    {
	    $it = $this->iterator();
	    $result = [];
	    foreach ($it as $c)
	    {
		    if ($c->hasInterface($interface))
		    {
			    $result[] = $c;
		    }
	    }
	    return $result;
    }

    public function iterator()
    {
      $it = new ClassIterator($this);
      return $it;
    }


}