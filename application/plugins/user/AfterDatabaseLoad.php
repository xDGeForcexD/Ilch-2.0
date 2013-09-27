<?php
/**
 * Holds User_AfterDatabaseLoadPlugin.
 *
 * @author Jainta Martin
 * @copyright Ilch Pluto
 * @package ilch
 */

defined('ACCESS') or die('no direct access');

/**
 * Does user operations before the controller loads.
 *
 * @author  Jainta Martin
 * @copyright Ilch Pluto
 * @package ilch
 */
class User_AfterDatabaseLoadPlugin
{
	/**
	 * Checks if a user id was given in the request and sets the user.
	 *
	 * If no user id is given a default user will be created.
	 *
	 * @param array $pluginData
	 */
	public function __construct(array $pluginData)
	{
		if(!isset($pluginData['config']))
		{
			return;
		}

		$userId = null;

		if(isset($_SESSION['user_id']))
		{
			$userId = (int)$_SESSION['user_id'];
		}

		$mapper = new User_UserMapper();
		$user = $mapper->getUserById($userId);

		Ilch_Registry::set('user', $user);

		$pluginData['translator']->setLocale($pluginData['config']->get('locale'));
	}
}