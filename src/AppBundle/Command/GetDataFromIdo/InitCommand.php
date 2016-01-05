<?php
namespace AppBundle\Command\GetDataFromIdo;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\DbObject\ReportDbConnector;
use AppBundle\Command\GetDataFromIdo\DataObject;
use PDO;

class InitCommand extends ContainerAwareCommand{
    //put your code here
    protected function configure() {
        $this
            ->setName('ido:init');
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $db = new ReportDbConnector();
        $statement = "SELECT service_id FROM report_perf_object_definition";
        $query = $db->prepare($statement);
        $query->execute();
        while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
            $Obj = new DataObject($result["service_id"]);
            $Obj->grabData();
        }
    }
}
