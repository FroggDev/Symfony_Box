<?php

namespace App\Controller;

use App\Common\Traits\MailerTrait;
use App\Exception\UndefinedRoleException;
use App\Form\User\BoxSubscribeType;
use App\SiteConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
                $route = "manager";
                break;
            case $user->hasRole('ROLE_MARKETING'):
                $route = "marketing";
                break;
            # A VIRER ??
            # case $user->hasRole('ROLE_PROVIDER'):   $route = "provider";   break;
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
     *     "/marketing/index.html",
     *      name="index_marketing"
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexMarketing(): Response
    {
        # Display index
        return $this->render('user/marketing.html.twig');
    }

    /**
     * @Route(
     *     "/provider/index.html",
     *      name="index_provider"
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexProvider(): Response
    {
        # Display index
        return $this->render('user/provider.html.twig');
    }

    /**
     * @Route(
     *     "/manager/index.html",
     *      name="index_manager"
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexManager(): Response
    {
        # Display index
        return $this->render('user/manager.html.twig');
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
        return $this->render('user/member.html.twig');
    }

    /**
     * @Route(
     *     "/admin/index.html",
     *      name="index_admin"
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAdmin(): Response
    {
        # Display index
        return $this->render('user/admin.html.twig');
    }

}