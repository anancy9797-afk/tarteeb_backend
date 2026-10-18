<?php

namespace Modules\Common\Helpers;

use UltraMsg\WhatsAppApi;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /** Digits-only default when `services.ultramsg.default_country_code` / env is empty */
    private const FALLBACK_COUNTRY_CODE = '968';

    protected $client;

    protected $schoolSettings;

    public function __construct($schoolSettings = null)
    {
        $this->schoolSettings = $schoolSettings;

        $token = $schoolSettings?->ultramsg_token
            ?: config('services.ultramsg.token');
        $instanceId = $schoolSettings?->ultramsg_instance_id
            ?: config('services.ultramsg.instance_id');

        if ($token === null || $token === '' || $instanceId === null || $instanceId === '') {
            Log::warning('WhatsAppService: UltraMsg credentials missing; messages will not send', [
                'school_id' => $schoolSettings?->school_id,
                'school_settings_row' => $schoolSettings !== null,
                'has_token' => $token !== null && $token !== '',
                'has_instance_id' => $instanceId !== null && $instanceId !== '',
            ]);
            $this->client = null;

            return;
        }

        $this->client = new WhatsAppApi($token, $instanceId);
    }

    public function sendMessage($to, $message, $priority = 10, $referenceId = null)
    {
        $originalTo = $to;
        $defaultCc = trim((string) config('services.ultramsg.default_country_code', '')) ?: self::FALLBACK_COUNTRY_CODE;
        $to = self::normalizePhoneForWhatsApp($to, $defaultCc);
        if ($to === '') {
            Log::warning('WhatsApp message skipped: empty phone after normalization', ['original' => $originalTo]);

            return;
        }

        if ($this->client === null) {
            Log::warning('WhatsApp message skipped: client not configured (no token/instance)', [
                'to' => $to,
                'school_id' => $this->schoolSettings?->school_id,
            ]);

            return;
        }

        try {
            $response = $this->client->sendChatMessage($to, $message, $priority, $referenceId);
            Log::info('WhatsApp message sent', ['to' => $to, 'response' => $response]);
        } catch (\Exception $e) {
            Log::error('WhatsApp message failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Egypt mobile (national 10 digits): 1[0125] + 8 digits (010, 011, 012, 015).
     * Oman: national 8 digits starting with 2, 7, or 9.
     *
     * @see normalizePhoneForWhatsApp() for full international output
     */
    public static function isEgyptOrOmanPhoneFormat(?string $phone): bool
    {
        if ($phone === null || $phone === '') {
            return false;
        }

        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if ($digits === '') {
            return false;
        }

        while (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (preg_match('/^20(1[0125]\d{8})$/', $digits)) {
            return true;
        }

        if (preg_match('/^968([279]\d{7})$/', $digits)) {
            return true;
        }

        if (preg_match('/^(?:0)?(1[0125]\d{8})$/', $digits)) {
            return true;
        }

        return (bool) preg_match('/^[279]\d{7}$/', $digits);
    }

    /**
     * UltraMsg expects a full international number with country code, digits only (e.g. 2010xxxxxxxx, 9689xxxxxxx).
     * Detects Egypt vs Oman from local or international forms; other numbers fall back to default country code behavior.
     *
     * @param  string|null  $defaultCountryCodeDigits  Digits only (e.g. "968"). From WHATSAPP_DEFAULT_COUNTRY_CODE when not null.
     */
    public static function normalizePhoneForWhatsApp($phone, ?string $defaultCountryCodeDigits = null): string
    {
        if ($phone === null || $phone === '') {
            return '';
        }

        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        while (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        // Egypt: already +20 national (12 digits)
        if (preg_match('/^20(1[0125]\d{8})$/', $digits)) {
            return $digits;
        }

        // Oman: already +968 (11 digits)
        if (preg_match('/^968([279]\d{7})$/', $digits)) {
            return $digits;
        }

        // Egypt: local 01… / 1… (no country code)
        if (preg_match('/^(?:0)?(1[0125]\d{8})$/', $digits, $m)) {
            return '20' . $m[1];
        }

        // Oman: local 8 digits (no country code)
        if (preg_match('/^[279]\d{7}$/', $digits)) {
            return '968' . $digits;
        }

        $cc = null;
        if ($defaultCountryCodeDigits !== null && $defaultCountryCodeDigits !== '') {
            $cc = preg_replace('/\D+/', '', $defaultCountryCodeDigits);
            if ($cc === '') {
                $cc = null;
            }
        }

        if ($cc !== null && ! str_starts_with($digits, $cc)) {
            $digits = ltrim($digits, '0');
            if ($digits !== '' && ! str_starts_with($digits, $cc)) {
                $digits = $cc . $digits;
            }
        }

        return $digits;
    }
}