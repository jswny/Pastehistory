# <img src="http://i.imgur.com/7PHng3K.png" width="50"/> Pastehistory
Pastehistory is a fully-featured scraping implementation and accompanying website for indexing pastes from Pastebin based on certain criteria. Pastehistory contains everything you need to roll your own paste mirroring website, including branding.

## Table of Contents
* [Features](#features)
* [Screenshots](#screenshots)
* [Requirements](#requirements)
* [Structure](#structure)
* [Setup](#setup)
* [Disclaimer](#disclaimer)
* [Licence](#licence)

## Features
* Written completely with PHP, HTML, and CSS
* Clean, markdown-based style
* Custom scraping with the ability to filter pastes based on keywords
* Optimized for SEO
  * Custom index to ensure all pages are crawled
  * Responsive page titles based on individual pastes and paste states
* Reports system with email notification
* Fully-featured admin panel including report management and paste removal
* Custom search all archived pastes, or recent Pastebin pastes
* Experimental viewer for HTML pastes
* Responsive and optimized for mobile
* Support for custom banner ads
* Custom favicon and logos created specifically for Pastehistory

## Screenshots

<h3 align="center">Home Page</h3>
<div align="center">
  <img src="http://i.imgur.com/mg4cSHF.png" width="800px"/>
</div>

<h3 align="center">Search Page</h3>
<div align="center">
  <img src="http://i.imgur.com/RriziUn.png" width="800px"/>
</div>

<h3 align="center">Admin Panel</h3>
<div align="center">
  <img src="http://i.imgur.com/WReIeK6.png" width="800px"/>
</div>

## Requirements
### Minimum Requirements
1. A webserver such as Apache or NGINX (may require extra configuration).
2. PHP 5.0 or higher.
3. An SQL distribution such as MariaDB.

### Test environment
1. CentOS 7.1.1503
2. Apache 2.4.6
3. PHP 5.4.16
4. MariaDB 5.5.44 (MySQL 15.1)

## Structure
* `bin` - Contains most of the internal scripts that work in the background

  * `bin/ads.php` - PHP include for banner ads; ad content should be placed in here

  * `bin/init.php` - PHP database connection

  * `bin/scrape.php` - Custom scraping script for pastes that cannot be accessed through the Pastebin scraping API. This file is not currently used but could be implemented if needed.

* `css` - Contains the stylesheets for Pastehistory

  * `css/common.css` - Custom stylesheet which contains style tweeks

  * `css/markdown.css` - Main GitHub-based stylesheet which is used for most elements

  * `css/responsive.css` - Responsive stylesheet for mobile optimization

* `img` - Contains all of the files needed for the Pastehistory favicon and the various branding icons

  * `img/favicon` - Contains all of the favicon files

  * `img/logo.png` - Official Pastehistory logo for branding purposes

  * `img/logo 2.png`, `img/logo 3.png`, `img/logo 4.png` - Alternate logos with different color schemes and styles

* `script` - Contains the parsing script, as well as the script for generating the index page in the background

  * `script/index.php` - Script for generating the site index for SEO based on it's template

  * `script/parse.php` - Main parsing script for Pastebin scraping API which archives pastes based on certain keywords

  * `script/template.php` - Template for generating the site index

* `.htaccess` - Contains rewrite rules to remove "www." URLs for SEO purposes, also contains password protection for the admin panel

* `admin.php` - Admin panel which can handle report tickets and remove pastes

* `archive.php` - Site index which contains a link to every archived paste in order to allow easy crawling for search engines

* `index.php` - Home page which contains a table of the last 15 archived pastes, and allows for manual paste viewing by Pastebin paste ID

* `random.php` - Experimental page which takes a random HTML paste from the last hundred and attempts to display it's content as HTML. **Possibly insecure**

* `Report.php` - Page for initiating reports. Should not be directly accessed other than from a paste

* `Search.php` - Search page which allows a user to search for archived pastes by certain keywords, or to search through recent Pastebin pastes

## Setup

1. You must have a Pastebin Pro account, and you must whitelist your server IP for scraping here: https://pastebin.com/api_scraping_faq
2. `parse.php` should be run as a cron job similar to this example: `* * * * * php parse.php > parse.log` which will run the parse script every minute. You will have to make sure you change the paths for this cron job based on your configuration. In this case, the `parse.php` file would be located in your home directory.
3. Run the following SQL command to create the required SQL structure (you may need to create a database named "paste" first):
  ```SQL
  -- phpMyAdmin SQL Dump
  -- version 4.4.15.1
  -- http://www.phpmyadmin.net
  --
  -- Host: localhost
  -- Generation Time: Jan 31, 2016 at 05:40 AM
  -- Server version: 5.5.44-MariaDB
  -- PHP Version: 5.4.16

  SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
  SET time_zone = "+00:00";

  --
  -- Database: `paste`
  --

  -- --------------------------------------------------------

  --
  -- Table structure for table `archive`
  --

  CREATE TABLE IF NOT EXISTS `archive` (
    `id` int(11) NOT NULL,
    `pid` text,
    `title` text,
    `date` text,
    `user` text,
    `size` int(11) DEFAULT NULL,
    `syntax` text,
    `text` text,
    `last_crawl` text,
    `remove` tinyint(1) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  -- --------------------------------------------------------

  --
  -- Table structure for table `report`
  --

  CREATE TABLE IF NOT EXISTS `report` (
    `id` int(11) NOT NULL,
    `pid` text,
    `date` text,
    `ip` text,
    `name` text,
    `email` text,
    `reason` text,
    `close` tinyint(4) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `archive`
  --
  ALTER TABLE `archive`
    ADD PRIMARY KEY (`id`);

  --
  -- Indexes for table `report`
  --
  ALTER TABLE `report`
    ADD PRIMARY KEY (`id`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `archive`
  --
  ALTER TABLE `archive`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  --
  -- AUTO_INCREMENT for table `report`
  --
  ALTER TABLE `report`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  ```
4. Open `bin/init.php` and fill in the following details:
  1. `$servername` - Your SQL server, usually "localhost"
  2. `$username` - Your SQL username, usually "root"
  3. `$password` - Your SQL password which corresponds to your SQL username
  4. `$db` - The name of your SQL database, usually "paste" if you are using the default Pastehistory configuration

5. Modify the following line in `script/parse.php` to archive pastes based on specific keywords. Simply add the keywords you want to use to filter pastes to the array: `$keywords = array('paste', 'test');`.

6. Create a .htpasswd file similar to `pastehistory.htpasswd` with a username and encrypted password to secure the admin panel. Ensure, the path to this file is properly specified in the `.htpasswd` file in the root directory.

7. Choose whether or not to include the ads header on your pages by including the following code, usually under the main page header and title: `<?php include 'bin/ads.php'; ?>`. The ads header is by default included on the home page only.

8. Ensure the proper timezone is selected by editing the following line: `date_default_timezone_set('America/New_York');` in both `bin/init.php` and `bin/script/parse.php`.

9. Ensure your server is configured to use the PHP `mail()` function. This may require you to set the hostname/FQDN of your server similar to this: `hostname pastehistory.com`. You may need to restart your server after you have done this.

10. Change the following lines in `report.php` to ensure the reports system works correctly:
  1. `$to = 'report@pastehistory.com';`
  2. `$header = "From:mailer@pastehistory.com \r\n";`

11. If you are hosting your database and parsing scripts on a separate server from your web server, you may want to automatically generate the link index hourly using a cron job to keep page load times low. In that case, you should run a cron job similar to this: `* * * * 0 php index.php > index.log`. Once again, you will have to change the paths based on where you are running the `index.php` generation script from. This example would also be running directly from the home directory.

## Disclaimer
This repository and its code is provided **as-is**. I've done my best to secure Pastehistory to prevent attacks like XSS and SQL-injection. However, I am in no way, shape, or form a security expert and most of this code should be considered vulnerable until properly reviewed by a security expert. In addition, Pastehistory is not a complete production product. Certain features may be missing and certain bugs may still exist.

## Licence
![Creative Commons Attribution 4.0](https://licensebuttons.net/l/by/3.0/88x31.png)

Pastehistory is licensed under the [Creative Commons Attribution 4.0 License](https://github.com/jswny/Pastehistory/blob/master/LICENSE).
