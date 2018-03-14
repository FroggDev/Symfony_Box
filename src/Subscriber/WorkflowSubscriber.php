<?php

namespace App\Subscriber;

use App\Common\Traits\MailerTrait;
use App\Entity\Box;
use App\SiteConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Workflow\Event\Event;

/**
 * Class WorkflowSubscriber
 * @package App\Subscriber
 */
class WorkflowSubscriber implements EventSubscriberInterface
{

    use MailerTrait;

    /**
     * SecurityController constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param Event $event
     */
    public function onTransition(Event $event)
    {
        $box = $event->getSubject();

        switch ($event->getTransition()->getName()) {
            case "product_request":
                $this->providerMailOk($box);
                break;

            case "marketing_approval":
                $this->managerMailOk($box);
                break;

            case "manager_approval":
                $this->userMailok($box);
                break;

            case "cancel_products_request":
                $this->marketingMailCancel($box);
                break;

            case "cancel_products_validation":
                $this->providerMailCancel($box);
                break;
        }
    }


    /**
     * @param Event $event
     */
    public function providerMailOk(Box $box)
    {
         $this->sendMail(SiteConfig::MAILMARKETING, SiteConfig::MAILPROVIDER, 'Products list request', 'providerMailOk', $box);
    }

    /**
     * @param Event $event
     */
    public function managerMailOk(Box $box)
    {
        $this->sendMail(SiteConfig::MAILMARKETING, SiteConfig::MAILMANAGER, 'Products list are requested from the provider', 'managerMailOk', $box);
    }

    /**
     * @param Event $event
     */
    public function userMailok(Box $box)
    {
        /**
         * TODO : SEND MAIL TO USERS
         */
        //$this->sendMail(SiteConfig::MAILMARKETING,SiteConfig::MAILMANAGER,'Products list request','providerMailOk');
    }

    /**
     * @param Event $event
     */
    public function marketingMailCancel(Box $box)
    {
        $this->sendMail(SiteConfig::MAILMARKETING, SiteConfig::MAILMARKETING, 'Provider cannot deliver all products', 'marketingMailCancel', $box);
    }

    /**
     * @param Event $event
     */
    public function providerMailCancel(Box $box)
    {
        $this->sendMail(SiteConfig::MAILMANAGER, SiteConfig::MAILMARKETING, 'Some products are not conform', 'managerMailCancel', $box);
    }

    /**
     * @param string $from
     * @param $to
     * @param $type
     * @param null $data
     */
    private function sendMail(string $from, string $to, string $title, string $type, $data = null)
    {
        # send the mail
        $this->send($from, $to, 'mail/workflow/' . $type . '.html.twig', SiteConfig::SITENAME . ' - ' . $title, $data);
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'workflow.transition' => 'onTransition',
        ];
    }
}
