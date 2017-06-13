<?php
namespace MapSeven\Neos\FormBundle\Finishers;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "MapSeven.Neos.FormBundle*
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Neos\Flow\Annotations as Flow;
use MapSeven\Neos\FormBundle\Domain\Model\FormData;
use MapSeven\Neos\FormBundle\Domain\Repository\FormDataRepository;
use MapSeven\Neos\FormBundle\ElasticSearch\Service\ElasticSearchService;
use Neos\Form\Core\Model\FormElementInterface;

/**
 * This finisher sends an email to one recipient
 *
 */
class PersistenceFinisher extends \Neos\Form\Core\Model\AbstractFinisher
{

    /**
     * @var array
     */
    protected $defaultOptions = array(
        'db' => true,
        'elasticSearch' => false
    );

    /**
     * @Flow\Inject
     * @var FormDataRepository
     */
    protected $formDataRepository;

    /**
     * @Flow\Inject
     * @var ElasticSearchService
     */
    protected $elasticSearchService;

    /**
     * @var \Neos\Flow\Security\Context
     */
    protected $securityContext;

    /**
     * Injects the security context
     *
     * @param \Neos\Flow\Security\Context $securityContext The security context
     * @return void
     */
    public function injectSecurityContext(\Neos\Flow\Security\Context $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Executes this finisher
     * @see AbstractFinisher::execute()
     *
     * @return void
     * @throws \Neos\Form\Exception\FinisherException
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $formValues = json_encode($formRuntime->getFormState()->getFormValues());
        $formIdentifier = $formRuntime->getFormDefinition()->getIdentifier();
        $accountIdentifier = $this->getAccountIdentifier();
        $formData = new FormData($formIdentifier, $accountIdentifier, $formValues);
        foreach ($this->defaultOptions as $option => $value) {
            $value = $this->parseOption($option);
            if ($value === false) {
                continue;
            }
            switch ($option) {
                case 'db':
                    $this->formDataRepository->add($formData);
                    break;
                case 'elasticSearch':
                    $properties = array();
                    $elements = $formRuntime->getFormDefinition()->getRenderablesRecursively();
                    foreach ($elements as $element) {
                        if (!$element instanceof FormElementInterface) {
                            continue;
                        }
                        $properties[$element->getIdentifier()] = $element->getProperties();
                    }
                    $this->elasticSearchService->index($formData, $properties);
            }
        }
    }

    /**
     * @return null|string
     */
    protected function getAccountIdentifier()
    {
        $accountIdentifier = null;
        if ($this->securityContext !== null && $this->securityContext->canBeInitialized()) {
            $account = $this->securityContext->getAccount();
            if ($account !== null) {
                $accountIdentifier = $account->getAccountIdentifier();
            }
        }
        return $accountIdentifier;
    }
}
