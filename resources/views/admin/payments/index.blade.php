@extends('layouts.main')

@section('title', 'Admin Payments')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pembayaran</h1>
                    <p class="text-gray-600 mt-1">Lihat semua pembayaran dan transaksi</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="btn-refresh" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('admin.partials.navbar', ['activeNav' => 'payments'])

        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Pembayaran</h3>
                <div class="text-sm text-gray-500">
                    <span id="total-count" class="font-semibold">-</span> Total Pembayaran
                </div>
            </div>
            <div class="p-6">
                <!-- Filters -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <input id="search-input" type="text" placeholder="Cari nama, email, atau ref transaksi..." class="rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                    <select id="status-filter" class="rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <option value="">Semua Status</option>
                        <option value="paid">Lunas</option>
                        <option value="pending">Menunggu</option>
                        <option value="failed">Gagal</option>
                        <option value="refunded">Dikembalikan</option>
                    </select>
                    <select id="currency-filter" class="rounded-md border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2">
                        <option value="">Semua Mata Uang</option>
                        <option value="IDR">IDR</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <!-- Loading -->
                <div id="payments-loading" class="py-12 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin text-3xl mb-3"></i>
                    <p class="mt-2">Memuat data pembayaran...</p>
                </div>

                <!-- Error -->
                <div id="payments-error" style="display:none" class="py-12 text-center text-red-500">
                    <i class="fas fa-exclamation-circle text-3xl mb-3"></i>
                    <p id="error-message" class="mt-2">Gagal memuat data.</p>
                    <button id="btn-retry" class="mt-3 px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                        Coba Lagi
                    </button>
                </div>

                <!-- Payments Table -->
                <div id="payments-table-wrapper" style="display:none" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kursus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="payments-tbody" class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                    <div id="payments-empty" style="display:none" class="py-12 text-center text-gray-400">
                        <i class="fas fa-receipt text-4xl mb-3"></i>
                        <p>Tidak ada pembayaran ditemukan</p>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="payments-pagination" style="display:none" class="mt-6 flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Menampilkan <span id="page-from">0</span>–<span id="page-to">0</span> dari <span id="page-total">0</span> hasil
                    </div>
                    <div class="flex items-center space-x-2">
                        <button id="btn-prev" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="page-indicator" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md">1</span>
                        <button id="btn-next" class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // ─── State ───────────────────────────────────────────────────────────────
    var s = {
        payments: [],
        page: 1,
        perPage: 10,
        lastPage: 1,
        total: 0,
        search: '',
        status: '',
        currency: '',
        searchTimer: null,
    };

    // ─── Helpers ─────────────────────────────────────────────────────────────
    function baseUrl() {
        var path = window.location.pathname;
        var idx  = path.indexOf('/admin');
        return idx !== -1
            ? window.location.origin + path.substring(0, idx)
            : window.location.origin;
    }

    function csrf() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function el(id) { return document.getElementById(id); }
    function show(id) { el(id).style.display = ''; }
    function hide(id) { el(id).style.display = 'none'; }

    function statusClass(status) {
        var map = {
            paid:     'bg-green-100 text-green-800',
            pending:  'bg-yellow-100 text-yellow-800',
            failed:   'bg-red-100 text-red-800',
            refunded: 'bg-blue-100 text-blue-800',
        };
        return map[status] || 'bg-gray-100 text-gray-800';
    }

    function fmtDate(d) {
        if (!d) return '-';
        try {
            return new Date(d).toLocaleString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        } catch(e) { return d; }
    }

    function fmtAmount(amount, curr) {
        try {
            return new Intl.NumberFormat('id-ID').format(Number(amount) || 0) + ' ' + (curr || '');
        } catch(e) { return (amount || 0) + ' ' + (curr || ''); }
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // ─── Render ──────────────────────────────────────────────────────────────
    function render() {
        hide('payments-loading');
        hide('payments-error');
        show('payments-table-wrapper');

        var tbody = el('payments-tbody');
        var empty = el('payments-empty');

        if (s.payments.length === 0) {
            tbody.innerHTML = '';
            show('payments-empty');
            hide('payments-pagination');
        } else {
            hide('payments-empty');
            tbody.innerHTML = s.payments.map(function(p) {
                return [
                    '<tr class="hover:bg-gray-50">',
                    '  <td class="px-6 py-4">',
                    '    <div class="text-sm font-medium text-gray-900">' + escHtml(p.user && p.user.name || 'N/A') + '</div>',
                    '    <div class="text-sm text-gray-500">'            + escHtml(p.user && p.user.email || 'N/A') + '</div>',
                    '  </td>',
                    '  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + escHtml(p.course && p.course.title || 'N/A') + '</td>',
                    '  <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">' + fmtAmount(p.amount, p.currency) + '</td>',
                    '  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escHtml(p.method || '-') + '</td>',
                    '  <td class="px-6 py-4 whitespace-nowrap">',
                    '    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' + statusClass(p.status) + '">' + escHtml(p.status || '-') + '</span>',
                    '  </td>',
                    '  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + escHtml(p.transaction_ref || '-') + '</td>',
                    '  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' + fmtDate(p.paid_at || p.created_at) + '</td>',
                    '  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">',
                    '    <button data-id="' + p.id + '" class="delete-btn text-red-600 hover:text-red-900">Hapus</button>',
                    '  </td>',
                    '</tr>',
                ].join('\n');
            }).join('');

            // Attach delete handlers to newly rendered buttons
            tbody.querySelectorAll('.delete-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    deletePayment(parseInt(this.getAttribute('data-id'), 10));
                });
            });

            show('payments-pagination');
        }

        // Stats
        el('total-count').textContent = s.total;
        el('page-from').textContent   = s.total === 0 ? 0 : (s.page - 1) * s.perPage + 1;
        el('page-to').textContent     = Math.min(s.page * s.perPage, s.total);
        el('page-total').textContent  = s.total;
        el('page-indicator').textContent = s.page;

        el('btn-prev').disabled = s.page <= 1;
        el('btn-next').disabled = s.page >= s.lastPage;
    }

    // ─── Load ────────────────────────────────────────────────────────────────
    function load() {
        el('payments-loading').style.display = '';
        el('payments-error').style.display   = 'none';
        el('payments-table-wrapper').style.display = 'none';
        el('payments-pagination').style.display    = 'none';

        var params = new URLSearchParams({
            per_page: s.perPage,
            page:     s.page,
            search:   s.search,
            status:   s.status,
            currency: s.currency,
        });

        var url = baseUrl() + '/admin/api/payments?' + params.toString();
        console.log('[Payments] GET', url);

        fetch(url, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrf(),
            },
        })
        .then(function(resp) {
            console.log('[Payments] status:', resp.status, resp.url);
            if (resp.status === 401) { throw new Error('Tidak terautentikasi (401). Silakan login ulang.'); }
            if (resp.status === 403) { throw new Error('Akses ditolak (403). Anda bukan admin.'); }
            if (!resp.ok)            { throw new Error('Server error: HTTP ' + resp.status); }
            return resp.clone().text().then(function(txt) {
                console.log('[Payments] RAW RESPONSE:', txt.substring(0, 800));
                try { return JSON.parse(txt); }
                catch(e) { throw new Error('Bukan JSON! Raw: ' + txt.substring(0, 200)); }
            });
        })
        .then(function(data) {
            console.log('[Payments] full data:', JSON.stringify(data).substring(0, 500));
            console.log('[Payments] received:', data.total, 'total, page', data.current_page, '/', data.last_page, 'rows:', (data.data || []).length);
            s.payments = data.data  || [];
            s.total    = data.total    || 0;
            s.lastPage = data.last_page || 1;
            s.page     = data.current_page || 1;
            render();
        })
        .catch(function(err) {
            console.error('[Payments] error:', err);
            el('payments-loading').style.display = 'none';
            el('payments-error').style.display   = '';
            el('error-message').textContent = err.message || String(err);
        });
    }

    // ─── Delete ──────────────────────────────────────────────────────────────
    function deletePayment(id) {
        if (!confirm('Hapus pembayaran ini?')) return;
        fetch(baseUrl() + '/admin/api/payments/' + id, {
            method: 'DELETE',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf(),
            },
        })
        .then(function(resp) {
            if (!resp.ok) throw new Error('HTTP ' + resp.status);
            load();
        })
        .catch(function(err) {
            alert('Gagal menghapus: ' + err.message);
        });
    }

    // ─── Events ──────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        load();

        el('btn-refresh').addEventListener('click', function() { load(); });
        el('btn-retry').addEventListener('click', function() { load(); });

        el('btn-prev').addEventListener('click', function() {
            if (s.page > 1) { s.page--; load(); }
        });
        el('btn-next').addEventListener('click', function() {
            if (s.page < s.lastPage) { s.page++; load(); }
        });

        el('search-input').addEventListener('input', function() {
            clearTimeout(s.searchTimer);
            var val = this.value;
            s.searchTimer = setTimeout(function() {
                s.search = val;
                s.page   = 1;
                load();
            }, 400);
        });

        el('status-filter').addEventListener('change', function() {
            s.status = this.value;
            s.page   = 1;
            load();
        });

        el('currency-filter').addEventListener('change', function() {
            s.currency = this.value;
            s.page     = 1;
            load();
        });
    });
})();
</script>

@endsection