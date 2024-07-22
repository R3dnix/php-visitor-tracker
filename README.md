# PHP Visitor Tracker
Simple but effective PHP Script to log data from visitors.

It will automatically create a table "visitors", in case it already does not exist. The script will log the following data:
- Ip
- Country
- UserAgent
- Browser
- Operating System
- Device Type
- Website Location
- Ref Page
- Time

Additional Features:
- Count amount of unique visits (Ignore repeated IP's and Bots)
- URL Token to avoid tracking your-self
- Retrieve countries only on panel side to minimize performance impact (Connecting to 3rd party API)

Preview: https://raw.githubusercontent.com/R3dnix/php-visitor-tracker/main/preview.png
---
The script gets initialized right away when the class is included. To start the logger just include the script and call $VisitorLog->start(). You can see an example in index.php

```
require_once "VisitorLog.php";
$VisitorLog->start();
```

You should edit the "VisitorLog.php" with your MySQL Database configuration, and also set your Token.

```
$token = "6yva46";
```

Use it in the URL to keep your own visits unlogged, and as a secutity feature for your traffic panel so that only you can see it.

Your traffic page: ``domain.com/traffic.php?token=6yva46``

Any other page you do NOT want to log yourself: ``domain.com/?token=6yva46`` ``domain.com/index.php?token=6yva46``



Contact email: makogroupltd@gmail.com
