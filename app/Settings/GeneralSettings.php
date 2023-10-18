<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public int $defaultNumberOfWorkshopDates;
    public int $defaultNumberOfExperts;
    public int $defaultNumberOfDocuments;
    public int $defaultNumberOfLaboratories;
    public int $defaultNumberOfStudyFields;
    public int $defaultNumberOfKeywords;

    public bool $enableApplicationForm;
    public bool $enableFinancialForm;
    public bool $enableAdditionalInformation;
    public bool $enableOtherAttachments;

    public string $extensionsApplicationForm;
    public string $extensionsFinancialForm;
    public string $extensionsAdditionalInformation;
    public string $extensionsOtherAttachments;

    public array $grades;
    public array $notation;

    public static function group(): string
    {
        return 'general';
    }
}
