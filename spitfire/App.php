<?php namespace spitfire;

use Controller;
use ReflectionClass;
use spitfire\core\app\ControllerLocator;
use spitfire\core\app\NamespaceMapping;
use spitfire\core\Environment;
use spitfire\core\Path;
use spitfire\core\router\Parameters;
use spitfire\core\router\reverser\ClosureReverser;
use spitfire\core\router\Router;
use spitfire\mvc\View;

/**
 * Spitfire Application Class. This class is the base of every other 'app', an 
 * app is a wrapper of controllers (this allows to plug them into other SF sites)
 * that defines a set of rules to avoid collisions with the rest of the apps.
 * 
 * Every app resides inside of a namespace, this externally defined variable
 * defines what calls Spitfire redirects to the app.
 * 
 * @author César de la Cal<cesar@magic3w.com>
 * @last-revision 2013-10-11
 */
abstract class App
{
	
	
	private $mapping;
	private $controllerLocator;
	
	/**
	 * Creates a new App. Receives the directory where this app resides in
	 * and the URI namespace it uses.
	 * 
	 * @param string $basedir The root directory of this app
	 * @param string $URISpace The URI namespace it 'owns'
	 * @param string $namespace The URI namespace it 'owns'
	 */
	public function __construct($basedir, $URISpace, $namespace = false) {
		$reflection = new ReflectionClass($this);
		$this->mapping = new NamespaceMapping($basedir, $URISpace, $namespace !== false? $namespace : $reflection->getNamespaceName());
		$this->controllerLocator = new ControllerLocator($this->mapping);
	}
	
	public function getMapping() {
		return $this->mapping;
	}
	
	/**
	 * 
	 * @return ControllerLocator
	 */
	public function getControllerLocator() {
		return $this->controllerLocator;
	}
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20190527
	 * @return string
	 */
	public function getBaseDir() {
		return $this->mapping->getBaseDir();
	}
	
	public function getView(Controller$controller) {
		
		$name = implode('\\', $this->controllerLocator->getControllerURI($controller));
		
		$c = $this->mapping->getNameSpace() . $name . 'View';
		if (!class_exists($c)) { $c = View::class; }
		
		return new $c($controller->context);
	}
	
	/**
	 * Creates the default routes for this application. Spitfire will assume that
	 * a /app/controller/action/object type of path is what you wish to use for
	 * your app. If you'd rather have custom rules - feel free to override these.
	 */
	public function createRoutes() {
		$us = $this->mapping->getURISpace();
		$ns = $us? '/' . $us : '';
		
		#The default route just returns a path based on app/controller/action/object
		#If your application does not wish this to happen, please override createRoutes
		#with your custome code.
		$default = Router::getInstance()->request($ns, function (Parameters$params, Parameters$server, $extension) use ($us) {
			$args = $params->getUnparsed();
			return new Path($us, array_shift($args), array_shift($args), $args, $extension);
		});
		
		#The reverser for the default route is rather simple again. 
		#It will concatenate app, controller and action
		$default->setReverser(new ClosureReverser(function (Path$path, $explicit = false) {
			$app        = $path->getApp();
			$controller = $path->getController();
			$action     = $path->getAction();
			$object     = $path->getObject();
			
			if ($action     ===        Environment::get('default_action')     && empty($object) && !$explicit)                   { $action     = ''; }
			if ($controller === (array)Environment::get('default_controller') && empty($object) && empty($action) && !$explicit) { $controller = Array(); }
			
			return '/' . trim(implode('/', array_filter(array_merge([$app], (array)$controller, [$action], $object))), '/');
		}));
	}
	
	abstract public function enable();
	abstract public function getAssetsDirectory();
	
	/**
	 * 
	 * @deprecated since version 0.1-dev 20190527
	 * @return string
	 */
	public function getNameSpace() {
		return $this->mapping->getNameSpace();
	}

	/**
	 * Returns the directory the templates are located in. This function should be 
	 * avoided in favor of the getDirectory function.
	 * 
	 * @deprecated since version 0.1-dev 20150423
	 */
	public function getTemplateDirectory() {
		return $this->mapping->getBaseDir() . 'templates/';
	}
	
}
