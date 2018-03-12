<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * TODO ADD COLOR TO CONSOLE
 */

/**
 * Class UserRoleManager
 * @package App\Command
 */
class UserRoleManager extends Command
{
    private $eManager;
    private $output;

    /**
     * UserRoleManager constructor.
     * @param null|string $name
     * @param EntityManagerInterface $eManager
     */
    public function __construct(?string $name = null, EntityManagerInterface $eManager)
    {
        parent::__construct($name);
        $this->eManager = $eManager;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:changeUserRole')
            // the short description shown while running "php bin/console list"
            ->setDescription('Change user role.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to change an user role in database...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->output = $output;

        $this->output->writeln([
            "Welcome to User Role Manager",
            "============================",
        ]);

        while(true){

            // outputs multiple lines to the console (adding "\n" at the end of each line)
            $this->output->writeln([
                '',
                '<question>Select an action (default=1):</question>',
                '1 : display user list',
                '2 : add user role (requier user id)',
                '3 : remove user role (requier user id)',
                '0 : exit',
                '',
            ]);

            # get user input
            $input = trim(fgets(STDIN));

            # set default input if no input
            if ($input == "") {
                $input = '1';
            }

            switch ($input) {
                case "1":
                    $this->displayUserList();
                    break;
                case "2":
                    $this->addUserRole();
                    break;
                case "3":
                    $this->removeUserRole();
                    break;
                case "0":
                    exit();
                    break;
                default:
                    # ERROR COLOR
                    $this->output->writeln("<error>$input is not a valid selection</error>");
                    break;
            }
        }

        /*
        $output->writeln([
            "You selected " . $input
        ]);
        */

    }

    private function displayUserList(): void
    {
        $display = [];

        $userList = $this->eManager->getRepository(User::class)->findAll();


        foreach ($userList as $user) {


            $display[] = $user->getId() . "\t\t" . $user->getEmail() . "\t\t" . join("+", $user->getRoles());
        }

        $this->output->writeln([
            '',
            'USer List',
            '---------'
        ]);

        $this->output->writeln($display);

    }


    private function addUserRole()
    {

        $user = $this->getUserId();

        if(!$user){
            return;
        }

        $this->output->writeln([
            '',
            '<question>Add a role to the user :</question>',
            '1 : MARKETING',
            '2 : MANAGER',
            '3 : ADMIN',
            '0 : cancel',
            '',
        ]);

        # get user input
        $input = trim(fgets(STDIN));

        $role=null;

        while(!$role) {
            switch ($input) {
                case "1":
                    $role = "ROLE_MARKETING";
                    break;
                case "2":
                    $role = "ROLE_MANAGER";
                    break;
                case "3":
                    $role = "ROLE_ADMIN";
                    break;
                case "0":
                    return;
                    break;
                default:
                    # ERROR COLOR
                    $this->output->writeln("<error>$input is not a valid selection</error>");
                    break;
            }
        }

        if( $user->hasRole($role) ){
            # ERROR COLOR
            $this->output->writeln("<error>the user already has the role " .$role ."</error>");
            return;
        }

        # update user role
        $user->setRoles($role);
        # save role to database
        $this->eManager->flush();

        # OK COLOR
        $this->output->writeln("<info>the role '$role' has been added to used with id '".$user->getId()."'</info>");
    }

    private function removeUserRole()
    {
        $user = $this->getUserId();

        $this->output->writeln([
            '',
            '<question>remove a role from the user :</question>'
        ]);

        $userRoles=$user->getRoles();

        for($i=1;$i<count($userRoles);$i++){
            $this->output->writeln($i . ' : '.$userRoles[$i]);
        }

        if($i==1){
            $this->output->writeln("<error>User don't have role to remove' !</error>");
            return;
        }

        $this->output->writeln('0 : cancel');

        # get user input
        $input = trim(fgets(STDIN));

        if($input=="0"){
            return;
        }

        $removed=$userRoles[$input];

        unset($userRoles[$input]);

        # update user info
        $user->setAllRoles($userRoles);
        # save role to database
        $this->eManager->flush();

        # OK COLOR
        $this->output->writeln("<info>the role '$removed' has been removed to used with id '".$user->getId()."'</info>");

    }

    private function getUserId() : ?User
    {
        $this->output->writeln([
            '',
            '<question>Select an id user :</question>',
        ]);

        # get user input
        $input = trim(fgets(STDIN));


        $user = $this->eManager->getRepository(User::class)->findOneBy(['id'=>$input]);

        if(!$user){
            # ERROR COLOR
            $this->output->writeln("<error>User id '$input' not found !</error>");
            return null;
        }

        return $user;
    }
}
