Mail2RSS (0.2 alpha)
====================

Display your latest mails in a RSS feed

Description
-----------

This PHP script allow you to display your last 20 mails from your inbox as a RSS feed.

No database needed nor external lib ;)

You SHOULD have the "imap" lib activate on your server (see http://www.php.net/manual/fr/imap.requirements.php) !

Installation & configuration
----------------------------

1. Change the user-defined constants on the beggining of the file (server address, login, password, port, etc)
2. Upload the file (mail2rss.php) on your web hosting
3. Display it on your browser with the correct token parameter (e.g. http://yourwebhost.com/mail2rss.php?token=YOUR_TOKEN)
4. Enjoy the feed !

Version history
---------------

+ 0.1 alpha :
	- Initial release

Future update
-------------

- Add some parameters to configure the feed (number of mails, sorting, disabling images, etc)
- Add an UI to configure the script
- Better security ?
- Auto-detect the server's configuration based on the host
- Add comments !

Author
------

- Kevin VUILLEUMIER (http://kevinvuilleumier.net)

Licence
-------

See "LICENCE" file on the same directory.
