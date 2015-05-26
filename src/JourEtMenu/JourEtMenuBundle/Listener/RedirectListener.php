<?php
namespace JourEtMenu\JourEtMenuBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RedirectListener 
{
    public function __construct(ContainerInterface $container)
    {
        $this->router = $container->get('router');
        $this->securityContext = $container->get('security.context');
    }
    
    public function onKernelRequest(GetResponseEvent $event)
    {
        $route = $event->getRequest()->attributes->get('_route');
        
        
           if ($route=='fos_user_registration_confirmed'){
        
            if ($this->securityContext->getToken()->getUser()->getType()=="clients") {
                $event->setResponse(new RedirectResponse($this->router->generate('acceuil_clients')));
            }
            else {
            	
            	$event->setResponse(new RedirectResponse($this->router->generate('acceuil_restaurateurs')));
            	
            }
           }
         
    }
}