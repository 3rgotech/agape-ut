<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JobTitle: string implements HasLabel
{
    case TEACHER_RESEARCHER       = 'teacher_researcher';
    case RESEARCHER               = 'researcher';
    case DOCTORAL_STUDENT         = 'doctoral_student';
    case POST_DOCTORAL_RESEARCHER = 'post_doctoral_researcher';
    case RESEARCH_ENGINEER        = 'research_engineer';
    case OTHER                    = 'other';

    public function getLabel(): ?string
    {
        return __('enums.job_title.' . $this->value);
    }
}
