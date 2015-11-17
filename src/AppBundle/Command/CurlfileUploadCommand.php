<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\OutputInterface;

class CurlfileUploadCommand extends ContainerAwareCommand {
    //put your code here
    const TARGET_URL = "anthonypoon.net/app_dev.php/upload";
    protected function configure() {
        $this
            ->setName("report:post");
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $urlArray = array(
            "availability_report" => "http://localhost/report/web/app_dev.php/ava_report",
            "207_server_report" => "http://localhost/report/web/app_dev.php/perf?group=1",
            "windows_server_report" => "http://localhost/report/web/app_dev.php/perf?group=2",
            "linux_server_report" => "http://localhost/report/web/app_dev.php/perf?group=3",
            "other_component_report" => "http://localhost/report/web/app_dev.php/perf?group=5",
            "ubuntu14_report" => "http://localhost/report/web/app_dev.php/perf?group=6"
        );
        $downloadPath = array();
        foreach ($urlArray as $fileName => $url) {
            $downloadPath[$fileName] = $this->downloadReport($url, $fileName);
        }
        $output = $this->postFile($downloadPath);
        echo $output["content"];
    }
    
    private function downloadReport($url, $fileName) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $rawData = curl_exec($ch);
        $error = curl_error($ch); 
        curl_close ($ch);
        $destination = __DIR__."/../tmp/$fileName.xlsx";
        $file = fopen($destination, "w+");
        fputs($file, $rawData);
        fclose($file);
        if (empty($error)) {
            return "@".$destination;
        } else {
            return $error;
        }
    }
    
    private function postFile($path) {
        //$post = array('extra_info' => '123456','file_contents'=>'@'.$path);
        $path = array_merge($path, array("password" => "this is a very hard password."));
        $options = array(
            CURLOPT_CUSTOMREQUEST  =>"POST",        //set request type post or get
            CURLOPT_POST           =>true,        //set to GET
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => true,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_POSTFIELDS      => $path
        );
        
        $ch = curl_init(self::TARGET_URL);
        curl_setopt_array($ch, $options);
        $output = array(
            "content" => curl_exec($ch),
            "err" => curl_errno($ch),
            "errMsg" => curl_error($ch),
            "header" => curl_getinfo($ch)
        );
        curl_close($ch);
        return $output;
    }
}
