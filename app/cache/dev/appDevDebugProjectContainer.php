<?php

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\TaggedContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;

/**
 * appDevDebugProjectContainer
 *
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 */
class appDevDebugProjectContainer extends Container implements TaggedContainerInterface
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct(new FrozenParameterBag($this->getDefaultParameters()));
    }

    /**
     * Gets the 'event_dispatcher' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Debug\EventDispatcher A Symfony\Bundle\FrameworkBundle\Debug\EventDispatcher instance.
     */
    protected function getEventDispatcherService()
    {
        $a = $this->get('logger');

        $this->services['event_dispatcher'] = $instance = new \Symfony\Bundle\FrameworkBundle\Debug\EventDispatcher($a);

        $instance->registerKernelListeners(array(0 => array(0 => new \Symfony\Bundle\FrameworkBundle\RequestListener($this, $this->get('router'), $a), 1 => new \Symfony\Component\HttpKernel\Cache\EsiListener(new \Symfony\Component\HttpKernel\Cache\Esi()), 2 => new \Symfony\Component\HttpKernel\ResponseListener(), 3 => new \Symfony\Component\HttpKernel\Profiler\ProfilerListener($this->get('profiler'), NULL, false)), -128 => array(0 => new \Symfony\Component\HttpKernel\Debug\ExceptionListener('Symfony\\Bundle\\FrameworkBundle\\Controller\\ExceptionController::showAction', $a), 1 => new \Symfony\Bundle\WebProfilerBundle\WebDebugToolbarListener($this->get('controller_resolver'), true))));

        return $instance;
    }

    /**
     * Gets the 'http_kernel' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\HttpKernel A Symfony\Component\HttpKernel\HttpKernel instance.
     */
    protected function getHttpKernelService()
    {
        return $this->services['http_kernel'] = new \Symfony\Component\HttpKernel\HttpKernel($this->get('event_dispatcher'), $this->get('controller_resolver'));
    }

    /**
     * Gets the 'request' service.
     *
     * @return Object An instance returned by http_kernel::getRequest().
     */
    protected function getRequestService()
    {
        return $this->get('http_kernel')->getRequest();
    }

    /**
     * Gets the 'response' service.
     *
     * @return Symfony\Component\HttpFoundation\Response A Symfony\Component\HttpFoundation\Response instance.
     */
    protected function getResponseService()
    {
        $instance = new \Symfony\Component\HttpFoundation\Response();

        $instance->setCharset('UTF-8');

        return $instance;
    }

    /**
     * Gets the 'filesystem' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Util\Filesystem A Symfony\Bundle\FrameworkBundle\Util\Filesystem instance.
     */
    protected function getFilesystemService()
    {
        return $this->services['filesystem'] = new \Symfony\Bundle\FrameworkBundle\Util\Filesystem();
    }

    /**
     * Gets the 'routing.loader.real' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader A Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader instance.
     */
    protected function getRouting_Loader_RealService()
    {
        $a = new \Symfony\Component\Routing\Loader\LoaderResolver();
        $a->addLoader(new \Symfony\Component\Routing\Loader\XmlFileLoader(array('Application' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Application', 'Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Bundle', 'Symfony\\Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/vendor/symfony/src/Symfony/Bundle')));
        $a->addLoader(new \Symfony\Component\Routing\Loader\YamlFileLoader(array('Application' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Application', 'Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Bundle', 'Symfony\\Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/vendor/symfony/src/Symfony/Bundle')));
        $a->addLoader(new \Symfony\Component\Routing\Loader\PhpFileLoader(array('Application' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Application', 'Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Bundle', 'Symfony\\Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/vendor/symfony/src/Symfony/Bundle')));

        return $this->services['routing.loader.real'] = new \Symfony\Bundle\FrameworkBundle\Routing\DelegatingLoader($this->get('controller_name_converter'), $this->get('logger'), $a);
    }

    /**
     * Gets the 'router' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Routing\Router A Symfony\Component\Routing\Router instance.
     */
    protected function getRouterService()
    {
        return $this->services['router'] = new \Symfony\Component\Routing\Router(new \Symfony\Bundle\FrameworkBundle\Routing\LazyLoader($this, 'routing.loader.real'), 'C:\\wamp\\www\\Symfony-2.0\\app/config/routing_dev.yml', array('cache_dir' => 'C:\\wamp\\www\\Symfony-2.0\\app/cache/dev', 'debug' => true, 'generator_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator', 'generator_base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator', 'generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper', 'generator_cache_class' => 'app_devUrlGenerator', 'matcher_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher', 'matcher_base_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher', 'matcher_dumper_class' => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper', 'matcher_cache_class' => 'app_devUrlMatcher'));
    }

    /**
     * Gets the 'validator' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Validator\Validator A Symfony\Component\Validator\Validator instance.
     */
    protected function getValidatorService()
    {
        return $this->services['validator'] = new \Symfony\Component\Validator\Validator(new \Symfony\Component\Validator\Mapping\ClassMetadataFactory(new \Symfony\Component\Validator\Mapping\Loader\LoaderChain(array(0 => new \Symfony\Component\Validator\Mapping\Loader\AnnotationLoader(array('validation' => 'Symfony\\Component\\Validator\\Constraints\\')), 1 => new \Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader('loadValidatorMetadata'), 2 => new \Symfony\Component\Validator\Mapping\Loader\XmlFilesLoader(array(0 => 'C:\\wamp\\www\\Symfony-2.0\\src\\vendor\\symfony\\src\\Symfony\\Bundle\\FrameworkBundle\\DependencyInjection/../../../Component/Form/Resources/config/validation.xml')), 3 => new \Symfony\Component\Validator\Mapping\Loader\YamlFilesLoader(array())))), new \Symfony\Bundle\FrameworkBundle\Validator\ConstraintValidatorFactory($this, array()));
    }

    /**
     * Gets the 'templating.engine.php' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\PhpEngine A Symfony\Bundle\FrameworkBundle\Templating\PhpEngine instance.
     */
    protected function getTemplating_Engine_PhpService()
    {
        $this->services['templating.engine.php'] = $instance = new \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine($this, $this->get('templating.loader'));

        $instance->setCharset('UTF-8');
        $instance->setHelpers(array('slots' => 'templating.helper.slots', 'assets' => 'templating.helper.assets', 'request' => 'templating.helper.request', 'session' => 'templating.helper.session', 'router' => 'templating.helper.router', 'actions' => 'templating.helper.actions', 'code' => 'templating.helper.code', 'translator' => 'templating.helper.translator', 'security' => 'templating.helper.security', 'form' => 'templating.helper.form'));

        return $instance;
    }

    /**
     * Gets the 'templating.helper.slots' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Templating\Helper\SlotsHelper A Symfony\Component\Templating\Helper\SlotsHelper instance.
     */
    protected function getTemplating_Helper_SlotsService()
    {
        return $this->services['templating.helper.slots'] = new \Symfony\Component\Templating\Helper\SlotsHelper();
    }

    /**
     * Gets the 'templating.helper.assets' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper instance.
     */
    protected function getTemplating_Helper_AssetsService()
    {
        return $this->services['templating.helper.assets'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper($this->get('http_kernel')->getRequest(), array(), NULL);
    }

    /**
     * Gets the 'templating.helper.request' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\RequestHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\RequestHelper instance.
     */
    protected function getTemplating_Helper_RequestService()
    {
        return $this->services['templating.helper.request'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RequestHelper($this->get('http_kernel')->getRequest());
    }

    /**
     * Gets the 'templating.helper.session' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\SessionHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\SessionHelper instance.
     */
    protected function getTemplating_Helper_SessionService()
    {
        return $this->services['templating.helper.session'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\SessionHelper($this->get('http_kernel')->getRequest());
    }

    /**
     * Gets the 'templating.helper.router' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper instance.
     */
    protected function getTemplating_Helper_RouterService()
    {
        return $this->services['templating.helper.router'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper($this->get('router'));
    }

    /**
     * Gets the 'templating.helper.actions' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper instance.
     */
    protected function getTemplating_Helper_ActionsService()
    {
        return $this->services['templating.helper.actions'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\ActionsHelper($this->get('controller_resolver'));
    }

    /**
     * Gets the 'templating.helper.code' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\CodeHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\CodeHelper instance.
     */
    protected function getTemplating_Helper_CodeService()
    {
        return $this->services['templating.helper.code'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\CodeHelper(NULL, 'C:\\wamp\\www\\Symfony-2.0\\app');
    }

    /**
     * Gets the 'templating.helper.translator' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper instance.
     */
    protected function getTemplating_Helper_TranslatorService()
    {
        return $this->services['templating.helper.translator'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\TranslatorHelper($this->get('translator'));
    }

    /**
     * Gets the 'templating.helper.security' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\SecurityHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\SecurityHelper instance.
     */
    protected function getTemplating_Helper_SecurityService()
    {
        return $this->services['templating.helper.security'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\SecurityHelper(NULL);
    }

    /**
     * Gets the 'templating.helper.form' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper A Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper instance.
     */
    protected function getTemplating_Helper_FormService()
    {
        return $this->services['templating.helper.form'] = new \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper($this->get('templating'));
    }

    /**
     * Gets the 'templating.name_parser' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser A Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser instance.
     */
    protected function getTemplating_NameParserService()
    {
        return $this->services['templating.name_parser'] = new \Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser($this->get('kernel'));
    }

    /**
     * Gets the 'session' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpFoundation\Session A Symfony\Component\HttpFoundation\Session instance.
     */
    protected function getSessionService()
    {
        $this->services['session'] = $instance = new \Symfony\Component\HttpFoundation\Session(new \Symfony\Component\HttpFoundation\SessionStorage\NativeSessionStorage(array('lifetime' => 3600)), array('default_locale' => 'en'));

        $instance->start();

        return $instance;
    }

    /**
     * Gets the 'translator.real' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Translation\Translator A Symfony\Bundle\FrameworkBundle\Translation\Translator instance.
     */
    protected function getTranslator_RealService()
    {
        $this->services['translator.real'] = $instance = new \Symfony\Bundle\FrameworkBundle\Translation\Translator($this, $this->get('translator.selector'), array('cache_dir' => 'C:\\wamp\\www\\Symfony-2.0\\app/cache/dev/translations', 'debug' => true), $this->get('session'));

        $instance->setFallbackLocale('en');

        return $instance;
    }

    /**
     * Gets the 'translator' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Translation\Translator A Symfony\Bundle\FrameworkBundle\Translation\Translator instance.
     */
    protected function getTranslatorService()
    {
        $this->services['translator'] = $instance = new \Symfony\Bundle\FrameworkBundle\Translation\Translator($this, $this->get('translator.selector'), array('cache_dir' => 'C:\\wamp\\www\\Symfony-2.0\\app/cache/dev/translations', 'debug' => true), $this->get('session'));

        $instance->setFallbackLocale('en');

        return $instance;
    }

    /**
     * Gets the 'translation.loader.php' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Translation\Loader\PhpFileLoader A Symfony\Component\Translation\Loader\PhpFileLoader instance.
     */
    protected function getTranslation_Loader_PhpService()
    {
        return $this->services['translation.loader.php'] = new \Symfony\Component\Translation\Loader\PhpFileLoader();
    }

    /**
     * Gets the 'translation.loader.yml' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Translation\Loader\YamlFileLoader A Symfony\Component\Translation\Loader\YamlFileLoader instance.
     */
    protected function getTranslation_Loader_YmlService()
    {
        return $this->services['translation.loader.yml'] = new \Symfony\Component\Translation\Loader\YamlFileLoader();
    }

    /**
     * Gets the 'translation.loader.xliff' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Translation\Loader\XliffFileLoader A Symfony\Component\Translation\Loader\XliffFileLoader instance.
     */
    protected function getTranslation_Loader_XliffService()
    {
        return $this->services['translation.loader.xliff'] = new \Symfony\Component\Translation\Loader\XliffFileLoader();
    }

    /**
     * Gets the 'profiler' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\Profiler\Profiler A Symfony\Component\HttpKernel\Profiler\Profiler instance.
     */
    protected function getProfilerService()
    {
        $this->services['profiler'] = $instance = new \Symfony\Component\HttpKernel\Profiler\Profiler(new \Symfony\Component\HttpKernel\Profiler\SQLiteProfilerStorage('C:\\wamp\\www\\Symfony-2.0\\app/cache/dev/profiler.db', 86400), $this->get('logger'));

        $instance->add($this->get('data_collector.config'));
        $instance->add($this->get('data_collector.request'));
        $instance->add($this->get('data_collector.security'));
        $instance->add($this->get('data_collector.exception'));
        $instance->add($this->get('data_collector.events'));
        $instance->add($this->get('data_collector.logger'));
        $instance->add($this->get('data_collector.timer'));
        $instance->add($this->get('data_collector.memory'));

        return $instance;
    }

    /**
     * Gets the 'data_collector.config' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\DataCollector\ConfigDataCollector A Symfony\Bundle\FrameworkBundle\DataCollector\ConfigDataCollector instance.
     */
    protected function getDataCollector_ConfigService()
    {
        return $this->services['data_collector.config'] = new \Symfony\Bundle\FrameworkBundle\DataCollector\ConfigDataCollector($this->get('kernel'), $this->get('router'));
    }

    /**
     * Gets the 'data_collector.request' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\DataCollector\RequestDataCollector A Symfony\Bundle\FrameworkBundle\DataCollector\RequestDataCollector instance.
     */
    protected function getDataCollector_RequestService()
    {
        return $this->services['data_collector.request'] = new \Symfony\Bundle\FrameworkBundle\DataCollector\RequestDataCollector();
    }

    /**
     * Gets the 'data_collector.security' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\DataCollector\SecurityDataCollector A Symfony\Component\HttpKernel\DataCollector\SecurityDataCollector instance.
     */
    protected function getDataCollector_SecurityService()
    {
        return $this->services['data_collector.security'] = new \Symfony\Component\HttpKernel\DataCollector\SecurityDataCollector(NULL);
    }

    /**
     * Gets the 'data_collector.exception' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\DataCollector\ExceptionDataCollector A Symfony\Component\HttpKernel\DataCollector\ExceptionDataCollector instance.
     */
    protected function getDataCollector_ExceptionService()
    {
        return $this->services['data_collector.exception'] = new \Symfony\Component\HttpKernel\DataCollector\ExceptionDataCollector();
    }

    /**
     * Gets the 'data_collector.events' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\DataCollector\EventDataCollector A Symfony\Component\HttpKernel\DataCollector\EventDataCollector instance.
     */
    protected function getDataCollector_EventsService()
    {
        $this->services['data_collector.events'] = $instance = new \Symfony\Component\HttpKernel\DataCollector\EventDataCollector();

        $instance->setEventDispatcher($this->get('event_dispatcher'));

        return $instance;
    }

    /**
     * Gets the 'data_collector.logger' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector A Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector instance.
     */
    protected function getDataCollector_LoggerService()
    {
        return $this->services['data_collector.logger'] = new \Symfony\Component\HttpKernel\DataCollector\LoggerDataCollector($this->get('logger'));
    }

    /**
     * Gets the 'data_collector.timer' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\DataCollector\TimerDataCollector A Symfony\Bundle\FrameworkBundle\DataCollector\TimerDataCollector instance.
     */
    protected function getDataCollector_TimerService()
    {
        return $this->services['data_collector.timer'] = new \Symfony\Bundle\FrameworkBundle\DataCollector\TimerDataCollector($this->get('kernel'));
    }

    /**
     * Gets the 'data_collector.memory' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector A Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector instance.
     */
    protected function getDataCollector_MemoryService()
    {
        return $this->services['data_collector.memory'] = new \Symfony\Component\HttpKernel\DataCollector\MemoryDataCollector();
    }

    /**
     * Gets the 'twig' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Twig_Environment A Twig_Environment instance.
     */
    protected function getTwigService()
    {
        $this->services['twig'] = $instance = new \Twig_Environment($this->get('twig.loader'), array('charset' => 'UTF-8', 'debug' => true, 'cache' => 'C:\\wamp\\www\\Symfony-2.0\\app/cache/dev/twig', 'strict_variables' => true));

        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\TransExtension($this->get('translator')));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\TemplatingExtension($this));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\FormExtension(array(0 => 'TwigBundle::form.twig.html')));
        $instance->addExtension(new \Symfony\Bundle\TwigBundle\Extension\SecurityExtension(NULL));

        return $instance;
    }

    /**
     * Gets the 'twig.loader' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\TwigBundle\Loader\FilesystemLoader A Symfony\Bundle\TwigBundle\Loader\FilesystemLoader instance.
     */
    protected function getTwig_LoaderService()
    {
        return $this->services['twig.loader'] = new \Symfony\Bundle\TwigBundle\Loader\FilesystemLoader($this->get('templating.name_parser'), array(0 => 'C:\\wamp\\www\\Symfony-2.0\\app/views/%bundle%/%controller%/%name%.%renderer%.%format%', 1 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Application/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%', 2 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Bundle/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%', 3 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/vendor/symfony/src/Symfony/Bundle/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%'), $this->get('logger'));
    }

    /**
     * Gets the 'templating.engine.twig' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\TwigBundle\TwigEngine A Symfony\Bundle\TwigBundle\TwigEngine instance.
     */
    protected function getTemplating_Engine_TwigService()
    {
        return $this->services['templating.engine.twig'] = new \Symfony\Bundle\TwigBundle\TwigEngine($this, $this->get('twig'), $this->get('twig.globals'));
    }

    /**
     * Gets the 'twig.globals' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\TwigBundle\GlobalVariables A Symfony\Bundle\TwigBundle\GlobalVariables instance.
     */
    protected function getTwig_GlobalsService()
    {
        return $this->services['twig.globals'] = new \Symfony\Bundle\TwigBundle\GlobalVariables($this);
    }

    /**
     * Gets the 'templating.loader' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Component\Templating\Loader\FilesystemLoader A Symfony\Component\Templating\Loader\FilesystemLoader instance.
     */
    protected function getTemplating_LoaderService()
    {
        $this->services['templating.loader'] = $instance = new \Symfony\Component\Templating\Loader\FilesystemLoader($this->get('templating.name_parser'), array(0 => 'C:\\wamp\\www\\Symfony-2.0\\app/views/%bundle%/%controller%/%name%.%renderer%.%format%', 1 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Application/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%', 2 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Bundle/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%', 3 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/vendor/symfony/src/Symfony/Bundle/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%'));

        $instance->setDebugger(new \Symfony\Bundle\FrameworkBundle\Templating\Debugger($this->get('logger')));

        return $instance;
    }

    /**
     * Gets the 'templating' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine A Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine instance.
     */
    protected function getTemplatingService()
    {
        $this->services['templating'] = $instance = new \Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine($this);

        $instance->setEngineIds(array(0 => 'templating.engine.twig', 1 => 'templating.engine.php'));

        return $instance;
    }

    /**
     * Gets the 'logger' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return Symfony\Bundle\ZendBundle\Logger\Logger A Symfony\Bundle\ZendBundle\Logger\Logger instance.
     */
    protected function getLoggerService()
    {
        $a = new \Zend\Log\Filter\Priority(7);

        $b = new \Zend\Log\Writer\Stream('C:\\wamp\\www\\Symfony-2.0\\app/logs/dev.log');
        $b->addFilter($a);
        $b->setFormatter(new \Zend\Log\Formatter\Simple('%timestamp% %priorityName%: %message%
'));

        $c = new \Symfony\Bundle\ZendBundle\Logger\DebugLogger();
        $c->addFilter($a);

        $this->services['logger'] = $instance = new \Symfony\Bundle\ZendBundle\Logger\Logger();

        $instance->addWriter($b);
        $instance->addWriter($c);

        return $instance;
    }

    /**
     * Gets the debug.event_dispatcher service alias.
     *
     * @return Symfony\Bundle\FrameworkBundle\Debug\EventDispatcher An instance of the event_dispatcher service
     */
    protected function getDebug_EventDispatcherService()
    {
        return $this->get('event_dispatcher');
    }

    /**
     * Gets the 'controller_name_converter' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * This service is private.
     * If you want to be able to request this service from the container directly,
     * make it public, otherwise you might end up with broken code.
     *
     * @return Symfony\Bundle\FrameworkBundle\Controller\ControllerNameConverter A Symfony\Bundle\FrameworkBundle\Controller\ControllerNameConverter instance.
     */
    protected function getControllerNameConverterService()
    {
        return $this->services['controller_name_converter'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameConverter($this->get('kernel'), $this->get('logger'));
    }

    /**
     * Gets the 'controller_resolver' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * This service is private.
     * If you want to be able to request this service from the container directly,
     * make it public, otherwise you might end up with broken code.
     *
     * @return Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver A Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver instance.
     */
    protected function getControllerResolverService()
    {
        return $this->services['controller_resolver'] = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerResolver($this, $this->get('controller_name_converter'), $this->get('logger'));
    }

    /**
     * Gets the 'translator.selector' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * This service is private.
     * If you want to be able to request this service from the container directly,
     * make it public, otherwise you might end up with broken code.
     *
     * @return Symfony\Component\Translation\MessageSelector A Symfony\Component\Translation\MessageSelector instance.
     */
    protected function getTranslator_SelectorService()
    {
        return $this->services['translator.selector'] = new \Symfony\Component\Translation\MessageSelector();
    }

    /**
     * Returns service ids for a given tag.
     *
     * @param string $name The tag name
     *
     * @return array An array of tags
     */
    public function findTaggedServiceIds($name)
    {
        static $tags = array(
            'templating.engine' => array(
                'templating.engine.php' => array(
                    0 => array(
                        'priority' => 128,
                    ),
                ),
                'templating.engine.twig' => array(
                    0 => array(
                        'priority' => 255,
                    ),
                ),
            ),
            'templating.helper' => array(
                'templating.helper.slots' => array(
                    0 => array(
                        'alias' => 'slots',
                    ),
                ),
                'templating.helper.assets' => array(
                    0 => array(
                        'alias' => 'assets',
                    ),
                ),
                'templating.helper.request' => array(
                    0 => array(
                        'alias' => 'request',
                    ),
                ),
                'templating.helper.session' => array(
                    0 => array(
                        'alias' => 'session',
                    ),
                ),
                'templating.helper.router' => array(
                    0 => array(
                        'alias' => 'router',
                    ),
                ),
                'templating.helper.actions' => array(
                    0 => array(
                        'alias' => 'actions',
                    ),
                ),
                'templating.helper.code' => array(
                    0 => array(
                        'alias' => 'code',
                    ),
                ),
                'templating.helper.translator' => array(
                    0 => array(
                        'alias' => 'translator',
                    ),
                ),
                'templating.helper.security' => array(
                    0 => array(
                        'alias' => 'security',
                    ),
                ),
                'templating.helper.form' => array(
                    0 => array(
                        'alias' => 'form',
                    ),
                ),
            ),
            'translation.loader' => array(
                'translation.loader.php' => array(
                    0 => array(
                        'alias' => 'php',
                    ),
                ),
                'translation.loader.yml' => array(
                    0 => array(
                        'alias' => 'yml',
                    ),
                ),
                'translation.loader.xliff' => array(
                    0 => array(
                        'alias' => 'xliff',
                    ),
                ),
            ),
            'data_collector' => array(
                'data_collector.config' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:config',
                    ),
                ),
                'data_collector.request' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:request',
                    ),
                ),
                'data_collector.security' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:security',
                    ),
                ),
                'data_collector.exception' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:exception',
                    ),
                ),
                'data_collector.events' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:events',
                    ),
                ),
                'data_collector.logger' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:logger',
                    ),
                ),
                'data_collector.timer' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:timer',
                    ),
                ),
                'data_collector.memory' => array(
                    0 => array(
                        'template' => 'WebProfilerBundle:Collector:memory',
                    ),
                ),
            ),
        );

        return isset($tags[$name]) ? $tags[$name] : array();
    }

    /**
     * Gets the default parameters.
     *
     * @return array An array of the default parameters
     */
    protected function getDefaultParameters()
    {
        return array(
            'kernel.root_dir' => 'C:\\wamp\\www\\Symfony-2.0\\app',
            'kernel.environment' => 'dev',
            'kernel.debug' => true,
            'kernel.name' => 'app',
            'kernel.cache_dir' => 'C:\\wamp\\www\\Symfony-2.0\\app/cache/dev',
            'kernel.logs_dir' => 'C:\\wamp\\www\\Symfony-2.0\\app/logs',
            'kernel.bundle_dirs' => array(
                'Application' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Application',
                'Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Bundle',
                'Symfony\\Bundle' => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/vendor/symfony/src/Symfony/Bundle',
            ),
            'kernel.bundles' => array(
                0 => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
                1 => 'Symfony\\Bundle\\TwigBundle\\TwigBundle',
                2 => 'Symfony\\Bundle\\ZendBundle\\ZendBundle',
                3 => 'Symfony\\Bundle\\SwiftmailerBundle\\SwiftmailerBundle',
                4 => 'Symfony\\Bundle\\DoctrineBundle\\DoctrineBundle',
                5 => 'Application\\JonTestBundle\\JonTestBundle',
                6 => 'Symfony\\Bundle\\WebProfilerBundle\\WebProfilerBundle',
            ),
            'kernel.charset' => 'UTF-8',
            'request_listener.class' => 'Symfony\\Bundle\\FrameworkBundle\\RequestListener',
            'controller_resolver.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerResolver',
            'controller_name_converter.class' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerNameConverter',
            'response_listener.class' => 'Symfony\\Component\\HttpKernel\\ResponseListener',
            'exception_listener.class' => 'Symfony\\Component\\HttpKernel\\Debug\\ExceptionListener',
            'exception_listener.controller' => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ExceptionController::showAction',
            'esi.class' => 'Symfony\\Component\\HttpKernel\\Cache\\Esi',
            'esi_listener.class' => 'Symfony\\Component\\HttpKernel\\Cache\\EsiListener',
            'csrf_secret' => 'xxxxxxxxxx',
            'event_dispatcher.class' => 'Symfony\\Bundle\\FrameworkBundle\\EventDispatcher',
            'http_kernel.class' => 'Symfony\\Component\\HttpKernel\\HttpKernel',
            'response.class' => 'Symfony\\Component\\HttpFoundation\\Response',
            'error_handler.class' => 'Symfony\\Component\\HttpKernel\\Debug\\ErrorHandler',
            'error_handler.level' => NULL,
            'filesystem.class' => 'Symfony\\Bundle\\FrameworkBundle\\Util\\Filesystem',
            'debug.event_dispatcher.class' => 'Symfony\\Bundle\\FrameworkBundle\\Debug\\EventDispatcher',
            'router.class' => 'Symfony\\Component\\Routing\\Router',
            'routing.loader.class' => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\DelegatingLoader',
            'routing.resolver.class' => 'Symfony\\Component\\Routing\\Loader\\LoaderResolver',
            'routing.loader.xml.class' => 'Symfony\\Component\\Routing\\Loader\\XmlFileLoader',
            'routing.loader.yml.class' => 'Symfony\\Component\\Routing\\Loader\\YamlFileLoader',
            'routing.loader.php.class' => 'Symfony\\Component\\Routing\\Loader\\PhpFileLoader',
            'router.options.generator_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'router.options.generator_base_class' => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
            'router.options.generator_dumper_class' => 'Symfony\\Component\\Routing\\Generator\\Dumper\\PhpGeneratorDumper',
            'router.options.matcher_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'router.options.matcher_base_class' => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
            'router.options.matcher_dumper_class' => 'Symfony\\Component\\Routing\\Matcher\\Dumper\\PhpMatcherDumper',
            'routing.resource' => 'C:\\wamp\\www\\Symfony-2.0\\app/config/routing_dev.yml',
            'validator.class' => 'Symfony\\Component\\Validator\\Validator',
            'validator.mapping.class_metadata_factory.class' => 'Symfony\\Component\\Validator\\Mapping\\ClassMetadataFactory',
            'validator.mapping.loader.loader_chain.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\LoaderChain',
            'validator.mapping.loader.static_method_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\StaticMethodLoader',
            'validator.mapping.loader.annotation_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\AnnotationLoader',
            'validator.mapping.loader.xml_file_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\XmlFileLoader',
            'validator.mapping.loader.yaml_file_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\YamlFileLoader',
            'validator.mapping.loader.xml_files_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\XmlFilesLoader',
            'validator.mapping.loader.yaml_files_loader.class' => 'Symfony\\Component\\Validator\\Mapping\\Loader\\YamlFilesLoader',
            'validator.mapping.loader.static_method_loader.method_name' => 'loadValidatorMetadata',
            'validator.validator_factory.class' => 'Symfony\\Bundle\\FrameworkBundle\\Validator\\ConstraintValidatorFactory',
            'validator.annotations.namespaces' => array(
                'validation' => 'Symfony\\Component\\Validator\\Constraints\\',
            ),
            'templating.engine.delegating.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\DelegatingEngine',
            'templating.engine.php.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\PhpEngine',
            'templating.loader.filesystem.class' => 'Symfony\\Component\\Templating\\Loader\\FilesystemLoader',
            'templating.loader.cache.class' => 'Symfony\\Component\\Templating\\Loader\\CacheLoader',
            'templating.loader.chain.class' => 'Symfony\\Component\\Templating\\Loader\\ChainLoader',
            'templating.helper.slots.class' => 'Symfony\\Component\\Templating\\Helper\\SlotsHelper',
            'templating.helper.assets.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\AssetsHelper',
            'templating.helper.actions.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\ActionsHelper',
            'templating.helper.router.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RouterHelper',
            'templating.helper.request.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\RequestHelper',
            'templating.helper.session.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\SessionHelper',
            'templating.helper.code.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\CodeHelper',
            'templating.helper.translator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\TranslatorHelper',
            'templating.helper.security.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\SecurityHelper',
            'templating.helper.form.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Helper\\FormHelper',
            'templating.assets.version' => NULL,
            'templating.assets.base_urls' => array(

            ),
            'templating.name_parser.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\TemplateNameParser',
            'templating.renderer.php.class' => 'Symfony\\Component\\Templating\\Renderer\\PhpRenderer',
            'debug.file_link_format' => NULL,
            'templating.debugger.class' => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\Debugger',
            'templating.loader.filesystem.path' => array(
                0 => 'C:\\wamp\\www\\Symfony-2.0\\app/views/%bundle%/%controller%/%name%.%renderer%.%format%',
                1 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Application/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%',
                2 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/Bundle/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%',
                3 => 'C:\\wamp\\www\\Symfony-2.0\\app/../src/vendor/symfony/src/Symfony/Bundle/%bundle%/Resources/views/%controller%/%name%.%renderer%.%format%',
            ),
            'templating.loader.cache.path' => NULL,
            'session.class' => 'Symfony\\Component\\HttpFoundation\\Session',
            'session.default_locale' => 'en',
            'session.storage.native.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\NativeSessionStorage',
            'session.storage.native.options' => array(
                'lifetime' => 3600,
            ),
            'session.storage.pdo.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\PdoSessionStorage',
            'session.storage.pdo.options' => array(

            ),
            'session.storage.array.class' => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\ArraySessionStorage',
            'session.storage.array.options' => array(

            ),
            'translator.class' => 'Symfony\\Bundle\\FrameworkBundle\\Translation\\Translator',
            'translator.identity.class' => 'Symfony\\Component\\Translation\\IdentityTranslator',
            'translator.selector.class' => 'Symfony\\Component\\Translation\\MessageSelector',
            'translation.loader.php.class' => 'Symfony\\Component\\Translation\\Loader\\PhpFileLoader',
            'translation.loader.yml.class' => 'Symfony\\Component\\Translation\\Loader\\YamlFileLoader',
            'translation.loader.xliff.class' => 'Symfony\\Component\\Translation\\Loader\\XliffFileLoader',
            'translator.fallback_locale' => 'en',
            'translation.resources' => array(
                0 => array(
                    0 => 'xliff',
                    1 => 'C:\\wamp\\www\\Symfony-2.0\\src\\vendor\\symfony\\src\\Symfony\\Bundle\\FrameworkBundle/Resources/translations\\validators.fr.xliff',
                    2 => 'fr',
                    3 => 'validators',
                ),
            ),
            'profiler.class' => 'Symfony\\Component\\HttpKernel\\Profiler\\Profiler',
            'profiler.storage.class' => 'Symfony\\Component\\HttpKernel\\Profiler\\SQLiteProfilerStorage',
            'profiler.storage.file' => 'C:\\wamp\\www\\Symfony-2.0\\app/cache/dev/profiler.db',
            'profiler.storage.lifetime' => 86400,
            'profiler_listener.class' => 'Symfony\\Component\\HttpKernel\\Profiler\\ProfilerListener',
            'profiler_listener.only_exceptions' => false,
            'data_collector.config.class' => 'Symfony\\Bundle\\FrameworkBundle\\DataCollector\\ConfigDataCollector',
            'data_collector.request.class' => 'Symfony\\Bundle\\FrameworkBundle\\DataCollector\\RequestDataCollector',
            'data_collector.security.class' => 'Symfony\\Component\\HttpKernel\\DataCollector\\SecurityDataCollector',
            'data_collector.exception.class' => 'Symfony\\Component\\HttpKernel\\DataCollector\\ExceptionDataCollector',
            'data_collector.events.class' => 'Symfony\\Component\\HttpKernel\\DataCollector\\EventDataCollector',
            'data_collector.logger.class' => 'Symfony\\Component\\HttpKernel\\DataCollector\\LoggerDataCollector',
            'data_collector.timer.class' => 'Symfony\\Bundle\\FrameworkBundle\\DataCollector\\TimerDataCollector',
            'data_collector.memory.class' => 'Symfony\\Component\\HttpKernel\\DataCollector\\MemoryDataCollector',
            'twig.class' => 'Twig_Environment',
            'twig.options' => array(
                'charset' => 'UTF-8',
                'debug' => true,
                'cache' => 'C:\\wamp\\www\\Symfony-2.0\\app/cache/dev/twig',
                'strict_variables' => true,
            ),
            'twig.loader.class' => 'Symfony\\Bundle\\TwigBundle\\Loader\\FilesystemLoader',
            'twig.globals.class' => 'Symfony\\Bundle\\TwigBundle\\GlobalVariables',
            'twig.form.resources' => array(
                0 => 'TwigBundle::form.twig.html',
            ),
            'templating.engine.twig.class' => 'Symfony\\Bundle\\TwigBundle\\TwigEngine',
            'debug.toolbar.class' => 'Symfony\\Bundle\\WebProfilerBundle\\WebDebugToolbarListener',
            'debug.toolbar.intercept_redirects' => true,
            'zend.logger.class' => 'Symfony\\Bundle\\ZendBundle\\Logger\\Logger',
            'zend.logger.priority' => NULL,
            'zend.logger.log_errors' => true,
            'zend.logger.writer.debug.class' => 'Symfony\\Bundle\\ZendBundle\\Logger\\DebugLogger',
            'zend.logger.writer.filesystem.class' => 'Zend\\Log\\Writer\\Stream',
            'zend.formatter.filesystem.class' => 'Zend\\Log\\Formatter\\Simple',
            'zend.formatter.filesystem.format' => '%timestamp% %priorityName%: %message%
',
            'zend.logger.path' => 'C:\\wamp\\www\\Symfony-2.0\\app/logs/dev.log',
            'kernel.compiled_classes' => array(
                0 => 'Symfony\\Component\\Routing\\RouterInterface',
                1 => 'Symfony\\Component\\Routing\\Router',
                2 => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcherInterface',
                3 => 'Symfony\\Component\\Routing\\Matcher\\UrlMatcher',
                4 => 'Symfony\\Component\\Routing\\Generator\\UrlGeneratorInterface',
                5 => 'Symfony\\Component\\Routing\\Generator\\UrlGenerator',
                6 => 'Symfony\\Component\\Routing\\Loader\\LoaderInterface',
                7 => 'Symfony\\Bundle\\FrameworkBundle\\Routing\\LazyLoader',
                8 => 'Symfony\\Component\\Templating\\DelegatingEngine',
                9 => 'Symfony\\Bundle\\FrameworkBundle\\Templating\\EngineInterface',
                10 => 'Symfony\\Component\\HttpFoundation\\Session',
                11 => 'Symfony\\Component\\HttpFoundation\\SessionStorage\\SessionStorageInterface',
                12 => 'Symfony\\Component\\HttpFoundation\\ParameterBag',
                13 => 'Symfony\\Component\\HttpFoundation\\HeaderBag',
                14 => 'Symfony\\Component\\HttpFoundation\\Request',
                15 => 'Symfony\\Component\\HttpFoundation\\Response',
                16 => 'Symfony\\Component\\HttpFoundation\\ResponseHeaderBag',
                17 => 'Symfony\\Component\\HttpKernel\\HttpKernel',
                18 => 'Symfony\\Component\\HttpKernel\\ResponseListener',
                19 => 'Symfony\\Component\\HttpKernel\\Controller\\ControllerResolver',
                20 => 'Symfony\\Component\\HttpKernel\\Controller\\ControllerResolverInterface',
                21 => 'Symfony\\Bundle\\FrameworkBundle\\RequestListener',
                22 => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerNameConverter',
                23 => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\ControllerResolver',
                24 => 'Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller',
                25 => 'Symfony\\Component\\EventDispatcher\\Event',
                26 => 'Symfony\\Component\\EventDispatcher\\EventDispatcher',
                27 => 'Symfony\\Bundle\\FrameworkBundle\\EventDispatcher',
                28 => 'Symfony\\Component\\Form\\FormConfiguration',
                56 => 'Twig_Environment',
                57 => 'Twig_ExtensionInterface',
                58 => 'Twig_Extension',
                59 => 'Twig_Extension_Core',
                60 => 'Twig_Extension_Escaper',
                61 => 'Twig_Extension_Optimizer',
                62 => 'Twig_LoaderInterface',
                63 => 'Twig_Loader_Filesystem',
                64 => 'Twig_Markup',
                65 => 'Twig_TemplateInterface',
                66 => 'Twig_Template',
            ),
        );
    }
}
