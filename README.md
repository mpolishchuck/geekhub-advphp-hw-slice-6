GeekHub Homework 12. The Simple Blog.
========================

[![Build Status](https://travis-ci.org/paulmaxwell/geekhub-advphp-hw-slice-6.png?branch=master)](https://travis-ci.org/paulmaxwell/geekhub-advphp-hw-slice-6)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/paulmaxwell/geekhub-advphp-hw-slice-6/badges/quality-score.png?s=2c14ef4935155a677e1e1fd979d5af6dd2101726)](https://scrutinizer-ci.com/g/paulmaxwell/geekhub-advphp-hw-slice-6/)

## Steps to deploy: ##

1. Clone repository to your local machine
2. Execute 'composer install' at the root directory, setup database server
3. Point DocumentRoot of your web server to the 'web' directory
4. Use reload.sh to deploy project
 ```
 sh bin/reload.sh
 ```
5. Use chmod.sh if your has inconvenient configuration
 ```
 sh bin/chmod.sh
 ```
6. Enjoy ;)
