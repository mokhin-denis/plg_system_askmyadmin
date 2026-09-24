<?php

namespace Joomla\Plugin\System\AskMyAdmin\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Event\Application\AfterInitialiseEvent;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\SubscriberInterface;

final class AskMyAdmin extends CMSPlugin implements SubscriberInterface
{
    private const SESSION_KEY = 'plg_system_askmyadmin.authorized_at';
    private const ACCESS_TTL = 300;

    public static function getSubscribedEvents(): array
    {
        return ['onAfterInitialise' => 'onAfterInitialise'];
    }

    public function onAfterInitialise(AfterInitialiseEvent $event): void
    {
        $app = $this->getApplication();

        if (!$app || !$app->isClient('administrator') || !$app->getIdentity()->guest) {
            return;
        }

        $keyName  = trim((string) $this->params->get('keyname', ''));
        $keyValue = (string) $this->params->get('keyvalue', '');

        if ($keyName === '' || $keyValue === '') {
            Log::add(
                'AskMyAdmin is enabled but its key name or key value is empty; administrator access was not blocked.',
                Log::WARNING,
                'plg_system_askmyadmin'
            );

            return;
        }

        $session = $app->getSession();
        $authorizedAt = (int) $session->get(self::SESSION_KEY, 0);

        if ($authorizedAt > 0 && time() - $authorizedAt <= self::ACCESS_TTL) {
            return;
        }

        if ($authorizedAt > 0) {
            $session->remove(self::SESSION_KEY);
        }

        $input = $app->getInput();

        if (strtoupper($input->getMethod()) === 'GET') {
            $requestKey = (string) $input->get($keyName, '', 'raw');

            if (hash_equals($keyValue, $requestKey)) {
                $session->set(self::SESSION_KEY, time());
                $app->redirect(Uri::base());
                $app->close();

                return;
            }
        }

        $url = trim((string) $this->params->get('url', ''));

        if ($url === '') {
            $url = Uri::root();
        }

        $app->redirect($url);
        $app->close();
    }
}
