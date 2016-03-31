<?php
namespace MapSeven\Neos\FormBundle\ElasticSearch\Client;

/**
 * This script belongs to the TYPO3 Flow package "MapSeven.Neos.FormBundle*
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * ClientFactory
 *
 * @Flow\Scope("singleton")
 */
class ClientFactory
{

    /**
     * @Flow\Inject
     * @var \Flowpack\ElasticSearch\Domain\Factory\ClientFactory
     */
    protected $clientFactory;

    /**
     * Create a client
     *
     * @return \Flowpack\ElasticSearch\Domain\Model\Client
     */
    public function create()
    {
        return $this->clientFactory->create(null, 'MapSeven\Neos\FormBundle\ElasticSearch\ElasticSearchClient');
    }
}
