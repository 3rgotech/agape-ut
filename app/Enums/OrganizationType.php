<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OrganizationType: string implements HasLabel
{
    case BIG_COMPANY                = 'big_company';
    case PRIVATE_COMPANY            = 'private_company';
    case SOCIAL_COMPANY             = 'social_company';
    case NON_LUCRATIVE_ORGANIZATION = 'non_lucrative_organization';
    case PUBLIC_ORGANIZATION        = 'public_organization';
    case LOCAL_ORGANIZATION         = 'local_organization';
    case PUBLIC_INSTITUTION         = 'public_institution';
    case COMMUNITY                  = 'community';
    case STARTUP                    = 'startup';
    case INTERNATIONAL_ORGANIZATION = 'international_organization';
    case OTHER_ACADEMIC             = 'other_academic';
    case INDIVIDUAL                 = 'individual';
    case OTHER                      = 'other';

    public function getLabel(): ?string
    {
        return __('enums.organization_type.' . $this->value);
    }
}
