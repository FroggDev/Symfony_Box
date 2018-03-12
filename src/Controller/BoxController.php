<?php
namespace App\Controller;

use App\Common\Traits\MailerTrait;
use App\Form\User\BoxSubscribeType;
use App\SiteConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BoxController
 * @package App\Controller
 */
class BoxController extends Controller
{
    use MailerTrait;


    /**
     * BoxController constructor.
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer=$mailer;
    }

    /**
     * @Route(
     *     "/box/subscribe.html",
     *      name="box_subscribe"
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function boxSubscribe(Request $request): Response
    {

        # New user registration
        $user = $this->getUser();

        # create the user form
        $form = $this->createForm(BoxSubscribeType::class, $user);

        # post data manager
        $form->handleRequest($request);

        # check form datas
        if ($form->isSubmitted() && $form->isValid()) {

            # get datas
            $user = $form->getData();

            if ($user->getHasSubscribe()) {
                $type = 'subscribe';
            } else {
                $type = 'unsubscribe';
            }

            # insert into database
            $eManager = $this->getDoctrine()->getManager();
            $eManager->flush();

            # send the mail
            $this->send(SiteConfig::SITEEMAIL, $user->getEmail(), 'mail/box' . $type . '.html.twig', SiteConfig::SITENAME . ' - Validation mail', $user);

            /**
             * TODO send to MARKETING A NEW USER HAS UN/SUBSCRIBE !
             */

            # redirect user
            return $this->redirectToRoute('index_member', [$type => 'success']);
        }

        # Display form view
        return $this->render('form/boxsubscribe.html.twig', [
            'form' => $form->createView()
        ]);

    }
}