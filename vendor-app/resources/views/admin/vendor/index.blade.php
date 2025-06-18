<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
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

        .vendor-header {
            gap: 8px;
            /* Jarak antar elemen */
        }

        .vendor-header .form-filter {
            margin-bottom: 0;
        }

        .vendor-header select.form-select {
            width: auto;
            min-width: 150px;
        }

        @media (max-width: 576px) {
            .vendor-header {
                flex-direction: column;
                align-items: stretch;
            }

            .vendor-header .form-filter,
            .vendor-header button {
                width: 100%;
            }
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
            <small class="text-warning">{{ ucfirst(Auth::user()->role) }}</small>
        </div>

        <a href="{{ route('admin.dashboard') }}">📊 Dashboard</a>
        <a href="#">💳 Transaksi</a>
        <a href="{{ route('admin.vendors.index') }}">🏢 Vendor</a>
        <a href="{{ route('admin.users.index') }}">👤 User</a>

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
            <h2 class="me-auto">Manajemen Vendor</h2>

            <form method="GET" action="{{ route('admin.vendors.index') }}">
                <div class="input-group input-group-sm" style="max-width: 250px;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari vendor...">
                    <button class="btn btn-light border" type="submit">🔍</button>
                </div>
            </form>

            <form method="GET" action="{{ route('admin.vendors.index') }}" class="form-filter me-2">
                <select name="kategori_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </form>

            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                data-bs-target="#tambahVendorModal">
                + Tambah Vendor
            </button>
        </div>

        <!-- Modal Tambah Vendor -->
        <div class="modal fade" id="tambahVendorModal" tabindex="-1" aria-labelledby="tambahVendorModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <form action="{{ route('admin.vendors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tambahVendorModalLabel">Tambah Vendor</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nama_vendor" class="form-label">Nama Vendor</label>
                                <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Vendor</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="kontak_whatsapp" class="form-label">Kontak WhatsApp</label>
                                <input type="text" class="form-control" id="kontak_whatsapp" name="kontak_whatsapp">
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="kategori_id" class="form-label">Kategori</label>
                                <select name="kategori_id" id="kategori_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="katalog" class="form-label">Katalog (PDF/Gambar)</label>
                                <input type="file" name="katalog" id="katalog" class="form-control"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark text-center align-middle">
                <tr>
                    <th>No</th>
                    <th>
                        <a href="{{ route('admin.vendors.index', ['sort' => request('sort') === 'nama_asc' ? 'nama_desc' : 'nama_asc', 'kategori_id' => request('kategori_id')]) }}"
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
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                    data-bs-target="#modalKatalog{{ $vendor->id }}">
                                    Lihat
                                </button>

                                <!-- Modal Katalog -->
                                <div class="modal fade" id="modalKatalog{{ $vendor->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Katalog Vendor</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                               @php
                                                    $ext = strtolower(pathinfo($vendor->katalog, PATHINFO_EXTENSION));
                                                @endphp

                                                @if ($ext === 'pdf')
                                                    <embed src="{{ asset('storage/katalog/' . $vendor->katalog) }}" type="application/pdf" width="100%" height="500px">
                                                @elseif (in_array($ext, ['jpg', 'jpeg', 'png']))
                                                    <img src="{{ asset('storage/katalog/' . $vendor->katalog) }}" class="img-fluid" alt="Katalog">
                                                @else
                                                    <p class="text-danger">Format file tidak didukung.</p>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <!-- Tombol Edit -->
                                <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#modalEditVendor{{ $vendor->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST"
                                    class="form-delete d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit Vendor -->
                    <div class="modal fade" id="modalEditVendor{{ $vendor->id }}" tabindex="-1"
                        aria-labelledby="modalEditVendorLabel{{ $vendor->id }}" aria-hidden="true" data-bs-backdrop="static"
                        data-bs-keyboard="false">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.vendors.update', $vendor->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalEditVendorLabel{{ $vendor->id }}">Edit Vendor</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <label>Nama Vendor</label>
                                            <input type="text" name="nama_vendor" value="{{ $vendor->nama_vendor }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Email</label>
                                            <input type="email" name="email" value="{{ $vendor->email }}"
                                                class="form-control">
                                        </div>
                                        <div class="mb-2">
                                            <label>Kontak WhatsApp</label>
                                            <input type="text" name="kontak_whatsapp" value="{{ $vendor->kontak_whatsapp }}"
                                                class="form-control">
                                        </div>
                                        <div class="mb-2">
                                            <label>Alamat</label>
                                            <textarea name="alamat" class="form-control">{{ $vendor->alamat }}</textarea>
                                        </div>
                                        <div class="mb-2">
                                            <label>Kategori</label>
                                            <select name="kategori_id" class="form-control" required>
                                                @foreach ($kategoris as $kategori)
                                                    <option value="{{ $kategori->id }}" {{ $vendor->kategori_id == $kategori->id ? 'selected' : '' }}>
                                                        {{ $kategori->nama_kategori }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Simpan</button>
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada vendor.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <script>
        // Tangani semua form dengan class "form-delete"
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Mencegah submit langsung

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data vendor akan dihapus secara permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit jika dikonfirmasi
                    }
                });
            });
        });
    </script>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</html>