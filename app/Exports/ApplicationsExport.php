<?php

namespace App\Exports;

use App\Models\Application;
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
        $carrier = $application->carriers()->where('main_carrier', true)->first();
        $data = collect([
            $application->id,
            $application->reference,
            $application->acronym,
            $application->title,
            $carrier['first_name'],
            $carrier['last_name'],
            $carrier['status'],
            $carrier['email'],
            $carrier['phone'],
        ]);
        foreach (range(1, $this->numberOfLaboratories) as $index) {
            $lab = $application->laboratories->get($index - 1);
            $data->push(
                $lab?->name ?? '',
                $lab?->unit_code ?? '',
                $lab?->regency ?? '',
                $lab?->director_email ?? '',
                $lab?->pivot?->contact_name ?? ''
            );
        }
        $data->push(Str::of($application->other_laboratories)->stripTags()->toString(),);
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
            '',
            '',
            '',
            '',
            ''
        ]);
        $secondRow = collect([
            __('exports.applications.columnGroups.application'),
            '',
            '',
            '',
            __('exports.applications.columnGroups.carrier'),
            '',
            '',
            '',
            '',
        ]);
        $thirdRow = collect([
            __('exports.applications.columns.id'),
            __('attributes.reference'),
            __('attributes.acronym'),
            __('attributes.title'),
            __('exports.applications.columns.carrier_last_name'),
            __('exports.applications.columns.carrier_first_name'),
            __('exports.applications.columns.carrier_status'),
            __('exports.applications.columns.carrier_email'),
            __('exports.applications.columns.carrier_phone'),
        ]);
        foreach (range(1, $this->numberOfLaboratories) as $index) {
            $firstRow->push('', '', '', '', '');
            $secondRow->push(
                __('exports.applications.columnGroups.laboratory', ['index' => '#' . $index]),
                '',
                '',
                '',
                ''
            );
            $thirdRow->push(
                __('exports.applications.columns.laboratory_name',           ['index' => '#' . $index]),
                __('exports.applications.columns.laboratory_unit_code',      ['index' => '#' . $index]),
                __('exports.applications.columns.laboratory_regency',        ['index' => '#' . $index]),
                __('exports.applications.columns.laboratory_director_email', ['index' => '#' . $index]),
                __('exports.applications.columns.laboratory_contact',        ['index' => '#' . $index]),
            );
        }
        $firstRow->push('');
        $secondRow->push('');
        $thirdRow->push(__('attributes.other_laboratories'));

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
        $firstRow->push(__('pages.apply.sections.budget'), '');
        $secondRow->push('', '');
        $thirdRow->push(
            __('attributes.amount_requested'),
            __('attributes.other_fundings'),
        );
        if ($this->generalSettings->enableBudgetIncomeOutcome) {
            $firstRow->push('', '');
            $secondRow->push('', '');
            $secondRow->push(
                __('attributes.total_expected_income'),
                __('attributes.total_expected_outcome')
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
