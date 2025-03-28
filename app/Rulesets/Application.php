<?php

namespace App\Rulesets;

use App\Enums\JobTitle;
use App\Enums\OrganizationType;
use App\Models\ProjectCall;
use App\Rules\ConsistentArrayKeys;
use App\Settings\GeneralSettings;
use App\Utils\Date;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class Application
{
    public static function rules(ProjectCall $projectCall): array
    {
        $generalSettings = app(GeneralSettings::class);
        $maxNumberOfKeywords = $projectCall->extra_attributes->get("number_of_keywords", null);
        $maxNumberOfStudyFields = $projectCall->extra_attributes->get("number_of_study_fields", null);
        $maxNumberOfDocuments = $projectCall->extra_attributes->get("number_of_documents", null);
        $rules = [
            'title'                                   => 'required|string|max:255',
            'acronym'                                 => 'required|string|max:255',
            'studyFields'                             => $maxNumberOfStudyFields > 0 ? ('required|array|min:1|max:' . $maxNumberOfStudyFields) : 'nullable',
            'summary.fr'                              => 'required',
            'summary.en'                              => 'required',
            'keywords'                                => $maxNumberOfKeywords > 0 ? ('required|array|min:1|max:' . $maxNumberOfKeywords) : 'nullable',
            'keywords.*'                              => 'max:100',
            'short_description'                       => 'required',
            'carriers'                                => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    $mainCarriers = collect($value)->filter(fn($carrier) => $carrier['main_carrier'] === true)->count();
                    if ($mainCarriers === 0) {
                        $fail(__('validation.custom.carrier.main_carrier_required'));
                    } else if ($mainCarriers >= 2) {
                        $fail(__('validation.custom.carrier.main_carrier_max'));
                    }
                }
            ],
            'carriers.*.first_name'                   => 'required|string|max:255',
            'carriers.*.last_name'                    => 'required|string|max:255',
            'carriers.*.email'                        => 'required|string|max:255|email',
            'carriers.*.phone'                        => 'required|string|max:255',
            'carriers.*.main_carrier'                 => 'boolean',
            'carriers.*.laboratory_id'                => 'nullable|required_without:carriers.*.organization|exists:laboratories,id',
            'carriers.*.job_title'                    => ['nullable', 'required_with:carriers.*.laboratory_id', Rule::enum(JobTitle::class)],
            'carriers.*.job_title_other'              => 'nullable|required_if:carriers.*.job_title,other|string|max:255',
            'carriers.*.organization'                 => 'nullable|required_without:carriers.*.laboratory_id|string|max:255',
            'carriers.*.organization_type'            => ['nullable', 'required_with:carriers.*.organization', Rule::enum(OrganizationType::class)],
            'carriers.*.organization_type_other'      => 'nullable|required_if:carriers.*.organization_type,other|string|max:255',
            'amount_requested'                        => 'required|numeric|min:0',
            'other_fundings'                          => 'required|numeric|min:0',
            'laboratory_budget'                       => ['array', 'min:1', 'max:2'],
            'laboratory_budget.*'                     => 'required|array',
            'laboratory_budget.*.laboratory_id'       => 'nullable|required_without:laboratory_budget.*.organization|exists:laboratories,id|distinct',
            'laboratory_budget.*.organization'        => 'nullable|required_without:laboratory_budget.*.laboratory_id|string|max:255|distinct',
            'laboratory_budget.*.total_amount'        => 'required|numeric|min:0',
            'laboratory_budget.*.hr_expenses'         => 'required|numeric|min:0',
            'laboratory_budget.*.operating_expenses'  => 'required|numeric|min:0',
            'laboratory_budget.*.investment_expenses' => 'required|numeric|min:0',
            'applicationForm'                         => ($generalSettings->enableApplicationForm && $projectCall->hasMedia('applicationForm'))
                ? 'required|array|min:1'
                : 'prohibited',
            'financialForm'                            => ($generalSettings->enableFinancialForm && $projectCall->hasMedia('financialForm'))
                ? 'required|array|min:1'
                : 'prohibited',
            'additionalInformation'                    => ($generalSettings->enableAdditionalInformation && $projectCall->hasMedia('additionalInformation'))
                ? 'required|array|min:1'
                : 'prohibited',
            'otherAttachments'                         => $generalSettings->enableOtherAttachments
                ? ['array', 'min:0', filled($maxNumberOfDocuments) ? 'max:' . $maxNumberOfDocuments : null]
                : 'prohibited',
        ];
        // Use settings to determine if these fields are required
        if ($generalSettings->enableBudgetIncomeOutcome) {
            $rules['total_expected_income']  = 'required|numeric|min:0';
            $rules['total_expected_outcome'] = 'required|numeric|min:0';
        }
        // Add rules for dynamic attributes
        $dynamicAttributes = $projectCall->projectCallType->dynamic_attributes;
        foreach ($dynamicAttributes as $attribute) {
            $attributeRules = [];

            $slug = 'extra_attributes.' . $attribute['slug'];
            if ($attribute['repeatable'] ?? false) {
                $attributeRules[$slug] = [
                    ($attribute['required'] ?? false) ? 'required' : 'nullable',
                    ($attribute['minItems'] ?? null) ? 'min:' . $attribute['minItems'] : null,
                    ($attribute['maxItems'] ?? null) ? 'max:' . $attribute['maxItems'] : null,
                ];
                $slug = $slug . '.*.value';
            }
            switch ($attribute['type']) {
                case 'text':
                case 'richtext':
                case 'textarea':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'nullable',
                        'string',
                        ($attribute['minValue'] ?? null) ? 'min:' . $attribute['minValue'] : null,
                        ($attribute['maxValue'] ?? null) ? 'max:' . $attribute['maxValue'] : null,
                    ];
                    break;
                case 'date':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'nullable',
                        'date',
                        ($attribute['minValue'] ?? null) ? 'after:' . self::formatDateForRule($attribute['minValue']) : null,
                        ($attribute['maxValue'] ?? null) ? 'before:' . self::formatDateForRule($attribute['maxValue']) : null,
                    ];
                    break;
                case 'checkbox':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'nullable',
                        'array'
                    ];
                    $attributeRules[$slug . '.*'] = [Rule::in(array_column($attribute['choices'], 'value'))];
                    break;
                case 'select':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'nullable',
                        $attribute['multiple'] ? 'array' : 'string'
                    ];
                    if ($attribute['multiple']) {
                        $attributeRules[$slug . '.*'] = [Rule::in(array_column($attribute['options'], 'value'))];
                    }
                    break;
            }

            foreach ($attributeRules as $name => $r) {
                $rules[$name] = array_filter($r);
            }
        }

        return $rules;
    }
    public static function messages(ProjectCall $projectCall): array
    {
        $messages = [
            'carriers.*.laboratory_id.required_without'      => __('validation.custom.carrier.laboratory_required'),
            'carriers.*.job_title.required_with'             => __('validation.custom.carrier.job_title_required'),
            'carriers.*.job_title_other.required_if'         => __('validation.custom.carrier.job_title_other_required'),
            'carriers.*.organization.required_without'       => __('validation.custom.carrier.organization_required'),
            'carriers.*.organization_type.required_with'     => __('validation.custom.carrier.organization_type_required'),
            'carriers.*.organization_type_other.required_if' => __('validation.custom.carrier.organization_type_other_required'),
        ];
        // Add messages for dynamic attributes
        $dynamicAttributes = $projectCall->projectCallType->dynamic_attributes;
        foreach ($dynamicAttributes as $attribute) {
            $slug = 'extra_attributes.' . $attribute['slug'];
            if ($attribute['repeatable'] ?? false) {
                if (filled($attribute['minItems'] ?? null)) {
                    $messages[$slug . '.min'] = __('validation.min.array', [
                        'attribute' => $attribute['label'][app()->getLocale()],
                        'min'       => $attribute['minItems']
                    ]);
                }
                if (filled($attribute['maxItems'] ?? null)) {
                    $messages[$slug . '.max'] = __('validation.max.array', [
                        'attribute' => $attribute['label'][app()->getLocale()],
                        'max'       => $attribute['maxItems']
                    ]);
                }
                $slug = $slug . '.*';
            }

            if ($attribute['type'] === 'date') {
                if (filled($attribute['minValue'] ?? null)) {
                    $messages[$slug . '.after'] = __('validation.after', [
                        'attribute' => $attribute['label'][app()->getLocale()],
                        'date' => self::formatDateForMessage($attribute['minValue'])
                    ]);
                }
                if (filled($attribute['maxValue'] ?? null)) {
                    $messages[$slug . '.before'] = __('validation.before', [
                        'attribute' => $attribute['label'][app()->getLocale()],
                        'date' => self::formatDateForMessage($attribute['maxValue'])
                    ]);
                }
            }
        }
        return $messages;
    }
    public static function attributes(ProjectCall $projectCall): array
    {
        $attributes = [
            'title'                                  => __('attributes.title'),
            'acronym'                                => __('attributes.acronym'),
            'carrier.first_name'                     => __('attributes.first_name'),
            'carrier.last_name'                      => __('attributes.last_name'),
            'carrier.email'                          => __('attributes.email'),
            'carrier.phone'                          => __('attributes.phone'),
            'carrier.status'                         => __('attributes.carrier_status'),
            'applicationLaboratories'                => __('resources.laboratory_plural'),
            'applicationLaboratories.*.contact_name' => __('attributes.contact_name'),
            'studyFields'                            => __('resources.study_field_plural'),
            'summary.fr'                             => __('attributes.summary_fr'),
            'summary.en'                             => __('attributes.summary_en'),
            'keywords'                               => __('attributes.keywords'),
            'keywords.*'                             => __('attributes.keywords'),
            'short_description'                      => __('attributes.short_description'),
            'amount_requested'                       => __('attributes.amount_requested'),
            'other_fundings'                         => __('attributes.other_fundings'),
            'total_expected_income'                  => __('attributes.total_expected_income'),
            'total_expected_outcome'                 => __('attributes.total_expected_outcome'),
            'laboratory_budget.*.laboratory_id'      => __('resources.laboratory'),
            'laboratory_budget.*.organization'       => __('attributes.organization'),
            'applicationForm'                        => __('attributes.files.applicationForm'),
            'financialForm'                          => __('attributes.files.financialForm'),
            'additionalInformation'                  => __('attributes.files.additionalInformation'),
            'otherAttachments'                       => __('attributes.files.otherAttachments'),
        ];
        // Add dynamic attributes
        $dynamicAttributes = $projectCall->projectCallType->dynamic_attributes;
        foreach ($dynamicAttributes as $attribute) {
            $attributes['extra_attributes.' . $attribute['slug'] . '.*.value'] = $attribute['label'][app()->getLocale()];
        }
        return $attributes;
    }

    public static function formatDateForRule(string $date): string
    {
        $dateObj = Date::parse($date);
        if ($dateObj !== null) {
            return $dateObj->format('Y-m-d');
        } else if (strtotime($date) !== false) {
            return $date;
        } else {
            throw new \InvalidArgumentException('Invalid date format');
        }
    }

    public static function formatDateForMessage(string $date): string
    {
        $dateObj = Date::parse($date);
        if ($dateObj !== null) {
            return $dateObj->format(__('misc.date_format'));
        } else if (strtotime($date) !== false) {
            return $date;
        } else {
            throw new \InvalidArgumentException('Invalid date format');
        }
    }
}
