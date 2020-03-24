# check-segreteria

With this package you can make a call through an Asterisk PBX to notify an email recepit.

You need to replace config.php.sample fields and then save the file as config.php
Then run checksegreteria.php with a Crontab every time you need to check the mailbox.

This check only emails unseen from the PBX email set in config.php