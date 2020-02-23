<?php

namespace App\Blueprints;

use App\Models\Common\CaseNoireModel;

interface BlueprintInterface
{
    public function getModelParams(bool $verify = true): array;
    /**
     * @return CaseNoireModel|null
     */
    public function findExistingModel();
    public function getSearchParams(): array;
    public function isValid(bool $verify = false): bool;
}