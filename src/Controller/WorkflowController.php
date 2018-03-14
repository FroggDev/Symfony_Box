<?php
namespace App\Controller;


use App\Entity\Box;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Registry;

/**
 * Class WorkflowController
 * @package App\Controller
 */
class WorkflowController extends Controller
{

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
        return $this->render('form/boxworkflow.html.twig', ['box' => $box , 'workflow' => $workflows]);
    }

    /**
     * @Route(
     *     "/admin/workflow/{action}/{id}.html",
     *      name="workflow_action"
     * )
     * @param Request $request
     * @return Response
     */
    public function workflowAction(string $action, Box $box,Registry $workflows): Response
    {
        if(!$box){
            /**
             * @TODO AN ERROR HERE
             */
        }

        $workflow = $workflows->get($box);

        # Do the action
        # -------------
        if ($workflow->can($box, $action)) {
            try {

                $workflow->apply($box, $action);

                # insert into database
                $eManager = $this->getDoctrine()->getManager();
                $eManager->flush();


            } catch (LogicException $e) {
                /**
                 * @TODO AN ERROR HERE
                 */
            }
        }
        else{
            /**
             * @TODO AN ERROR HERE
             */
        }


        # Display box workflow
        return $this->render('form/boxworkflow.html.twig', ['box' => $box,'workflow' => $workflows]);
    }

}