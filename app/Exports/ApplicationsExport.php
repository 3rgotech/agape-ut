<?php

namespace App\Exports;

use App\Models\Application;
use App\Models\Laboratory;
use App\Models\ProjectCall;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ApplicationsExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithColumnFormatting, Responsable, WithEvents
{
    use Exportable;

    private $fileName;
    private $writerType = Excel::XLSX;

    protected GeneralSettings $generalSettings;

    protected int $numberOfLaboratories;
    protected int $numberOfStudyFields;
    protected int $numberOfKeywords;
    protected int $numberOfCarriers;
    protected Collection $dynamicFields;
    protected array $merges = [];


    public function __construct(protected ProjectCall $projectCall)
    {
        $this->fileName = config('app.name') . '-' . __('exports.applications.name') . '-' . $projectCall->reference . '-' . date('Y-m-d') . '.xlsx';

        $this->generalSettings = app(GeneralSettings::class);
        $this->dynamicFields = collect($this->projectCall->projectCallType->dynamic_attributes ?? []);

        $collection = $this->collection();
        $this->numberOfLaboratories = $collection->map(fn(Application $application) => $application->laboratories->count())->max() ?? 1;
        $this->numberOfStudyFields = $collection->map(fn(Application $application) => $application->studyFields->count())->max() ?? 1;
        $this->numberOfKeywords = $collection->map(fn(Application $application) => count($application->keywords))->max() ?? 1;
        $this->numberOfCarriers = $collection->map(fn(Application $application) => $application->carriers()->where('main_carrier', false)->count())->max() ?? 0;
    }

    public function collection()
    {
        return $this->projectCall
            ->applications()
            ->whereNotNull('submitted_at')
            ->get();
    }

    public function map($application): array
    {
        /**
         * @var Application $application
         */
        $data = collect([
            $application->id,
            $application->reference,
            $application->acronym,
            $application->title,
        ]);
        foreach (range(1, $this->numberOfStudyFields) as $index) {
            $sf = $application->studyFields->get($index - 1);
            $data->push($sf?->name ?? '');
        }
        foreach (range(1, $this->numberOfKeywords) as $index) {
            $data->push($application->keywords[$index - 1] ?? '');
        }
        $sectionFields = $this->dynamicFields->filter(fn($field) => $field['section'] === 'general');
        foreach ($sectionFields as $field) {
            $value = $application->extra_attributes->get($field['slug']) ?? '';
            if (is_iterable($value)) {
                $value = implode(', ', $value);
            }
            $data->push($value);
        }

        $data->push(
            Str::of($application->short_description)->stripTags()->toString(),
            Str::of($application->translate('summary', 'fr'))->stripTags()->toString(),
            Str::of($application->translate('summary', 'en'))->stripTags()->toString(),
        );
        $sectionFields = $this->dynamicFields->filter(fn($field) => $field['section'] === 'scientific');
        foreach ($sectionFields as $field) {
            $value = $application->extra_attributes->get($field['slug']) ?? '';
            if (is_iterable($value)) {
                $value = implode(', ', $value);
            }
            $data->push($value);
        }

        $carriers    = $application->carriers()->where('main_carrier', true)->get()->values();
        $carrier1    = $carriers->count() > 0 ? $carriers->first() : null;
        $carrier2    = $carriers->count() > 1 ? $carriers->last() : null;
        $teamMembers = $application->carriers()->where('main_carrier', false)->get()->values();

        $allCarriers = collect([
            // Main carriers (1 or 2, null if not present)
            $carrier1,
            $carrier2,
            // Team members (variable number, empty arrays if not present)
            ...$teamMembers,
            // Fill with null to reach the max amount
            ...Collection::times($this->numberOfCarriers - count($teamMembers), fn() => []),
        ]);

        foreach ($allCarriers as $carrier) {
            if (filled($carrier)) {
                $data->push(
                    $carrier->first_name,
                    $carrier->last_name,
                    $carrier->email,
                    $carrier->phone ?? '',
                    filled($carrier->laboratory) ? $carrier->laboratory->name : '',
                    filled($carrier->laboratory) ? $carrier->getJobTitle() : '',
                    blank($carrier->laboratory) ? $carrier->organization : '',
                    blank($carrier->laboratory) ? $carrier->getOrganizationType() : '',
                );
            } else {
                $data->push(...Collection::times(8, fn() => ''));
            }
        }

        $data->push(
            $application->amount_requested,
            $application->other_fundings
        );
        if ($this->generalSettings->enableBudgetIncomeOutcome) {
            $data->push(
                $application->total_expected_income,
                $application->total_expected_outcome
            );
        }
        $sectionFields = $this->dynamicFields->filter(fn($field) => $field['section'] === 'budget');
        foreach ($sectionFields as $field) {
            $value = $application->extra_attributes->get($field['slug']) ?? '';
            if (is_iterable($value)) {
                $value = implode(', ', $value);
            }
            $data->push($value);
        }
        foreach (range(0, 1) as $index) {
            if (filled($application->laboratory_budget[$index] ?? null)) {
                $budget = $application->laboratory_budget[$index];
                $data->push(
                    (
                        filled($budget['laboratory_id'] ?? null)
                        ? Laboratory::find($budget['laboratory_id'])?->name
                        : $budget['organization']
                    ) ?? '',
                    $budget['total_amount'] ?? '',
                    $budget['hr_expenses'] ?? '',
                    $budget['operating_expenses'] ?? '',
                    $budget['investment_expenses'] ?? '',
                    $budget['internship_expenses'] ?? '',
                );
            } else {
                $data->push(...Collection::times(6, fn() => ''));
            }
        }

        return $data->toArray();
    }

    public function columnFormats(): array
    {
        return [
            // 'I' => __('misc.excel_datetime_format'),
        ];
    }


    public function headings(): array
    {
        $firstRow = collect([
            __('resources.project_call'),
            $this->projectCall->reference,
            '',
            __('pages.apply.sections.general'),
        ]);
        $secondRow = collect([
            __('exports.applications.columnGroups.application'),
            '',
            '',
            '',
        ]);
        $thirdRow = collect([
            __('exports.applications.columns.id'),
            __('attributes.reference'),
            __('attributes.acronym'),
            __('attributes.title'),
        ]);
        $firstRow->push(...Collection::times($this->numberOfStudyFields, fn() => ''));
        $secondRow->push(__('exports.applications.columnGroups.study_fields'));
        $secondRow->push(...Collection::times($this->numberOfStudyFields - 1, fn() => ''));
        $thirdRow->push(...Collection::times(
            $this->numberOfStudyFields,
            fn($i) => __('exports.applications.columns.study_field', ['index' => '#' . $i])
        ));

        $firstRow->push(...Collection::times($this->numberOfKeywords, fn() => ''));
        $secondRow->push(__('exports.applications.columnGroups.keywords'));
        $secondRow->push(...Collection::times($this->numberOfKeywords - 1, fn() => ''));
        $thirdRow->push(...Collection::times(
            $this->numberOfKeywords,
            fn($i) => __('exports.applications.columns.keyword', ['index' => '#' . $i])
        ));

        // Dynamic fields of first section
        $sectionFields = $this->dynamicFields->filter(fn($field) => $field['section'] === 'general');
        foreach ($sectionFields as $field) {
            $firstRow->push('');
            $secondRow->push('');
            $thirdRow->push($field['label'][app()->getLocale()]);
        }

        // Second section
        $firstRow->push(__('pages.apply.sections.scientific'), '', '');
        $secondRow->push('', '', '');
        $thirdRow->push(
            __('attributes.short_description'),
            __('attributes.summary_fr'),
            __('attributes.summary_en'),
        );

        // Dynamic fields of second section
        $sectionFields = $this->dynamicFields->filter(fn($field) => $field['section'] === 'scientific');
        foreach ($sectionFields as $field) {
            $firstRow->push('');
            $secondRow->push('');
            $thirdRow->push($field['label'][app()->getLocale()]);
        }

        // Third section
        $carrierFields = [
            __('exports.applications.columns.carrier_last_name'),
            __('exports.applications.columns.carrier_first_name'),
            __('exports.applications.columns.carrier_email'),
            __('exports.applications.columns.carrier_phone'),
            __('resources.laboratory'),
            __('attributes.job_title'),
            __('attributes.organization'),
            __('attributes.organization_type')
        ];
        $firstRow->push(__('pages.apply.sections.carriers'));
        $firstRow->push(...Collection::times($this->numberOfCarriers * count($carrierFields) - 1, fn() => ''));
        $secondRow->push(__('exports.applications.columnGroups.carrier1'));
        $secondRow->push(...Collection::times(count($carrierFields) - 1, fn() => ''));
        $secondRow->push(__('exports.applications.columnGroups.carrier2'));
        $secondRow->push(...Collection::times(count($carrierFields) - 1, fn() => ''));
        $thirdRow->push(...$carrierFields, ...$carrierFields);
        foreach (range(1, $this->numberOfCarriers) as $index) {
            $secondRow->push(__('exports.applications.columnGroups.teamMembers', ['index' => $index]));
            $secondRow->push(...Collection::times(count($carrierFields) - 1, fn() => ''));
            $thirdRow->push(...$carrierFields);
        }

        // Fourth section
        $firstRow->push(__('pages.apply.sections.budget'), '');
        $secondRow->push('', '');
        $thirdRow->push(
            __('attributes.amount_requested'),
            __('attributes.other_fundings'),
        );
        if ($this->generalSettings->enableBudgetIncomeOutcome) {
            $firstRow->push('', '');
            $secondRow->push('', '');
            $thirdRow->push(
                __('attributes.total_expected_income'),
                __('attributes.total_expected_outcome')
            );
        }
        foreach (range(0, 1) as $index) {
            $firstRow->push(...Collection::times(6, fn() => ''));
            $secondRow->push(
                __('exports.applications.columns.budget_laboratory', ['index' => '#' . ($index + 1)]),
                ...Collection::times(5, fn() => '')
            );
            $thirdRow->push(
                __('resources.laboratory') . '/' . __('attributes.organization'),
                __('attributes.total_amount'),
                __('attributes.hr_expenses'),
                __('attributes.operating_expenses'),
                __('attributes.investment_expenses'),
                __('attributes.internship_expenses'),
            );
        }

        // Dynamic fields of third section
        $sectionFields = $this->dynamicFields->filter(fn($field) => $field['section'] === 'budget');
        foreach ($sectionFields as $field) {
            $firstRow->push('');
            $secondRow->push('');
            $thirdRow->push($field['label'][app()->getLocale()]);
        }

        return [
            $firstRow->all(),
            $secondRow->all(),
            $thirdRow->all(),
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->freezePane('D4');
                // TODO : Merges
            },
        ];
    }
}
