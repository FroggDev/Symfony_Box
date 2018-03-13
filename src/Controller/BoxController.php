<?php
namespace App\Controller;

use App\Common\Traits\MailerTrait;
use App\Entity\Box;
use App\Form\Admin\BoxCreateType;
use App\Form\User\BoxSubscribeType;
use App\SiteConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

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
     * @param Request $request
     * @return Response
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


    /**
     * @Route(
     *     "/admin/box/create.html",
     *      name="box_create"
     * )
     * @param Request $request
     * @return Response
     */
    public function boxCreate(Request $request): Response
    {

        # New box
        $box = new Box();

        # create the user form
        $form = $this->createForm(BoxCreateType::class, $box);

        # post data manager
        $form->handleRequest($request);

        # check form datas
        if ($form->isSubmitted() && $form->isValid()) {

            # get datas
            $box = $form->getData();

            # get the image file
            $featuredImage = $box->getFeaturedImage();

            # Only if image exist
            if ($featuredImage) {
                # !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                # get file name DO NOT FORGET TO ENABLE extension=php_fileinfo.dll
                # !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                $fileName = $box->getNameSlugified() . '.' . $featuredImage->guessExtension();

                # move uploaded file
                $featuredImage->move(
                    $this->getParameter('app.boxes.assets.dir'),
                    $fileName
                );

                # update image name
                $box->setFeaturedImage($fileName);
            }

            # insert into database
            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($box);
            $eManager->flush();

            # redirect user
            return $this->redirectToRoute('box_workflow',['id'=>$box->getId()]);
        }

        # Display form view
        return $this->render('form/boxcreate.html.twig', [
            'form' => $form->createView()
        ]);

    }


    /**
     * @Route(
     *     "/admin/box/workflow/{id}.html",
     *      name="box_workflow"
     * )
     * @param Request $request
     * @return Response
     */
    public function boxWorkflow(Box $box,Request $request,Registry $workflows): Response
    {
        # Display box workflow
        return $this->render('form/boxworkflow.html.twig', ['box' => $box]);
    }

    /*

            VarDumper::dump($box);


        $workflow = $workflows->get($box);
        VarDumper::dump($box->getWorkflowStatus());

        // if there are multiple workflows for the same class,
        // pass the workflow name as the second argument
        // $workflow = $workflows->get($post, 'blog_publishing');
        VarDumper::dump($workflow->can($box, 'product_request'));  // True
        VarDumper::dump($workflow->can($box, 'marketing_approval'));// False
        VarDumper::dump($workflow->can($box, 'manager_approval'));  // False


        if($workflow->can($box, 'product_request')){
            try {
                $workflow->apply($box, 'product_request');
            } catch (LogicException $e) {
                VarDumper::dump("ERROR");
            }
        }

        if($workflow->can($box, 'marketing_approval')){
            try {
                $workflow->apply($box, 'marketing_approval');
            } catch (LogicException $e) {
                VarDumper::dump("ERROR");
            }
        }

if($workflow->can($box, 'manager_approval')){
try {
$workflow->apply($box, 'manager_approval');
} catch (LogicException $e) {
    VarDumper::dump("ERROR");
}
        }


        # insert into database
        $eManager = $this->getDoctrine()->getManager();
        $eManager->persist($box);
        $eManager->flush();

        VarDumper::dump($box->getWorkflowStatus());

        exit();
     *
     *
     * */



}