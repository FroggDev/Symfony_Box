<?php

namespace App\Controller;

use App\Entity\Box;
use App\Exception\UndefinedRoleException;
use App\SiteConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoggedController
 * @package App\Controller
 */
class LoggedController extends Controller
{
    /**
     * User logged in
     * @Route (
     *     "/register/logged.html",
     *      name="index_logged"
     *     )
     * @return Response
     */
    public function logged()
    {
        # get connected user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        # get route based on role
        switch (true) {
            case $user->hasRole('ROLE_ADMIN'):
                $route = "admin";
                break;
            case $user->hasRole('ROLE_MANAGER'):
                $route = "admin";
                break;
            case $user->hasRole('ROLE_MARKETING'):
                $route = "admin";
                break;
            case $user->hasRole('ROLE_MEMBER'):
                $route = "member";
                break;

            default:
                throw new UndefinedRoleException();
                break;
        }

        # redirect user
        return $this->redirectToRoute("index_" . $route);
    }

    /**
     * @Route(
     *     "/member/index.html",
     *      name="index_member"
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexMember(): Response
    {
        # Display index
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route(
     *     "/admin/index.html",
     *      name="index_admin",
     *      requirements={"currentPage" : "\d+"},
     *      defaults={"currentPage"="1"},
     *      methods={"GET"}
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAdmin(string $currentPage): Response
    {

        # FROM DOCTRINE
        # get repo category
        $repositoryBox = $this->getDoctrine()->getRepository(Box::class);

        # get category from category
        $boxes = $repositoryBox->findAll();

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
}