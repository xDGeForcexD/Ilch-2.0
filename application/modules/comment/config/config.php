<?php
/**
 * @package ilch
 */

namespace Comment\Config;
defined('ACCESS') or die('no direct access');

class Config extends \Ilch\Config\Install
{
    public function install()
    {
        $this->db()->queryMulti($this->getInstallSql());
    }

    public function getInstallSql()
    {
        return 'CREATE TABLE IF NOT EXISTS `[prefix]_comments` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `key` varchar(255) NOT NULL,
				  `text` mediumtext NOT NULL,
				  `date_created` datetime NOT NULL,
				  `user_id` int(11) NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1';
    }
}
