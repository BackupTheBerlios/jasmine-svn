# Database: print
# Table: 'jobs_log'
#
CREATE TABLE `jobs_log` (
  `id` mediumint(9) NOT NULL auto_increment,
  `date` timestamp(14) NOT NULL,
  `job_id` tinytext NOT NULL,
  `printer` tinytext NOT NULL,
  `user` tinytext NOT NULL,
  `title` tinytext NOT NULL,
  `copies` smallint(6) NOT NULL default '0',
  `pages` smallint(6) NOT NULL default '0',
  `options` tinytext NOT NULL,
  `doc` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM COMMENT='List all the jobs successfully sent for printing';
