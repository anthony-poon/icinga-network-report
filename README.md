Icinga Network Report
======

A Symfony project created on September 21, 2015, 9:38 am.
I wrote this project for my company to monitor network performance. The monitoring itself is done my Icinga2, a netowrk monitor program forked from Nagios. Performance data is inserted into MySql database using IDOUtils, a plugin provided by Icinga also.

My program capture data from Icinga's database every 15 min using crontab and PHP scripts. It will output an availability and performance report via HTTP request. Another cron job will run every 10:00 PM everyday to send the report via Symfony's mailer.

Structure

*src
  *AppBundle
    *Command
      - CurlUploadCommand.php => a PHP script that will upload the xlsx report via HTTP to my private server using cURL
      - EmailCommand.php => PHP script that is run by crontab to email every 10:00 PM
        Controller
      - PerDataReportController.php => Symfony controller that call classes in Model/Performance to output performance report
      - ReportController.php => Symfony controller that call classes in Model/Availability to output availability report
    DbObject
      - ClassicDbConnector.php => Predefined PDO object to connect to self database
      - IcingaDbConnector.php => Predefinded PDO object to connect to Icinga's database
    IdoDataGrabber
      - PHP scripts that run seperate from Symfony. Run directly from command line / crontab. Grab data from Icinga and insert into self db
        To Do:
          write into Symfony Cli.
    Model
      Contains all the data/model object
        - Availability => Data object for availability report
        - Performance => Data object for performance report
          PerfDataHandle => Parser that convert Nagios perfdata (String) into numerical data. The handle is registered in database.
          PerfDataObject => Query database and decide which handle will be called. Output object for View
    View
      To be called by controller to output excel. Use PHPExcel.
    tmp
      Temp folder to store files that will be upload to my private server,
</div>