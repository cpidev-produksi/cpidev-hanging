<?php

namespace App\Support;

use App\Models\HangingForm;
use App\Models\MonitorControl;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;

/**
 * Nama + QR code untuk kolom Dibuat oleh / Diketahui oleh / Disetujui oleh
 * pada PDF "Form Perhitungan Hanging Ayam", serta pembuat PDF-nya.
 */
class HangingApproval
{
    private const TZ = 'Asia/Jakarta';

    /** QR sebagai data-URI SVG (aman dipakai di <img> DomPDF). */
    public static function qrDataUri(string $text, int $size = 220): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($size, 1),
            new SvgImageBackEnd()
        );

        $svg = (new Writer($renderer))->writeString($text);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /** URL PDF yang bisa dibuka lewat scan QR (signed, tanpa login). */
    public static function publicPdfUrl(HangingForm $form): string
    {
        return URL::signedRoute('hanging-forms.public-pdf', ['hangingForm' => $form->getKey()]);
    }

    /** Teks yang muncul saat QR Diketahui/Disetujui di-scan. */
    public static function approvalText(string $label, string $name, $at): string
    {
        $tanggal = $at ? $at->copy()->timezone(self::TZ)->format('d/m/Y H:i') : '-';

        return $label . "\nNama: " . $name . "\nTanggal: " . $tanggal;
    }

    public static function signatures(HangingForm $form): array
    {
        $out = [
            'created'  => ['name' => null, 'qr' => null],
            'known'    => ['name' => null, 'qr' => null],
            'approved' => ['name' => null, 'qr' => null],
        ];

        if (!empty($form->creator_name)) {
            $out['created'] = [
                'name' => $form->creator_name,
                'qr'   => self::qrDataUri(self::publicPdfUrl($form)),
            ];
        }

        if (!empty($form->known_name) && $form->known_at) {
            $out['known'] = [
                'name' => $form->known_name,
                'qr'   => self::qrDataUri(self::approvalText('Diketahui oleh', $form->known_name, $form->known_at)),
            ];
        }

        if (!empty($form->approved_name) && $form->approved_at) {
            $out['approved'] = [
                'name' => $form->approved_name,
                'qr'   => self::qrDataUri(self::approvalText('Disetujui oleh', $form->approved_name, $form->approved_at)),
            ];
        }

        return $out;
    }

    /** Dipakai bersama oleh Summary PDF, daftar Form Hanging Ayam, dan link QR publik. */
    public static function pdf(MonitorControl $mc)
    {
        $mc->loadMissing(['farm', 'expedition', 'plateNumber', 'hangingForm.lines.sets']);

        $form   = $mc->hangingForm;
        $report = HangingReportBuilder::build($mc, $form);
        $report['signatures'] = self::signatures($form);

        return Pdf::loadView('transaction.monitor_controls.hanging_report_pdf', [
            'mc' => $mc,
            'r'  => $report,
        ])->setPaper('A4', 'portrait');
    }
}
