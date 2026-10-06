@php $u = $user ?? null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <x-label for="name" required>Nama</x-label>
        <x-input id="name" name="name" value="{{ old('name', $u->name ?? '') }}" required />
        @error('name') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
    </div>

    <div>
        <x-label for="email" required>Email</x-label>
        <x-input type="email" id="email" name="email" value="{{ old('email', $u->email ?? '') }}" required />
        @error('email') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
    </div>

    <div>
        <x-label for="role" required>Peran</x-label>
        <x-select id="role" name="role" required>
            <option value="admin_pmo" @selected(old('role', $u->role ?? '') === 'admin_pmo')>Administrator PMO</option>
            <option value="staff_pmo" @selected(old('role', $u->role ?? '') === 'staff_pmo')>Staff PMO</option>
            <option value="viewer" @selected(old('role', $u->role ?? 'viewer') === 'viewer')>Manajemen / Viewer</option>
        </x-select>
    </div>

    <div>
        <x-label for="password" :required="is_null($u)">
            {{ $u ? 'Password Baru' : 'Password' }}
        </x-label>
        <div class="relative mt-1">
            <x-input type="password" id="password" name="password" :required="is_null($u)"
                      placeholder="{{ $u ? 'Kosongkan jika tidak diubah' : '' }}"
                      class="pr-10"
                      oninput="handlePasswordInput()" />
            <button type="button"
                    onclick="togglePasswordField('password', 'eye-pass-open', 'eye-pass-closed')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-400 hover:text-ink-600 focus:outline-none"
                    aria-label="Tampilkan atau sembunyikan password"
                    tabindex="-1">
                {{-- Mata-coret: tampil saat password tersembunyi (default) --}}
                <svg id="eye-pass-closed" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
                {{-- Mata terbuka: tampil saat password terlihat --}}
                <svg id="eye-pass-open" xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
        @error('password') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
    </div>

    {{-- Konfirmasi Password Baru --}}
    <div id="password-confirm-wrapper" class="{{ is_null($u) ? '' : 'hidden' }}">
        <x-label for="password_confirmation" :required="is_null($u)">
            Konfirmasi {{ $u ? 'Password Baru' : 'Password' }}
        </x-label>
        <div class="relative mt-1">
            <x-input type="password" id="password_confirmation" name="password_confirmation"
                      :required="is_null($u)"
                      placeholder="Ulangi password yang sama"
                      class="pr-10" />
            <button type="button"
                    onclick="togglePasswordField('password_confirmation', 'eye-confirm-open', 'eye-confirm-closed')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-ink-400 hover:text-ink-600 focus:outline-none"
                    aria-label="Tampilkan atau sembunyikan konfirmasi password"
                    tabindex="-1">
                {{-- Mata-coret: tampil saat password tersembunyi (default) --}}
                <svg id="eye-confirm-closed" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                </svg>
                {{-- Mata terbuka: tampil saat password terlihat --}}
                <svg id="eye-confirm-open" xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
        @error('password_confirmation') <p class="mt-1 text-xs text-status-danger">{{ $message }}</p> @enderror
    </div>

    <script>
        /* Toggle show/hide individual password field */
        function togglePasswordField(inputId, iconOpenId, iconClosedId) {
            var input      = document.getElementById(inputId);
            var iconOpen   = document.getElementById(iconOpenId);
            var iconClosed = document.getElementById(iconClosedId);
            if (!input) return;
            if (input.type === 'password') {
                // Password menjadi terlihat → mata terbuka, sembunyikan mata-coret
                input.type = 'text';
                iconOpen   && iconOpen.classList.remove('hidden');
                iconClosed && iconClosed.classList.add('hidden');
            } else {
                // Password kembali tersembunyi → mata-coret, sembunyikan mata terbuka
                input.type = 'password';
                iconOpen   && iconOpen.classList.add('hidden');
                iconClosed && iconClosed.classList.remove('hidden');
            }
        }

        /* Pada mode edit: tampilkan kolom konfirmasi hanya jika password mulai diisi */
        function handlePasswordInput() {
            var passInput   = document.getElementById('password');
            var confirmWrap = document.getElementById('password-confirm-wrapper');
            var confirmInput = document.getElementById('password_confirmation');
            if (!confirmWrap || !passInput) return;

            if (passInput.value.trim().length > 0) {
                confirmWrap.classList.remove('hidden');
                confirmInput && (confirmInput.required = true);
            } else {
                confirmWrap.classList.add('hidden');
                confirmInput && (confirmInput.required = false);
                confirmInput && (confirmInput.value = '');
            }
        }
    </script>

    @if ($u)
        <div class="flex items-center gap-2 sm:col-span-2">
            <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $u->is_active))
                   class="rounded border-ink-100 text-pmo-500 focus:ring-pmo-500">
            <label for="is_active" class="text-sm text-ink-700">Akun aktif</label>
        </div>
    @endif
</div>
