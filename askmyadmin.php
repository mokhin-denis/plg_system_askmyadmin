<?php
/**
*
* plg_system_askmyadmin is an extended version of plg_backendtoken plugin
*
* AskMyAdmin prevent of login to backend of site till entering correct key=value pair.
*
* Copyright (C) 2013-2018 mokhin-tech.ru. All rights reserved. 
*
* Author of BackEndToken is:
* Axel < axel@quelloffen.com >
* http://www.joomlaconsulting.de
*
* Author of AskMyAdmin is:
* Denis Mokhin < denis@mokhin-tech.ru >
* http://mokhin-tech.ru
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

class plgSystemAskMyAdmin extends  JPlugin 
{
    /**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  3.8.0
	 */
	protected $app;
    
    /**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct(& $subject, $config)
	{
        parent::__construct($subject, $config);
        
        // Get the application if not done by JPlugin. This may happen during upgrades from Joomla 2.5.
		if (!$this->app)
		{
			$this->app = JFactory::getApplication();
		}
    }
    
    function onAfterInitialise()
	{
        $app  = $this->app;

		if (!$app->isClient('administrator'))
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

        $request = $app->input->request;
        
        if( $app->input->getMethod() == 'GET' )
		{		
			$requestKey = $request->get($keyname, 'no token set');
		}
		
		if( $app->input->getMethod() == 'POST' )
		{
            if( isset($_SERVER['HTTP_REFERER']) )
            {
                $ref =  $_SERVER['HTTP_REFERER'];
                $u = JURI::getInstance( $ref );
                $requestKey = $u->getVar( $keyname, 'no token set' );                
            }
		}                
		
		//invalid access token
		if( $keyvalue != $requestKey )
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