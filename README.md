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
