<?php

namespace Sm\CoreBundle\EventListener;

use Symfony\Component\Translation\IdentityTranslator;

use Symfony\Component\HttpKernel\KernelEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Symfony\Component\HttpKernel\Profiler\Profiler;

class MessagesListener
{
    private $translator;

    public function __construct(IdentityTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        // do not capture redirects or modify XML HTTP Requests
        if (!$request->isXmlHttpRequest()) {
            return;
        }
        $response = $event->getResponse();

        if ('json' == $request->getRequestFormat()) {

            $flashBag = $request->getSession()->getFlashBag();

            $content = json_decode($response->getContent(), true);

            $flashBag = $flashBag->all();

            if ($flashBag) {

                foreach ($flashBag as $type => $messages) {
                    foreach ($messages as $i => $message) {
                        $flashBag[$type][$i] = $this->translator->trans($message);
                    }
                }
                $content['flashbag'] = $flashBag;
                $response->setContent(json_encode($content));
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -1000),
        );
    }
}
