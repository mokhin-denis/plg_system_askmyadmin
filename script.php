<?php

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerAdapter;

class PlgSystemAskMyAdminInstallerScript
{
    public function postflight(string $type, InstallerAdapter $parent): void
    {
        if (!in_array($type, ['install', 'update'], true)) {
            return;
        }

        $legacyFile = JPATH_PLUGINS . '/system/askmyadmin/askmyadmin.php';

        if (is_file($legacyFile)) {
            @unlink($legacyFile);
        }
    }
}
