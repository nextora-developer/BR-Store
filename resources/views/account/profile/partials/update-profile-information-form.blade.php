<header>
    {{-- Title + Badge --}}
    <div class="flex items-center gap-3">
        <h2 class="text-lg font-semibold text-[#0A0A0C]">
            {{ __('Profile Information') }}
        </h2>

        {{-- Verification Badge --}}
        @if ($user->is_verified)
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                       text-[11px] font-bold uppercase tracking-wider
                       bg-emerald-100 text-emerald-700 border border-emerald-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Verified
            </span>
        @else
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                       text-[11px] font-bold uppercase tracking-wider
                       bg-gray-100 text-gray-500 border border-gray-200">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Unverified
            </span>
        @endif
    </div>

    <p class="mt-1 text-sm text-gray-500">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    {{-- Notice --}}
    <div class="mt-4 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        <strong class="font-semibold">Notice:</strong>
        For security and verification purposes, identity information can only be submitted once.
        Subsequent changes require administrator verification.
    </div>

</header>


{{-- 用于重发验证邮件 --}}
<form id="send-verification" method="post" action="{{ route('verification.send') }}" class="mt-4">
    @csrf
</form>

@php
    $isAdmin = auth()->check() && auth()->user()->is_admin;

    $lockName = !$isAdmin && !empty($user->name);
    $lockPhone = !$isAdmin && !empty($user->phone);
    $lockEmail = !$isAdmin && !empty($user->email);

    $lockIcNumber = !$isAdmin && !empty($user->ic_number);
    $lockBirthDate = !$isAdmin && !empty($user->birth_date);
    $lockIcImage = !$isAdmin && !empty($user->ic_image);
@endphp



<form method="post" action="{{ route('account.profile.update') }}" enctype="multipart/form-data"
    class="mt-5 space-y-5">
    @csrf
    @method('patch')

    {{-- Name + IC + Birth Date --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm text-gray-500 mb-1">
                {{ __('Name') }}
            </label>

            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                {{ $lockName ? 'readonly' : '' }}
                class="w-full rounded-xl border-gray-200 text-base px-3 py-3
           focus:border-[#D4AF37] focus:ring-[#D4AF37]/30
           {{ $lockName ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" />



            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- IC Number --}}
        <div>
            <label for="ic_number" class="block text-sm text-gray-500 mb-1">
                {{ __('IC Number') }}
            </label>

            @php
                $lockIcNumber = !auth()->user()->is_admin && !empty($user->ic_number);
            @endphp

            <input id="ic_number" name="ic_number" type="text" value="{{ old('ic_number', $user->ic_number) }}"
                placeholder="e.g. 990101-01-1234" @disabled($lockIcNumber)
                class="w-full rounded-xl border-gray-200 text-base px-3 py-3
           focus:border-[#D4AF37] focus:ring-[#D4AF37]/30
           disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" />

            @error('ic_number')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Birth Date --}}
        <div>
            <label for="birth_date" class="block text-sm text-gray-500 mb-1">
                {{ __('Birth Date') }}
            </label>

            @php
                $lockBirthDate = !auth()->user()->is_admin && !empty($user->birth_date);
            @endphp

            <input id="birth_date" name="birth_date" type="date"
                value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}"
                {{ $lockBirthDate ? 'readonly' : '' }}
                class="w-full rounded-xl border-gray-200 text-base px-3 py-3
           focus:border-[#D4AF37] focus:ring-[#D4AF37]/30
           {{ $lockBirthDate ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" />


            @error('birth_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>



    {{-- Phone + Email --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Phone --}}
        <div>
            <label for="phone" class="block text-sm text-gray-500 mb-1">
                {{ __('Phone Number') }}
            </label>

            <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                placeholder="e.g. 0182222507" @disabled($lockPhone)
                class="w-full rounded-xl border-gray-200 text-base px-3 py-3
           focus:border-[#D4AF37] focus:ring-[#D4AF37]/30
           disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed" />


            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm text-gray-500 mb-1">
                {{ __('Email') }}
            </label>

            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                {{ $lockEmail ? 'readonly' : '' }}
                class="w-full rounded-xl border-gray-200 text-base px-3 py-3
           focus:border-[#D4AF37] focus:ring-[#D4AF37]/30
           {{ $lockEmail ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" />



            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror


            {{-- 邮箱未验证提示 --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div class="mt-2 text-sm text-gray-700 space-y-1">
                    <p>
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline font-medium text-[#8f6a10] hover:text-[#D4AF37] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#D4AF37]/60 text-sm">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- IC Image Upload --}}
        <div x-data="{
            preview: null,
            fileChosen(event) {
                const file = event.target.files[0];
                if (!file) return;
                // Revoke old object URL to prevent memory leaks
                if (this.preview) URL.revokeObjectURL(this.preview);
                this.preview = URL.createObjectURL(file);
            },
            clearPreview() {
                this.preview = null;
                this.$refs.fileInput.value = '';
            }
        }" class="space-y-2">

            <label class="block text-sm font-medium text-gray-700">
                {{ __('IC Image') }}
            </label>

            <div class="flex flex-col md:flex-row gap-5 items-center md:items-start">
                {{-- Preview Box --}}
                <div class="relative group w-40 h-28 flex-shrink-0">
                    <div
                        class="w-full h-full rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 overflow-hidden flex items-center justify-center transition-colors group-hover:border-[#D4AF37]/50">

                        {{-- Show Preview if selected, else show DB image, else show placeholder --}}
                        <template x-if="preview">
                            <img :src="preview" class="w-full h-full object-cover" alt="New IC Preview">
                        </template>

                        <template x-if="!preview">
                            @if ($user->ic_image)
                                <img src="{{ asset('storage/' . $user->ic_image) }}" class="w-full h-full object-cover"
                                    alt="Current IC Image">
                            @else
                                <div class="flex flex-col items-center text-gray-400">
                                    <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="text-[10px] uppercase tracking-wider font-semibold">No Image</span>
                                </div>
                            @endif
                        </template>
                    </div>

                    {{-- Quick Reset Button (Only shows if a new file is selected) --}}
                    <button x-show="preview" @click="clearPreview()" type="button" @disabled($lockIcImage)
                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600 focus:outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Upload Controls --}}
                <div class="flex-1 w-full">
                    @php
                        $lockIcImage = !auth()->user()->is_admin && !empty($user->ic_image);
                    @endphp

                    <input type="file" name="ic_image" x-ref="fileInput" accept="image/*" @change="fileChosen"
                        @disabled($lockIcImage)
                        class="block w-full text-sm text-gray-500
           file:mr-4 file:py-2 file:px-4
           file:rounded-full file:border-0
           file:text-sm file:font-semibold
           file:bg-[#D4AF37]/10 file:text-[#8f6a10]
           hover:file:bg-[#D4AF37]/20
           focus:outline-none cursor-pointer
           disabled:opacity-60 disabled:cursor-not-allowed" />

                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-gray-400">Accepted: JPG, PNG (Max 2MB)</span>
                    </div>

                    @error('ic_image')
                        <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>


    </div>

    {{-- 底部按钮 --}}
    <div class="flex items-center gap-4 pt-3">
        <button type="submit"
            class="px-7 py-3 rounded-full bg-[#D4AF37] text-white text-base font-semibold shadow hover:brightness-110 transition">
            {{ __('Save') }}
        </button>

        @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-[#8f6a10]">
                {{ __('Saved.') }}
            </p>
        @endif
    </div>
</form>
