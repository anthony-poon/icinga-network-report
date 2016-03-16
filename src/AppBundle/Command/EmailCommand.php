<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;
use Swift_Attachment;
use Swift_Message;
use AppBundle\DbObject\ReportDbConnector;

class EmailCommand extends ContainerAwareCommand {
    //put your code here
    private static $db;

    protected function configure() {
        $this
            ->setName("report:mail")
            ->setDefinition(new InputDefinition(array(
                new InputArgument('dest', InputArgument::IS_ARRAY),
            )));
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        //allow_url_fopen must be open in php.ini
        if (empty(self::$db)) {
            self::$db = new ReportDbConnector();
        }
        $dest = $input->getArgument("dest");
        $mail = Swift_Message::newInstance()->setSubject('Network report')
        ->setFrom('root@asia-minerals.com')
        ->setTo($dest)
        ->setBody('As attached');
		
        $reportList = self::$db->getAllReportGroupId();
		// Need to add http 404 or timeout
        foreach ($reportList as $groupId => $filename) {
            $mail->attach(Swift_Attachment::fromPath("http://ubuntu-test/report/web/app_dev.php/perf?group=$groupId")->setFilename("$filename.xlsx"));
        }
        $mail->attach(Swift_Attachment::fromPath("http://ubuntu-test/report/web/app_dev.php/ava_report")->setFilename("availability_report.xlsx"));
        $this->getContainer()->get('mailer')->send($mail);
    }
}
