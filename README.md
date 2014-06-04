# WSU Magento Network and Securities extension

This is a total security extension providing LDAP services, honeypots, and more for both the admin and customer areas.


##Security is important
Remember to not open up holes by using real users for test users, and reading how LDAP works is your best bet.  Here is a nice [10-Minute LDAP Tutorial from O’Reilly](http://oreilly.com/perl/excerpts/system-admin-with-perl/ten-minute-ldap-utorial.html). (Written for Perl but it's a good read to bootstrap)


##LDAP

There are options to allow for creation of users and the a choice in the handling.  

You have 3 combo's to do

1. LDAP only
1. LDAP fall back to system
1. System fall back to LDAP
1. System only (but this is the extension turned off so 3 combos :))

##IPv4 filtering
You can lock down the frontend or admin to include or exclude IPv4s.  A great example of usage is to set a regex pattern for the admin so that you must be with in an IP range in order to access the admin.  Combine this with VPN services and you will be able to still edit from anywhere in the world yet be secured under your network.

##Store level login requirements
You are able to set a store up so that you must login in order to view any products.  In a network setup, this is store by store.


##Honey Pots
Honeypot protection is added to decrease spam in your reviews.  Later it will be used to reduce traffic

Honeypot protection goals

1. Create a honey pot with the same name as one of the default fields. Make it look legit with a label.
    Make it look perfectly legit with label and icon. We don’t want to alert the bot in any way 
    that this field is special.
1. Place the honey pot in the form in a random location. Keep moving it around between the valid fields.
We don’t want the spam bot writer to simply ignore the same field based on index.
1. The honeypot fields are dynamically created and formatted to look like something the spam bots will want 
to fill out but not something that is easy to get to with regex to skip.
1. An expiration is added to your form keeping spam bots from using the same fields and submitting the form later.
1. Honey pot to keep the valid users from filling it out, hidden with dynamically applied CSS.
1. OnPostaction Make sure to check pots for spammer junk


##fail2ban
A formatted log is dumped out ready for fail2ban.  This is formatted in the same way WordPress’ spam logs are.  To use just 
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
