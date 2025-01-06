<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateCarriers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agape:migrate-carriers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating carriers...');
        $this->withProgressBar(DB::table('applications')->get(), function ($application) {
            $laboratories = DB::table('application_laboratory')->where('application_id', $application->id)->orderBy('order')->get();

            foreach ($laboratories as $laboratory) {
                $carrier = json_decode($application->carrier ?? '[]', false);
                $attributes = ($laboratory->order === 1 && filled($carrier))
                    ? [
                        'first_name'   => $carrier?->first_name ?? 'N/A',
                        'last_name'    => $carrier?->last_name ?? 'N/A',
                        'email'        => $carrier?->email ?? 'N/A',
                        'phone'        => $carrier?->phone ?? 'N/A',
                        'main_carrier' => true,
                    ] :
                    [
                        'first_name'   => 'N/A',
                        'last_name'    => 'N/A',
                        'email'        => 'N/A',
                        'phone'        => 'N/A',
                        'main_carrier' => false,
                    ];
                DB::table('carriers')->insert([
                    ...$attributes,
                    'application_id'          => $application->id,
                    'laboratory_id'           => $laboratory->laboratory_id,
                    'job_title'               => 'other',
                    'job_title_other'         => 'N/A',
                    'organization_type'       => 'other',
                    'organization'            => 'N/A',
                    'organization_type_other' => 'N/A',
                ]);
            }
        });
        $this->info('Carriers migrated successfully');
    }
}
