<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Bisnis Unit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
        <div class="d-flex align-items-center mb-3 flex-wrap vendor-header">
            <h2 class="me-auto">Daftar Vendor</h2>

            <!-- Form Pencarian -->
            <form method="GET" action="{{ route('unit.pemesanan.index') }}" class="me-2">
                <div class="input-group input-group-sm" style="max-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari vendor...">
                    <button class="btn btn-light border" type="submit">🔍</button>
                </div>
            </form>

            <!-- Filter Kategori -->
            <form method="GET" action="{{ route('unit.pemesanan.index') }}" class="form-filter">
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
        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark text-center align-middle">
                <tr>
                    <th>No</th>
                    <th>
                        <a href="{{ route('unit.pemesanan.index', ['sort' => request('sort') === 'nama_asc' ? 'nama_desc' : 'nama_asc', 'kategori_id' => request('kategori_id')]) }}"
                            class="text-white text-decoration-none">
                            Nama Vendor
                            @if(request('sort') === 'nama_asc')

                            @elseif(request('sort') === 'nama_desc')

                            @endif
                        </a>
                    </th>
                    <th>Email</th>
                    <th>WhatsApp</th>
                    <th>Alamat</th>
                    <th>Kategori</th>
                    <th>Katalog</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vendors as $index => $vendor)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $vendor->nama_vendor }}</td>
                        <td>{{ $vendor->email }}</td>
                        <td>{{ $vendor->kontak_whatsapp }}</td>
                        <td>{{ $vendor->alamat }}</td>
                        <td>{{ $vendor->kategori->nama_kategori ?? '-' }}</td>
                        <td class="text-center">
                            @if ($vendor->katalog)
                                <!-- Tombol Lihat (Modal) -->
                                <button type="button" class="btn btn-sm btn-info me-1" data-bs-toggle="modal"
                                    data-bs-target="#modalKatalog{{ $vendor->id }}" title="Lihat Katalog">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <!-- Tombol Download -->
                                <a href="{{ asset('storage/katalog/' . $vendor->katalog) }}" class="btn btn-sm btn-secondary"
                                    download title="Download Katalog">
                                    <i class="fas fa-download"></i>
                                </a>

                                <!-- Modal Lihat Katalog -->
                                <div class="modal fade" id="modalKatalog{{ $vendor->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Katalog {{ $vendor->nama_vendor }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                @php
                                                    $ext = strtolower(pathinfo($vendor->katalog, PATHINFO_EXTENSION));
                                                @endphp

                                                @if ($ext === 'pdf')
                                                    <embed src="{{ asset('storage/katalog/' . $vendor->katalog) }}"
                                                        type="application/pdf" width="100%" height="500px">
                                                @elseif (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ asset('storage/katalog/' . $vendor->katalog) }}" class="img-fluid"
                                                        alt="Katalog">
                                                @else
                                                    <p class="text-danger">Format katalog tidak dikenali.</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#modalPesan{{ $vendor->id }}">
                                Pesan
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada vendor.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @foreach($vendors as $vendor)
        <!-- Modal Pemesanan -->
        <div class="modal fade" id="modalPesan{{ $vendor->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $vendor->id }}"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ route('unit.pemesanan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pesan ke {{ $vendor->nama_vendor }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>

                        <div class="modal-body">
                            @php
                                $fields = json_decode($vendor->kategori->input_fields ?? '[]', true);
                            @endphp

                            @if(is_array($fields))
                                <div id="items-container-{{ $vendor->id }}">
                                    <div class="item-group border rounded p-3 mb-3">
                                        @foreach($fields as $field)
                                            @if ($field === 'upload_gambar')
                                                <div class="mb-2">
                                                    <label class="form-label">Upload Gambar</label>
                                                    <input type="file" name="items[0][upload_gambar][]" class="form-control" multiple>
                                                </div>
                                            @elseif ($field === 'jumlah')
                                                <div class="mb-2">
                                                    <label class="form-label">Jumlah</label>
                                                    <input type="number" name="items[0][jumlah]" class="form-control" required>
                                                </div>
                                            @elseif ($field === 'metode_pengadaan')
                                                <div class="mb-2">
                                                    <label class="form-label">Metode Pengadaan</label>
                                                    <select name="items[0][metode_pengadaan]" class="form-select" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="sewa">Sewa</option>
                                                        <option value="beli">Beli</option>
                                                    </select>
                                                </div>
                                            @else
                                                <div class="mb-2">
                                                    <label class="form-label">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                                    <input type="text" name="items[0][{{ $field }}]" class="form-control" required>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-secondary mb-3"
                                    onclick="tambahItem({{ $vendor->id }})">
                                    + Tambah Item
                                </button>
                            @else
                                <div class="alert alert-warning">Input form untuk vendor ini belum dikonfigurasi dengan benar.
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Kirim Pesanan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <!-- Script Tambah Item -->
    <script>
        function tambahItem(vendorId) {
            const container = document.getElementById('items-container-' + vendorId);
            const itemGroups = container.querySelectorAll('.item-group');
            const newIndex = itemGroups.length;

            const firstGroup = itemGroups[0];
            const clone = firstGroup.cloneNode(true);

            // Update name attribute & kosongkan input
            clone.querySelectorAll('input, select').forEach(el => {
                const regex = /\[\d+\]/;
                el.name = el.name.replace(regex, `[${newIndex}]`);
                if (el.type === 'file') {
                    el.value = null;
                } else if (el.tagName.toLowerCase() === 'select') {
                    el.selectedIndex = 0;
                } else {
                    el.value = '';
                }
            });

            container.appendChild(clone);
        }
    </script>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>