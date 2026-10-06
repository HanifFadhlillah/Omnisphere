<?php

namespace Database\Seeders;

use App\Models\MilestoneOutputFulfillment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectDummySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin_pmo')->first();
        $staff = User::where('role', 'staff_pmo')->first();

        if (! $admin || ! $staff) {
            $this->command->warn('Jalankan RoleUserSeeder dulu sebelum ProjectDummySeeder.');
            return;
        }

        // Project dataset dengan kombinasi status, deadline, dan milestone bervariasi
        $projects = [
            [
                'code'           => 'KP-2026-001',
                'name'           => 'Integrasi SAP ERP Modul Keuangan',
                'description'    => 'Integrasi modul keuangan SAP ERP dengan sistem billing eksisting untuk otomatisasi rekonsiliasi.',
                'vendor_name'    => 'PT Solusi Digital Nusantara',
                'business_owner' => 'Divisi Keuangan',
                'status'         => 'implementasi',
                'start_offset'   => -60,
                'end_offset'     => 45,
                'milestones'     => [
                    ['title' => 'Kick-off & Perencanaan',         'days' => 10, 'status' => 'done'],
                    ['title' => 'Blueprint & Desain Arsitektur',  'days' => 20, 'status' => 'done'],
                    ['title' => 'Konfigurasi & Development Modul','days' => 30, 'status' => 'in_progress'],
                    ['title' => 'System Integration Test (SIT)',  'days' => 20, 'status' => 'pending'],
                    ['title' => 'Go-Live & Serah Terima',          'days' => 10, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2026-002',
                'name'           => 'Sistem Monitoring Debit Air Real-time',
                'description'    => 'Pengembangan dashboard monitoring debit air bendungan secara real-time berbasis IoT.',
                'vendor_name'    => 'PT Teknologi Air Indonesia',
                'business_owner' => 'Divisi Operasi & Pemeliharaan',
                'status'         => 'quality_assurance',
                'start_offset'   => -80,
                'end_offset'     => 5, // Alert Kuning (<=7 hari)
                'milestones'     => [
                    ['title' => 'Survey Lokasi & Spesifikasi IoT', 'days' => 10, 'status' => 'done'],
                    ['title' => 'Instalasi Sensor & Telemetri',    'days' => 25, 'status' => 'done'],
                    ['title' => 'Integrasi Data & Dashboard',     'days' => 25, 'status' => 'done'],
                    ['title' => 'Quality Assurance & Kalibrasi',   'days' => 15, 'status' => 'under_review'],
                    ['title' => 'Serah Terima & Pelatihan',        'days' => 10, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2026-003',
                'name'           => 'Aplikasi Mobile Layanan Pelanggan',
                'description'    => 'Aplikasi mobile untuk pengaduan dan permohonan layanan air baku oleh pelanggan korporat.',
                'vendor_name'    => 'PT Karya Aplikasi Bangsa',
                'business_owner' => 'Divisi Pelayanan Pelanggan',
                'status'         => 'quality_assurance',
                'start_offset'   => -50,
                'end_offset'     => 3, // Alert Kuning (<=7 hari)
                'milestones'     => [
                    ['title' => 'Inception & UI/UX Prototyping',   'days' => 10, 'status' => 'done'],
                    ['title' => 'Development Backend API & Mobile', 'days' => 25, 'status' => 'done'],
                    ['title' => 'Security Assessment & QA',        'days' => 15, 'status' => 'in_progress'],
                    ['title' => 'Deployment Playstore/AppStore',   'days' => 10, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2025-014',
                'name'           => 'Digitalisasi Arsip Kontrak Vendor',
                'description'    => 'Digitalisasi dan pengindeksan seluruh arsip kontrak vendor 5 tahun terakhir.',
                'vendor_name'    => 'PT Arsip Data Sejahtera',
                'business_owner' => 'Divisi Pengadaan',
                'status'         => 'manage_operation',
                'is_completed'   => true,
                'start_offset'   => -180,
                'end_offset'     => -30,
                'milestones'     => [
                    ['title' => 'Inventarisasi Dokumen',           'days' => 20, 'status' => 'done'],
                    ['title' => 'Pemindaian & OCR Document',       'days' => 50, 'status' => 'done'],
                    ['title' => 'Pengindeksan & Upload Database',  'days' => 50, 'status' => 'done'],
                    ['title' => 'Training User & BAST',            'days' => 30, 'status' => 'done'],
                ],
            ],
            [
                'code'           => 'KP-2026-005',
                'name'           => 'Upgrade Infrastruktur Jaringan Kantor Pusat',
                'description'    => 'Peningkatan kapasitas jaringan dan keamanan siber di kantor pusat PJT II.',
                'vendor_name'    => 'PT Jaringan Prima Teknindo',
                'business_owner' => 'Divisi IT',
                'status'         => 'feasibility_study',
                'start_offset'   => 10,
                'end_offset'     => 120,
                'milestones'     => [
                    ['title' => 'Assessment Topologi & Fiber Optic','days' => 15, 'status' => 'in_progress'],
                    ['title' => 'Pengadaan Router & Switch Core',   'days' => 30, 'status' => 'pending'],
                    ['title' => 'Migration & Cutover Network',      'days' => 40, 'status' => 'pending'],
                    ['title' => 'Security Hardening',               'days' => 25, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2026-006',
                'name'           => 'Implementasi GIS Aset Infrastruktur',
                'description'    => 'Pemetaan digital seluruh aset jaringan pipa dan infrastruktur menggunakan GIS berbasis web.',
                'vendor_name'    => 'PT Geospasial Tekindo',
                'business_owner' => 'Divisi Aset & Pemeliharaan',
                'status'         => 'implementasi',
                'start_offset'   => -110,
                'end_offset'     => -10, // Alert Merah (Overdue)
                'milestones'     => [
                    ['title' => 'Pemetaan Spasial & Baseline',     'days' => 20, 'status' => 'done'],
                    ['title' => 'Digitasi Jaringan Pipa & Aset',   'days' => 40, 'status' => 'done'],
                    ['title' => 'Pengembangan Portal Web GIS',      'days' => 30, 'status' => 'in_progress'],
                    ['title' => 'Integration & Field Testing',     'days' => 20, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2026-007',
                'name'           => 'Portal E-Procurement Internal PJT II',
                'description'    => 'Sistem pengadaan barang dan jasa internal berbasis web untuk menggantikan proses manual.',
                'vendor_name'    => 'PT Digital Procurement Solutions',
                'business_owner' => 'Divisi Pengadaan',
                'status'         => 'quality_assurance',
                'start_offset'   => -90,
                'end_offset'     => -5, // Alert Merah (Overdue)
                'milestones'     => [
                    ['title' => 'Analisis Workflow Pengadaan',     'days' => 15, 'status' => 'done'],
                    ['title' => 'Dev Modul Vendor & e-Tendering',  'days' => 30, 'status' => 'done'],
                    ['title' => 'Penetration Test & Security Audit','days' => 20, 'status' => 'under_review'],
                    ['title' => 'Rollout & Sosialisasi Vendor',    'days' => 15, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2026-008',
                'name'           => 'Sistem Manajemen Energi PLTA',
                'description'    => 'Pemantauan dan optimalisasi penggunaan energi di seluruh PLTA yang dikelola PJT II.',
                'vendor_name'    => 'PT Energi Cerdas Indonesia',
                'business_owner' => 'Divisi Pembangkitan',
                'status'         => 'perencanaan',
                'start_offset'   => -30,
                'end_offset'     => 90,
                'milestones'     => [
                    ['title' => 'Studi Audit Energi Awal',         'days' => 15, 'status' => 'done'],
                    ['title' => 'Penyusunan Dokumen SRS & HPS',    'days' => 25, 'status' => 'in_progress'],
                    ['title' => 'Pengadaan Smart Meter PLTA',      'days' => 35, 'status' => 'pending'],
                    ['title' => 'Commissioning & Handover',        'days' => 25, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2026-009',
                'name'           => 'Dashboard Pelaporan Kinerja Direksi',
                'description'    => 'Dashboard eksekutif untuk pelaporan KPI operasional dan keuangan kepada Direksi PJT II.',
                'vendor_name'    => 'PT Solusi Data Analytics',
                'business_owner' => 'Sekretariat Perusahaan',
                'status'         => 'implementasi',
                'start_offset'   => -75,
                'end_offset'     => 7, // Alert Kuning (<=7 hari)
                'milestones'     => [
                    ['title' => 'ETL Pipeline & Data Warehouse',   'days' => 25, 'status' => 'done'],
                    ['title' => 'Desain Visual Dashboard Exec',    'days' => 15, 'status' => 'done'],
                    ['title' => 'Testing Performance & Security',  'days' => 20, 'status' => 'in_progress'],
                    ['title' => 'Launching Executive View',         'days' => 10, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2025-010',
                'name'           => 'Migrasi Email Korporat ke Microsoft 365',
                'description'    => 'Migrasi seluruh akun email korporat dari server on-premise ke Microsoft 365.',
                'vendor_name'    => 'PT Mitra Cloud Indonesia',
                'business_owner' => 'Divisi IT',
                'status'         => 'manage_operation',
                'is_completed'   => true,
                'start_offset'   => -210,
                'end_offset'     => -60,
                'milestones'     => [
                    ['title' => 'Assessment & Tenant Provisioning','days' => 20, 'status' => 'done'],
                    ['title' => 'Migration Batch 1-5',             'days' => 60, 'status' => 'done'],
                    ['title' => 'Hybrid Exchange Decommission',   'days' => 40, 'status' => 'done'],
                    ['title' => 'User Training & Helpdesk',        'days' => 30, 'status' => 'done'],
                ],
            ],
            [
                'code'           => 'KP-2026-011',
                'name'           => 'Change Request — Penambahan Modul HR',
                'description'    => 'Penambahan modul manajemen sumber daya manusia setelah proyek ERP selesai.',
                'vendor_name'    => 'PT Solusi Digital Nusantara',
                'business_owner' => 'Divisi HR',
                'status'         => 'change_request',
                'start_offset'   => -20,
                'end_offset'     => 60,
                'milestones'     => [
                    ['title' => 'Scope Definition CR HR',          'days' => 10, 'status' => 'done'],
                    ['title' => 'Adjustment Codebase & DB Schema',  'days' => 20, 'status' => 'in_progress'],
                    ['title' => 'UAT & User Sign-off',             'days' => 15, 'status' => 'pending'],
                ],
            ],
            [
                'code'           => 'KP-2026-012',
                'name'           => 'Modernisasi SCADA Bendungan Jatiluhur',
                'description'    => 'Pembaruan sistem kontrol otomasi dan pemantauan dam safety Bendungan Jatiluhur.',
                'vendor_name'    => 'PT Automation Tech System',
                'business_owner' => 'Divisi Sumber Daya Air',
                'status'         => 'implementasi',
                'start_offset'   => -15,
                'end_offset'     => 105,
                'milestones'     => [
                    ['title' => 'Assessment Sensor & RTU Field',   'days' => 10, 'status' => 'done'],
                    ['title' => 'Procurement Hardware SCADA',      'days' => 50, 'status' => 'in_progress'],
                    ['title' => 'Software Integration & Testing',   'days' => 30, 'status' => 'pending'],
                    ['title' => 'FAT & SAT Field Commissioning',    'days' => 20, 'status' => 'pending'],
                ],
            ],
        ];

        foreach ($projects as $data) {
            $project = Project::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name'                => $data['name'],
                    'description'         => $data['description'],
                    'vendor_name'         => $data['vendor_name'],
                    'business_owner'      => $data['business_owner'],
                    'status'              => $data['status'],
                    'is_completed'        => $data['is_completed'] ?? false,
                    'start_date'          => now()->addDays($data['start_offset']),
                    'end_date'            => now()->addDays($data['end_offset']),
                    'progress_percentage' => 0,
                    'created_by'          => $admin->id,
                ]
            );

            // Re-seed milestones
            $project->milestones()->delete();
            $this->seedMilestonesForProject($project, $data['milestones'], $admin, $staff);

            // Re-seed change requests & documents
            $this->seedChangeRequest($project, $staff, $admin);
            $this->seedDocuments($project, $staff);

            // Recalculate & save exact progress percentage
            $newProgress = $project->calculateProgressFromMilestones();
            $project->update(['progress_percentage' => $newProgress]);
        }
    }

    private function seedMilestonesForProject(Project $project, array $milestonesData, User $admin, User $staff): void
    {
        $currentDate = $project->start_date->copy();

        foreach ($milestonesData as $index => $m) {
            $startDate = $currentDate->copy();
            $dueDate   = $startDate->copy()->addDays($m['days']);
            $currentDate = $dueDate->copy();

            $completedAt = null;
            if ($m['status'] === 'done') {
                $completedAt = $dueDate->copy();
            }

            $validationNote = null;
            if ($m['status'] === 'needs_revision') {
                $validationNote = 'Dokumen pengujian perlu dilengkapi dengan tanda tangan tim Security Audit.';
            }

            $milestone = $project->milestones()->create([
                'title'           => $m['title'],
                'start_date'      => $startDate,
                'due_date'        => $dueDate,
                'status'          => $m['status'],
                'completed_at'    => $completedAt,
                'validation_note' => $validationNote,
            ]);

            $this->seedMilestoneOutputs($milestone, $index, $project, $staff, $admin);
        }
    }

    private function seedMilestoneOutputs(
        $milestone,
        int $index,
        Project $project,
        User $staff,
        User $admin
    ): void {
        $outputSets = [
            0 => [
                ['label' => 'Notulen Kick-off Meeting',  'type' => 'file',     'is_required' => true,  'description' => 'Notulen resmi rapat kick-off ditandatangani semua pihak.'],
                ['label' => 'Project Charter',           'type' => 'file',     'is_required' => true,  'description' => null],
                ['label' => 'Risk Register Awal',        'type' => 'freetext', 'is_required' => false, 'description' => 'Tuliskan risiko-risiko awal.'],
            ],
            1 => [
                ['label' => 'Laporan Design System',     'type' => 'file',     'is_required' => true,  'description' => null],
                ['label' => 'Dokumen Teknis Arsitektur', 'type' => 'file',     'is_required' => true,  'description' => 'Dokumen desain teknis.'],
                ['label' => 'Catatan Kendala Teknis',    'type' => 'freetext', 'is_required' => false, 'description' => 'Jelaskan kendala teknis.'],
            ],
            2 => [
                ['label' => 'Test Case Pengujian',       'type' => 'file',     'is_required' => true,  'description' => 'Dokumen skenario pengujian.'],
                ['label' => 'Berita Acara Testing',      'type' => 'file',     'is_required' => true,  'description' => null],
                ['label' => 'Defect Log & Remediation',  'type' => 'freetext', 'is_required' => false, 'description' => 'Daftar defect.'],
            ],
            3 => [
                ['label' => 'BAST (Berita Acara Serah Terima)', 'type' => 'file',     'is_required' => true,  'description' => 'Dokumen serah terima.'],
                ['label' => 'Catatan Go-Live & Cutover',        'type' => 'freetext', 'is_required' => true,  'description' => 'Catat kondisi saat go-live.'],
                ['label' => 'Panduan Operasional User Manual',   'type' => 'file',    'is_required' => false, 'description' => 'Panduan penggunaan.'],
            ],
        ];

        $outputs = $outputSets[$index % 4];

        foreach ($outputs as $order => $outputData) {
            $created = $milestone->outputs()->create([
                'label'       => $outputData['label'],
                'type'        => $outputData['type'],
                'description' => $outputData['description'],
                'is_required' => $outputData['is_required'],
                'order'       => $order,
            ]);

            // Tentukan apakah output ini harus diisi berdasarkan status milestone
            $shouldFulfill = false;
            if ($milestone->status === 'done' || $milestone->status === 'under_review') {
                $shouldFulfill = true;
            } elseif ($milestone->status === 'in_progress' && $outputData['is_required'] && $order === 0) {
                // Untuk status in_progress, isi 1 output wajib saja
                $shouldFulfill = true;
            } elseif ($milestone->status === 'needs_revision') {
                $shouldFulfill = true;
            }

            if ($shouldFulfill) {
                $submitter   = ($index % 2 === 0) ? $admin : $staff;
                $fulfilledAt = $milestone->completed_at ?? now()->subDays(rand(1, 10));

                if ($outputData['type'] === 'freetext') {
                    MilestoneOutputFulfillment::create([
                        'output_id'        => $created->id,
                        'project_id'       => $project->id,
                        'type'             => 'freetext',
                        'freetext_content' => "Output telah dipenuhi untuk milestone \"{$milestone->title}\". Dikonfirmasi sesuai rencana.",
                        'submitted_by'     => $submitter->id,
                        'created_at'       => $fulfilledAt,
                        'updated_at'       => $fulfilledAt,
                    ]);
                } else {
                    $filename = 'dummy-' . Str::slug($created->label) . '.txt';
                    $content  = "Dokumen output: {$created->label}\nMilestone: {$milestone->title}\nProyek: {$project->name}\nDibuat otomatis oleh ProjectDummySeeder.";
                    $path     = "projects/{$project->id}/milestone-outputs/{$filename}";
                    Storage::disk('local')->put($path, $content);

                    MilestoneOutputFulfillment::create([
                        'output_id'         => $created->id,
                        'project_id'        => $project->id,
                        'type'              => 'file',
                        'file_path'         => $path,
                        'original_filename' => $filename,
                        'file_size'         => Storage::disk('local')->size($path),
                        'mime_type'         => 'text/plain',
                        'submitted_by'      => $submitter->id,
                        'created_at'        => $fulfilledAt,
                        'updated_at'        => $fulfilledAt,
                    ]);
                }
            }
        }
    }

    private function seedChangeRequest(Project $project, User $staff, User $admin): void
    {
        if ($project->changeRequests()->exists()) {
            return;
        }

        $changeRequests = [
            [
                'title'           => 'Perubahan Ruang Lingkup Modul Pelaporan',
                'description'     => 'Penambahan kebutuhan laporan khusus dari tim manajemen yang belum tercakup di SRS awal. Diperlukan 2 minggu tambahan untuk pengembangan.',
                'submission_date' => now()->subDays(14),
                'requested_by'    => $staff->id,
                'handled_by'      => $admin->id,
                'follow_up_notes' => 'Disetujui dengan perpanjangan jadwal 2 minggu. Timeline diperbarui.',
            ],
            [
                'title'           => 'Penambahan Integrasi Notifikasi Email',
                'description'     => 'User meminta sistem mengirimkan notifikasi email otomatis untuk setiap perubahan status proyek.',
                'submission_date' => now()->subDays(5),
                'requested_by'    => $staff->id,
                'handled_by'      => null,
                'follow_up_notes' => null,
            ],
        ];

        foreach ($changeRequests as $crData) {
            $project->changeRequests()->create($crData);
        }
    }

    private function seedDocuments(Project $project, User $staff): void
    {
        if ($project->documents()->exists()) {
            return;
        }

        $documents = [
            [
                'name'            => 'Berita Acara Kick-off',
                'category'        => 'berita_acara',
                'custom_category' => null,
                'suffix'          => 'ba-kickoff',
                'notes'           => 'Berita acara rapat kick-off proyek.',
            ],
            [
                'name'            => 'Kontrak Pekerjaan',
                'category'        => 'contract',
                'custom_category' => null,
                'suffix'          => 'kontrak',
                'notes'           => 'Dokumen kontrak resmi antara PJT II dan vendor.',
            ],
            [
                'name'            => 'Laporan Progres Bulanan',
                'category'        => 'progress_report',
                'custom_category' => null,
                'suffix'          => 'laporan-progres',
                'notes'           => 'Laporan progres bulanan kepada manajemen.',
            ],
        ];

        foreach ($documents as $docData) {
            $filename = 'dummy-' . Str::slug($project->code) . '-' . $docData['suffix'] . '.txt';
            $content  = "Dokumen: {$docData['name']}\nProyek: {$project->name}\nKode: {$project->code}\nDibuat otomatis oleh ProjectDummySeeder.";
            $path     = "projects/{$project->id}/documents/{$filename}";

            Storage::disk('local')->put($path, $content);

            $project->documents()->create([
                'name'              => $docData['name'],
                'category'          => $docData['category'],
                'custom_category'   => $docData['custom_category'],
                'file_path'         => $path,
                'original_filename' => $filename,
                'file_size'         => Storage::disk('local')->size($path),
                'mime_type'         => 'text/plain',
                'notes'             => $docData['notes'],
                'uploaded_by'       => $staff->id,
            ]);
        }
    }
}
