<?php

namespace App\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->hasField('deletedAt')) {
            return $targetTableAlias . '.deleted_at IS NULL';
        }

        return '';
    }
}
