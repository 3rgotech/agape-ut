<?php

namespace App\Rulesets;

use App\Enums\JobTitle;
use App\Enums\OrganizationType;
use App\Models\ProjectCall;
use App\Settings\GeneralSettings;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class Application
{
    public static function rules(ProjectCall $projectCall): array
    {
        $generalSettings = app(GeneralSettings::class);
        $maxNumberOfKeywords = $projectCall->extra_attributes->get("number_of_keywords", null);
        $maxNumberOfLaboratories = $projectCall->extra_attributes->get("number_of_laboratories", null);
        $maxNumberOfStudyFields = $projectCall->extra_attributes->get("number_of_study_fields", null);
        $maxNumberOfDocuments = $projectCall->extra_attributes->get("number_of_documents", null);
        $rules = [
            'title'                              => 'required|string|max:255',
            'acronym'                            => 'required|string|max:255',
            'studyFields'                        => ['required', 'array', 'min:1', filled($maxNumberOfStudyFields) ? 'max:' . $maxNumberOfStudyFields : null],
            'summary.fr'                         => 'required',
            'summary.en'                         => 'required',
            'keywords'                           => array_filter(['required', 'array', 'min:1', filled($maxNumberOfKeywords) ? 'max:' . $maxNumberOfKeywords : null]),
            'keywords.*'                         => 'max:100',
            'short_description'                  => 'required',
            'carriers'                           => 'required|array|min:1',
            'carriers.*.first_name'              => 'required|string|max:255',
            'carriers.*.last_name'               => 'required|string|max:255',
            'carriers.*.email'                   => 'required|string|max:255|email',
            'carriers.*.phone'                   => 'required|string|max:255',
            'carriers.*.laboratory_id'           => 'nullable|required_without:carriers.*.organization|exists:laboratories,id',
            'carriers.*.job_title'               => ['nullable', 'required_with:carriers.*.laboratory_id', Rule::enum(JobTitle::class)],
            'carriers.*.job_title_other'         => 'nullable|required_if:carriers.*.job_title,other|string|max:255',
            'carriers.*.organization'            => 'nullable|required_without:carriers.*.laboratory_id|string|max:255',
            'carriers.*.organization_type'       => ['nullable', 'required_with:carriers.*.organization', Rule::enum(OrganizationType::class)],
            'carriers.*.organization_type_other' => 'nullable|required_if:carriers.*.organization_type,other|string|max:255',
            'amount_requested'                   => 'required|numeric|min:0',
            'other_fundings'                     => 'required|numeric|min:0',
            'applicationForm'                    => ($generalSettings->enableApplicationForm && $projectCall->hasMedia('applicationForm'))
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
                    ($attribute['required'] ?? false) ? 'required' : 'sometimes',
                    ($attribute['minItems'] ?? null) ? 'min:' . $attribute['minItems'] : null,
                    ($attribute['maxItems'] ?? null) ? 'max:' . $attribute['maxItems'] : null,
                ];
                $slug = $attribute['slug'] . '.*';
            }
            switch ($attribute['type']) {
                case 'text':
                case 'richtext':
                case 'textarea':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'sometimes',
                        'string',
                        ($attribute['minValue'] ?? null) ? 'min:' . $attribute['minValue'] : null,
                        ($attribute['maxValue'] ?? null) ? 'max:' . $attribute['maxValue'] : null,
                    ];
                    break;
                case 'date':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'sometimes',
                        'date',
                        ($attribute['minValue'] ?? null) ? 'after:' . self::formatDateForRule($attribute['minValue']) : null,
                        ($attribute['maxValue'] ?? null) ? 'before:' . self::formatDateForRule($attribute['maxValue']) : null,
                    ];
                    break;
                case 'checkbox':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'sometimes',
                        'array'
                    ];
                    $attributeRules[$slug . '.*'] = [Rule::in(array_column($attribute['choices'], 'value'))];
                    break;
                case 'select':
                    $attributeRules[$slug] = [
                        ($attribute['required'] ?? false) ? 'required' : 'sometimes',
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
                $slug = $attribute['slug'] . '.*';
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
            'applicationForm'                        => __('attributes.files.applicationForm'),
            'financialForm'                          => __('attributes.files.financialForm'),
            'additionalInformation'                  => __('attributes.files.additionalInformation'),
            'otherAttachments'                       => __('attributes.files.otherAttachments'),
        ];
        // Add dynamic attributes
        $dynamicAttributes = $projectCall->projectCallType->dynamic_attributes;
        foreach ($dynamicAttributes as $attribute) {
            $attributes['extra_attributes.' . $attribute['slug']] = $attribute['label'][app()->getLocale()];
        }
        return $attributes;
    }

    public static function formatDateForRule(string $date): string
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        } else if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } else if (strtotime($date) !== false) {
            return $date;
        } else {
            throw new \InvalidArgumentException('Invalid date format');
        }
    }

    public static function formatDateForMessage(string $date): string
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return Carbon::createFromFormat('Y-m-d', $date)->format(__('misc.date_format'));
        } else if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            return Carbon::createFromFormat('d/m/Y', $date)->format(__('misc.date_format'));
        } else if (strtotime($date) !== false) {
            return $date;
        } else {
            throw new \InvalidArgumentException('Invalid date format');
        }
    }
}
