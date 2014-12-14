<?php

namespace Themety;

use ReflectionClass;
use Illuminate\Http\Request;
use Illuminate\Config\FileLoader;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Config\Repository as ConfigRepository;

class Themety extends Container
{
    /**
     * Themety Instance
     *
     * @var \Themety\Themety
     */
    public static $inst;


	/**
	 * Indicates if the application has "booted".
	 *
	 * @var bool
	 */
	protected $booted = false;

	/**
	 * All of the registered service providers.
	 *
	 * @var array
	 */
	protected $serviceProviders = array();


	/**
	 * The names of the loaded service providers.
	 *
	 * @var array
	 */
	protected $loadedProviders = array();


    /**
     * Constructor
     *
     * @param Request $request
	 * @return void
     */
    public function __construct(Request $request = null)
    {
        $this->registerBaseBindings($request);
        $this->registerEventProvider();
    }


	/**
	 * Register the basic bindings into the container.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return void
	 */
	protected function registerBaseBindings($request)
	{
        $request || ($request = Request::createFromGlobals());

		$this->instance('request', $request);

		$this->instance('Illuminate\Container\Container', $this);
	}



    /**
     * Init Themety module
     */
    static function init($settings = array())
    {

        $o = array_merge(array(
            'appPath' => ABSPATH . 'wp-app/',
            'templateUri' => get_bloginfo('stylesheet_directory'),
            'templatePath' => get_template_directory(),
            'env' => 'production',
        ), $settings);

        $themetyClass = get_called_class();
        $themety = new $themetyClass;

        Facade::clearResolvedInstances();
        Facade::setFacadeApplication($themety);

        $themety->loadConfigs($o['appPath'], $o['env']);
        foreach ($o as $key => $value) {
            $themety['config']->set($key, $value);
        }

        $themety->registerCoreContainerAliases();

        $themety->instance('app', $themety);

        $themety->registerProviders();

        return $themety;
    }


    public static function app()
    {
        if (!self::$inst) {
            self::$inst = new self;
        }
        return self::$inst;
    }


    /**
     * Load configs
     *
     * @return void
     */
    public function loadConfigs($appPath, $env = null)
    {
        $this->instance('config', new ConfigRepository(
            new FileLoader(new Filesystem, $appPath.'/config'), $env
        ));
    }


    /**
     * Register providers
     */
    public function registerProviders() {
        $provides = $this['config']->get('app.providers');
        foreach ($provides as $provider) {
            $this->register($provider);
        }
    }

    /**
	 * Register the core class aliases in the container.
	 *
	 * @return void
	 */
	public function registerCoreContainerAliases()
	{
		$aliases = array(
			'app'            => 'Themety\Themety',
            'config'         => 'Illuminate\Config\Repository',
		);

		foreach ($aliases as $key => $alias)
		{
			$this->alias($key, $alias);
		}
	}


	/**
	 * Register the event service provider.
	 *
	 * @return void
	 */
	protected function registerEventProvider()
	{
		$this->register(new EventServiceProvider($this));
	}


	/**
	 * Register a service provider with the application.
	 *
	 * @param  \Illuminate\Support\ServiceProvider|string  $provider
	 * @param  array  $options
	 * @param  bool   $force
	 * @return \Illuminate\Support\ServiceProvider
	 */
	public function register($provider, $options = array(), $force = false)
	{
		if ($registered = $this->getRegistered($provider) && ! $force)
                                     return $registered;

		// If the given "provider" is a string, we will resolve it, passing in the
		// application instance automatically for the developer. This is simply
		// a more convenient way of specifying your service provider classes.
		if (is_string($provider))
		{
			$provider = $this->resolveProviderClass($provider);
		}

		$provider->register();

		// Once we have registered the service we will iterate through the options
		// and set each of them on the application so they will be available on
		// the actual loading of the service objects and for developer usage.
		foreach ($options as $key => $value)
		{
			$this[$key] = $value;
		}

		$this->markAsRegistered($provider);

		// If the application has already booted, we will call this boot method on
		// the provider class so it has an opportunity to do its boot logic and
		// will be ready for any usage by the developer's application logics.
		if ($this->booted) $provider->boot();

		return $provider;
	}


	/**
	 * Get the registered service provider instance if it exists.
	 *
	 * @param  \Illuminate\Support\ServiceProvider|string  $provider
	 * @return \Illuminate\Support\ServiceProvider|null
	 */
	public function getRegistered($provider)
	{
		$name = is_string($provider) ? $provider : get_class($provider);

		if (array_key_exists($name, $this->loadedProviders))
		{
			return array_first($this->serviceProviders, function($key, $value) use ($name)
			{
				return get_class($value) == $name;
			});
		}
	}


	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param  string  $provider
	 * @return \Illuminate\Support\ServiceProvider
	 */
	public function resolveProviderClass($provider)
	{
		return new $provider($this);
	}

	/**
	 * Mark the given provider as registered.
	 *
	 * @param  \Illuminate\Support\ServiceProvider
	 * @return void
	 */
	protected function markAsRegistered($provider)
	{
		$this['events']->fire($class = get_class($provider), array($provider));

		$this->serviceProviders[] = $provider;

		$this->loadedProviders[$class] = true;
	}


    /**-----------------------------------------------------------------------------------------------------------------
     *                                                                                                  Utils
     -----------------------------------------------------------------------------------------------------------------*/
    /**
     * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
     * @param    string   $str                     String in underscore format
     * @param    bool     $capitalise_first_char   If true, capitalise the first char in $str
     * @return   string                              $str translated into camel caps
     */
    public static function toCamelCase($str, $capitalise_first_char = false) {
        if($capitalise_first_char) {
            $str[0] = strtoupper($str[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');
        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    /**
     * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
     * @param    string   $str    String in camel case format
     * @return    string            $str Translated into underscore format
     */
    public static function fromCamelCase($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    public function getAssetUri($file, $absolute = true)
    {
        $reflector = new ReflectionClass(get_called_class());
        $fn = pathinfo($reflector->getFileName(), PATHINFO_DIRNAME);
        $fn = preg_replace('/' . preg_quote(__NAMESPACE__) . '$/', '', $fn);
        $fn = realpath($fn . '..');
        $fn = preg_replace('/^' . preg_quote(ABSPATH, '/') . '/', '', $fn);
        $fn = "/$fn/assets/$file";
        $absolute && ($fn = get_site_url() . $fn);
        return $fn;
    }

}
