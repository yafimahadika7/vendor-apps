<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Bisnis Unit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            width: 220px;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #343a40;
            color: white;
            padding-top: 1rem;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar.hide {
            left: -220px;
        }

        .sidebar a,
        .sidebar form button {
            color: white;
            padding: 10px 20px;
            display: block;
            text-decoration: none;
            background: none;
            border: none;
            text-align: left;
            width: 100%;
        }

        .sidebar a:hover,
        .sidebar form button:hover {
            background-color: #495057;
        }

        .topbar {
            height: 60px;
            background-color: #f8f9fa;
            padding: 0 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-left: 220px;
            transition: margin-left 0.3s ease;
        }

        .topbar.collapsed {
            margin-left: 0;
        }

        .content {
            margin-left: 220px;
            padding: 2rem;
            transition: margin-left 0.3s ease;
        }

        .content.collapsed {
            margin-left: 0;
        }

        .toggle-btn {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -220px;
            }

            .sidebar.show {
                left: 0;
            }

            .topbar,
            .content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="text-center mb-3">
            <strong>{{ Auth::user()->name }}</strong><br>
            <small class="text-warning">{{ ucfirst(Auth::user()->unit_name) }}</small>
        </div>

        <a href="{{ route('unit.dashboard') }}">📊 Dashboard</a>
        <a href="{{ route('unit.transaksi.index') }}">📦 Transaksi</a>
        <a href="{{ route('unit.pemesanan.index') }}">📝 Pemesanan</a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">🚪 Logout</button>
        </form>
    </div>

    <!-- Topbar -->
    <div class="topbar" id="topbar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <span>Selamat Datang, {{ Auth::user()->name }}</span>
    </div>

    <!-- Content -->
    <div class="content" id="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap vendor-header">
            <h2 class="mb-2 mb-md-0">Daftar Transaksi</h2>

            <div class="d-flex flex-wrap gap-2">

                <!-- Filter Status -->
                <form method="GET" action="{{ route('unit.transaksi.index') }}">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="gagal" {{ request('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                        <option value="sukses" {{ request('status') == 'sukses' ? 'selected' : '' }}>Sukses</option>
                    </select>
                </form>

                <!-- Form Pencarian -->
                <form method="GET" action="{{ route('unit.transaksi.index') }}">
                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Cari vendor...">
                        <button class="btn btn-light border" type="submit">🔍</button>
                    </div>
                </form>

                <!-- Filter Kategori -->
                <form method="GET" action="{{ route('unit.transaksi.index') }}">
                    <select name="kategori_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center align-middle">
                    <tr>
                        <th>No</th>
                        <th>Vendor</th>
                        <th>Kategori</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $transaksi)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="text-center align-middle">{{ $transaksi->vendor->nama_vendor ?? '-' }}</td>
                            <td class="text-center align-middle">{{ $transaksi->kategori->nama_kategori ?? '-' }}</td>
                            <td>
                                <ul class="mb-0">
                                    @foreach($transaksi->items as $item)
                                        <li class="mb-2">
                                            {{-- Tampilkan item biasa --}}
                                            @foreach($item as $key => $val)
                                                @if($key === 'upload_gambar')
                                                    @continue
                                                @endif

                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                @if(is_array($val))
                                                    @foreach ($val as $subVal)
                                                        {!! is_array($subVal) ? nl2br(e(json_encode($subVal, JSON_PRETTY_PRINT))) : e($subVal) !!}
                                                        <br>
                                                    @endforeach
                                                @else
                                                    {{ $val }}
                                                @endif
                                                <br>
                                            @endforeach

                                            {{-- Tampilkan gambar jika ada --}}
                                            <!-- @if(isset($item['upload_gambar']) && is_array($item['upload_gambar']))
                                                                                        <div class="mt-2">
                                                                                            @foreach($item['upload_gambar'] as $img)
                                                                                                <img src="{{ asset('storage/' . $img) }}" alt="Gambar"
                                                                                                    class="img-thumbnail me-1 mb-1" width="80">
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif -->
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-center align-middle"><span
                                    class="badge bg-secondary">{{ ucfirst($transaksi->status) }}</span></td>
                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($transaksi->created_at)->format('d-m-Y H:i') }}
                            </td>
                            <td class="text-center align-middle">
                                <form action="{{ route('unit.transaksi.reorder', $transaksi->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning">🔁 Reorder</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('main-content');
            const topbar = document.getElementById('topbar');

            sidebar.classList.toggle('hide');
            content.classList.toggle('collapsed');
            topbar.classList.toggle('collapsed');
        }
    </script>

</body>

</html>