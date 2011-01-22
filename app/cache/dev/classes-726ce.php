<?php
namespace Symfony\Component\Routing
{
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
interface RouterInterface extends UrlMatcherInterface, UrlGeneratorInterface
{
}
}
namespace Symfony\Component\Routing
{
use Symfony\Component\Routing\Loader\LoaderInterface;
class Router implements RouterInterface
{
    protected $matcher;
    protected $generator;
    protected $options;
    protected $defaults;
    protected $context;
    protected $loader;
    protected $collection;
    protected $resource;
    public function __construct(LoaderInterface $loader, $resource, array $options = array(), array $context = array(), array $defaults = array())
    {
        $this->loader = $loader;
        $this->resource = $resource;
        $this->context = $context;
        $this->defaults = $defaults;
        $this->options = array(
            'cache_dir'              => null,
            'debug'                  => false,
            'generator_class'        => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_base_class'   => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'generator_cache_class'  => 'ProjectUrlGenerator',
            'matcher_class'          => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_base_class'     => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'matcher_dumper_class'   => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class'    => 'ProjectUrlMatcher',
        );
                if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
            throw new \InvalidArgumentException(sprintf('The Router does not support the following options: \'%s\'.', implode('\', \'', $diff)));
        }
        $this->options = array_merge($this->options, $options);
    }
    public function getRouteCollection()
    {
        if (null === $this->collection) {
            $this->collection = $this->loader->load($this->resource);
        }
        return $this->collection;
    }
    public function setContext(array $context = array())
    {
        $this->context = $context;
    }
    public function setDefaults(array $defaults = array())
    {
        $this->defaults = $defaults;
    }
    public function generate($name, array $parameters = array(), $absolute = false)
    {
        return $this->getGenerator()->generate($name, $parameters, $absolute);
    }
    public function match($url)
    {
        return $this->getMatcher()->match($url);
    }
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }
        if (null === $this->options['cache_dir'] || null === $this->options['matcher_cache_class']) {
            return $this->matcher = new $this->options['matcher_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }
        $class = $this->options['matcher_cache_class'];
        if ($this->needsReload($class)) {
            $dumper = new $this->options['matcher_dumper_class']($this->getRouteCollection());
            $options = array(
                'class'      => $class,
                'base_class' => $this->options['matcher_base_class'],
            );
            $this->updateCache($class, $dumper->dump($options));
        }
        require_once $this->getCacheFile($class);
        return $this->matcher = new $class($this->context, $this->defaults);
    }
    public function getGenerator()
    {
        if (null !== $this->generator) {
            return $this->generator;
        }
        if (null === $this->options['cache_dir'] || null === $this->options['generator_cache_class']) {
            return $this->generator = new $this->options['generator_class']($this->getRouteCollection(), $this->context, $this->defaults);
        }
        $class = $this->options['generator_cache_class'];
        if ($this->needsReload($class)) {
            $dumper = new $this->options['generator_dumper_class']($this->getRouteCollection());
            $options = array(
                'class'      => $class,
                'base_class' => $this->options['generator_base_class'],
            );
            $this->updateCache($class, $dumper->dump($options));
        }
        require_once $this->getCacheFile($class);
        return $this->generator = new $class($this->context, $this->defaults);
    }
    protected function updateCache($class, $dump)
    {
        $this->writeCacheFile($this->getCacheFile($class), $dump);
        if ($this->options['debug']) {
            $this->writeCacheFile($this->getCacheFile($class, 'meta'), serialize($this->getRouteCollection()->getResources()));
        }
    }
    protected function needsReload($class)
    {
        $file = $this->getCacheFile($class);
        if (!file_exists($file)) {
            return true;
        }
        if (!$this->options['debug']) {
            return false;
        }
        $metadata = $this->getCacheFile($class, 'meta');
        if (!file_exists($metadata)) {
            return true;
        }
        $time = filemtime($file);
        $meta = unserialize(file_get_contents($metadata));
        foreach ($meta as $resource) {
            if (!$resource->isUptodate($time)) {
                return true;
            }
        }
        return false;
    }
    protected function getCacheFile($class, $extension = 'php')
    {
        return $this->options['cache_dir'].'/'.$class.'.'.$extension;
    }
    protected function writeCacheFile($file, $content)
    {
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content) && @rename($tmpFile, $file)) {
            chmod($file, 0644);
            return;
        }
        throw new \RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }
}
}
namespace Symfony\Component\Routing\Matcher
{
interface UrlMatcherInterface
{
    function match($url);
}
}
namespace Symfony\Component\Routing\Matcher
{
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
class UrlMatcher implements UrlMatcherInterface
{
    protected $routes;
    protected $defaults;
    protected $context;
    public function __construct(RouteCollection $routes, array $context = array(), array $defaults = array())
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->defaults = $defaults;
    }
    public function match($url)
    {
        $url = $this->normalizeUrl($url);
        foreach ($this->routes->all() as $name => $route) {
            $compiledRoute = $route->compile();
            if (isset($this->context['method']) && (($req = $route->getRequirement('_method')) && !preg_match(sprintf('#^(%s)$#xi', $req), $this->context['method']))) {
                continue;
            }
                        if ('' !== $compiledRoute->getStaticPrefix() && 0 !== strpos($url, $compiledRoute->getStaticPrefix())) {
                continue;
            }
            if (!preg_match($compiledRoute->getRegex(), $url, $matches)) {
                continue;
            }
            return array_merge($this->mergeDefaults($matches, $route->getDefaults()), array('_route' => $name));
        }
        return false;
    }
    protected function mergeDefaults($params, $defaults)
    {
        $parameters = array_merge($this->defaults, $defaults);
        foreach ($params as $key => $value) {
            if (!is_int($key)) {
                $parameters[$key] = urldecode($value);
            }
        }
        return $parameters;
    }
    protected function normalizeUrl($url)
    {
                if ('/' !== substr($url, 0, 1)) {
            $url = '/'.$url;
        }
                if (false !== $pos = strpos($url, '?')) {
            $url = substr($url, 0, $pos);
        }
                return preg_replace('#/+#', '/', $url);
    }
}
}
namespace Symfony\Component\Routing\Generator
{
interface UrlGeneratorInterface
{
    function generate($name, array $parameters, $absolute = false);
}
}
namespace Symfony\Component\Routing\Generator
{
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
class UrlGenerator implements UrlGeneratorInterface
{
    protected $routes;
    protected $defaults;
    protected $context;
    protected $cache;
    public function __construct(RouteCollection $routes, array $context = array(), array $defaults = array())
    {
        $this->routes = $routes;
        $this->context = $context;
        $this->defaults = $defaults;
        $this->cache = array();
    }
    public function generate($name, array $parameters, $absolute = false)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new \InvalidArgumentException(sprintf('Route "%s" does not exist.', $name));
        }
        if (!isset($this->cache[$name])) {
            $this->cache[$name] = $route->compile();
        }
        return $this->doGenerate($this->cache[$name]->getVariables(), $route->getDefaults(), $route->getRequirements(), $this->cache[$name]->getTokens(), $parameters, $name, $absolute);
    }
    protected function doGenerate($variables, $defaults, $requirements, $tokens, $parameters, $name, $absolute)
    {
        $defaults = array_merge($this->defaults, $defaults);
        $tparams = array_merge($defaults, $parameters);
                if ($diff = array_diff_key($variables, $tparams)) {
            throw new \InvalidArgumentException(sprintf('The "%s" route has some missing mandatory parameters (%s).', $name, implode(', ', $diff)));
        }
        $url = '';
        $optional = true;
        foreach ($tokens as $token) {
            if ('variable' === $token[0]) {
                if (false === $optional || !isset($defaults[$token[3]]) || (isset($parameters[$token[3]]) && $parameters[$token[3]] != $defaults[$token[3]])) {
                                        if (isset($requirements[$token[3]]) && !preg_match('#^'.$requirements[$token[3]].'$#', $tparams[$token[3]])) {
                        throw new \InvalidArgumentException(sprintf('Parameter "%s" for route "%s" must match "%s" ("%s" given).', $token[3], $name, $requirements[$token[3]], $tparams[$token[3]]));
                    }
                    $url = $token[1].urlencode($tparams[$token[3]]).$url;
                    $optional = false;
                }
            } elseif ('text' === $token[0]) {
                $url = $token[1].$token[2].$url;
                $optional = false;
            } else {
                                if ($segment = call_user_func_array(array($this, 'generateFor'.ucfirst(array_shift($token))), array_merge(array($optional, $tparams), $token))) {
                    $url = $segment.$url;
                    $optional = false;
                }
            }
        }
        if (!$url) {
            $url = '/';
        }
                if ($extra = array_diff_key($parameters, $variables, $defaults)) {
            $url .= '?'.http_build_query($extra);
        }
        $url = (isset($this->context['base_url']) ? $this->context['base_url'] : '').$url;
        if ($absolute && isset($this->context['host'])) {
            $url = 'http'.(isset($this->context['is_secure']) && $this->context['is_secure'] ? 's' : '').'://'.$this->context['host'].$url;
        }
        return $url;
    }
}
}
namespace Symfony\Component\Routing\Loader
{
use Symfony\Component\Routing\Loader\LoaderResolver;
interface LoaderInterface
{
    function load($resource, $type = null);
    function supports($resource, $type = null);
    function getResolver();
    function setResolver(LoaderResolver $resolver);
}
}
namespace Symfony\Bundle\FrameworkBundle\Routing
{
use Symfony\Component\Routing\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Loader\LoaderResolver as BaseLoaderResolver;
class LazyLoader implements LoaderInterface
{
    protected $container;
    protected $service;
    public function __construct(ContainerInterface $container, $service)
    {
        $this->container = $container;
        $this->service = $service;
    }
    public function load($resource, $type = null)
    {
        return $this->container->get($this->service)->load($resource, $type);
    }
    public function supports($resource, $type = null)
    {
        return $this->container->get($this->service)->supports($resource, $type);
    }
    public function getResolver()
    {
        return $this->container->get($this->service)->getResolver();
    }
    public function setResolver(BaseLoaderResolver $resolver)
    {
        $this->container->get($this->service)->setResolver($resolver);
    }
}
}
namespace Symfony\Component\Templating
{
class DelegatingEngine implements EngineInterface
{
    protected $engines;
    public function __construct(array $engines = array())
    {
        $this->engines = array();
        foreach ($engines as $engine) {
            $this->addEngine($engine);
        }
    }
    public function render($name, array $parameters = array())
    {
        return $this->getEngine($name)->render($name, $parameters);
    }
    public function exists($name)
    {
        return $this->getEngine($name)->exists($name);
    }
    public function load($name)
    {
        return $this->getEngine($name)->load($name);
    }
    public function addEngine(EngineInterface $engine)
    {
        $this->engines[] = $engine;
    }
    public function supports($name)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($name)) {
                return true;
            }
        }
        return false;
    }
    protected function getEngine($name)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($name)) {
                return $engine;
            }
        }
        throw new \RuntimeException(sprintf('No engine is able to work with the "%s" template.', $name));
    }
}
}
namespace Symfony\Bundle\FrameworkBundle\Templating
{
use Symfony\Component\Templating\EngineInterface as BaseEngineInterface;
use Symfony\Component\HttpFoundation\Response;
interface EngineInterface extends BaseEngineInterface
{
    function renderResponse($view, array $parameters = array(), Response $response = null);
}
}
namespace Symfony\Component\HttpFoundation
{
use Symfony\Component\HttpFoundation\SessionStorage\SessionStorageInterface;
class Session implements \Serializable
{
    protected $storage;
    protected $attributes;
    protected $oldFlashes;
    protected $started;
    protected $options;
    public function __construct(SessionStorageInterface $storage, array $options = array())
    {
        $this->storage = $storage;
        $this->options = $options;
        $this->attributes = array('_flash' => array(), '_locale' => $this->getDefaultLocale());
        $this->started = false;
    }
    public function start()
    {
        if (true === $this->started) {
            return;
        }
        $this->storage->start();
        $this->attributes = $this->storage->read('_symfony2');
        if (!isset($this->attributes['_flash'])) {
            $this->attributes['_flash'] = array();
        }
        if (!isset($this->attributes['_locale'])) {
            $this->attributes['_locale'] = $this->getDefaultLocale();
        }
                $this->oldFlashes = array_flip(array_keys($this->attributes['_flash']));
        $this->started = true;
    }
    public function has($name)
    {
        return array_key_exists($name, $this->attributes);
    }
    public function get($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }
    public function set($name, $value)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes[$name] = $value;
    }
    public function getAttributes()
    {
        return $this->attributes;
    }
    public function setAttributes($attributes)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes = $attributes;
    }
    public function remove($name)
    {
        if (false === $this->started) {
            $this->start();
        }
        if (array_key_exists($name, $this->attributes)) {
            unset($this->attributes[$name]);
        }
    }
    public function clear()
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes = array();
    }
    public function invalidate()
    {
        $this->clear();
        $this->storage->regenerate();
    }
    public function getId()
    {
        return $this->storage->getId();
    }
    public function getLocale()
    {
        return $this->attributes['_locale'];
    }
    public function setLocale($locale)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes['_locale'] = $locale;
    }
    public function getFlashes()
    {
        return $this->attributes['_flash'];
    }
    public function setFlashes($values)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes['_flash'] = $values;
    }
    public function getFlash($name, $default = null)
    {
        return array_key_exists($name, $this->attributes['_flash']) ? $this->attributes['_flash'][$name] : $default;
    }
    public function setFlash($name, $value)
    {
        if (false === $this->started) {
            $this->start();
        }
        $this->attributes['_flash'][$name] = $value;
        unset($this->oldFlashes[$name]);
    }
    public function hasFlash($name)
    {
        return array_key_exists($name, $this->attributes['_flash']);
    }
    public function removeFlash($name)
    {
        unset($this->attributes['_flash'][$name]);
    }
    public function clearFlashes()
    {
        $this->attributes['_flash'] = array();
    }
    public function save()
    {
        if (true === $this->started) {
            if (isset($this->attributes['_flash'])) {
                $this->attributes['_flash'] = array_diff_key($this->attributes['_flash'], $this->oldFlashes);
            }
            $this->storage->write('_symfony2', $this->attributes);
        }
    }
    public function __destruct()
    {
        $this->save();
    }
    public function serialize()
    {
        return serialize(array($this->storage, $this->options));
    }
    public function unserialize($serialized)
    {
        list($this->storage, $this->options) = unserialize($serialized);
        $this->attributes = array();
        $this->started = false;
    }
    protected function getDefaultLocale()
    {
        return isset($this->options['default_locale']) ? $this->options['default_locale'] : 'en';
    }
}
}
namespace Symfony\Component\HttpFoundation\SessionStorage
{
interface SessionStorageInterface
{
    function start();
    function getId();
    function read($key);
    function remove($key);
    function write($key, $data);
    function regenerate($destroy = false);
}
}
namespace Symfony\Component\HttpFoundation
{
class Response
{
    public $headers;
    protected $content;
    protected $version;
    protected $statusCode;
    protected $statusText;
    protected $charset = 'UTF-8';
    static public $statusTexts = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
        $this->headers = new ResponseHeaderBag($headers);
    }
    public function __toString()
    {
        $content = '';
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/html; charset='.$this->charset);
        }
                $content .= sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText)."\n";
                foreach ($this->headers->all() as $name => $values) {
            foreach ($values as $value) {
                $content .= "$name: $value\n";
            }
        }
        $content .= "\n".$this->getContent();
        return $content;
    }
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }
    public function sendHeaders()
    {
        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'text/html; charset='.$this->charset);
        }
                header(sprintf('HTTP/%s %s %s', $this->version, $this->statusCode, $this->statusText));
                foreach ($this->headers->all() as $name => $values) {
            foreach ($values as $value) {
                header($name.': '.$value);
            }
        }
                foreach ($this->headers->getCookies() as $cookie) {
            setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
        }
    }
    public function sendContent()
    {
        echo $this->content;
    }
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }
    public function setContent($content)
    {
        $this->content = $content;
    }
    public function getContent()
    {
        return $this->content;
    }
    public function setProtocolVersion($version)
    {
        $this->version = $version;
    }
    public function getProtocolVersion()
    {
        return $this->version;
    }
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = (int) $code;
        if ($this->statusCode < 100 || $this->statusCode > 599) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }
        $this->statusText = false === $text ? '' : (null === $text ? self::$statusTexts[$this->statusCode] : $text);
    }
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    public function getCharset()
    {
        return $this->charset;
    }
    public function isCacheable()
    {
        if (!in_array($this->statusCode, array(200, 203, 300, 301, 302, 404, 410))) {
            return false;
        }
        if ($this->headers->hasCacheControlDirective('no-store') || $this->headers->getCacheControlDirective('private')) {
            return false;
        }
        return $this->isValidateable() || $this->isFresh();
    }
    public function isFresh()
    {
        return $this->getTtl() > 0;
    }
    public function isValidateable()
    {
        return $this->headers->has('Last-Modified') || $this->headers->has('ETag');
    }
    public function setPrivate()
    {
        $this->headers->removeCacheControlDirective('public');
        $this->headers->addCacheControlDirective('private');
    }
    public function setPublic()
    {
        $this->headers->addCacheControlDirective('public');
        $this->headers->removeCacheControlDirective('private');
    }
    public function mustRevalidate()
    {
        return $this->headers->hasCacheControlDirective('must-revalidate') || $this->headers->has('must-proxy-revalidate');
    }
    public function getDate()
    {
        if (null === $date = $this->headers->getDate('Date')) {
            $date = new \DateTime(null, new \DateTimeZone('UTC'));
            $this->headers->set('Date', $date->format('D, d M Y H:i:s').' GMT');
        }
        return $date;
    }
    public function getAge()
    {
        if ($age = $this->headers->get('Age')) {
            return $age;
        }
        return max(time() - $this->getDate()->format('U'), 0);
    }
    public function expire()
    {
        if ($this->isFresh()) {
            $this->headers->set('Age', $this->getMaxAge());
        }
    }
    public function getExpires()
    {
        return $this->headers->getDate('Expires');
    }
    public function setExpires(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Expires');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Expires', $date->format('D, d M Y H:i:s').' GMT');
        }
    }
    public function getMaxAge()
    {
        if ($age = $this->headers->getCacheControlDirective('s-maxage')) {
            return $age;
        }
        if ($age = $this->headers->getCacheControlDirective('max-age')) {
            return $age;
        }
        if (null !== $this->getExpires()) {
            return $this->getExpires()->format('U') - $this->getDate()->format('U');
        }
        return null;
    }
    public function setMaxAge($value)
    {
        $this->headers->addCacheControlDirective('max-age', $value);
    }
    public function setSharedMaxAge($value)
    {
        $this->headers->addCacheControlDirective('s-maxage', $value);
    }
    public function getTtl()
    {
        if ($maxAge = $this->getMaxAge()) {
            return $maxAge - $this->getAge();
        }
        return null;
    }
    public function setTtl($seconds)
    {
        $this->setSharedMaxAge($this->getAge() + $seconds);
    }
    public function setClientTtl($seconds)
    {
        $this->setMaxAge($this->getAge() + $seconds);
    }
    public function getLastModified()
    {
        return $this->headers->getDate('LastModified');
    }
    public function setLastModified(\DateTime $date = null)
    {
        if (null === $date) {
            $this->headers->remove('Last-Modified');
        } else {
            $date = clone $date;
            $date->setTimezone(new \DateTimeZone('UTC'));
            $this->headers->set('Last-Modified', $date->format('D, d M Y H:i:s').' GMT');
        }
    }
    public function getEtag()
    {
        return $this->headers->get('ETag');
    }
    public function setEtag($etag = null, $weak = false)
    {
        if (null === $etag) {
            $this->headers->remove('Etag');
        } else {
            if (0 !== strpos($etag, '"')) {
                $etag = '"'.$etag.'"';
            }
            $this->headers->set('ETag', (true === $weak ? 'W/' : '').$etag);
        }
    }
    public function setCache(array $options)
    {
        if ($diff = array_diff_key($options, array('etag', 'last_modified', 'max_age', 's_maxage', 'private', 'public'))) {
            throw new \InvalidArgumentException(sprintf('Response does not support the following options: "%s".', implode('", "', array_keys($diff))));
        }
        if (isset($options['etag'])) {
            $this->setEtag($options['etag']);
        }
        if (isset($options['last_modified'])) {
            $this->setLastModified($options['last_modified']);
        }
        if (isset($options['max_age'])) {
            $this->setMaxAge($options['max_age']);
        }
        if (isset($options['s_maxage'])) {
            $this->setSharedMaxAge($options['s_maxage']);
        }
        if (isset($options['public'])) {
            if ($options['public']) {
                $this->setPublic();
            } else {
                $this->setPrivate();
            }
        }
        if (isset($options['private'])) {
            if ($options['private']) {
                $this->setPrivate();
            } else {
                $this->setPublic();
            }
        }
    }
    public function setNotModified()
    {
        $this->setStatusCode(304);
        $this->setContent(null);
                foreach (array('Allow', 'Content-Encoding', 'Content-Language', 'Content-Length', 'Content-MD5', 'Content-Type', 'Last-Modified') as $header) {
            $this->headers->remove($header);
        }
    }
    public function setRedirect($url, $status = 302)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        $this->setStatusCode($status);
        if (!$this->isRedirect()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }
        $this->headers->set('Location', $url);
        $this->setContent(sprintf('<html><head><meta http-equiv="refresh" content="1;url=%s"/></head></html>', htmlspecialchars($url, ENT_QUOTES)));
    }
    public function hasVary()
    {
        return (Boolean) $this->headers->get('Vary');
    }
    public function getVary()
    {
        if (!$vary = $this->headers->get('Vary')) {
            return array();
        }
        return is_array($vary) ? $vary : preg_split('/[\s,]+/', $vary);
    }
    public function setVary($headers, $replace = true)
    {
        $this->headers->set('Vary', $headers, $replace);
    }
    public function isNotModified(Request $request)
    {
        $lastModified = $request->headers->get('If-Modified-Since');
        $notModified = false;
        if ($etags = $request->getEtags()) {
            $notModified = (in_array($this->getEtag(), $etags) || in_array('*', $etags)) && (!$lastModified || $this->headers->get('Last-Modified') == $lastModified);
        } elseif ($lastModified) {
            $notModified = $lastModified == $this->headers->get('Last-Modified');
        }
        if ($notModified) {
            $this->setNotModified();
        }
        return $notModified;
    }
        public function isInvalid()
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }
    public function isInformational()
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }
    public function isSuccessful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
    public function isRedirection()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }
    public function isClientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }
    public function isServerError()
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }
    public function isOk()
    {
        return 200 === $this->statusCode;
    }
    public function isForbidden()
    {
        return 403 === $this->statusCode;
    }
    public function isNotFound()
    {
        return 404 === $this->statusCode;
    }
    public function isRedirect()
    {
        return in_array($this->statusCode, array(301, 302, 303, 307));
    }
    public function isEmpty()
    {
        return in_array($this->statusCode, array(201, 204, 304));
    }
    public function isRedirected($location)
    {
        return $this->isRedirect() && $location == $this->headers->get('Location');
    }
}
}
namespace Symfony\Component\HttpFoundation
{
class ResponseHeaderBag extends HeaderBag
{
    protected $computedCacheControl = array();
    public function replace(array $headers = array())
    {
        parent::replace($headers);
        if (!isset($this->headers['cache-control'])) {
            $this->set('cache-control', '');
        }
    }
    public function set($key, $values, $replace = true)
    {
        parent::set($key, $values, $replace);
                if ('cache-control' === strtr(strtolower($key), '_', '-')) {
            $computed = $this->computeCacheControlValue();
            $this->headers['cache-control'] = array($computed);
            $this->computedCacheControl = $this->parseCacheControl($computed);
        }
    }
    public function remove($key)
    {
        parent::remove($key);
        if ('cache-control' === strtr(strtolower($key), '_', '-')) {
            $this->computedCacheControl = array();
        }
    }
    public function hasCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl);
    }
    public function getCacheControlDirective($key)
    {
        return array_key_exists($key, $this->computedCacheControl) ? $this->computedCacheControl[$key] : null;
    }
    public function clearCookie($name)
    {
        $this->setCookie(new Cookie($name, null, time() - 86400));
    }
    protected function computeCacheControlValue()
    {
        if (!$this->cacheControl && !$this->has('ETag') && !$this->has('Last-Modified') && !$this->has('Expires')) {
            return 'no-cache';
        }
        if (!$this->cacheControl) {
                        return 'private, max-age=0, must-revalidate';
        }
        $header = $this->getCacheControlHeader();
        if (isset($this->cacheControl['public']) || isset($this->cacheControl['private'])) {
            return $header;
        }
                if (!isset($this->cacheControl['s-maxage'])) {
            return $header.', private';
        }
        return $header;
    }
}
}
namespace Symfony\Component\HttpKernel
{
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class HttpKernel implements HttpKernelInterface
{
    protected $dispatcher;
    protected $resolver;
    protected $request;
    public function __construct(EventDispatcher $dispatcher, ControllerResolverInterface $resolver)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
    }
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
                $previousRequest = $this->request;
        $this->request = $request;
        try {
            $response = $this->handleRaw($request, $type);
        } catch (\Exception $e) {
            if (false === $catch) {
                $this->request = $previousRequest;
                throw $e;
            }
                        $event = new Event($this, 'core.exception', array('request_type' => $type, 'request' => $request, 'exception' => $e));
            $this->dispatcher->notifyUntil($event);
            if (!$event->isProcessed()) {
                $this->request = $previousRequest;
                throw $e;
            }
            $response = $this->filterResponse($event->getReturnValue(), $request, 'A "core.exception" listener returned a non response object.', $type);
        }
                $this->request = $previousRequest;
        return $response;
    }
    public function getRequest()
    {
        return $this->request;
    }
    protected function handleRaw(Request $request, $type = self::MASTER_REQUEST)
    {
                $event = new Event($this, 'core.request', array('request_type' => $type, 'request' => $request));
        $this->dispatcher->notifyUntil($event);
        if ($event->isProcessed()) {
            return $this->filterResponse($event->getReturnValue(), $request, 'A "core.request" listener returned a non response object.', $type);
        }
                if (false === $controller = $this->resolver->getController($request)) {
            throw new NotFoundHttpException('Unable to find the controller.');
        }
        $event = new Event($this, 'core.controller', array('request_type' => $type, 'request' => $request));
        $this->dispatcher->filter($event, $controller);
        $controller = $event->getReturnValue();
                if (!is_callable($controller)) {
            throw new \LogicException(sprintf('The controller must be a callable (%s).', var_export($controller, true)));
        }
                $arguments = $this->resolver->getArguments($request, $controller);
                $retval = call_user_func_array($controller, $arguments);
                $event = new Event($this, 'core.view', array('request_type' => $type, 'request' => $request));
        $this->dispatcher->filter($event, $retval);
        return $this->filterResponse($event->getReturnValue(), $request, sprintf('The controller must return a response (instead of %s).', is_object($event->getReturnValue()) ? 'an object of class '.get_class($event->getReturnValue()) : is_array($event->getReturnValue()) ? 'an array' : str_replace("\n", '', var_export($event->getReturnValue(), true))), $type);
    }
    protected function filterResponse($response, $request, $message, $type)
    {
        if (!$response instanceof Response) {
            throw new \RuntimeException($message);
        }
        $event = $this->dispatcher->filter(new Event($this, 'core.response', array('request_type' => $type, 'request' => $request)), $response);
        $response = $event->getReturnValue();
        if (!$response instanceof Response) {
            throw new \RuntimeException('A "core.response" listener returned a non response object.');
        }
        return $response;
    }
}
}
namespace Symfony\Component\HttpKernel
{
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
class ResponseListener
{
    public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('core.response', array($this, 'filter'), $priority);
    }
    public function filter(Event $event, Response $response)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->get('request_type') || $response->headers->has('Content-Type')) {
            return $response;
        }
        $request = $event->get('request');
        $format = $request->getRequestFormat();
        if ((null !== $format) && $mimeType = $request->getMimeType($format)) {
            $response->headers->set('Content-Type', $mimeType);
        }
        return $response;
    }
}
}
namespace Symfony\Component\HttpKernel\Controller
{
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
class ControllerResolver implements ControllerResolverInterface
{
    protected $logger;
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }
    public function getController(Request $request)
    {
        if (!$controller = $request->attributes->get('_controller')) {
            if (null !== $this->logger) {
                $this->logger->err('Unable to look for the controller as the "_controller" parameter is missing');
            }
            return false;
        }
        if ($controller instanceof \Closure) {
            return $controller;
        }
        list($controller, $method) = $this->createController($controller);
        if (!method_exists($controller, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s::%s" does not exist.', get_class($controller), $method));
        }
        if (null !== $this->logger) {
            $this->logger->info(sprintf('Using controller "%s::%s"', get_class($controller), $method));
        }
        return array($controller, $method);
    }
    public function getArguments(Request $request, $controller)
    {
        $attributes = $request->attributes->all();
        if (is_array($controller)) {
            list($controller, $method) = $controller;
            $m = new \ReflectionMethod($controller, $method);
            $parameters = $m->getParameters();
            $repr = sprintf('%s::%s()', get_class($controller), $method);
        } else {
            $f = new \ReflectionFunction($controller);
            $parameters = $f->getParameters();
            $repr = 'Closure';
        }
        $arguments = array();
        foreach ($parameters as $param) {
            if (array_key_exists($param->getName(), $attributes)) {
                $arguments[] = $attributes[$param->getName()];
            } elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            } else {
                throw new \RuntimeException(sprintf('Controller "%s" requires that you provide a value for the "$%s" argument (because there is no default value or because there is a non optional argument after this one).', $repr, $param->getName()));
            }
        }
        return $arguments;
    }
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }
        list($class, $method) = explode('::', $controller);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
        return array(new $class(), $method);
    }
}
}
namespace Symfony\Component\HttpKernel\Controller
{
use Symfony\Component\HttpFoundation\Request;
interface ControllerResolverInterface
{
    function getController(Request $request);
    function getArguments(Request $request, $controller);
}
}
namespace Symfony\Bundle\FrameworkBundle
{
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
class RequestListener
{
    protected $router;
    protected $logger;
    protected $container;
    public function __construct(ContainerInterface $container, RouterInterface $router, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->router = $router;
        $this->logger = $logger;
    }
    public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('core.request', array($this, 'handle'), $priority);
    }
    public function handle(Event $event)
    {
        $request = $event->get('request');
        $master = HttpKernelInterface::MASTER_REQUEST === $event->get('request_type');
        $this->initializeSession($request, $master);
        $this->initializeRequestAttributes($request, $master);
    }
    protected function initializeSession(Request $request, $master)
    {
        if (!$master) {
            return;
        }
                if (null === $request->getSession()) {
            $request->setSession($this->container->get('session'));
        }
                if ($request->hasSession()) {
            $request->getSession()->start();
        }
    }
    protected function initializeRequestAttributes(Request $request, $master)
    {
        if ($master) {
                                    $this->router->setContext(array(
                'base_url'  => $request->getBaseUrl(),
                'method'    => $request->getMethod(),
                'host'      => $request->getHost(),
                'is_secure' => $request->isSecure(),
            ));
        }
        if ($request->attributes->has('_controller')) {
                        return;
        }
                if (false !== $parameters = $this->router->match($request->getPathInfo())) {
            if (null !== $this->logger) {
                $this->logger->info(sprintf('Matched route "%s" (parameters: %s)', $parameters['_route'], str_replace("\n", '', var_export($parameters, true))));
            }
            $request->attributes->add($parameters);
            if ($locale = $request->attributes->get('_locale')) {
                $request->getSession()->setLocale($locale);
            }
        } elseif (null !== $this->logger) {
            $this->logger->err(sprintf('No route found for %s', $request->getPathInfo()));
        }
    }
}
}
namespace Symfony\Bundle\FrameworkBundle\Controller
{
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
class ControllerNameConverter
{
    protected $kernel;
    protected $logger;
    public function __construct(Kernel $kernel, LoggerInterface $logger = null)
    {
        $this->kernel = $kernel;
        $this->logger = $logger;
        $this->namespaces = array_keys($kernel->getBundleDirs());
    }
    public function toShortNotation($controller)
    {
        if (2 != count($parts = explode('::', $controller))) {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid class::method controller string.', $controller));
        }
        list($class, $method) = $parts;
        if ('Action' != substr($method, -6)) {
            throw new \InvalidArgumentException(sprintf('The "%s::%s" method does not look like a controller action (it does not end with Action)', $class, $method));
        }
        $action = substr($method, 0, -6);
        if (!preg_match('/Controller\\\(.*)Controller$/', $class, $match)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not look like a controller class (it does not end with Controller)', $class));
        }
        $controller = $match[1];
        $bundle = null;
        $namespace = substr($class, 0, strrpos($class, '\\'));
        foreach ($this->namespaces as $prefix) {
            if (0 === $pos = strpos($namespace, $prefix)) {
                                $bundle = substr($namespace, strlen($prefix) + 1, -11);
            }
        }
        if (null === $bundle) {
            throw new \InvalidArgumentException(sprintf('The "%s" class does not belong to a known bundle namespace.', $class));
        }
        return str_replace('\\', '', $bundle).':'.$controller.':'.$action;
    }
    public function fromShortNotation($controller)
    {
        if (3 != count($parts = explode(':', $controller))) {
            throw new \InvalidArgumentException(sprintf('The "%s" controller is not a valid a:b:c controller string.', $controller));
        }
        list($bundle, $controller, $action) = $parts;
        $class = null;
        $logs = array();
        foreach ($this->namespaces as $namespace) {
            $prefix = null;
            foreach ($this->kernel->getBundles() as $b) {
                if ($bundle === $b->getName() && 0 === strpos($b->getNamespace(), $namespace)) {
                    $prefix = $b->getNamespace();
                    break;
                }
            }
            if (null === $prefix) {
                continue;
            }
            $try = $prefix.'\\Controller\\'.$controller.'Controller';
            if (!class_exists($try)) {
                if (null !== $this->logger) {
                    $logs[] = sprintf('Failed finding controller "%s:%s" from namespace "%s" (%s)', $bundle, $controller, $namespace, $try);
                }
            } else {
                if (!$this->kernel->isClassInActiveBundle($try)) {
                    throw new \LogicException(sprintf('To use the "%s" controller, you first need to enable the Bundle "%s" in your Kernel class.', $try, $namespace.'\\'.$bundle));
                }
                $class = $try;
                break;
            }
        }
        if (null === $class) {
            if (null !== $this->logger) {
                foreach ($logs as $log) {
                    $this->logger->info($log);
                }
            }
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s:%s".', $bundle, $controller));
        }
        return $class.'::'.$action.'Action';
    }
}
}
namespace Symfony\Bundle\FrameworkBundle\Controller
{
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameConverter;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
class ControllerResolver extends BaseControllerResolver
{
    protected $container;
    protected $converter;
    protected $esiSupport;
    public function __construct(ContainerInterface $container, ControllerNameConverter $converter, LoggerInterface $logger = null)
    {
        $this->container = $container;
        $this->converter = $converter;
        parent::__construct($logger);
    }
    protected function createController($controller)
    {
        if (false === strpos($controller, '::')) {
            $count = substr_count($controller, ':');
            if (2 == $count) {
                                $controller = $this->converter->fromShortNotation($controller);
            } elseif (1 == $count) {
                                list($service, $method) = explode(':', $controller);
                return array($this->container->get($service), $method);
            } else {
                throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
            }
        }
        list($class, $method) = explode('::', $controller);
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }
        $controller = new $class();
        if ($controller instanceof ContainerAwareInterface) {
            $controller->setContainer($this->container);
        }
        return array($controller, $method);
    }
    public function forward($controller, array $attributes = array(), array $query = array())
    {
        $attributes['_controller'] = $controller;
        $subRequest = $this->container->get('request')->duplicate($query, null, $attributes);
        return $this->container->get('kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
    public function render($controller, array $options = array())
    {
        $options = array_merge(array(
            'attributes'    => array(),
            'query'         => array(),
            'ignore_errors' => !$this->container->getParameter('kernel.debug'),
            'alt'           => array(),
            'standalone'    => false,
            'comment'       => '',
        ), $options);
        if (!is_array($options['alt'])) {
            $options['alt'] = array($options['alt']);
        }
        if (null === $this->esiSupport) {
            $this->esiSupport = $this->container->has('esi') && $this->container->get('esi')->hasSurrogateEsiCapability($this->container->get('request'));
        }
        if ($this->esiSupport && $options['standalone']) {
            $uri = $this->generateInternalUri($controller, $options['attributes'], $options['query']);
            $alt = '';
            if ($options['alt']) {
                $alt = $this->generateInternalUri($options['alt'][0], isset($options['alt'][1]) ? $options['alt'][1] : array(), isset($options['alt'][2]) ? $options['alt'][2] : array());
            }
            return $this->container->get('esi')->renderIncludeTag($uri, $alt, $options['ignore_errors'], $options['comment']);
        }
        $request = $this->container->get('request');
                if (0 === strpos($controller, '/')) {
            $subRequest = Request::create($controller, 'get', array(), $request->cookies->all(), array(), $request->server->all());
            $subRequest->setSession($request->getSession());
        } else {
            $options['attributes']['_controller'] = $controller;
            $options['attributes']['_format'] = $request->getRequestFormat();
            $subRequest = $request->duplicate($options['query'], null, $options['attributes']);
        }
        try {
            $response = $this->container->get('kernel')->handle($subRequest, HttpKernelInterface::SUB_REQUEST, true);
            if (200 != $response->getStatusCode()) {
                throw new \RuntimeException(sprintf('Error when rendering "%s" (Status code is %s).', $request->getUri(), $response->getStatusCode()));
            }
            return $response->getContent();
        } catch (\Exception $e) {
            if ($options['alt']) {
                $alt = $options['alt'];
                unset($options['alt']);
                $options['attributes'] = isset($alt[1]) ? $alt[1] : array();
                $options['query'] = isset($alt[2]) ? $alt[2] : array();
                return $this->render($alt[0], $options);
            }
            if (!$options['ignore_errors']) {
                throw $e;
            }
        }
    }
    public function generateInternalUri($controller, array $attributes = array(), array $query = array())
    {
        if (0 === strpos($controller, '/')) {
            return $controller;
        }
        $uri = $this->container->get('router')->generate('_internal', array(
            'controller' => $controller,
            'path'       => $attributes ? http_build_query($attributes) : 'none',
            '_format'    => $this->container->get('request')->getRequestFormat(),
        ), true);
        if ($query) {
            $uri = $uri.'?'.http_build_query($query);
        }
        return $uri;
    }
}
}
namespace Symfony\Bundle\FrameworkBundle\Controller
{
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerAware;
class Controller extends ContainerAware
{
    public function createResponse($content = '', $status = 200, array $headers = array())
    {
        $response = $this->container->get('response');
        $response->setContent($content);
        $response->setStatusCode($status);
        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }
        return $response;
    }
    public function generateUrl($route, array $parameters = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }
    public function forward($controller, array $path = array(), array $query = array())
    {
        return $this->container->get('controller_resolver')->forward($controller, $path, $query);
    }
    public function redirect($url, $status = 302)
    {
        $response = $this->container->get('response');
        $response->setRedirect($url, $status);
        return $response;
    }
    public function renderView($view, array $parameters = array())
    {
        return $this->container->get('templating')->render($view, $parameters);
    }
    public function render($view, array $parameters = array(), Response $response = null)
    {
        return $this->container->get('templating')->renderResponse($view, $parameters, $response);
    }
    public function has($id)
    {
        return $this->container->has($id);
    }
    public function get($id)
    {
        return $this->container->get($id);
    }
}
}
namespace Symfony\Component\EventDispatcher
{
class Event
{
    protected $value = null;
    protected $processed = false;
    protected $subject;
    protected $name;
    protected $parameters;
    public function __construct($subject, $name, $parameters = array())
    {
        $this->subject = $subject;
        $this->name = $name;
        $this->parameters = $parameters;
    }
    public function getSubject()
    {
        return $this->subject;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setReturnValue($value)
    {
        $this->value = $value;
    }
    public function getReturnValue()
    {
        return $this->value;
    }
    public function setProcessed($processed)
    {
        $this->processed = (boolean) $processed;
    }
    public function isProcessed()
    {
        return $this->processed;
    }
    public function all()
    {
        return $this->parameters;
    }
    public function has($name)
    {
        return array_key_exists($name, $this->parameters);
    }
    public function get($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new \InvalidArgumentException(sprintf('The event "%s" has no "%s" parameter.', $this->name, $name));
        }
        return $this->parameters[$name];
    }
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }
}
}
namespace Symfony\Component\EventDispatcher
{
class EventDispatcher
{
    protected $listeners = array();
    public function connect($name, $listener, $priority = 0)
    {
        if (!isset($this->listeners[$name][$priority])) {
            if (!isset($this->listeners[$name])) {
                $this->listeners[$name] = array();
            }
            $this->listeners[$name][$priority] = array();
        }
        $this->listeners[$name][$priority][] = $listener;
    }
    public function disconnect($name, $listener = null)
    {
        if (!isset($this->listeners[$name])) {
            return false;
        }
        if (null === $listener) {
            unset($this->listeners[$name]);
            return;
        }
        foreach ($this->listeners[$name] as $priority => $callables) {
            foreach ($callables as $i => $callable) {
                if ($listener === $callable) {
                    unset($this->listeners[$name][$priority][$i]);
                }
            }
        }
    }
    public function notify(Event $event)
    {
        foreach ($this->getListeners($event->getName()) as $listener) {
            call_user_func($listener, $event);
        }
        return $event;
    }
    public function notifyUntil(Event $event)
    {
        foreach ($this->getListeners($event->getName()) as $listener) {
            if (call_user_func($listener, $event)) {
                $event->setProcessed(true);
                break;
            }
        }
        return $event;
    }
    public function filter(Event $event, $value)
    {
        foreach ($this->getListeners($event->getName()) as $listener) {
            $value = call_user_func($listener, $event, $value);
        }
        $event->setReturnValue($value);
        return $event;
    }
    public function hasListeners($name)
    {
        return (Boolean) count($this->getListeners($name));
    }
    public function getListeners($name)
    {
        if (!isset($this->listeners[$name])) {
            return array();
        }
        $listeners = array();
        $all = $this->listeners[$name];
        krsort($all);
        foreach ($all as $l) {
            $listeners = array_merge($listeners, $l);
        }
        return $listeners;
    }
}
}
namespace Symfony\Bundle\FrameworkBundle
{
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;
class EventDispatcher extends BaseEventDispatcher
{
    public function registerKernelListeners(array $kernelListeners)
    {
        foreach ($kernelListeners as $priority => $listeners) {
            foreach ($listeners as $listener) {
                $listener->register($this, $priority);
            }
        }
    }
}
}
namespace Symfony\Component\Form
{
class FormConfiguration
{
    protected static $defaultCsrfSecrets = array();
    protected static $defaultCsrfProtection = false;
    protected static $defaultCsrfFieldName = '_token';
    protected static $defaultLocale = null;
    static public function setDefaultLocale($defaultLocale)
    {
        self::$defaultLocale = $defaultLocale;
    }
    static public function getDefaultLocale()
    {
        return isset(self::$defaultLocale) ? self::$defaultLocale : \Locale::getDefault();
    }
    static public function enableDefaultCsrfProtection()
    {
        self::$defaultCsrfProtection = true;
    }
    static public function isDefaultCsrfProtectionEnabled()
    {
        return self::$defaultCsrfProtection;
    }
    static public function disableDefaultCsrfProtection()
    {
        self::$defaultCsrfProtection = false;
    }
    static public function setDefaultCsrfFieldName($name)
    {
        self::$defaultCsrfFieldName = $name;
    }
    static public function getDefaultCsrfFieldName()
    {
        return self::$defaultCsrfFieldName;
    }
    static public function setDefaultCsrfSecrets(array $secrets)
    {
        self::$defaultCsrfSecrets = $secrets;
    }
    static public function addDefaultCsrfSecret($secret)
    {
        self::$defaultCsrfSecrets[] = $secret;
    }
    static public function clearDefaultCsrfSecrets()
    {
        self::$defaultCsrfSecrets = array();
    }
    static public function getDefaultCsrfSecrets()
    {
        return self::$defaultCsrfSecrets;
    }
}
}
namespace
{
class Twig_Environment
{
    const VERSION = '1.0.0-RC2';
    protected $charset;
    protected $loader;
    protected $debug;
    protected $autoReload;
    protected $cache;
    protected $lexer;
    protected $parser;
    protected $compiler;
    protected $baseTemplateClass;
    protected $extensions;
    protected $parsers;
    protected $visitors;
    protected $filters;
    protected $tests;
    protected $functions;
    protected $globals;
    protected $runtimeInitialized;
    protected $loadedTemplates;
    protected $strictVariables;
    protected $unaryOperators;
    protected $binaryOperators;
    protected $templateClassPrefix = '__TwigTemplate_';
    protected $functionCallbacks;
    protected $filterCallbacks;
    public function __construct(Twig_LoaderInterface $loader = null, $options = array())
    {
        if (null !== $loader) {
            $this->setLoader($loader);
        }
        $options = array_merge(array(
            'debug'               => false,
            'charset'             => 'UTF-8',
            'base_template_class' => 'Twig_Template',
            'strict_variables'    => false,
            'autoescape'          => true,
            'cache'               => false,
            'auto_reload'         => null,
            'optimizations'       => -1,
        ), $options);
        $this->debug              = (bool) $options['debug'];
        $this->charset            = $options['charset'];
        $this->baseTemplateClass  = $options['base_template_class'];
        $this->autoReload         = null === $options['auto_reload'] ? $this->debug : (bool) $options['auto_reload'];
        $this->extensions         = array(
            'core'      => new Twig_Extension_Core(),
            'escaper'   => new Twig_Extension_Escaper((bool) $options['autoescape']),
            'optimizer' => new Twig_Extension_Optimizer($options['optimizations']),
        );
        $this->strictVariables    = (bool) $options['strict_variables'];
        $this->runtimeInitialized = false;
        $this->setCache($options['cache']);
        $this->functionCallbacks = array();
        $this->filterCallbacks = array();
    }
    public function getBaseTemplateClass()
    {
        return $this->baseTemplateClass;
    }
    public function setBaseTemplateClass($class)
    {
        $this->baseTemplateClass = $class;
    }
    public function enableDebug()
    {
        $this->debug = true;
    }
    public function disableDebug()
    {
        $this->debug = false;
    }
    public function isDebug()
    {
        return $this->debug;
    }
    public function enableAutoReload()
    {
        $this->autoReload = true;
    }
    public function disableAutoReload()
    {
        $this->autoReload = false;
    }
    public function isAutoReload()
    {
        return $this->autoReload;
    }
    public function enableStrictVariables()
    {
        $this->strictVariables = true;
    }
    public function disableStrictVariables()
    {
        $this->strictVariables = false;
    }
    public function isStrictVariables()
    {
        return $this->strictVariables;
    }
    public function getCache()
    {
        return $this->cache;
    }
    public function setCache($cache)
    {
        $this->cache = $cache ? $cache : false;
    }
    public function getCacheFilename($name)
    {
        if (false === $this->cache) {
            return false;
        }
        $class = substr($this->getTemplateClass($name), strlen($this->templateClassPrefix));
        return $this->getCache().'/'.substr($class, 0, 2).'/'.substr($class, 2, 2).'/'.substr($class, 4).'.php';
    }
    public function getTemplateClass($name)
    {
        return $this->templateClassPrefix.md5($this->loader->getCacheKey($name));
    }
    public function getTemplateClassPrefix()
    {
        return $this->templateClassPrefix;
    }
    public function loadTemplate($name)
    {
        $cls = $this->getTemplateClass($name);
        if (isset($this->loadedTemplates[$cls])) {
            return $this->loadedTemplates[$cls];
        }
        if (!class_exists($cls, false)) {
            if (false === $cache = $this->getCacheFilename($name)) {
                eval('?>'.$this->compileSource($this->loader->getSource($name), $name));
            } else {
                if (!file_exists($cache) || ($this->isAutoReload() && !$this->loader->isFresh($name, filemtime($cache)))) {
                    $this->writeCacheFile($cache, $this->compileSource($this->loader->getSource($name), $name));
                }
                require_once $cache;
            }
        }
        if (!$this->runtimeInitialized) {
            $this->initRuntime();
        }
        return $this->loadedTemplates[$cls] = new $cls($this);
    }
    public function clearTemplateCache()
    {
        $this->loadedTemplates = array();
    }
    public function clearCacheFiles()
    {
        if (false === $this->cache) {
            return;
        }
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cache), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if ($file->isFile()) {
                @unlink($file->getPathname());
            }
        }
    }
    public function getLexer()
    {
        if (null === $this->lexer) {
            $this->lexer = new Twig_Lexer($this);
        }
        return $this->lexer;
    }
    public function setLexer(Twig_LexerInterface $lexer)
    {
        $this->lexer = $lexer;
    }
    public function tokenize($source, $name = null)
    {
        return $this->getLexer()->tokenize($source, $name);
    }
    public function getParser()
    {
        if (null === $this->parser) {
            $this->parser = new Twig_Parser($this);
        }
        return $this->parser;
    }
    public function setParser(Twig_ParserInterface $parser)
    {
        $this->parser = $parser;
    }
    public function parse(Twig_TokenStream $tokens)
    {
        return $this->getParser()->parse($tokens);
    }
    public function getCompiler()
    {
        if (null === $this->compiler) {
            $this->compiler = new Twig_Compiler($this);
        }
        return $this->compiler;
    }
    public function setCompiler(Twig_CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }
    public function compile(Twig_NodeInterface $node)
    {
        return $this->getCompiler()->compile($node)->getSource();
    }
    public function compileSource($source, $name = null)
    {
        return $this->compile($this->parse($this->tokenize($source, $name)));
    }
    public function setLoader(Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;
    }
    public function getLoader()
    {
        return $this->loader;
    }
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    public function getCharset()
    {
        return $this->charset;
    }
    public function initRuntime()
    {
        $this->runtimeInitialized = true;
        foreach ($this->getExtensions() as $extension) {
            $extension->initRuntime($this);
        }
    }
    public function hasExtension($name)
    {
        return isset($this->extensions[$name]);
    }
    public function getExtension($name)
    {
        if (!isset($this->extensions[$name])) {
            throw new Twig_Error_Runtime(sprintf('The "%s" extension is not enabled.', $name));
        }
        return $this->extensions[$name];
    }
    public function addExtension(Twig_ExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
    }
    public function removeExtension($name)
    {
        unset($this->extensions[$name]);
    }
    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }
    public function getExtensions()
    {
        return $this->extensions;
    }
    public function addTokenParser(Twig_TokenParserInterface $parser)
    {
        if (null === $this->parsers) {
            $this->getTokenParsers();
        }
        $this->parsers->addTokenParser($parser);
    }
    public function getTokenParsers()
    {
        if (null === $this->parsers) {
            $this->parsers = new Twig_TokenParserBroker;
            foreach ($this->getExtensions() as $extension) {
                $parsers = $extension->getTokenParsers();
                foreach($parsers as $parser) {
                    if ($parser instanceof Twig_TokenParserInterface) {
                        $this->parsers->addTokenParser($parser);
                    } else if ($parser instanceof Twig_TokenParserBrokerInterface) {
                        $this->parsers->addTokenParserBroker($parser);
                    } else {
                        throw new Twig_Error_Runtime('getTokenParsers() must return an array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances');
                    }
                }
            }
        }
        return $this->parsers;
    }
    public function addNodeVisitor(Twig_NodeVisitorInterface $visitor)
    {
        if (null === $this->visitors) {
            $this->getNodeVisitors();
        }
        $this->visitors[] = $visitor;
    }
    public function getNodeVisitors()
    {
        if (null === $this->visitors) {
            $this->visitors = array();
            foreach ($this->getExtensions() as $extension) {
                $this->visitors = array_merge($this->visitors, $extension->getNodeVisitors());
            }
        }
        return $this->visitors;
    }
    public function addFilter($name, Twig_FilterInterface $filter)
    {
        if (null === $this->filters) {
            $this->loadFilters();
        }
        $this->filters[$name] = $filter;
    }
    public function getFilter($name)
    {
        if (null === $this->filters) {
            $this->loadFilters();
        }
        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }
        foreach ($this->filterCallbacks as $callback) {
            if (false !== $filter = call_user_func($callback, $name)) {
                return $filter;
            }
        }
        return false;
    }
    public function registerUndefinedFilterCallback($callable)
    {
        $this->filterCallbacks[] = $callable;
    }
    protected function loadFilters()
    {
        $this->filters = array();
        foreach ($this->getExtensions() as $extension) {
            $this->filters = array_merge($this->filters, $extension->getFilters());
        }
    }
    public function addTest($name, Twig_TestInterface $test)
    {
        if (null === $this->tests) {
            $this->getTests();
        }
        $this->tests[$name] = $test;
    }
    public function getTests()
    {
        if (null === $this->tests) {
            $this->tests = array();
            foreach ($this->getExtensions() as $extension) {
                $this->tests = array_merge($this->tests, $extension->getTests());
            }
        }
        return $this->tests;
    }
    public function addFunction($name, Twig_FunctionInterface $function)
    {
        if (null === $this->functions) {
            $this->loadFunctions();
        }
        $this->functions[$name] = $function;
    }
    public function getFunction($name)
    {
        if (null === $this->functions) {
            $this->loadFunctions();
        }
        if (isset($this->functions[$name])) {
            return $this->functions[$name];
        }
        foreach ($this->functionCallbacks as $callback) {
            if (false !== $function = call_user_func($callback, $name)) {
                return $function;
            }
        }
        return false;
    }
    public function registerUndefinedFunctionCallback($callable)
    {
        $this->functionCallbacks[] = $callable;
    }
    protected function loadFunctions() {
        $this->functions = array();
        foreach ($this->getExtensions() as $extension) {
            $this->functions = array_merge($this->functions, $extension->getFunctions());
        }
    }
    public function addGlobal($name, $value)
    {
        if (null === $this->globals) {
            $this->getGlobals();
        }
        $this->globals[$name] = $value;
    }
    public function getGlobals()
    {
        if (null === $this->globals) {
            $this->globals = array();
            foreach ($this->getExtensions() as $extension) {
                $this->globals = array_merge($this->globals, $extension->getGlobals());
            }
        }
        return $this->globals;
    }
    public function getUnaryOperators()
    {
        if (null === $this->unaryOperators) {
            $this->initOperators();
        }
        return $this->unaryOperators;
    }
    public function getBinaryOperators()
    {
        if (null === $this->binaryOperators) {
            $this->initOperators();
        }
        return $this->binaryOperators;
    }
    protected function initOperators()
    {
        $this->unaryOperators = array();
        $this->binaryOperators = array();
        foreach ($this->getExtensions() as $extension) {
            $operators = $extension->getOperators();
            if (!$operators) {
                continue;
            }
            if (2 !== count($operators)) {
                throw new InvalidArgumentException(sprintf('"%s::getOperators()" does not return a valid operators array.', get_class($extension)));
            }
            $this->unaryOperators = array_merge($this->unaryOperators, $operators[0]);
            $this->binaryOperators = array_merge($this->binaryOperators, $operators[1]);
        }
    }
    protected function writeCacheFile($file, $content)
    {
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content)) {
                        if (@rename($tmpFile, $file) || (@copy($tmpFile, $file) && unlink($tmpFile))) {
                chmod($file, 0644);
                return;
            }
        }
        throw new Twig_Error_Runtime(sprintf('Failed to write cache file "%s".', $file));
    }
}
}
namespace
{
interface Twig_ExtensionInterface
{
    function initRuntime(Twig_Environment $environment);
    function getTokenParsers();
    function getNodeVisitors();
    function getFilters();
    function getTests();
    function getFunctions();
    function getOperators();
    function getGlobals();
    function getName();
}
}
namespace
{
abstract class Twig_Extension implements Twig_ExtensionInterface
{
    public function initRuntime(Twig_Environment $environment)
    {
    }
    public function getTokenParsers()
    {
        return array();
    }
    public function getNodeVisitors()
    {
        return array();
    }
    public function getFilters()
    {
        return array();
    }
    public function getTests()
    {
        return array();
    }
    public function getFunctions()
    {
        return array();
    }
    public function getOperators()
    {
        return array();
    }
    public function getGlobals()
    {
        return array();
    }
}
}
namespace
{
class Twig_Extension_Core extends Twig_Extension
{
    public function getTokenParsers()
    {
        return array(
            new Twig_TokenParser_For(),
            new Twig_TokenParser_If(),
            new Twig_TokenParser_Extends(),
            new Twig_TokenParser_Include(),
            new Twig_TokenParser_Block(),
            new Twig_TokenParser_Filter(),
            new Twig_TokenParser_Macro(),
            new Twig_TokenParser_Import(),
            new Twig_TokenParser_From(),
            new Twig_TokenParser_Set(),
            new Twig_TokenParser_Spaceless(),
        );
    }
    public function getFilters()
    {
        $filters = array(
                        'date'    => new Twig_Filter_Function('twig_date_format_filter'),
            'format'  => new Twig_Filter_Function('sprintf'),
            'replace' => new Twig_Filter_Function('twig_strtr'),
                        'url_encode'  => new Twig_Filter_Function('twig_urlencode_filter'),
            'json_encode' => new Twig_Filter_Function('json_encode'),
                        'title'      => new Twig_Filter_Function('twig_title_string_filter', array('needs_environment' => true)),
            'capitalize' => new Twig_Filter_Function('twig_capitalize_string_filter', array('needs_environment' => true)),
            'upper'      => new Twig_Filter_Function('strtoupper'),
            'lower'      => new Twig_Filter_Function('strtolower'),
            'striptags'  => new Twig_Filter_Function('strip_tags'),
                        'join'    => new Twig_Filter_Function('twig_join_filter'),
            'reverse' => new Twig_Filter_Function('twig_reverse_filter'),
            'length'  => new Twig_Filter_Function('twig_length_filter', array('needs_environment' => true)),
            'sort'    => new Twig_Filter_Function('twig_sort_filter'),
            'merge'   => new Twig_Filter_Function('twig_array_merge'),
                        'default' => new Twig_Filter_Function('twig_default_filter'),
            'keys'    => new Twig_Filter_Function('twig_get_array_keys_filter'),
                        'escape' => new Twig_Filter_Function('twig_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe')),
            'e'      => new Twig_Filter_Function('twig_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe')),
        );
        if (function_exists('mb_get_info')) {
            $filters['upper'] = new Twig_Filter_Function('twig_upper_filter', array('needs_environment' => true));
            $filters['lower'] = new Twig_Filter_Function('twig_lower_filter', array('needs_environment' => true));
        }
        return $filters;
    }
    public function getFunctions()
    {
        return array(
            'range'    => new Twig_Function_Method($this, 'getRange'),
            'constant' => new Twig_Function_Method($this, 'getConstant'),
            'cycle'    => new Twig_Function_Method($this, 'getCycle'),
        );
    }
    public function getRange($start, $end, $step = 1)
    {
        return range($start, $end, $step);
    }
    public function getConstant($value)
    {
        return constant($value);
    }
    public function getCycle($values, $i)
    {
        if (!is_array($values) && !$values instanceof ArrayAccess) {
            return $values;
        }
        return $values[$i % count($values)];
    }
    public function getTests()
    {
        return array(
            'even'        => new Twig_Test_Function('twig_test_even'),
            'odd'         => new Twig_Test_Function('twig_test_odd'),
            'defined'     => new Twig_Test_Function('twig_test_defined'),
            'sameas'      => new Twig_Test_Function('twig_test_sameas'),
            'none'        => new Twig_Test_Function('twig_test_none'),
            'divisibleby' => new Twig_Test_Function('twig_test_divisibleby'),
            'constant'    => new Twig_Test_Function('twig_test_constant'),
            'empty'       => new Twig_Test_Function('twig_test_empty'),
        );
    }
    public function getOperators()
    {
        return array(
            array(
                'not' => array('precedence' => 50, 'class' => 'Twig_Node_Expression_Unary_Not'),
                '-'   => array('precedence' => 50, 'class' => 'Twig_Node_Expression_Unary_Neg'),
                '+'   => array('precedence' => 50, 'class' => 'Twig_Node_Expression_Unary_Pos'),
            ),
            array(
                'or'     => array('precedence' => 10, 'class' => 'Twig_Node_Expression_Binary_Or', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'and'    => array('precedence' => 15, 'class' => 'Twig_Node_Expression_Binary_And', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '=='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Equal', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '!='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_NotEqual', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '<'      => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Less', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '>'      => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_Greater', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '>='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_GreaterEqual', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '<='     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_LessEqual', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'not in' => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_NotIn', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'in'     => array('precedence' => 20, 'class' => 'Twig_Node_Expression_Binary_In', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '+'      => array('precedence' => 30, 'class' => 'Twig_Node_Expression_Binary_Add', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '-'      => array('precedence' => 30, 'class' => 'Twig_Node_Expression_Binary_Sub', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '~'      => array('precedence' => 40, 'class' => 'Twig_Node_Expression_Binary_Concat', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '*'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Mul', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '/'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Div', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '//'     => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_FloorDiv', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '%'      => array('precedence' => 60, 'class' => 'Twig_Node_Expression_Binary_Mod', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'is'     => array('precedence' => 100, 'callable' => array($this, 'parseTestExpression'), 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                'is not' => array('precedence' => 100, 'callable' => array($this, 'parseNotTestExpression'), 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '..'     => array('precedence' => 110, 'class' => 'Twig_Node_Expression_Binary_Range', 'associativity' => Twig_ExpressionParser::OPERATOR_LEFT),
                '**'     => array('precedence' => 200, 'class' => 'Twig_Node_Expression_Binary_Power', 'associativity' => Twig_ExpressionParser::OPERATOR_RIGHT),
            ),
        );
    }
    public function parseNotTestExpression($parser, $node)
    {
        return new Twig_Node_Expression_Unary_Not($this->parseTestExpression($parser, $node), $parser->getCurrentToken()->getLine());
    }
    public function parseTestExpression($parser, $node)
    {
        $stream = $parser->getStream();
        $name = $stream->expect(Twig_Token::NAME_TYPE);
        $arguments = null;
        if ($stream->test(Twig_Token::PUNCTUATION_TYPE, '(')) {
            $arguments = $parser->getExpressionParser()->parseArguments($node);
        }
        return new Twig_Node_Expression_Test($node, $name->getValue(), $arguments, $parser->getCurrentToken()->getLine());
    }
    public function getName()
    {
        return 'core';
    }
}
function twig_date_format_filter($date, $format = 'F j, Y H:i')
{
    if (!$date instanceof DateTime) {
        $date = new DateTime((ctype_digit($date) ? '@' : '').$date);
    }
    return $date->format($format);
}
function twig_urlencode_filter($url, $raw = false)
{
    if ($raw) {
        return rawurlencode($url);
    }
    return urlencode($url);
}
function twig_array_merge($arr1, $arr2)
{
    if (!is_array($arr1) || !is_array($arr2)) {
        throw new Twig_Error_Runtime('The merge filter only work with arrays or hashes.');
    }
    return array_merge($arr1, $arr2);
}
function twig_join_filter($value, $glue = '')
{
    return implode($glue, (array) $value);
}
function twig_default_filter($value, $default = '')
{
    return twig_test_empty($value) ? $default : $value;
}
function twig_get_array_keys_filter($array)
{
    if (is_object($array) && $array instanceof Traversable) {
        return array_keys(iterator_to_array($array));
    }
    if (!is_array($array)) {
        return array();
    }
    return array_keys($array);
}
function twig_reverse_filter($array)
{
    if (is_object($array) && $array instanceof Traversable) {
        return array_reverse(iterator_to_array($array));
    }
    if (!is_array($array)) {
        return array();
    }
    return array_reverse($array);
}
function twig_sort_filter($array)
{
    asort($array);
    return $array;
}
function twig_in_filter($value, $compare)
{
    if (is_array($compare)) {
        return in_array($value, $compare);
    } elseif (is_string($compare)) {
        return false !== strpos($compare, (string) $value);
    } elseif (is_object($compare) && $compare instanceof Traversable) {
        return in_array($value, iterator_to_array($compare, false));
    }
    return false;
}
function twig_strtr($pattern, $replacements)
{
    return str_replace(array_keys($replacements), array_values($replacements), $pattern);
}
function twig_escape_filter(Twig_Environment $env, $string, $type = 'html')
{
    if (is_object($string) && $string instanceof Twig_Markup) {
        return $string;
    }
    if (!is_string($string) && !(is_object($string) && method_exists($string, '__toString'))) {
        return $string;
    }
    switch ($type) {
        case 'js':
                                    $charset = $env->getCharset();
            if ('UTF-8' != $charset) {
                $string = _twig_convert_encoding($string, 'UTF-8', $charset);
            }
            if (null === $string = preg_replace_callback('#[^\p{L}\p{N} ]#u', '_twig_escape_js_callback', $string)) {
                throw new Twig_Error_Runtime('The string to escape is not a valid UTF-8 string.');
            }
            if ('UTF-8' != $charset) {
                $string = _twig_convert_encoding($string, $charset, 'UTF-8');
            }
            return $string;
        case 'html':
            return htmlspecialchars($string, ENT_QUOTES, $env->getCharset());
        default:
            throw new Twig_Error_Runtime(sprintf('Invalid escape type "%s".', $type));
    }
}
function twig_escape_filter_is_safe(Twig_Node $filterArgs)
{
    foreach ($filterArgs as $arg) {
        if ($arg instanceof Twig_Node_Expression_Constant) {
            return array($arg->getAttribute('value'));
        } else {
            return array();
        }
        break;
    }
    return array('html');
}
if (function_exists('iconv')) {
    function _twig_convert_encoding($string, $to, $from)
    {
        return iconv($from, $to, $string);
    }
} elseif (function_exists('mb_convert_encoding')) {
    function _twig_convert_encoding($string, $to, $from)
    {
        return mb_convert_encoding($string, $to, $from);
    }
} else {
    function _twig_convert_encoding($string, $to, $from)
    {
        throw new Twig_Error_Runtime('No suitable convert encoding function (use UTF-8 as your encoding or install the iconv or mbstring extension).');
    }
}
function _twig_escape_js_callback($matches)
{
    $char = $matches[0];
        if (!isset($char[1])) {
        return '\\x'.substr('00'.bin2hex($char), -2);
    }
        $char = _twig_convert_encoding($char, 'UTF-16BE', 'UTF-8');
    return '\\u'.substr('0000'.bin2hex($char), -4);
}
if (function_exists('mb_get_info')) {
    function twig_length_filter(Twig_Environment $env, $thing)
    {
        return is_string($thing) ? mb_strlen($thing, $env->getCharset()) : count($thing);
    }
    function twig_upper_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtoupper($string, $charset);
        }
        return strtoupper($string);
    }
    function twig_lower_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtolower($string, $charset);
        }
        return strtolower($string);
    }
    function twig_title_string_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_convert_case($string, MB_CASE_TITLE, $charset);
        }
        return ucwords(strtolower($string));
    }
    function twig_capitalize_string_filter(Twig_Environment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtoupper(mb_substr($string, 0, 1, $charset)).
                         mb_strtolower(mb_substr($string, 1, mb_strlen($string), $charset), $charset);
        }
        return ucfirst(strtolower($string));
    }
}
else
{
    function twig_length_filter(Twig_Environment $env, $thing)
    {
        return is_string($thing) ? strlen($thing) : count($thing);
    }
    function twig_title_string_filter(Twig_Environment $env, $string)
    {
        return ucwords(strtolower($string));
    }
    function twig_capitalize_string_filter(Twig_Environment $env, $string)
    {
        return ucfirst(strtolower($string));
    }
}
function twig_ensure_traversable($seq)
{
    if (is_array($seq) || (is_object($seq) && $seq instanceof Traversable)) {
        return $seq;
    } else {
        return array();
    }
}
function twig_test_sameas($value, $test)
{
    return $value === $test;
}
function twig_test_none($value)
{
    return null === $value;
}
function twig_test_divisibleby($value, $num)
{
    return 0 == $value % $num;
}
function twig_test_even($value)
{
    return $value % 2 == 0;
}
function twig_test_odd($value)
{
    return $value % 2 == 1;
}
function twig_test_constant($value, $constant)
{
    return constant($constant) === $value;
}
function twig_test_defined($name, $context)
{
    return array_key_exists($name, $context);
}
function twig_test_empty($value)
{
    return null === $value || false === $value || '' === (string) $value;
}
}
namespace
{
class Twig_Extension_Escaper extends Twig_Extension
{
    protected $autoescape;
    public function __construct($autoescape = true)
    {
        $this->autoescape = $autoescape;
    }
    public function getTokenParsers()
    {
        return array(new Twig_TokenParser_AutoEscape());
    }
    public function getNodeVisitors()
    {
        return array(new Twig_NodeVisitor_Escaper());
    }
    public function getFilters()
    {
        return array(
            'raw' => new Twig_Filter_Function('twig_raw_filter', array('is_safe' => array('all'))),
        );
    }
    public function isGlobal()
    {
        return $this->autoescape;
    }
    public function getName()
    {
        return 'escaper';
    }
}
function twig_raw_filter($string)
{
    return $string;
}
}
namespace
{
class Twig_Extension_Optimizer extends Twig_Extension
{
    protected $optimizers;
    public function __construct($optimizers = -1)
    {
        $this->optimizers = $optimizers;
    }
    public function getNodeVisitors()
    {
        return array(new Twig_NodeVisitor_Optimizer($this->optimizers));
    }
    public function getName()
    {
        return 'optimizer';
    }
}
}
namespace
{
interface Twig_LoaderInterface
{
    function getSource($name);
    function getCacheKey($name);
    function isFresh($name, $time);
}
}
namespace
{
class Twig_Loader_Filesystem implements Twig_LoaderInterface
{
    protected $paths;
    protected $cache;
    public function __construct($paths)
    {
        $this->setPaths($paths);
    }
    public function getPaths()
    {
        return $this->paths;
    }
    public function setPaths($paths)
    {
                $this->cache = array();
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        $this->paths = array();
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                throw new Twig_Error_Loader(sprintf('The "%s" directory does not exist.', $path));
            }
            $this->paths[] = $path;
        }
    }
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) < $time;
    }
    protected function findTemplate($name)
    {
                $name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }
        $this->validateName($name);
        foreach ($this->paths as $path) {
            if (is_file($path.'/'.$name)) {
                return $this->cache[$name] = $path.'/'.$name;
            }
        }
        throw new Twig_Error_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths)));
    }
    protected function validateName($name)
    {
        $parts = explode('/', $name);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
            } elseif ('.' !== $part) {
                ++$level;
            }
            if ($level < 0) {
                throw new Twig_Error_Loader(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }
}
}
namespace
{
class Twig_Markup
{
    protected $content;
    public function __construct($content)
    {
        $this->content = (string) $content;
    }
    public function __toString()
    {
        return $this->content;
    }
}
}
namespace
{
interface Twig_TemplateInterface
{
    const ANY_CALL    = 'any';
    const ARRAY_CALL  = 'array';
    const METHOD_CALL = 'method';
    function render(array $context);
    function display(array $context);
    function getEnvironment();
}
}
namespace
{
abstract class Twig_Template implements Twig_TemplateInterface
{
    static protected $cache = array();
    protected $env;
    protected $blocks;
    public function __construct(Twig_Environment $env)
    {
        $this->env = $env;
        $this->blocks = array();
    }
    public function getTemplateName()
    {
        return null;
    }
    public function getEnvironment()
    {
        return $this->env;
    }
    public function getParent(array $context)
    {
        return false;
    }
    public function displayParentBlock($name, array $context, array $blocks = array())
    {
        if (false !== $parent = $this->getParent($context)) {
            $parent->displayBlock($name, $context, $blocks);
        } else {
            throw new Twig_Error_Runtime('This template has no parent', -1, $this->getTemplateName());
        }
    }
    public function displayBlock($name, array $context, array $blocks = array())
    {
        if (isset($blocks[$name])) {
            $b = $blocks;
            unset($b[$name]);
            call_user_func($blocks[$name], $context, $b);
        } elseif (isset($this->blocks[$name])) {
            call_user_func($this->blocks[$name], $context, $blocks);
        } elseif (false !== $parent = $this->getParent($context)) {
            $parent->displayBlock($name, $context, array_merge($this->blocks, $blocks));
        }
    }
    public function renderParentBlock($name, array $context, array $blocks = array())
    {
        ob_start();
        $this->displayParentBlock($name, $context, $blocks);
        return new Twig_Markup(ob_get_clean());
    }
    public function renderBlock($name, array $context, array $blocks = array())
    {
        ob_start();
        $this->displayBlock($name, $context, $blocks);
        return new Twig_Markup(ob_get_clean());
    }
    public function hasBlock($name)
    {
        return isset($this->blocks[$name]);
    }
    public function getBlockNames()
    {
        return array_keys($this->blocks);
    }
    public function render(array $context)
    {
        ob_start();
        try {
            $this->display($context);
        } catch (Exception $e) {
            ob_end_clean();
            throw $e;
        }
        return ob_get_clean();
    }
    protected function getContext($context, $item, $line = -1)
    {
        if (!array_key_exists($item, $context)) {
            throw new Twig_Error_Runtime(sprintf('Variable "%s" does not exist', $item), $line, $this->getTemplateName());
        }
        return $context[$item];
    }
    protected function getAttribute($object, $item, array $arguments = array(), $type = Twig_TemplateInterface::ANY_CALL, $noStrictCheck = false, $line = -1)
    {
                if (Twig_TemplateInterface::METHOD_CALL !== $type) {
            if ((is_array($object) || is_object($object) && $object instanceof ArrayAccess) && isset($object[$item])) {
                return $object[$item];
            }
            if (Twig_TemplateInterface::ARRAY_CALL === $type) {
                if (!$this->env->isStrictVariables() || $noStrictCheck) {
                    return null;
                }
                if (is_object($object)) {
                    throw new Twig_Error_Runtime(sprintf('Key "%s" in object (with ArrayAccess) of type "%s" does not exist', $item, get_class($object)), $line, $this->getTemplateName());
                                } else {
                    throw new Twig_Error_Runtime(sprintf('Key "%s" for array with keys "%s" does not exist', $item, implode(', ', array_keys($object))), $line, $this->getTemplateName());
                }
            }
        }
        if (!is_object($object)) {
            if (!$this->env->isStrictVariables() || $noStrictCheck) {
                return null;
            }
            throw new Twig_Error_Runtime(sprintf('Item "%s" for "%s" does not exist', $item, $object), $line, $this->getTemplateName());
        }
                $class = get_class($object);
        if (!isset(self::$cache[$class])) {
            $r = new ReflectionClass($class);
            self::$cache[$class] = array('methods' => array(), 'properties' => array());
            foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                self::$cache[$class]['methods'][strtolower($method->getName())] = true;
            }
            foreach ($r->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                self::$cache[$class]['properties'][$property->getName()] = true;
            }
        }
                if (Twig_TemplateInterface::METHOD_CALL !== $type) {
            if (isset(self::$cache[$class]['properties'][$item]) || isset($object->$item)) {
                if ($this->env->hasExtension('sandbox')) {
                    $this->env->getExtension('sandbox')->checkPropertyAllowed($object, $item);
                }
                return $object->$item;
            }
        }
                $lcItem = strtolower($item);
        if (isset(self::$cache[$class]['methods'][$lcItem])) {
            $method = $item;
        } elseif (isset(self::$cache[$class]['methods']['get'.$lcItem])) {
            $method = 'get'.$item;
        } elseif (isset(self::$cache[$class]['methods']['is'.$lcItem])) {
            $method = 'is'.$item;
        } elseif (isset(self::$cache[$class]['methods']['__call'])) {
            $method = $item;
        } else {
            if (!$this->env->isStrictVariables() || $noStrictCheck) {
                return null;
            }
            throw new Twig_Error_Runtime(sprintf('Method "%s" for object "%s" does not exist', $item, get_class($object)), $line, $this->getTemplateName());
        }
        if ($this->env->hasExtension('sandbox')) {
            $this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
        }
        $ret = call_user_func_array(array($object, $method), $arguments);
        if ($object instanceof Twig_TemplateInterface) {
            return new Twig_Markup($ret);
        }
        return $ret;
    }
}
}
