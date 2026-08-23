<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\SmtpTestMail;
use Throwable;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function index(): Response
    {
        $this->authorizeSuperAdmin(request());
        return Inertia::render('Admin/Settings/Index', [
            'settings' => [
                'logo_url' => AppSetting::valueFor('branding.logo_path') ? Storage::url(AppSetting::valueFor('branding.logo_path')) : null,
                'mail_host' => AppSetting::valueFor('mail.host', config('mail.mailers.smtp.host')),
                'mail_port' => AppSetting::valueFor('mail.port', config('mail.mailers.smtp.port')),
                'mail_username' => AppSetting::valueFor('mail.username', config('mail.mailers.smtp.username')),
                'mail_encryption' => AppSetting::valueFor('mail.encryption', config('mail.mailers.smtp.scheme', 'tls')),
                'mail_from_address' => AppSetting::valueFor('mail.from_address', config('mail.from.address')),
                'mail_from_name' => AppSetting::valueFor('mail.from_name', config('mail.from.name')),
                'mail_password_set' => filled(AppSetting::valueFor('mail.password')),
                'registration_disabled' => filter_var(AppSetting::valueFor('registration.disabled', '0'), FILTER_VALIDATE_BOOLEAN),
                'video_upload_disabled' => filter_var(AppSetting::valueFor('registration.video_upload_disabled', '0'), FILTER_VALIDATE_BOOLEAN),
            ],
            'admins' => User::query()->whereIn('role', ['super_admin', 'admin'])->latest()->get(['id', 'name', 'username', 'email', 'role', 'is_active', 'created_at']),
        ]);
    }

    public function logo(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin($request);
        $data = $request->validate(['logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048']]);
        $old = AppSetting::valueFor('branding.logo_path');
        $path = $data['logo']->store('branding', 'public');
        AppSetting::put('branding.logo_path', $path);
        if ($old) Storage::disk('public')->delete($old);

        return back()->with('success', 'Logo berhasil diperbarui.');
    }

    public function smtp(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin($request);
        $data = $request->validate([
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'integer', 'between:1,65535'],
            'mail_username' => ['required', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['required', Rule::in(['tls', 'ssl'])],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);
        foreach (['host', 'port', 'username', 'encryption', 'from_address', 'from_name'] as $key) AppSetting::put("mail.$key", $data["mail_$key"]);
        if (filled($data['mail_password'] ?? null)) AppSetting::put('mail.password', $data['mail_password'], true);

        return back()->with('success', 'SMTP Gmail berhasil disimpan.');
    }

    public function registration(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin($request);
        $data = $request->validate([
            'registration_disabled' => ['required', 'boolean'],
            'video_upload_disabled' => ['required', 'boolean'],
        ]);
        AppSetting::put('registration.disabled', $data['registration_disabled'] ? '1' : '0');
        AppSetting::put('registration.video_upload_disabled', $data['video_upload_disabled'] ? '1' : '0');

        return back()->with('success', $data['registration_disabled']
            ? 'Pendaftaran berhasil ditutup. Form dan proses pengiriman data telah dinonaktifkan.'
            : 'Pengaturan pendaftaran berhasil disimpan.');
    }

    public function testSmtp(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin($request);
        $data = $request->validate([
            'test_email' => ['required', 'email:rfc', 'max:255'],
        ]);

        if (! filled(AppSetting::valueFor('mail.password'))) {
            return back()->with('error', 'App Password SMTP belum tersimpan. Simpan konfigurasi SMTP terlebih dahulu.');
        }

        try {
            Mail::purge('smtp');
            Mail::mailer('smtp')->to($data['test_email'])->send(new SmtpTestMail(
                recipient: $data['test_email'],
                senderName: (string) AppSetting::valueFor('mail.from_name', config('mail.from.name')),
            ));

            return back()->with('success', "Test email berhasil dikirim ke {$data['test_email']}. Silakan periksa kotak masuk atau folder spam.");
        } catch (Throwable $exception) {
            Log::error('SMTP test email failed.', [
                'recipient' => $data['test_email'],
                'exception' => $exception,
            ]);

            return back()->with('error', 'Test email gagal dikirim. Periksa SMTP Host, Port, Email, App Password, dan Enkripsi lalu coba kembali.');
        }
    }

    public function admin(Request $request): RedirectResponse
    {
        $this->authorizeSuperAdmin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'alpha_dash', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['super_admin', 'admin'])],
        ]);
        User::query()->create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['role'],
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        $this->authorizeSuperAdmin($request);
        abort_if($request->user()->is($user), 422, 'Akun yang sedang digunakan tidak dapat dinonaktifkan.');
        abort_unless(in_array($user->role, ['super_admin', 'admin'], true), 404);
        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Status akun admin diperbarui.');
    }

    private function authorizeSuperAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'super_admin', 403);
    }
}
