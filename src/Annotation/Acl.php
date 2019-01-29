<?php

namespace Everycheck\Acl;

use Doctrine\Common\Annotations\Annotation as DoctrineAnnotation;

/**
 * @DoctrineAnnotation
 * @Target("CLASS")
 */
class Acl
{
    /**
     * @Required
     *
     * @var string
     */
    public $class;

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}