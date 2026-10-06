<x-app-layout title="Kelola Pengguna" eyebrow="Administrator PMO">

    {{-- ===== Toolbar: Filter + Tambah ===== --}}
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="GET" class="flex flex-1 flex-col gap-2 sm:flex-row sm:items-center">
            <x-select name="role" class="sm:max-w-[180px]" onchange="this.form.submit()">
                <option value="">Semua peran</option>
                <option value="admin_pmo"  @selected(request('role') === 'admin_pmo')>Administrator PMO</option>
                <option value="staff_pmo"  @selected(request('role') === 'staff_pmo')>Staff PMO</option>
                <option value="viewer"     @selected(request('role') === 'viewer')>Viewer</option>
            </x-select>

            <x-select name="status" class="sm:max-w-[160px]" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <option value="active"   @selected(request('status') === 'active')>Aktif</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
            </x-select>

            @if (request()->hasAny(['role', 'status']))
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center text-xs font-medium text-ink-500 hover:text-ink-700">
                    Reset
                </a>
            @endif
        </form>

        <x-button as="link" href="{{ route('users.create') }}" class="shrink-0">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pengguna
        </x-button>
    </div>

    <div class="overflow-hidden rounded-xl2 border border-ink-100 bg-surface shadow-card">
        @if ($users->isEmpty())
            <div class="px-6 py-14 text-center">
                <p class="text-sm font-medium text-ink-700">Tidak ada pengguna yang cocok.</p>
                <p class="mt-1 text-xs text-ink-500">Coba ubah kata kunci pencarian atau filter.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-ink-100 text-sm">
                <thead class="bg-canvas">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-ink-500">
                        <th class="px-5 py-3">Nama</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Peran</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($users as $user)
                        <tr class="transition hover:bg-canvas">
                            <td class="px-5 py-3.5 font-medium text-ink-900">{{ $user->name }}</td>
                            <td class="px-5 py-3.5 text-ink-700">{{ $user->email }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-full bg-pmo-100 px-2.5 py-1 text-xs font-medium text-pmo-700">
                                    {{ match($user->role) { 'admin_pmo' => 'Administrator PMO', 'staff_pmo' => 'Staff PMO', default => 'Viewer' } }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center rounded-full bg-status-successBg px-2.5 py-1 text-xs font-medium text-status-success">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-status-neutralBg px-2.5 py-1 text-xs font-medium text-status-neutral">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('users.edit', $user) }}" class="text-xs font-semibold text-pmo-600 hover:text-pmo-700">Ubah</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="border-t border-ink-100 px-5 py-3">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
