<!-- Bootstrap Bundle -->
<footer class="bg-light text-dark pt-5 pb-4 shadow-sm mt-0">
  <div class="container">
    <div class="row gy-4 text-center text-md-start align-items-start">

      <!-- Logo & Deskripsi -->
      <div class="col-12 col-md-5">
        <div class="mb-3">
          <img src="{{ asset('img/logowarna.png') }}" alt="Batik Wistara" height="80">
        </div>
        <p class="mx-auto mx-md-0" style="max-width: 90%;">
          <strong>Batik Wistara</strong> adalah wujud cinta terhadap warisan budaya Indonesia. 
          Dibuat dengan tangan yang terampil dan penuh cinta.
        </p>
      </div>

      <!-- Navigasi -->
      <div class="d-none d-md-block col-md-3">
        <h5 class="fw-bold mb-3">Navigasi</h5>
        <ul class="list-unstyled">
          <li><a href="{{ url('/') }}" class="text-dark text-decoration-none d-block">Beranda</a></li>
          <li><a href="{{ url('/tentang') }}" class="text-dark text-decoration-none d-block">Tentang</a></li>
          <li><a href="{{ url('/katalog') }}" class="text-dark text-decoration-none d-block">Katalog</a></li>
          <li><a href="{{ url('/berita') }}" class="text-dark text-decoration-none d-block">Berita</a></li>
        </ul>
      </div>

      <!-- Kontak -->
      <div class="col-12 col-md-4">
        <h5 class="fw-bold mb-3">Kontak Kami</h5>
        <ul class="list-unstyled">
          <li class="mb-2">
            <strong>Alamat:</strong><br>
            <a href="https://maps.app.goo.gl/WqHPo5eNBDqHykhM8" target="_blank" class="text-dark text-decoration-none d-block">
              Jl. Tambak Medokan Ayu VI C No.56B, Surabaya, Jawa Timur 60295
            </a>
          </li>
          <li class="mb-2">
            <strong>WhatsApp:</strong><br>
            <a href="https://wa.me/6281234567890" class="text-dark text-decoration-none d-block">
              0812-3456-7890
            </a>
          </li>
          <li>
            <strong>Email:</strong><br>
            <a href="mailto:official.batikwistara@gmail.com" class="text-dark text-decoration-none d-block">
              official.batikwistara@gmail.com
            </a>
          </li>
        </ul>
      </div>

    </div>

    <hr class="border-top border-secondary my-4">

    <div class="text-center small">
      &copy; {{ date('Y') }} Batik Wistara. Seluruh hak cipta dilindungi.
    </div>
  </div>
</footer>


<!-- ================= SCRIPTS ================= -->
<script type="module" src="https://cdn.jsdelivr.net/npm/lite-youtube-embed@0.3.0/src/lite-yt-embed.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lite-youtube-embed@0.3.0/src/lite-yt-embed.css">

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      AOS.init({
        once: false,          // ❗ animasi akan dijalankan ulang setiap scroll masuk viewport
        duration: 1000,       // durasi animasi (ms)
        easing: 'ease-in-out',
        offset: 120           // jarak sebelum elemen muncul
      });
    });
  </script>

</body>
</html>

@include('inc.chatbot')