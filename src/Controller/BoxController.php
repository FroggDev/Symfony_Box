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
use Symfony\Component\VarDumper\VarDumper;
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
     *     "/admin/index/{search}/{currentPage}.html",
     *      name="index_admin",
     *      requirements={"currentPage" : "\d+"},
     *      defaults={"currentPage"="1","search"="fulllist"},
     *      methods={"GET"}
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAdmin(string $currentPage,string $search): Response
    {

        # FROM DOCTRINE
        # get repo category
        $repositoryBox = $this->getDoctrine()->getRepository(Box::class);

        # get category from category
        $boxes = $repositoryBox->findAll();

        # Apply search filter
        if($search!="fulllist"){
            # search in array
            $boxes = array_filter(
                $boxes,
                function (Box $box) use ($search) {
                    return (
                        preg_match("/$search/i", $box->getName())
                        ||
                        preg_match("/$search/i", $box->getDescription())
                    );
                }
            );
        }

        # get number of elenmts
        $countBox =count($boxes);

        # get only wanted articles
        $boxes = array_slice($boxes, ($currentPage-1) * SiteConfig::NBBOXPERPAGE, SiteConfig::NBBOXPERPAGE);

        # number of pagination
        $countPagination =  ceil($countBox / SiteConfig::NBBOXPERPAGE);

        # display page from twig template
        return $this->render('user/admin.html.twig', [
            'boxes' => $boxes,
            'currentPage' => $currentPage,
            'countPagination' => $countPagination
        ]);
    }

    /**
     * @Route(
     *     "/admin/box/edit/{id}.html",
     *      name="box_edit",
     *     defaults={"id"="0"}
     * )
     * @param Request $request
     * @return Response
     */
    public function boxEdit(string $id , Request $request): Response
    {
        //check if box exist
        $box = $this
            ->getDoctrine()
            ->getRepository(Box::class)
            ->findOneBy(['id'=>$id]);

        if($box){
            $featuredImageSave = $box->getFeaturedImage();
        }


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

                VarDumper::dump("PASSING HERE ???");

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
            }else{
                if(isset($featuredImageSave)) {
                    $box->setFeaturedImage($featuredImageSave);
                }
            }

            # insert into database
            $eManager = $this->getDoctrine()->getManager();
            $eManager->persist($box);
            $eManager->flush();

            # redirect user
            return $this->redirectToRoute('box_workflow',['id'=>$box->getId()]);
        }

        # Display form view
        return $this->render('form/boxedit.html.twig', [
            'form' => $form->createView()
        ]);

    }

}