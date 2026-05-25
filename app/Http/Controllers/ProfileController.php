<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $user->load('role');

        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required','string','max:150'],
            'email' => ['required','email','max:150','unique:users,email,'.$user->id],
            'username' => ['required','string','max:50','unique:users,username,'.$user->id],
        ]);

        $user->update($data);

        return back()->with('status', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required','string'],
            'new_password' => ['required','string','min:6','confirmed'],
        ], [
            'new_password.confirmed' => 'Konfirmasi password tidak sama.',
            'new_password.min' => 'Password minimal 6 karakter.',
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password lama salah.',
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return back()->with('status', 'Password berhasil diubah.');
    }
    
    public function updateSignature(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'signature' => ['required', 'image', 'mimes:png', 'max:2048'], // max 2MB
        ], [
            'signature.mimes' => 'Signature harus PNG.',
        ]);

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
        }

        $path = $request->file('signature')->store('signatures', 'public');

        $user->update([
            'signature_path' => $path,
        ]);

        return back()->with('status', 'Signature berhasil diupload.');
    }
}