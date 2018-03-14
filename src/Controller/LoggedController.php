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
}