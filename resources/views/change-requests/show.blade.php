<x-app-layout title="CR — {{ $project->name }}" eyebrow="Management Change Request">

    {{-- ===== Breadcrumb ===== --}}
    <div class="mb-5 flex items-center gap-2 text-xs text-ink-500">
        <a href="{{ route('change-requests.index') }}" class="hover:text-pmo-600 hover:underline">Change Request</a>
        <svg class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
        </svg>
        <span class="font-medium text-ink-900">{{ $project->name }}</span>
    </div>

    {{-- ===== Header Proyek ===== --}}
    <div class="mb-5 rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <div class="flex items-center gap-2.5">
                    <h2 class="font-display text-base font-bold text-ink-900">{{ $project->name }}</h2>
                    <x-status-badge :status="$project->status" />
                </div>
                <p class="mt-0.5 text-xs text-ink-500">{{ $project->code }}</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-ink-500">
                @if ($project->vendor_name)
                    <span><span class="font-medium text-ink-700">Vendor:</span> {{ $project->vendor_name }}</span>
                @endif
                @if ($project->end_date)
                    <span><span class="font-medium text-ink-700">Selesai:</span> {{ $project->end_date->translatedFormat('d M Y') }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== Form Tambah Item CR ===== --}}
    @if (auth()->user()->canManage())
        <details class="mb-5 rounded-xl2 border border-ink-100 bg-surface p-5 shadow-card"
                 {{ $errors->any() ? 'open' : '' }}>
            <summary class="cursor-pointer text-sm font-semibold text-pmo-600">+ Tambahkan Item CR</summary>

            @if ($errors->any())
                <div class="mt-3 rounded-lg bg-status-dangerBg px-4 py-3 text-sm text-status-danger">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('change-requests.store') }}"
                  class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">


                <div class="sm:col-span-2">
                    <x-label for="cr_title" required>Judul Item CR</x-label>
                    <x-input id="cr_title" name="title" value="{{ old('title') }}"
                             placeholder="Misal: Penyesuaian modul laporan, Tambah fitur export Excel..."
                             :error="$errors->first('title')" required />
                    @error('title')
                        <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <x-label for="cr_description">Catatan</x-label>
                    <x-textarea id="cr_description" name="description"
                                placeholder="Deskripsikan kebutuhan atau perubahan yang diminta...">{{ old('description') }}</x-textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-label for="cr_submission_date" required>Tanggal Pengajuan</x-label>
                    <x-input type="date" id="cr_submission_date" name="submission_date"
                             value="{{ old('submission_date', now()->format('Y-m-d')) }}" required />
                    @error('submission_date')
                        <p class="mt-1 text-xs text-status-danger">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end">
                    <x-button type="submit">Simpan Item CR</x-button>
                </div>
            </form>
        </details>
    @endif

    {{-- ===== Daftar Item CR ===== --}}
    <div class="rounded-xl2 border border-ink-100 bg-surface shadow-card">

        {{-- Header list --}}
        <div class="flex items-center justify-between border-b border-ink-100 px-5 py-4">
            <div>
                <h3 class="text-sm font-semibold text-ink-900">Daftar Item Change Request</h3>
                <p class="mt-0.5 text-xs text-ink-500">
                    {{ $changeRequests->count() }} item CR untuk proyek ini
                </p>
            </div>
        </div>

        @if ($changeRequests->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-9 w-9 text-ink-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                </svg>
                <p class="mt-3 text-sm font-medium text-ink-700">Belum ada item CR.</p>
                @if (auth()->user()->canManage())
                    <p class="mt-1 text-xs text-ink-500">Klik <strong>+ Tambahkan Item CR</strong> di atas untuk memulai.</p>
                @endif
            </div>
        @else
            <ul class="divide-y divide-ink-100">
                @foreach ($changeRequests as $index => $cr)
                    <li x-data="{ editing: false }" class="px-5 py-4">

                        {{-- ── Tampilan normal ── --}}
                        <div x-show="!editing">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    {{-- Nomor urut + Judul --}}
                                    <div class="flex items-start gap-2.5">
                                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-pmo-100 text-[10px] font-bold text-pmo-700">
                                            {{ $index + 1 }}
                                        </span>
                                        <p class="text-sm font-semibold text-ink-900">{{ $cr->title }}</p>
                                    </div>

                                    {{-- Catatan --}}
                                    @if ($cr->description)
                                        <p class="mt-1.5 pl-7 text-xs text-ink-700">{{ $cr->description }}</p>
                                    @endif

                                    {{-- Catatan Tindak Lanjut --}}
                                    @if ($cr->follow_up_notes)
                                        <div class="mt-2 ml-7 rounded-lg bg-canvas px-3 py-2 text-xs text-ink-600">
                                            <span class="font-medium">📝 Tindak Lanjut:</span> {{ $cr->follow_up_notes }}
                                        </div>
                                    @endif

                                    {{-- Meta --}}
                                    <p class="mt-1.5 pl-7 text-xs text-ink-400">
                                        Diajukan {{ $cr->submission_date->translatedFormat('d M Y') }}
                                        @if ($cr->requester) &middot; oleh {{ $cr->requester->name }} @endif
                                    </p>
                                </div>

                                @if (auth()->user()->canManage())
                                    <div class="flex shrink-0 items-center gap-3">
                                        <button type="button" @click="editing = true"
                                                class="inline-flex items-center gap-1 text-xs font-medium text-pmo-600 hover:underline">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                            </svg>
                                            Edit
                                        </button>
                                        <form method="POST"
                                              action="{{ route('change-requests.destroy', [$project, $cr]) }}"
                                              onsubmit="return confirm('Hapus item CR ini?')"
                                              class="inline-flex">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs font-medium text-status-danger hover:underline">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- ── Form edit inline ── --}}
                        @if (auth()->user()->canManage())
                            <form x-show="editing" x-cloak method="POST"
                                  action="{{ route('change-requests.update', [$project, $cr]) }}"
                                  class="mt-3 grid grid-cols-1 gap-3 rounded-xl border border-pmo-200 bg-pmo-50/50 p-4 sm:grid-cols-2">
                                @csrf @method('PUT')

                                <div class="sm:col-span-2">
                                    <x-label for="edit_title_{{ $cr->id }}" required>Judul Item CR</x-label>
                                    <x-input id="edit_title_{{ $cr->id }}" name="title"
                                             value="{{ old('title', $cr->title) }}" required />
                                </div>

                                <div class="sm:col-span-2">
                                    <x-label for="edit_desc_{{ $cr->id }}">Catatan</x-label>
                                    <x-textarea id="edit_desc_{{ $cr->id }}" name="description"
                                                rows="3">{{ old('description', $cr->description) }}</x-textarea>
                                </div>

                                <div class="sm:col-span-2">
                                    <x-label for="edit_notes_{{ $cr->id }}">Catatan Tindak Lanjut</x-label>
                                    <x-textarea id="edit_notes_{{ $cr->id }}" name="follow_up_notes"
                                                rows="2"
                                                placeholder="Opsional — hasil tindak lanjut atas CR ini">{{ old('follow_up_notes', $cr->follow_up_notes) }}</x-textarea>
                                </div>

                                <div class="flex items-center gap-3 sm:col-span-2">
                                    <x-button type="submit">Simpan Perubahan</x-button>
                                    <button type="button" @click="editing = false"
                                            class="text-sm font-medium text-ink-500 hover:text-ink-700">Batal</button>
                                </div>
                            </form>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

</x-app-layout>
