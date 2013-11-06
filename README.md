# WSU Magento Newtwork and Securities extension

This module provides LDAP service for both the admin and customer areas.  Also adds layers of protect from spam bots.


##LDAP

There are options to allow for creation of users and the a choic in the handling.  

You have 3 combo's to do

1. LDAP only
1. LDAP fall back to system
1. System fall back to LDAP
1. System only (but this is the extension turned off so 3 combos :))

##Security is important
Remeber to not open up holes by using real users for test users, and reading how LDAP works is your best bet.  Here is a nice [10-Minute LDAP Tutorial from O’Reilly](http://oreilly.com/perl/excerpts/system-admin-with-perl/ten-minute-ldap-utorial.html). (Writen for Perl but it's a good read to bootstrap)

##Honey Pots
Honeypot protection is added to decrease spam in your reviews.  Later it will be used to reduce traffic

Honeypot protection goals

1. Create a honey pot with the same name as one of the default fields. Make it look legit with a label.
    Make it look perfectly legit with label and icon. We don’t want to alert the bot in any way 
    that this field is special.
1. Place the honey pot in the form in a random location. Keep moving it around between the valid fields.
We don’t want the spam bot writer to simply ignore the same field based on index.
1. The honeypot feilds are dynamicly created and formated to look like something the spam bots will want 
to fill out but not something that is easy to get to with regex to skip.
1. An expiration is added to your form keeping spam bots from using the same fields and submitting the form later.
1. honey pot to keep the valid users from filling it out, hidden with dynamicly applied CSS.
1. OnPostaction Make sure to check pots for spamer junk


##fail2ban
A formated log is dumped out ready for fail2ban.  This is formated in the same way Wordpresses spam logs are.  To use just 
**TBD**


spam-log.conf
```
[Definition]
failregex = ^\s*comment id=\d+ from host=<HOST> marked as spam$
ignoreregex =
```

jail.local
```
[spam-log]
enabled  = true
port     = http,https
filter   = spam-log
logpath  = /srv/www/log/spam.log
maxretry = 5
findtime = 3600
bantime  = 86400
```