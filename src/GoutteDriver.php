<?php

/*
 * This file is part of the Behat\Mink.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Mink\Driver;

use Behat\Mink\Driver\Goutte\Client as ExtendedClient;
use Goutte\Client;
use Symfony\Component\BrowserKit\HttpBrowser;

/**
 * Goutte driver.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class GoutteDriver extends BrowserKitDriver
{
    /**
     * Initializes Goutte driver.
     *
     * @param Client $client Goutte client instance
     */
    public function __construct(Client $client = null)
    {
        parent::__construct($client ?: new ExtendedClient());
    }

    /**
     * {@inheritdoc}
     */
    public function setBasicAuth($user, $password)
    {
        if ($this->isClientHttpBrowser()) {
            parent::setBasicAuth($user, $password);

            return;
        }
        if (false === $user) {
            $this->getClient()->resetAuth();

            return;
        }

        $this->getClient()->setAuth($user, $password);
    }

    /**
     * Gets the Goutte client.
     *
     * The method is overwritten only to provide the appropriate return type hint.
     *
     * @return Client
     */
    public function getClient()
    {
        return parent::getClient();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        parent::reset();
        if (!$this->isClientHttpBrowser()) {
            $this->getClient()->resetAuth();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareUrl($url)
    {
        $parts = parse_url($url);
        if (isset($parts['user']) || isset($parts['pass'])) {
            $this->setBasicAuth(
                isset($parts['user']) ? $parts['user'] : '',
                isset($parts['pass']) ? $parts['pass'] : ''
            );
        }
        return $url;
    }

    /**
     * Indicates whether the client is an instance of HttpBrowser
     *
     * As of Goutte version 4.0, the client is just an unmodified extension of
     * the HttpBrowser class from BrowserKit.
     *
     * @return bool
     */
    private function isClientHttpBrowser()
    {
        return ($this->getClient() instanceof HttpBrowser);
    }
}
