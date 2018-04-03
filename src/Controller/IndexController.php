<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

/**
 * Class IndexController
 * @package App\Controller
 */
class IndexController extends Controller
{
    /**
     * @Route(
     *     "/test.html",
     *      name="test_workflow"
     * )
     * @param Registry $workflows
     */
    public function edit(Registry $workflows)
    {
/*
        $post = new Box();
        $workflow = $workflows->get($post);

        // if there are multiple workflows for the same class,
        // pass the workflow name as the second argument
        // $workflow = $workflows->get($post, 'blog_publishing');

        var_dump($workflow->can($post, 'request_review'));
        var_dump($workflow->can($post, 'wait_for_manager'));
        var_dump($workflow->can($post, 'manager_approval'));
        var_dump($workflow->can($post, 'send'));


        // Update the currentState on the post
        try {
            $workflow->apply($post, 'request_review');
        } catch (LogicException $e) {
            print_r("ERROR 1");
        }
        // See all the available transitions for the post in the current state
        $transitions = $workflow->getEnabledTransitions($post);
        var_dump($transitions);

        // Update the currentState on the post
        try {
            $workflow->apply($post, 'wait_for_manager');
        } catch (LogicException $e) {
            print_r("ERROR 2");
        }
        // See all the available transitions for the post in the current state
        $transitions = $workflow->getEnabledTransitions($post);
        var_dump($transitions);

        // Update the currentState on the post
        try {
            $workflow->apply($post, 'send');
        } catch (LogicException $e) {
            print_r("ERROR 3");
        }
        // See all the available transitions for the post in the current state
        $transitions = $workflow->getEnabledTransitions($post);
        var_dump($transitions);

        exit();*/
    }
}
