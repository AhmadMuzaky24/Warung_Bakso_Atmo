<?php

namespace App\Services;

use App\Models\CateringOrder;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;

class CateringReportExportService
{
    /**
     * Membuat file XLSX laporan pesanan katering dan mengembalikan path sementara serta nama file unduhan.
     *
     * @param  Collection<int, CateringOrder>  $orders
     * @param  array<string, mixed>  $filters
     * @return array{path: string, filename: string}
     */
    public function createXlsx(Collection $orders, array $filters = []): array
    {
        $tempBase = tempnam(sys_get_temp_dir(), 'bakso_atmo_report_');
        $tempPath = $tempBase.'.xlsx';
        @rename($tempBase, $tempPath);

        $writer = new Writer;
        $writer->openToFile($tempPath);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Laporan Katering');

        $titleStyle = new Style(fontBold: true, fontSize: 13, fontColor: '29251F');
        $headerStyle = new Style(fontBold: true, fontColor: 'FFFFFF', backgroundColor: 'A94F32');
        $totalStyle = new Style(fontBold: true, backgroundColor: 'F8ECE6');

        $startDate = is_string($filters['start_date'] ?? null) && $filters['start_date'] !== ''
            ? $filters['start_date']
            : 'Semua';
        $endDate = is_string($filters['end_date'] ?? null) && $filters['end_date'] !== ''
            ? $filters['end_date']
            : 'Semua';
        $statusLabel = isset($filters['status']) && isset(CateringOrder::STATUS_LABELS[$filters['status']])
            ? CateringOrder::STATUS_LABELS[$filters['status']]
            : 'Semua';
        $paymentLabel = isset($filters['payment_status']) && isset(CateringOrder::PAYMENT_STATUS_LABELS[$filters['payment_status']])
            ? CateringOrder::PAYMENT_STATUS_LABELS[$filters['payment_status']]
            : 'Semua';

        $writer->addRow(Row::fromValuesWithStyle(['Laporan Pesanan Katering — '.config('bakso_atmo.name')], $titleStyle));
        $writer->addRow(Row::fromValues(['Periode Acara', $startDate.' s/d '.$endDate, 'Diekspor Pada', now()->format('Y-m-d H:i')]));
        $writer->addRow(Row::fromValues(['Filter Status Pesanan', $statusLabel, 'Filter Pembayaran', $paymentLabel]));
        $writer->addRow(Row::fromValues([]));

        $writer->addRow(Row::fromValuesWithStyle([
            'No',
            'Kode Booking',
            'Tanggal Pesan',
            'Tanggal Acara',
            'Pelanggan',
            'No. WhatsApp',
            'Alamat Acara',
            'Ringkasan Menu',
            'Total Porsi',
            'Total Pesanan (Rp)',
            'DP (Rp)',
            'Sisa Bayar (Rp)',
            'Status Pesanan',
            'Status Pembayaran',
            'Catatan',
        ], $headerStyle));

        $grandTotal = 0;
        $grandDp = 0;
        $grandRemaining = 0;
        $grandPortions = 0;

        foreach ($orders as $index => $order) {
            $menuSummary = $order->items
                ->map(fn ($item): string => $item->menu_name.' ('.$item->quantity.')')
                ->implode(', ');
            $totalPortions = (int) $order->items->sum('quantity');
            $remaining = max(0, $order->total_amount - $order->dp_amount);

            $grandTotal += $order->total_amount;
            $grandDp += $order->dp_amount;
            $grandRemaining += $remaining;
            $grandPortions += $totalPortions;

            $writer->addRow(Row::fromValues([
                $index + 1,
                $order->booking_code,
                $order->created_at?->format('Y-m-d H:i') ?? '-',
                $order->event_date->format('Y-m-d'),
                $order->user?->name ?? '-',
                $order->customer_phone,
                $order->event_address,
                $menuSummary !== '' ? $menuSummary : '-',
                $totalPortions,
                $order->total_amount,
                $order->dp_amount,
                $remaining,
                $order->status_label,
                $order->payment_status_label,
                $order->notes ?? '-',
            ]));
        }

        $writer->addRow(Row::fromValuesWithStyle([
            '',
            'TOTAL',
            '',
            '',
            $orders->count().' pesanan',
            '',
            '',
            '',
            $grandPortions,
            $grandTotal,
            $grandDp,
            $grandRemaining,
            '',
            '',
            '',
        ], $totalStyle));

        $writer->close();

        return [
            'path' => $tempPath,
            'filename' => 'laporan-katering-bakso-atmo-'.now()->format('Ymd-His').'.xlsx',
        ];
    }
}
