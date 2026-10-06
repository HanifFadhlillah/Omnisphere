@php $p = $project ?? null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <x-label for="code" required>Nomor Kontrak Pekerjaan</x-label>
        <x-input id="code" name="code" value="{{ old('code', $p->code ?? '') }}" :error="$errors->first('code')" required />
        @error('code') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
    </div>

    <div>
        <x-label for="status" required>Jenis Proyek</x-label>
        <x-select id="status" name="status" :error="$errors->first('status')" required>
            @foreach ([
                'feasibility_study' => 'Feasibility Study (FS)',
                'perencanaan'       => 'Penyusunan Dokumen Perencanaan',
                'quality_assurance' => 'Pekerjaan Quality Assurance',
                'implementasi'      => 'Pekerjaan Implementasi',
                'manage_operation'  => 'Manage Operation',
                'change_request'    => 'Change Request',
            ] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $p->status ?? 'implementasi') === $value)>{{ $label }}</option>
            @endforeach
        </x-select>
    </div>

    <div class="sm:col-span-2">
        <x-label for="name" required>Nama Proyek</x-label>
        <x-input id="name" name="name" value="{{ old('name', $p->name ?? '') }}" :error="$errors->first('name')" required />
        @error('name') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <x-label for="description">Deskripsi</x-label>
        <x-textarea id="description" name="description">{{ old('description', $p->description ?? '') }}</x-textarea>
    </div>

    <div>
        <x-label for="vendor_name">Vendor</x-label>
        <x-input id="vendor_name" name="vendor_name" value="{{ old('vendor_name', $p->vendor_name ?? '') }}" />
    </div>

    <div>
        <x-label for="business_owner">Business Owner</x-label>
        <x-input id="business_owner" name="business_owner" value="{{ old('business_owner', $p->business_owner ?? '') }}" />
    </div>

    <div>
        <x-label for="start_date">Tanggal Mulai</x-label>
        <x-input type="date" id="start_date" name="start_date" value="{{ old('start_date', optional($p?->start_date)->format('Y-m-d')) }}" />
    </div>

    <div>
        <x-label for="end_date">Tenggat / Target Selesai</x-label>
        <x-input type="date" id="end_date" name="end_date" value="{{ old('end_date', optional($p?->end_date)->format('Y-m-d')) }}"
                  :error="$errors->first('end_date')" />
        @error('end_date') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
    </div>

    {{-- Toggle: Tandai Selesai — hanya Admin PMO --}}
    @if (auth()->user()->canManage())
        @php $currentProgress = $p->progress_percentage ?? 0; @endphp
        <div class="sm:col-span-2">
            @if ($currentProgress >= 100)
                {{-- Progress 100% — tampilkan toggle --}}
                <label for="is_completed" class="flex cursor-pointer items-start gap-3 rounded-xl border border-ink-200 bg-canvas p-4 hover:border-pmo-300 hover:bg-pmo-50/40 transition">
                    <div class="relative mt-0.5 shrink-0">
                        <input type="checkbox" id="is_completed" name="is_completed" value="1"
                               class="peer sr-only"
                               {{ old('is_completed', $p->is_completed ?? false) ? 'checked' : '' }}>
                        <div class="h-5 w-9 rounded-full bg-ink-200 transition peer-checked:bg-pmo-500"></div>
                        <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-ink-900">Tandai Proyek Sebagai Selesai</p>
                        <p class="mt-0.5 text-xs text-ink-500">
                            Proyek yang ditandai selesai akan muncul di halaman <strong>Change Request</strong> dan tidak dihitung sebagai proyek berjalan.
                            <span class="ml-1 font-medium text-status-success">✓ Progress sudah 100%</span>
                        </p>
                    </div>
                </label>
            @else
                {{-- Progress belum 100% — sembunyikan toggle, tampilkan keterangan --}}
                <div class="flex items-start gap-3 rounded-xl border border-ink-100 bg-canvas p-4 opacity-60 cursor-not-allowed select-none">
                    <div class="relative mt-0.5 shrink-0">
                        <div class="h-5 w-9 rounded-full bg-ink-200"></div>
                        <div class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow"></div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-ink-400">Tandai Proyek Sebagai Selesai</p>
                        <p class="mt-0.5 text-xs text-ink-400">
                            Opsi ini hanya tersedia jika progress proyek sudah <strong>100%</strong>.
                            Progress saat ini: <strong>{{ $currentProgress }}%</strong> — selesaikan semua milestone terlebih dahulu.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
