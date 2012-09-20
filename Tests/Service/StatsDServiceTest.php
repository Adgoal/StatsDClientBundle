<?php

namespace Liuggio\StatsDClientBundle\Service {

    class dumpSocket
    {
        public static $socket;
        public static $message;
        public static $len;
        public static $flags;
        public static $host;
        public static $port;

        public static function clean() {
            dumpSocket::$socket = null;
            dumpSocket::$message = null;
            dumpSocket::$len = null;
            dumpSocket::$flags = null;
            dumpSocket::$host = null;
            dumpSocket::$port = null;

        }
    }

    function socket_sendto($socket, $message, $len, $flags, $host, $port)
    {
        dumpSocket::$socket = $socket;
        dumpSocket::$message = $message;
        dumpSocket::$len = $len;
        dumpSocket::$flags = $flags;
        dumpSocket::$host = $host;
        dumpSocket::$port = $port;
        return $len;
    }

}

namespace Liuggio\StatsDClientBundle\Tests\Service {

    use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

    use Liuggio\StatsDClientBundle\Service\StatsDClientService;
    use Liuggio\StatsDClientBundle\Entity\StatsData;

    class StatDServiceTest extends WebTestCase
    {
        public function testSend()
        {
            $host = 'buum';
            $port = 1;
            //building statService
            $statd = new StatsDClientService($host, $port, false);
            // building entity
            $entity = new StatsData();
            $entity->setKey('key');
            $entity->setValue('value|g');
            $msg = $entity->getMessage();

            $statd->send($entity);
            // we want to test that the socket sent that data.
            $this->assertEquals(\Liuggio\StatsDClientBundle\Service\dumpSocket::$message, $msg);
            $this->assertEquals(\Liuggio\StatsDClientBundle\Service\dumpSocket::$host, $host);
            $this->assertEquals(\Liuggio\StatsDClientBundle\Service\dumpSocket::$port, $port);
            // clean static file
            \Liuggio\StatsDClientBundle\Service\dumpSocket::clean();

        }

        public function testReduceCount()
        {
            $host = 'buum';
            $port = 1;
            $statd = new StatsDClientService($host, $port, false);

            $entity = new StatsData();
            $entity->setKey('key');
            $entity->setValue('1|c');
            $array[] = $entity;

            $entity = new StatsData();
            $entity->setKey('key');
            $entity->setValue('2|c');
            $array[] = $entity;

            $reducedMessage = array("key:1|c,key:2|c");

            $this->assertEquals($statd->reduceCount($array),$reducedMessage);
            \Liuggio\StatsDClientBundle\Service\dumpSocket::clean();
        }


    }
//namespace bracket
}