<?php
    
    namespace App\EventSubscriber;
    
    use Symfony\Bundle\FrameworkBundle\Console\Application;
    use Symfony\Component\Console\Input\ArrayInput;
    use Symfony\Component\Console\Output\NullOutput;
    use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpKernel\Event\RequestEvent;
    use Symfony\Component\HttpKernel\KernelEvents;
    use Symfony\Component\HttpKernel\KernelInterface;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    
    class WebsiteConfiguredSubscriber implements EventSubscriberInterface
    {
        
        private $params;
        private $router;
        private $kernelInterface;
        
        public function __construct(ParameterBagInterface $params, UrlGeneratorInterface $router, KernelInterface $kernelInterface)
        {
            $this->params = $params;
            $this->router = $router;
            $this->kernelInterface = $kernelInterface;
        }
        
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::REQUEST => 'onRequest',
            ];
        }
        
        public function onRequest(RequestEvent $event)
        {
            if ($this->params->get('is_website_configured') == '0' && strpos($event->getRequest()->getPathInfo(), 'install/install.php') === false && strpos($event->getRequest()->getPathInfo(), '_wdt') === false) {
                $this->warmUpCaches();
                $event->setResponse(new RedirectResponse($this->router->generate('installer_install')));
            }
        }
        
        public function warmUpCaches()
        {
            $application = new Application($this->kernelInterface);
            $application->setAutoExit(false);
            
            $wampupCacheProd = new ArrayInput([
                'command' => 'cache:warmup',
                'env' => 'prod',
            ]);
            $outputWampupCacheProd = new NullOutput();
            $application->run($wampupCacheProd, $outputWampupCacheProd);
            
            $wampupCacheDev = new ArrayInput([
                'command' => 'cache:warmup',
                'env' => 'dev',
            ]);
            $outputWampupCacheDev = new NullOutput();
            $application->run($wampupCacheDev, $outputWampupCacheDev);
        }
        
    }
    
    