<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\HangingForm;
use Illuminate\Console\Command;

class BackfillAuditLogs extends Command
{
    protected $signature = 'audit:backfill';
    protected $description = 'Backfill audit log from existing data';

    public function handle()
    {
        $forms = HangingForm::with(['monitorControl','returItems'])->get();

        foreach ($forms as $form) {
            // Deteksi form_key otomatis
            $formKey = 'hanging_form';

            if (
                !is_null($form->dead_count) ||
                !is_null($form->retur_count) ||
                !is_null($form->retur_total_kg) ||
                $form->returItems->count() > 0
            ) {
                $formKey = 'retur_mati';
            }

            if (
                !is_null($form->basket_condition) ||
                !is_null($form->truck_platform_condition) ||
                !is_null($form->feather_condition)
            ) {
                $formKey = 'qc_kondisi';
            }

            // Ambil nilai perubahan (from null)
            $changes = [
                'dead_count' => ['before' => null, 'after' => $form->dead_count],
                'retur_count' => ['before' => null, 'after' => $form->retur_count],
                'retur_total_kg' => ['before' => null, 'after' => $form->retur_total_kg],
                'basket_condition' => ['before' => null, 'after' => $form->basket_condition],
                'truck_platform_condition' => ['before' => null, 'after' => $form->truck_platform_condition],
                'feather_condition' => ['before' => null, 'after' => $form->feather_condition],
                'status' => ['before' => null, 'after' => $form->status],
                'unloading_time' => ['before' => null, 'after' => $form->unloading_time],
                'finish_time' => ['before' => null, 'after' => $form->finish_time],
            ];

            // Hapus perubahan yang null semua
            $changes = array_filter($changes, function ($c) {
                return !is_null($c['after']);
            });

            AuditLog::create([
                'auditable_type' => HangingForm::class,
                'auditable_id' => $form->id,
                'form_key' => $formKey,
                'action' => 'backfill',

                'changes' => $changes ?: null,

                'meta' => [
                    'report_code' => $form->monitorControl?->report_code,
                    'location' => $form->monitorControl?->location,
                    'truck_no' => $form->monitorControl?->truck_no,
                ],
                'created_at' => $form->updated_at,
            ]);
        }

        $this->info('Backfill selesai.');
    }
}