<?php

namespace App\Service\Twig\Entity;


use App\Entity\Box;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

/**
 * Class WorkflowAppRuntime
 * @package App\Service\Twig\Entity
 */
class WorkflowAppRuntime
{
    private $token_storage;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * AppRuntime constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, $token_storage)
    {
        $this->router = $router;
        $this->token_storage = $token_storage;
    }


    /**
     * @param Registry $workflow
     * @param Box $box
     */
    public function displayWorkflowNotification(Registry $workflows)
    {

        $user = $this->token_storage->getToken()->getUser();

        /**
         * TODO search in database all pending action linked to role !
         */

        //echo ',<br/>';
    }


    /**
     * @param Registry $workflow
     * @param Box $box
     */
    public function workflowLinks(Registry $workflows, Box $box)
    {
        $workflow = $workflows->get($box);

        $user = $this->token_storage->getToken()->getUser();

        if ((
                $workflow->can($box, 'product_request')
                && $box->getWorkflowStatus() == "box_creation")
            || $user->hasRole('ROLE_ADMIN')) {
            echo "<a href = \"" . $this->router->generate('box_edit', ['id' => $box->getId()]) . "\" class=\"form-control\" >Edit box </a >";
        }

        if ($workflow->can($box, 'cancel_' . $box->getWorkflowStatus())) {
            echo "<a href = \"" . $this->router->generate('workflow_action', ['id' => $box->getId(), 'action' => 'cancel_' . $box->getWorkflowStatus()]) . "\" class=\"form-control\" >Unvalidate current workflow state</a >";
        }

        if ($workflow->can($box, 'product_request')) {
            echo "<a href = \"" . $this->router->generate('workflow_action', ['id' => $box->getId(), 'action' => 'product_request']) . "\" class=\"form-control\" >Box products selection validation</a >";
        }

        if ($workflow->can($box, 'marketing_approval')) {
            echo "<a href = \"" . $this->router->generate('workflow_action', ['id' => $box->getId(), 'action' => 'marketing_approval']) . "\" class=\"form-control\" >Box Products available from provider validation</a >";
        }

        if ($workflow->can($box, 'manager_approval')) {
            echo "<a href = \"" . $this->router->generate('workflow_action', ['id' => $box->getId(), 'action' => 'manager_approval']) . "\" class=\"form-control\" >Box Products are conform and ready to be sent validation</a >";
        }

        if ($box->getWorkflowStatus() == "sent") {
            $boxName = $box->getName();
            echo <<<EOT
            <br/>
            <div class="alert alert-success">
                <p class="text-center"><i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i></p>
                <p class="text-center">Congratulation the SutekinaBox '$boxName' bas been sent !
            </div>
EOT;
        }
    }
}