@include('admin.header')

<div class="container py-5">
  <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 900px; background-color: var(--light-gray, #f8f9fb);">
    <!-- Header -->
    <div class="card-header text-white text-center rounded-top-4 py-4" style="background-color: var(--dark-navy, #071739);">
      <h3 class="mb-0 fw-bold" style="letter-spacing: 0.5px;">Tambah Berita Baru</h3>
    </div>

    <!-- Body -->
    <div class="card-body px-5 py-4" style="background-color: #fdfdfd;">
      <form action="{{ route('admin.berita.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Judul -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark-navy">Judul Berita</label>
          <input
            type="text"
            name="judul"
            class="form-control border-0 shadow-sm rounded-3 p-3 fs-5"
            placeholder="Masukkan judul berita..."
            required>
        </div>

        <!-- Konten -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark-navy">Konten</label>
          <textarea id="editor" name="konten"></textarea>
        </div>

        <!-- Gambar -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark-navy">Gambar (pilih salah satu)</label>
          <input type="file" name="gambar_file" class="form-control border-0 shadow-sm rounded-3 mb-2 p-2">
          <input type="url" name="gambar_url" class="form-control border-0 shadow-sm rounded-3 p-3" placeholder="Atau tempel URL gambar...">
        </div>

        <!-- Sumber -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark-navy">Sumber</label>
          <input
            type="text"
            name="sumber"
            class="form-control border-0 shadow-sm rounded-3 p-3"
            placeholder="Nama sumber (opsional)">
        </div>

        <!-- Tautan -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark-navy">Tautan Sumber</label>
          <input
            type="url"
            name="tautan_sumber"
            class="form-control border-0 shadow-sm rounded-3 p-3"
            placeholder="https://... (opsional)">
        </div>

        <!-- Tanggal -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark-navy">Tanggal Berita</label>
          <input
            type="date"
            name="tanggal"
            class="form-control border-0 shadow-sm rounded-3 p-3"
            value="{{ date('Y-m-d') }}">
          <small class="text-muted ms-1">Tanggal rilis berita internal atau eksternal.</small>
        </div>

        <!-- Status Draft / Publish -->
        <div class="mb-4">
          <label class="form-label fw-semibold text-dark-navy">Status Berita</label>
          <select name="status" class="form-select border-0 shadow-sm rounded-3 p-3">
            <option value="draft">Simpan sebagai Draft</option>
            <option value="published">Publikasikan</option>
          </select>
        </div>

        <!-- Tombol -->
        <div class="d-flex justify-content-end gap-3 mt-4">
          <a href="{{ route('admin.berita.index') }}" class="btn btn-custom-secondary px-4 py-2 rounded-3 fw-semibold">
            Kembali
          </a>
          <button class="btn btn-custom-primary px-4 py-2 rounded-3 fw-semibold" type="submit">
            Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('admin.footer')

<!-- ✨ TinyMCE -->
<script src="https://cdn.tiny.cloud/1/tuy4locoxw7b1g0t76h8p18h5dax2hw0a1y0ghclapgbpyw1/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: 'textarea[name=konten]',
    height: 400,
    menubar: true,
    plugins: 'image link media table code lists',
    toolbar:
      'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | image media link table | code',
    automatic_uploads: true,
    images_upload_url: '/upload',
    file_picker_types: 'image',
    content_style: 'body { font-family:Poppins, sans-serif; font-size:14px }'
  });
</script>

<style>
  :root {
    --dark-navy: #071739;
    --blue-gray: #4B6382;
    --light-gray: #f5f7fa;
    --gold: #d4af37;
  }

  .text-dark-navy { color: var(--dark-navy); }

  input:focus, textarea:focus, select:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(7, 23, 57, 0.2);
  }

  .btn-custom-primary {
    background-color: var(--dark-navy);
    color: #fff;
    border: none;
    transition: 0.3s ease;
  }
  .btn-custom-primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
  }
  .btn-custom-primary:active {
    background-color: var(--gold);
    color: #000;
    transform: scale(0.98);
  }

  .btn-custom-secondary {
    background-color: var(--blue-gray);
    color: #fff;
    border: none;
    transition: 0.3s ease;
  }
  .btn-custom-secondary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
  }
  .btn-custom-secondary:active {
    background-color: var(--gold);
    color: #000;
    transform: scale(0.98);
  }
</style>
