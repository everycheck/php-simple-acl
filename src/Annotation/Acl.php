<?php

namespace Everycheck\Acl\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Acl extends Annotation
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