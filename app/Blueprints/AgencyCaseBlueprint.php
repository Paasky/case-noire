<?php

namespace App\Blueprints;

use App\Constants\CaseConst;
use App\Managers\LocationManager;
use App\Models\Agency;
use App\Models\AgencyCase;
use App\Models\CaseTemplate;
use App\Models\Location;

class AgencyCaseBlueprint implements BlueprintInterface
{
    /** @var Agency */
    protected $agency;
    /** @var CaseTemplate */
    protected $caseTemplate;
    /** @var Location */
    protected $location;
    protected $status = CaseConst::STATUS_OPEN;
    protected $data = [];

    /**
     * AgencyCaseBlueprint constructor.
     * @param Agency $agency
     * @param CaseTemplate $caseTemplate
     * @param Location $location
     */
    public function __construct(Agency $agency, CaseTemplate $caseTemplate, Location $location)
    {
        $this->agency = $agency;
        $this->caseTemplate = $caseTemplate;
        $this->location = $location;
    }

    public function getModelParams(bool $verify = true): array
    {
        if ($verify) {
            $this->isValid(true);
        }

        return [
            'agency_id' => $this->agency->id,
            'case_template_id' => $this->caseTemplate->id,
            'location_id' => $this->location->id,
            'coords' => LocationManager::getCoordsNextToLocation($this->location),
            'status' => $this->status ?: null,
            'data' => $this->data ?: null,
        ];
    }

    public function findExistingModel(): ?AgencyCase
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return AgencyCase::where($this->getSearchParams())->first();
    }

    public function getSearchParams(): array
    {
        return [
            'agency_id' => $this->agency->id,
            'case_template_id' => $this->caseTemplate->id,
        ];
    }

    public function isValid(bool $verify = false): bool
    {
        $errors = [];

        if (!$this->agency->id) {
            $errors[] = "Agency doesn't have ID";
        }

        if (!$this->caseTemplate->id) {
            $errors[] = "CaseTemplate doesn't have ID";
        }

        if (!$this->location->id) {
            $errors[] = "Location doesn't have ID";
        }
        if (!$this->location->coords) {
            $errors[] = "Location doesn't have coordinates";
        }

        if (!in_array($this->status, CaseConst::STATUSES)) {
            $errors[] = "Invalid status $this->status";
        }

        if ($errors) {
            if ($verify) {
                throw new \InvalidArgumentException("Invalid AgencyCaseBlueprint: " . json_encode($errors));
            }
            return false;
        }
        return true;
    }

    /**
     * @return Agency
     */
    public function getAgency(): Agency
    {
        return $this->agency;
    }

    /**
     * @param Agency $agency
     * @return AgencyCaseBlueprint
     */
    public function setAgency(Agency $agency): AgencyCaseBlueprint
    {
        $this->agency = $agency;
        return $this;
    }

    /**
     * @return CaseTemplate
     */
    public function getCaseTemplate(): CaseTemplate
    {
        return $this->caseTemplate;
    }

    /**
     * @param CaseTemplate $caseTemplate
     * @return AgencyCaseBlueprint
     */
    public function setCaseTemplate(CaseTemplate $caseTemplate): AgencyCaseBlueprint
    {
        $this->caseTemplate = $caseTemplate;
        return $this;
    }

    /**
     * @return Location
     */
    public function getLocation(): Location
    {
        return $this->location;
    }

    /**
     * @param Location $location
     * @return AgencyCaseBlueprint
     */
    public function setLocation(Location $location): AgencyCaseBlueprint
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return AgencyCaseBlueprint
     */
    public function setStatus(string $status): AgencyCaseBlueprint
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return AgencyCaseBlueprint
     */
    public function setData(array $data): AgencyCaseBlueprint
    {
        $this->data = $data;
        return $this;
    }
}