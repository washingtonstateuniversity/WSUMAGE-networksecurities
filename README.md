## WSU Magento LDAP extension

This module provides LDAP service for both the admin and customer areas.  There are options to allow for creation of users and the a choic in the handling.  

You have 3 combo's to do
1. LDAP only
1. LDAP fall back to system
1. System fall back to LDAP
1. System only (but this is the extension turned off so 3 combos :))

##Security is important
Remeber to not open up holes by using real users for test users, and reading how LDAP works is your best bet.  Here is a nice [10-Minute LDAP Tutorial from O’Reilly](http://oreilly.com/perl/excerpts/system-admin-with-perl/ten-minute-ldap-utorial.html). (Writen for Perl but it's a good read to bootstrap)