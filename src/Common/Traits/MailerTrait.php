<?php
namespace App\Common\Traits;

/**
 * Trait MailerTrait
 * @package App\Common\Traits
 */
trait MailerTrait
{
    /**
     * @var \Swift_Mailer $mailer $mailer
     */
    private $mailer;

    private $templating;

    /**
     * @param string $from
     * @param string $to
     * @param string $template
     * @param string $subject
     * @param $data
     *
     * @see https://symfony.com/doc/current/email/dev_environment.html
     * @see  Controller & injection __construct(\Swift_Mailer $mailer)
     */
    public function send(string  $from, string $to, string $template, string $subject, $data)
    {

        if (method_exists($this, 'renderView')) {
            $render = $this->renderView(
                // templates/emails/registration.html.twig
                $template,
                array('data' => $data)
            );
        } else {
            $render = $this->templating->render(
                // templates/emails/registration.html.twig
                $template,
                array('data' => $data)
            );
        }

        $message = (new \Swift_Message($subject))
            ->setFrom($from)
            ->setTo($to)
            ->setBody($render, 'text/html')
            /*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    array('name' => $name)
                ),
                'text/plain'
            )
            */
        ;

        $this->mailer->send($message);
    }
}
