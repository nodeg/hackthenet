-- phpMyAdmin SQL Dump
-- version 2.7.0-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Mar 11, 2006 at 09:17 PM
-- Server version: 4.1.14
-- PHP Version: 5.1.3-dev
-- 
-- Database: `browsergameengine`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `forum_categories`
-- 

CREATE TABLE `forum_categories` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` char(64) NOT NULL default '',
  `sort_index` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sort_index` (`sort_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `forums`
-- 

CREATE TABLE `forums` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL default '',
  `categorie` mediumint(8) unsigned NOT NULL default '0',
  `description` mediumtext NOT NULL,
  `sort_index` smallint(5) unsigned NOT NULL default '0',
  `locked` tinyint(1) unsigned NOT NULL default '0',
  `last_post_id` mediumint(8) unsigned NOT NULL default '0',
  `last_post_topic_first_post` mediumint(8) unsigned NOT NULL default '0',
  `last_post_user` mediumint(8) unsigned NOT NULL default '0',
  `last_post_time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `categorie` (`categorie`),
  KEY `sort_index` (`sort_index`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `group_permissions`
-- 

CREATE TABLE `group_permissions` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `group_id` mediumint(8) unsigned NOT NULL default '0',
  `forum_id` smallint(5) unsigned NOT NULL default '0',
  `action` enum('browse_topic_list','read_topics','write_reply','create_topic','sticky_topic','see_existance','close_topic','edit_others_posts','delete_others_posts','move_topics') NOT NULL default 'browse_topic_list',
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`),
  KEY `action` (`action`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(48) NOT NULL default '',
  `hidden` tinyint(1) unsigned NOT NULL default '0',
  `undeletable` tinyint(1) unsigned NOT NULL default '0',
  `rank_name` varchar(32) NOT NULL default '',
  `rank_icon` varchar(255) NOT NULL default '',
  `allow_customtitle` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `posts`
-- 

CREATE TABLE `posts` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `poster_id` mediumint(8) unsigned NOT NULL default '0',
  `poster_ip` varchar(64) NOT NULL default '',
  `topic_id` mediumint(8) unsigned NOT NULL default '0',
  `time` int(10) unsigned NOT NULL default '0',
  `subject` varchar(64) NOT NULL default '',
  `text` mediumtext NOT NULL,
  `parse_bbcode` tinyint(1) unsigned NOT NULL default '0',
  `replace_smilies` tinyint(1) unsigned NOT NULL default '0',
  `edit_count` smallint(5) unsigned NOT NULL default '0',
  `last_edit_time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `topic_id` (`topic_id`),
  FULLTEXT KEY `subject` (`subject`),
  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `privmsgs`
-- 

CREATE TABLE `privmsgs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sender` mediumint(8) unsigned NOT NULL default '0',
  `recipient` mediumint(8) unsigned NOT NULL default '0',
  `sender_box` enum('out','out:arc','deleted') NOT NULL default 'out',
  `recipient_box` enum('in','in:arc','deleted') NOT NULL default 'in',
  `subject` varchar(255) NOT NULL default '',
  `text` mediumtext NOT NULL,
  `time` int(10) unsigned NOT NULL default '0',
  `read` tinyint(1) unsigned NOT NULL default '0',
  `parse_bbcode` tinyint(1) unsigned NOT NULL default '0',
  `replace_smilies` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sender` (`sender`),
  KEY `recipient` (`recipient`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `settings`
-- 

CREATE TABLE `settings` (
  `key` varchar(48) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`key`(10))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `temp`
-- 

CREATE TABLE `temp` (
  `key` varchar(16) NOT NULL default '',
  `value` mediumtext NOT NULL,
  PRIMARY KEY  (`key`(8))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `topics`
-- 

CREATE TABLE `topics` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `forum_id` mediumint(8) unsigned NOT NULL default '0',
  `sticky` tinyint(1) unsigned NOT NULL default '0',
  `locked` tinyint(1) unsigned NOT NULL default '0',
  `first_post_id` mediumint(8) unsigned NOT NULL default '0',
  `last_post_id` mediumint(8) unsigned NOT NULL default '0',
  `last_post_user` mediumint(8) unsigned NOT NULL default '0',
  `last_post_time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `last_post_time` (`last_post_time`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `user_groups`
-- 

CREATE TABLE `user_groups` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `group_id` mediumint(8) unsigned NOT NULL default '0',
  `is_main_group` tinyint(1) unsigned NOT NULL default '0',
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(18) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `sess_id` varchar(24) NOT NULL default '',
  `sess_ipmask` varchar(11) NOT NULL default '',
  `sess_lastcall` int(10) unsigned NOT NULL default '0',
  `email` varchar(32) NOT NULL default '',
  `is_admin` tinyint(1) unsigned NOT NULL default '0',
  `warnings` tinyint(2) unsigned NOT NULL default '0',
  `registered_time` int(10) unsigned NOT NULL default '0',
  `unread_post_data` mediumtext NOT NULL,
  `unread_post_data_last_update` int(10) unsigned NOT NULL default '0',
  `residence` varchar(32) NOT NULL default '',
  `jabber_id` varchar(48) NOT NULL default '',
  `email_public` tinyint(1) unsigned NOT NULL default '0',
  `invisible` tinyint(1) unsigned NOT NULL default '0',
  `cluster` varchar(16) NOT NULL default '',
  `scroll_menu` tinyint(1) unsigned NOT NULL default '1',
  `avatar` varchar(10) NOT NULL default '',
  `customtitle` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `session_id` (`sess_id`(8))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `watched_topics`
-- 

CREATE TABLE `watched_topics` (
  `topic_id` mediumint(8) unsigned NOT NULL default '0',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `flag` tinyint(1) unsigned NOT NULL default '1',
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
