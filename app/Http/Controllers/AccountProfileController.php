<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('account.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validated();

        if (!$user->is_admin) {
            if (!empty($user->name))  unset($data['name']);
            if (!empty($user->phone)) unset($data['phone']);
            if (!empty($user->email)) unset($data['email']);

            if (!empty($user->ic_number))  unset($data['ic_number']);
            if (!empty($user->birth_date)) unset($data['birth_date']);

            unset($data['ic_image']);
        }


        $user->fill($data);

        if ($request->hasFile('ic_image')) {
            $canUpload = $user->is_admin || empty($user->ic_image);

            if ($canUpload) {
                if ($user->ic_image && Storage::disk('public')->exists($user->ic_image)) {
                    Storage::disk('public')->delete($user->ic_image);
                }

                $user->ic_image = $request->file('ic_image')->store('ic-images', 'public');
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('account.profile.edit')
            ->with('success', 'Profile update successfully.');
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'name'       => $user->name ? ['sometimes'] : ['required', 'string', 'max:255'],
            'email'      => $user->email ? ['sometimes'] : ['required', 'string', 'lowercase', 'email', 'max:255'],
            'phone'      => $user->phone ? ['sometimes'] : ['nullable', 'string', 'max:30'],

            'ic_number'  => $user->ic_number ? ['sometimes'] : ['nullable', 'string', 'max:30'],
            'birth_date' => $user->birth_date ? ['sometimes'] : ['nullable', 'date'],

            'ic_image'   => ['nullable', 'image', 'max:2048'],
        ];
    }



    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
