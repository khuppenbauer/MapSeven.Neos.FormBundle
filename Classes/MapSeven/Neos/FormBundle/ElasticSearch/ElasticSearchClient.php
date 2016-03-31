<?php
namespace MapSeven\Neos\FormBundle\ElasticSearch;

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
 * The elasticsearch client to be used by the formBundle
 *
 * @Flow\Scope("singleton")
 */
class ElasticSearchClient extends \Flowpack\ElasticSearch\Domain\Model\Client
{

    /**
     * The index name to be used for querying
     *
     * @var string
     */
    protected $indexName;

    /**
     * Get the index name to be used
     *
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * Retrieve the index to be used for querying or on-the-fly indexing.
     * In ElasticSearch, this index is an *alias* to the currently used index.
     *
     * @return \Flowpack\ElasticSearch\Domain\Model\Index
     */
    public function getIndex()
    {
        return $this->findIndex($this->indexName);
    }
}
