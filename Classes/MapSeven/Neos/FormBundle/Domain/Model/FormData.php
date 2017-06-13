<?php
namespace MapSeven\Neos\FormBundle\Domain\Model;

/*
 * This file is part of the MapSeven.Neos.FormBundle package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\ValueObject
 */
class FormData
{

    /**
     * The formData formIdentifier
     *
     * @var string
     */
    protected $formIdentifier;

    /**
     * The formData accountIdentifier
     *
     * @var string
     * @ORM\Column(nullable=true)
     */
    protected $accountIdentifier = null;

    /**
     * The formData date
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * The formData formValues
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $formValues;


    /**
     * Constructs this post
     */
    public function __construct($formIdentifier, $accountIdentifier = null, $formValues)
    {
        $this->date = new \DateTime();
        $this->formIdentifier = $formIdentifier;
        $this->accountIdentifier = $accountIdentifier;
        $this->formValues = $formValues;
    }

    /**
     * @return string
     */
    public function getFormIdentifier()
    {
        return $this->formIdentifier;
    }

    /**
     * @return string
     */
    public function getAccountIdentifier()
    {
        return $this->accountIdentifier;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getFormValues()
    {
        return $this->formValues;
    }
}
