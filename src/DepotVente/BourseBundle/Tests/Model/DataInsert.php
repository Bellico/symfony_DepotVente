<?php

namespace DepotVente\BourseBundle\Tests\DataInsert;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DataFixturesTest extends WebTestCase {

    public function __construct() {

        $kernelNameClass = $this->getKernelClass();
        $kernel = new $kernelNameClass('test', true);
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testPurgeData() {
        $em = $this->getDoctrine()->getManager();
       $query =0;
       $this->assertEmpty($query->fetchAll());
    }

}