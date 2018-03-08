<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use AppBundle\Entity\Client;
use AppBundle\Entity\Phone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Yaml\Yaml;


class LoadingDatasCommand extends ContainerAwareCommand
{
    private $em;
    private $encoder;

    public function __construct($name = null, UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        parent::__construct($name);
        $this->encoder=$encoder;
        $this->em=$em;
    }

    protected function configure()
    {
        $this
            ->setName('app:import-fixtures')
            ->setDescription('Imports datas into Client, User and Phone tables');
    }

    public function getDatas($entity)
    {
        $fixturesPath = $this->getContainer()->getParameter('fixtures_directory');
        $fixtures = Yaml::parse(file_get_contents( $fixturesPath.'/Fixtures'.$entity.'.yml', true));
        return $fixtures;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emi = $this->getContainer()->get('doctrine.orm.entity_manager');
        $ClientRepo = $emi->getRepository('AppBundle:Client');

        # Phones
        $phone = $this->getDatas('Phone');
        foreach ($phone['Phone'] as $reference => $column)
        {
            $phone = new Phone();
            $phone->setBrand($column['brand']);
            $phone->setModel($column['model']);
            $phone->setReference($column['ref']);
            $phone->setOpSystem($column['opsyst']);
            $phone->setStorage($column['storage']);
            $phone->setColor($column['color']);
            $phone->setDescription($column['descr']);
            $emi->persist($phone);
        }
        $emi->flush();

        # Clients
        $client = $this->getDatas('Client');

        foreach ($client['Client'] as $reference => $column)
        {
            $client = new Client();
            $client->setName($column['name']);
            $client->setRandomId($column['randomid']);
            $client->setSecret($column['secret']);
            $client->setAllowedGrantTypes($column['type']);
            $emi->persist($client);
        }
        $emi->flush();

        # Users
        $user = $this->getDatas('User');

        foreach ($user['User'] as $reference => $column)
        {
            $user = new User();
            $user->setUsername($column['username']);
            $linkedClient = $ClientRepo->find($column['client']);
            $user->setClient($linkedClient);
            $user->setEmail($column['email']);
            $encoded = $this->encoder->encodePassword($user, $column['password']);
            $user->setPassword($encoded);
            $user->setRoles($column['roles']);
            $emi->persist($user);
        }
        $emi->flush();
    }
}
