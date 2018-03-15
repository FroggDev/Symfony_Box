<?php

namespace App\Controller;

use App\Common\Traits\MailerTrait;
use App\Entity\Box;
use App\Entity\Product;
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
        $this->mailer = $mailer;
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

        # Get user
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
     *     "/admin/{search}/{currentPage}.html",
     *      name="index_admin",
     *      requirements={"currentPage" : "\d+"},
     *      defaults={"currentPage"="1","search"="fulllist"},
     *      methods={"GET"}
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function boxList(Registry $workflows, string $currentPage, string $search): Response
    {

        # FROM DOCTRINE
        # get repo category
        $repositoryBox = $this->getDoctrine()->getRepository(Box::class);

        # get category from category
        $boxes = $repositoryBox->findAll();

        $tmpboxes = $this->getMyBoxes($workflows, $boxes);


        # Apply search filter
        if ($search != "fulllist" && $search != "atwork") {
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

        # Apply search filter
        if ($search == "atwork") {
            $boxes = $tmpboxes;

            //externalized for more options
            /*
            # search in array
            $boxes = array_filter(
                $boxes,
                function (Box $box) use ($workflows) {
                    $workflow = $workflows->get($box);
                    return
                     ($workflow->can($box, 'product_request')) ||
                     ($workflow->can($box, 'marketing_approval')) ||
                     ($workflow->can($box, 'manager_approval'));
                }
            );
            */
        }

        # get number of elenmts
        $countBox = count($boxes);

        # get only wanted articles
        $boxes = array_slice($boxes, ($currentPage - 1) * SiteConfig::NBBOXPERPAGE, SiteConfig::NBBOXPERPAGE);

        # number of pagination
        $countPagination = ceil($countBox / SiteConfig::NBBOXPERPAGE);

        # display page from twig template
        return $this->render('user/admin.html.twig', [
            'boxes' => $boxes,
            'currentPage' => $currentPage,
            'countPagination' => $countPagination,
            'countBox' => count($tmpboxes),
            'search' => $search
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
    public function boxEdit(string $id, Request $request): Response
    {
        //check if box exist
        $box = $this
            ->getDoctrine()
            ->getRepository(Box::class)
            ->findOneBy(['id' => $id]);

        if ($box) {
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


            ###
            # PRODUCTS
            ###
            # FROM DOCTRINE
            # get repo category
            $repositoryProduct = $this->getDoctrine()->getRepository(Product::class);
            # Recover products ID
            $products = [];
            $productIds = explode(',', filter_input(INPUT_POST, 'products'));
            foreach ($productIds as $id) {
                $tmp = $repositoryProduct->findOneByIdFromProvider(intval($id));
                if ($tmp) {
                    $products[] = $tmp;
                }
            }
            $box->setProducts($products);
            ###
            # END PRODUCTS
            ###

            ###
            # IMAGE
            ###
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
            } else {
                if (isset($featuredImageSave)) {
                    $box->setFeaturedImage($featuredImageSave);
                }
            }
            ###
            # ENDIMAGE
            ###

            if ($box->getPrice()>SiteConfig::MAXPRICE) {
                return $this->redirectToRoute('box_edit', ['id' => $box->getId(),'error' =>'price']);
            }

            # insert into database
            try {
                $eManager = $this->getDoctrine()->getManager();
                $eManager->persist($box);
                $eManager->flush();
            } catch (\Exception $e) {
                /**
                 * @TODO : FIX THE PERSIST CASCADE BUG !
                 */

                VarDumper::dump($e);
            }

            # redirect user
            return $this->redirectToRoute('box_workflow', ['id' => $box->getId()]);
        }

        # get repo category
        $repositoryProduct = $this->getDoctrine()->getRepository(Product::class);

        # get products and box products
        $boxProducts = $box ? $box->getProducts()->getValues() : [];
        $products = array_udiff($repositoryProduct->findAllFromProvider(), $boxProducts, 'Self::compareProducts');


        # Display form view
        return $this->render(
            'form/boxedit.html.twig',
            [
                'form' => $form->createView(),
                'products' => $products,
                'boxproducts' => $boxProducts
            ]
        );
    }

    /**
     * @param Registry $workflows
     * @param array $boxes
     * @return array
     */
    private function getMyBoxes(Registry $workflows, array $boxes)
    {

        $tmpBoxes = [];

        foreach ($boxes as $box) {
            $workflow = $workflows->get($box);
            if (($workflow->can($box, 'product_request')) ||
                ($workflow->can($box, 'marketing_approval')) ||
                ($workflow->can($box, 'manager_approval'))) {
                $tmpBoxes[] = $box;
            }
        }

        return $tmpBoxes;
    }

    /**
     * @param $products1
     * @param $products2
     * @return mixed
     */
    private function compareProducts($products1, $products2)
    {
        return $products1->getId() - $products2->getId();
    }
}
