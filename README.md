Icinga Network Report
======

A Symfony project created on September 21, 2015, 9:38 am.
I wrote this project for my company to monitor network performance. The monitoring itself is done my Icinga2, a netowrk monitor program forked from Nagios. Performance data is inserted into MySql database using IDOUtils, a plugin provided by Icinga also.

My program capture data from Icinga's database every 15 min using crontab and PHP scripts. It will output an availability and performance report via HTTP request. Another cron job will run every 10:00 PM everyday to send the report via Symfony's mailer.

Structure

- src<br />
  - AppBundle<br />
    - Command<br />
      - CurlUploadCommand.php => a PHP script that will upload the xlsx report via HTTP to my private server using cURL<br />
      - EmailCommand.php => PHP script that is run by crontab to email every 10:00 PM<br />
        Controller<br />
      - PerDataReportController.php => Symfony controller that call classes in Model/Performance to output performance report<br />
      - ReportController.php => Symfony controller that call classes in Model/Availability to output availability report<br />
    DbObject<br />
      - ClassicDbConnector.php => Predefined PDO object to connect to self database<br />
      - IcingaDbConnector.php => Predefinded PDO object to connect to Icinga's database<br />
    IdoDataGrabber<br />
      - PHP scripts that run seperate from Symfony. Run directly from command line / crontab. Grab data from Icinga and insert into self db<br />
        To Do:<br />
          write into Symfony Cli.<br />
    Model<br />
      Contains all the data/model object<br />
        - Availability => Data object for availability report<br />
        - Performance => Data object for performance report<br />
          PerfDataHandle => Parser that convert Nagios perfdata (String) into numerical data. The handle is registered in database.<br />
          PerfDataObject => Query database and decide which handle will be called. Output object for View<br />
    View<br />
      To be called by controller to output excel. Use PHPExcel.<br />
    tmp<br />
      Temp folder to store files that will be upload to my private server<br />
