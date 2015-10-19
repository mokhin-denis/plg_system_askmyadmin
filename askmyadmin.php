<?php
/**
*
* plg_askmyadmin is an extended version of plg_backendtoken plugin
*
* AskMyAdmin prevent of login to backend of site till entering correct key=value pair.
*
* Copyright (C) 2013-2014 my-j.ru. All rights reserved. 
*
* Author of BackEndToken is:
* Axel < axel[at]quelloffen.com >
* http://www.joomlaconsulting.de
*
* Author of AskMyAdmin is:
* Denis E Mokhin < denis[at]my-j.ru >
* http://my-j.ru
*
* @license GNU GPL, see http://www.gnu.org/licenses/gpl-2.0.html
* 
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
**/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.plugin.plugin' );

class plgSystemAskMyAdmin extends  JPlugin 
{

	function plgAskMyAdmin( &$subject, $config = array() )
	{
		parent :: __construct($subject, $config);
		
		$plugin =& JPluginHelper::getPlugin( 'system', 'askmyadmin');
 		$this->params = new JParameter( $plugin->params );	
	}

	function onAfterInitialise()
	{
		$app = JFactory::getApplication();

		if( !$app->isAdmin() ) 
		{
			return;
		}
				
		//already logged in
		$user = JFactory::getUser();
 
		if( !$user->guest )
		{
		 	return;
		}
				
		$keyname = $this->params->get('keyname', 'key');
		$keyvalue   = $this->params->get('keyvalue', '1');
		

		if( JRequest::getMethod() == 'GET' )
		{		
			$request = JRequest::getVar( $keyname, 'no token set', 'GET' );
		}
		
		if( JRequest::getMethod() == 'POST' )
		{		
			$ref =  $_SERVER['HTTP_REFERER'];
			$u =& JURI::getInstance( $ref );
			$request = $u->getVar( $keyname, 'no token set' );			
		}		
		
		//invalid access token
		if( $keyvalue != $request )
		{
			$url = $this->params->get('url' );
			
			//fallback to site
			if( 0 == strlen( $url ) )
			{
				$url = JURI::root();		
			}
			
			$app->redirect( $url );
			die;			
		}
	}
}

?>