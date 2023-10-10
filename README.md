# dns-diff
compare / diff 2 DNS servers. useful when migrating dns servers

lets say you're migrating from Dreamhost DNS to Cloudflare DNS, and you wonder if you forgot something, run:
```
php dns_diff.php ignore_columns=TTL dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com domain=loltek.net
```
and you should get something like:
```diff


DNS records differ for loltek.net MX dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "loltek.net" MX +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "loltek.net" MX +cl +ttlid +nomultiline
Diff:
@@ -5,8 +5,8 @@ array (
     'TTL' => 300,
     'CLASS' => 'IN',
     'TYPE' => 'MX',
-    'PRIORITY' => 0,
-    'HOST' => 'mx2.mailchannels.net.',
+    'PRIORITY' => 1,
+    'HOST' => 'mx1.mailchannels.net.',
   ),
   1 => 
   array (
@@ -14,7 +14,7 @@ array (
     'TTL' => 300,
     'CLASS' => 'IN',
     'TYPE' => 'MX',
-    'PRIORITY' => 0,
-    'HOST' => 'mx1.mailchannels.net.',
+    'PRIORITY' => 2,
+    'HOST' => 'mx2.mailchannels.net.',
   ),
 )

DNS records differ for loltek.net NS dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "loltek.net" NS +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "loltek.net" NS +cl +ttlid +nomultiline
Diff:
@@ -2,25 +2,17 @@ array (
   0 => 
   array (
     'DOMAIN' => 'loltek.net.',
-    'TTL' => 14400,
+    'TTL' => 86400,
     'CLASS' => 'IN',
     'TYPE' => 'NS',
-    'HOST' => 'ns3.dreamhost.com.',
+    'HOST' => 'andy.ns.cloudflare.com.',
   ),
   1 => 
   array (
     'DOMAIN' => 'loltek.net.',
-    'TTL' => 14400,
+    'TTL' => 86400,
     'CLASS' => 'IN',
     'TYPE' => 'NS',
-    'HOST' => 'ns1.dreamhost.com.',
-  ),
-  2 => 
-  array (
-    'DOMAIN' => 'loltek.net.',
-    'TTL' => 14400,
-    'CLASS' => 'IN',
-    'TYPE' => 'NS',
-    'HOST' => 'ns2.dreamhost.com.',
+    'HOST' => 'cheryl.ns.cloudflare.com.',
   ),
 )

DNS records differ for loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for loltek.net SOA dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "loltek.net" SOA +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "loltek.net" SOA +cl +ttlid +nomultiline
Diff:
@@ -2,15 +2,15 @@ array (
   0 => 
   array (
     'DOMAIN' => 'loltek.net.',
-    'TTL' => 300,
+    'TTL' => 1800,
     'CLASS' => 'IN',
     'TYPE' => 'SOA',
-    'MNAME' => 'ns1.dreamhost.com.',
-    'RNAME' => 'hostmaster.dreamhost.com.',
-    'SERIAL' => 2023100800,
-    'REFRESH' => 17330,
-    'RETRY' => 600,
-    'EXPIRE' => 1814400,
-    'MINIMUM' => 300,
+    'MNAME' => 'andy.ns.cloudflare.com.',
+    'RNAME' => 'dns.cloudflare.com.',
+    'SERIAL' => 2322320836,
+    'REFRESH' => 10000,
+    'RETRY' => 2400,
+    'EXPIRE' => 604800,
+    'MINIMUM' => 1800,
   ),
 )

DNS records differ for www.loltek.net A dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "www.loltek.net" A +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "www.loltek.net" A +cl +ttlid +nomultiline
Diff:
@@ -1,7 +1,14 @@
 array (
   0 => 
   array (
-    'DOMAIN' => 'www.loltek.net.',
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'CNAME',
+  ),
+  1 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
     'TTL' => 300,
     'CLASS' => 'IN',
     'TYPE' => 'A',
DNS records differ for www.loltek.net CNAME dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "www.loltek.net" CNAME +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "www.loltek.net" CNAME +cl +ttlid +nomultiline
Diff:
@@ -1,2 +1,9 @@
 array (
+  0 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'CNAME',
+  ),
 )

DNS records differ for www.loltek.net MX dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "www.loltek.net" MX +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "www.loltek.net" MX +cl +ttlid +nomultiline
Diff:
@@ -1,2 +1,27 @@
 array (
+  0 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'CNAME',
+  ),
+  1 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'MX',
+    'PRIORITY' => 1,
+    'HOST' => 'mx1.mailchannels.net.',
+  ),
+  2 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'MX',
+    'PRIORITY' => 2,
+    'HOST' => 'mx2.mailchannels.net.',
+  ),
 )

DNS records differ for www.loltek.net NS dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "www.loltek.net" NS +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "www.loltek.net" NS +cl +ttlid +nomultiline
Diff:
@@ -1,2 +1,25 @@
 array (
+  0 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'CNAME',
+  ),
+  1 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 86400,
+    'CLASS' => 'IN',
+    'TYPE' => 'NS',
+    'HOST' => 'andy.ns.cloudflare.com.',
+  ),
+  2 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 86400,
+    'CLASS' => 'IN',
+    'TYPE' => 'NS',
+    'HOST' => 'cheryl.ns.cloudflare.com.',
+  ),
 )

DNS records differ for www.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "www.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "www.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'www.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for www.loltek.net SOA dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "www.loltek.net" SOA +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "www.loltek.net" SOA +cl +ttlid +nomultiline
Diff:
@@ -1,2 +1,23 @@
 array (
+  0 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'CNAME',
+  ),
+  1 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 1800,
+    'CLASS' => 'IN',
+    'TYPE' => 'SOA',
+    'MNAME' => 'andy.ns.cloudflare.com.',
+    'RNAME' => 'dns.cloudflare.com.',
+    'SERIAL' => 2322320836,
+    'REFRESH' => 10000,
+    'RETRY' => 2400,
+    'EXPIRE' => 604800,
+    'MINIMUM' => 1800,
+  ),
 )

DNS records differ for www.loltek.net TXT dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "www.loltek.net" TXT +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "www.loltek.net" TXT +cl +ttlid +nomultiline
Diff:
@@ -1,2 +1,17 @@
 array (
+  0 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'CNAME',
+  ),
+  1 => 
+  array (
+    'DOMAIN' => 'loltek.net.',
+    'TTL' => 300,
+    'CLASS' => 'IN',
+    'TYPE' => 'TXT',
+    'TXT' => '"v=spf1 mx include:netblocks.dreamhost.com include:relay.mailchannels.net -all"',
+  ),
 )

DNS records differ for mail.loltek.net A dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "mail.loltek.net" A +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "mail.loltek.net" A +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'mail.loltek.net.',
-    'TTL' => 300,
-    'CLASS' => 'IN',
-    'TYPE' => 'A',
-    'IP' => '64.90.62.162',
-  ),
 )

DNS records differ for mail.loltek.net MX dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "mail.loltek.net" MX +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "mail.loltek.net" MX +cl +ttlid +nomultiline
Diff:
@@ -1,20 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'mail.loltek.net.',
-    'TTL' => 300,
-    'CLASS' => 'IN',
-    'TYPE' => 'MX',
-    'PRIORITY' => 0,
-    'HOST' => 'mx1.mailchannels.net.',
-  ),
-  1 => 
-  array (
-    'DOMAIN' => 'mail.loltek.net.',
-    'TTL' => 300,
-    'CLASS' => 'IN',
-    'TYPE' => 'MX',
-    'PRIORITY' => 0,
-    'HOST' => 'mx2.mailchannels.net.',
-  ),
 )

DNS records differ for mail.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "mail.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "mail.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'mail.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for webmail.loltek.net A dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "webmail.loltek.net" A +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "webmail.loltek.net" A +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'webmail.loltek.net.',
-    'TTL' => 300,
-    'CLASS' => 'IN',
-    'TYPE' => 'A',
-    'IP' => '69.163.136.138',
-  ),
 )

DNS records differ for webmail.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "webmail.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "webmail.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'webmail.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for smtp.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "smtp.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "smtp.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'smtp.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for pop.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "pop.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "pop.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'pop.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for pop3.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "pop3.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "pop3.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'pop3.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for imap.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "imap.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "imap.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'imap.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for imap4.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "imap4.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "imap4.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'imap4.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for ftp.loltek.net A dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "ftp.loltek.net" A +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "ftp.loltek.net" A +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'ftp.loltek.net.',
-    'TTL' => 300,
-    'CLASS' => 'IN',
-    'TYPE' => 'A',
-    'IP' => '173.236.227.214',
-  ),
 )

DNS records differ for ftp.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "ftp.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "ftp.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'ftp.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for cpanel.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "cpanel.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "cpanel.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'cpanel.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for whm.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "whm.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "whm.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'whm.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for webdisk.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "webdisk.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "webdisk.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'webdisk.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for ns1.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "ns1.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "ns1.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'ns1.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for ns2.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "ns2.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "ns2.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'ns2.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for ns3.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "ns3.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "ns3.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'ns3.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )

DNS records differ for ns4.loltek.net RRSIG dns1=ns1.dreamhost.com dns2=andy.ns.cloudflare.com
dig @"ns1.dreamhost.com" "ns4.loltek.net" RRSIG +cl +ttlid +nomultiline
dig @"andy.ns.cloudflare.com" "ns4.loltek.net" RRSIG +cl +ttlid +nomultiline
Diff:
@@ -1,10 +1,2 @@
 array (
-  0 => 
-  array (
-    'DOMAIN' => 'ns4.loltek.net.',
-    'TTL' => 3600,
-    'CLASS' => 'IN',
-    'TYPE' => 'HINFO',
-    'unknown1' => '"RFC8482" ""',
-  ),
 )
```
