# php-visitor-tracker
Simple but effective PHP Script to log data from visitors.

It will automatically create a table "visitors", in case it already does not exist. The script will log the following data:
- Ip
- UserAgent
- Browser
- Operating System
- Device Type
- Web Location
- Ref Page
- Time

The script gets initialized right away when the class is included. To start the logger just include the script:

``
require_once "system/VisitorLog.php";``

You should edit the "VisitorLog.php" with your MySQL Database configuration, and also set your Token. Use it in the URL to keep your own visits unlogged, and as a secutity feature for your traffic panel so that only you can see it. domain.com/traffic.php?token=6yva46 or domain.com/?token=6yva46

``
$token = "6yva46";``

Contact email: marex.crazy@gmail.com
