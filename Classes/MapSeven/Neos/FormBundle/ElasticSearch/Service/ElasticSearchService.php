<?php
namespace MapSeven\Neos\FormBundle\ElasticSearch\Service;

/**
 * This script belongs to the TYPO3 Flow package "MapSeven.Neos.FormBundle*
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Flowpack\ElasticSearch\Annotations\Transform;
use Flowpack\ElasticSearch\Domain\Model\Document;
use Flowpack\ElasticSearch\Domain\Model\GenericType;
use Flowpack\ElasticSearch\Domain\Model\Index;
use Flowpack\ElasticSearch\Domain\Model\Mapping;
use Flowpack\ElasticSearch\Domain\Factory\ClientFactory;
use Flowpack\ElasticSearch\Indexer\Object\Transform\TransformerFactory;
use MapSeven\Neos\FormBundle\Domain\Model\FormData;
use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
use Neos\Flow\Reflection\ReflectionService;

/**
 *
 * @Flow\Scope("singleton")
 */
class ElasticSearchService
{

    /**
     * The Index Name
     *
     * @var string
     * @Flow\Inject(setting="elasticSearch.indexName")
     */
    protected $indexName;

    /**
     * The Index Name
     *
     * @var string
     * @Flow\Inject(setting="elasticSearch.defaultMapping")
     */
    protected $defaultMapping;

    /**
     * The Index Name
     *
     * @var string
     * @Flow\Inject(setting="elasticSearch.transform")
     */
    protected $transform;

    /**
     * @Flow\Inject
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @Flow\Inject
     * @var TransformerFactory
     */
    protected $transformerFactory;

    /**
     * @Flow\Inject
     * @var ReflectionService
     */
    protected $reflectionService;

    /**
     * @Flow\Inject
     * @var \Neos\Flow\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;


    /**
     * Index the submitted form data to elastic.
     *
     * @param FormData $formData
     * @param array $properties
     */
    public function index(FormData $formData, $properties)
    {
        $id = $this->persistenceManager->getIdentifierByObject($formData);
        $formValues = json_decode($formData->getFormValues(), true);
        $formValues['_date'] = $formData->getDate();
        $data = $this->parseData($formValues, $properties);
        $index = $this->getIndex();
        $type = $this->getType($index, $formData->getFormIdentifier(), $properties);
        $document = new Document($type, $data, $id);
        $document->store();
    }

    /**
     * @return Index
     */
    protected function getIndex()
    {
        $searchClient = $this->clientFactory->create();
        $index = $searchClient->findIndex($this->indexName);
        if ($index->exists() === false) {
            $index->create();
        }
        return $index;
    }

    /**
     * @param Index $index
     * @param string $typeName
     * @param array $properties
     * @return GenericType
     */
    protected function getType(Index $index, $typeName, $properties)
    {
        $type = new GenericType($index, $typeName);

        if ($index->request('HEAD', '/' . $typeName)->getStatusCode() == '404') {
            $mapping[$typeName] = $this->defaultMapping;
            foreach ($properties as $propertyName => $property) {
                $propertyMapping = Arrays::getValueByPath($property, 'elasticSearch.mapping');
                if (!empty($propertyMapping) && isset($propertyMapping['type'])) {
                    $mapping[$typeName]['properties'][$propertyName] = $propertyMapping;
                }
            }
            $type->request('PUT', '/_mapping', array(), json_encode($mapping));
        }
        return $type;
    }

    /**
     * @param array $data
     * @param array $properties
     * @return array
     */
    protected function parseData($data, $properties)
    {
        foreach ($data as $propertyName => $value) {
            $transform = Arrays::getValueByPath($properties, $propertyName . '.elasticSearch.transform');
            if (empty($transform)) {
                $transform = Arrays::getValueByPath($this->transform, $propertyName);
                if (empty($transform)) {
                    continue;
                }
            }
            $transformAnnotation = new Transform();
            $transformAnnotation->type = $transform['type'];
            if (isset($transform['options'])) {
                $transformAnnotation->options = $transform['options'];
            }
            $value = $this->transformerFactory->create($transformAnnotation->type)->transformByAnnotation($value, $transformAnnotation);
            $data[$propertyName] = $value;
        }
        return $data;
    }
}
