<?php

defined('_JEXEC') or die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Plugin\System\AskMyAdmin\Extension\AskMyAdmin;

return new class () implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(
            PluginInterface::class,
            function () {
                $plugin = new AskMyAdmin((array) PluginHelper::getPlugin('system', 'askmyadmin'));
                $plugin->setApplication(Factory::getApplication());

                return $plugin;
            }
        );
    }
};
