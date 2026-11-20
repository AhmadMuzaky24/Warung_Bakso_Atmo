{{-- Satu-satunya sumber CSS halaman customer (KEPUTUSAN D3 fase 2).
     Nol markup, nol nama class di luar 18 nama yang dikunci architecture §7.2. --}}
<style>
    .customer-shell { max-width: 900px; margin: 0 auto; padding: 40px 24px 64px; }
    .customer-page-head { display: flex; flex-wrap: wrap; gap: 12px 24px; align-items: flex-end; justify-content: space-between; margin-bottom: 24px; }
    .customer-page-head h1 { font-size: 32px; font-weight: 700; line-height: 1.25; margin: 0; }
    .customer-page-head p { margin: 6px 0 0; font-size: 16px; color: #5f4d3c; }

    .customer-card { background: #fff; border-radius: 18px; padding: 24px; box-shadow: 0 6px 20px rgba(35, 23, 7, .08); }
    .customer-card + .customer-card { margin-top: 16px; }
    .customer-card h2, .customer-card h3 { font-size: 20px; font-weight: 600; line-height: 1.35; margin: 0 0 12px; }
    .customer-card > a { display: block; color: inherit; transition: transform .2s ease, box-shadow .2s ease; }
    .customer-card > a:hover { transform: translateY(-2px); }
    .customer-card > strong { display: block; font-size: 32px; font-weight: 700; line-height: 1.2; }
    .customer-card > small { display: block; font-size: 14px; color: #5f4d3c; }
    .customer-card p { margin: 0 0 6px; font-size: 16px; }

    .customer-table { width: 100%; border-collapse: collapse; font-size: 16px; }
    .customer-table th { background: #f7e7d8; color: #231707; text-align: left; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; padding: 12px 16px; }
    .customer-table td { padding: 16px; border-bottom: 1px solid #efe0d0; vertical-align: top; }
    .customer-table tbody tr:hover { background: #fdf6ef; }
    .customer-table [data-num] { text-align: right; font-variant-numeric: tabular-nums; }

    .customer-form { display: grid; gap: 16px; }
    .customer-form__row { display: grid; gap: 6px; }
    .customer-form__row > label { font-size: 14px; font-weight: 600; }
    .customer-form__row > small { font-size: 14px; color: #5f4d3c; }
    .customer-form input[type="text"], .customer-form input[type="tel"], .customer-form input[type="date"],
    .customer-form input[type="number"], .customer-form select, .customer-form textarea {
        width: 100%; min-height: 44px; padding: 10px 12px; border: 1px solid #8a6f56; border-radius: 8px;
        background: #fff; color: #231707; font: inherit; font-size: 16px;
    }
    .customer-form textarea { min-height: 88px; resize: vertical; }
    .customer-form input:focus, .customer-form select:focus, .customer-form textarea:focus {
        outline: none; border-color: #c0440f; box-shadow: 0 0 0 3px rgba(192, 68, 15, .18);
    }
    .customer-form [aria-invalid="true"] { border-color: #8a2a2a; }
    .customer-form small[data-error] { font-size: 14px; color: #8a2a2a; }
    .customer-form input[readonly] { background: #f7f1ea; color: #5f4d3c; }
    .customer-form button { min-height: 44px; min-width: 44px; }

    .customer-alert { padding: 16px; border-radius: 12px; margin-bottom: 24px; font-weight: 600; font-size: 16px; border-left: 4px solid currentColor; }
    .customer-alert--success { background: #d9f3e3; color: #1e6b3a; }
    .customer-alert--error { background: #fdf2f2; color: #8a2a2a; }
    .customer-alert:not([class*="--"]) { background: #fdf1d8; color: #7a4a12; }

    .customer-empty { text-align: center; padding: 48px 24px; background: #fff; border-radius: 18px; }
    .customer-empty h3 { font-size: 20px; font-weight: 600; margin: 16px 0 8px; }
    .customer-empty p { font-size: 16px; color: #5f4d3c; max-width: 380px; margin: 0 auto 24px; }

    .customer-total { display: grid; gap: 8px; border-top: 1px solid #efe0d0; padding-top: 16px; margin-top: 16px; }
    .customer-total > div { display: flex; justify-content: space-between; gap: 16px; font-variant-numeric: tabular-nums; }
    .customer-total > div:last-child { font-size: 20px; font-weight: 700; }

    /* Kartu "Detail acara" (rev-8/rev-9) — penanda khusus, .customer-total global tidak diubah
       supaya ringkasan biaya (Subtotal/DP/Sisa bayar) tetap sama.
       Ditulis SETELAH `.customer-total > div` karena spesifisitasnya sama. */
    .customer-detail > div { display: grid; grid-template-columns: 7rem 1fr; gap: 2px 16px; align-items: baseline; }
    .customer-detail > div > span:first-child { font-size: 14px; color: #5f4d3c; font-weight: 600; }
    .customer-detail > div > span:last-child { min-width: 0; overflow-wrap: anywhere; text-align: left; }
    /* baris terakhir di kartu ini adalah "Catatan", bukan total: jangan 20px bold */
    .customer-detail > div:last-child { font-size: inherit; font-weight: 400; }

    .customer-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 999px; font-size: 13px; font-weight: 600; white-space: nowrap; }
    .customer-badge--submitted { background: #f0bf8c; color: #231707; }
    .customer-badge--processing { background: #fde6d3; color: #6f4015; }
    .customer-badge--completed { background: #d9f3e3; color: #1e6b3a; }
    .customer-badge--rejected { background: #f6d9d9; color: #8a2a2a; }
    .customer-badge--pending { background: #fdf1d8; color: #7a4a12; }
    .customer-badge--verified { background: #d9f3e3; color: #1e6b3a; }
    .customer-badge--payment-rejected { background: #f6d9d9; color: #8a2a2a; }

    .customer-shell nav { display: flex; justify-content: center; margin-top: 24px; }
    .customer-shell .pagination { display: flex; gap: 8px; list-style: none; margin: 0; padding: 0; }
    .customer-shell .pagination a, .customer-shell .pagination span {
        display: inline-flex; align-items: center; justify-content: center; min-height: 44px; min-width: 44px;
        padding: 8px 14px; border: 1px solid #8a6f56; border-radius: 8px; font-size: 14px; font-weight: 600;
    }
    .customer-shell .pagination a:hover { background: #f7e7d8; }
    .customer-shell .pagination span { color: #5f4d3c; }

    .customer-shell a:focus-visible, .customer-shell button:focus-visible, .customer-form input:focus-visible,
    .customer-form select:focus-visible, .customer-form textarea:focus-visible {
        outline: 2px solid #c0440f; outline-offset: 2px;
    }

    /* Tombol WA di kartu detail: `.customer-card > a` bikin color:inherit + display:block,
       jadi warna teks, display, dan lebar harus dipaksa di sini. */
    .customer-card a.btn--dark {
        display: flex; align-items: center; justify-content: center;
        color: #fff; text-align: center;
        width: 100%; max-width: 340px; margin-inline: auto;
    }
    .customer-card a.btn--light {
        display: inline-flex; align-items: center; justify-content: center;
        color: #231707; text-align: center;
    }

    /* Baris menu: pemisah visual antar baris + tombol hapus disembunyikan saat tinggal 1 baris */
    .customer-form [data-item-row] + [data-item-row] { border-top: 1px solid #efe0d0; padding-top: 16px; margin-top: 8px; }
    .customer-form [hidden] { display: none !important; }

    @media (max-width: 680px) {
        .customer-page-head h1 { font-size: 26px; }

        /* Detail acara: label di atas, isi turun penuh ke bawah (rev-8) */
        .customer-detail > div { display: block; }
        .customer-detail > div > span { display: block; }
        .customer-detail > div > span:first-child { margin-bottom: 2px; }
        .customer-detail > div > span:last-child { text-align: left; }
        .customer-table thead { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
        .customer-table tr { display: block; border-bottom: 1px solid #efe0d0; padding: 12px 0; }
        .customer-table td { display: flex; justify-content: space-between; gap: 12px; border: 0; padding: 6px 0; }
        .customer-table td::before { content: attr(data-label); font-size: 14px; font-weight: 600; color: #5f4d3c; }
        .customer-table [data-num] { text-align: left; }
    }

    @media (prefers-reduced-motion: reduce) {
        .customer-card > a:hover { transform: none; }
    }
</style>
