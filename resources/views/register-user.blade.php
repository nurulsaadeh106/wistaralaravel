@include('inc.header')

<div class="register-page d-flex align-items-center justify-content-center min-vh-100">
  <!-- Overlay -->
  <div class="register-overlay"></div>

  <!-- Box Register -->
  <div class="register-box bg-white shadow rounded p-4 p-md-5 position-relative">
    <h2 class="text-center fw-bold mb-4 font-caslon">Daftar Akun</h2>

    {{-- Alert Pesan --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('user.register.post') }}">
      @csrf

      <!-- Nama -->
      <div class="mb-3">
        <label for="name" class="form-label fw-semibold text-dark">Nama Lengkap</label>
        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama lengkap" required>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label fw-semibold text-dark">Email</label>
        <input 
          type="email"
          name="email"
          id="email"
          class="form-control"
          placeholder="contoh: nama@email.com"
          required
        >
        <small class="text-muted">Gunakan format email yang valid.</small>
        <div id="emailError" class="text-danger small mt-1"></div>
      </div>

      <!-- Nomor Telepon -->
      <div class="mb-3">
        <label for="phone" class="form-label fw-semibold text-dark">Nomor Telepon</label>
        <input
          type="tel"
          name="phone"
          id="phone"
          class="form-control"
          placeholder="Contoh: 081234567890"
          maxlength="15"
          required
        >
        <div id="phoneError" class="text-danger small mt-1"></div>
      </div>

      <!-- Password -->
      <div class="mb-3 position-relative">
        <label for="password" class="form-label fw-semibold text-dark">Password</label>
        <div class="input-group">
          <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
          <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
            <i class="fa fa-eye"></i>
          </button>
        </div>
        <small class="text-muted d-block mt-1">
          Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, angka, dan simbol.
        </small>
        <div id="passwordStrengthBar" class="progress mt-2" style="height: 5px;">
          <div class="progress-bar" role="progressbar"></div>
        </div>
        <div id="passwordStrengthText" class="small mt-1 fw-semibold"></div>
      </div>

      <!-- Konfirmasi Password -->
      <div class="mb-3 position-relative">
        <label for="password_confirmation" class="form-label fw-semibold text-dark">Konfirmasi Password</label>
        <div class="input-group">
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password" required>
          <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
            <i class="fa fa-eye"></i>
          </button>
        </div>
      </div>

      <!-- Tombol -->
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-dark py-2 fw-semibold font-caslon">
          Daftar Sekarang
        </button>
      </div>

      <!-- Link Login -->
      <div class="text-center">
        <small class="text-dark">
          Sudah punya akun?
          <a href="{{ url('/login') }}" class="text-decoration-none fw-semibold">Login di sini</a>
        </small>
      </div>
    </form>
  </div>
</div>

<script>
  // 🛡️ Validasi Strength Password
  const passwordField = document.getElementById('password');
  const strengthBar = document.querySelector('#passwordStrengthBar .progress-bar');
  const strengthText = document.getElementById('passwordStrengthText');

  passwordField.addEventListener('input', function () {
    const val = this.value;
    let strength = 0;
    if (/[a-z]/.test(val)) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[\W_]/.test(val)) strength++;
    if (val.length >= 8) strength++;

    let width = (strength / 5) * 100;
    strengthBar.style.width = width + '%';

    if (strength === 0) {
      strengthBar.className = 'progress-bar';
      strengthText.textContent = '';
    } else if (strength <= 2) {
      strengthBar.className = 'progress-bar bg-danger';
      strengthText.textContent = 'Password Lemah';
    } else if (strength <= 4) {
      strengthBar.className = 'progress-bar bg-warning';
      strengthText.textContent = 'Password Sedang';
    } else {
      strengthBar.className = 'progress-bar bg-success';
      strengthText.textContent = 'Password Kuat';
    }
  });

  // 👁️ Toggle Show / Hide Password
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const icon = btn.querySelector('i');

      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
    });
  });

  // 📞 Validasi Nomor HP
  const phoneInput = document.getElementById('phone');
  const phoneError = document.getElementById('phoneError');
  phoneInput.addEventListener('input', function () {
    const val = this.value;
    const regex = /^\+?[0-9]{10,15}$/;
    if (!regex.test(val)) {
      phoneError.textContent = "Nomor telepon harus 10–15 digit (boleh diawali +).";
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
    } else {
      phoneError.textContent = "";
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
    }
  });

  // 📧 Validasi Email
  const emailInput = document.getElementById('email');
  const emailError = document.getElementById('emailError');
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  emailInput.addEventListener('input', function () {
    const val = this.value.trim();
    if (!emailRegex.test(val)) {
      emailError.textContent = "Format email tidak valid.";
      this.classList.add('is-invalid');
      this.classList.remove('is-valid');
    } else {
      emailError.textContent = "";
      this.classList.remove('is-invalid');
      this.classList.add('is-valid');
    }
  });

// === 📧 Cek Email Sudah Terdaftar ===
const emailInput = document.getElementById('email');
const emailError = document.getElementById('emailError');
emailInput.addEventListener('blur', async () => {
  const val = emailInput.value.trim();
  if (!val) return;

  const response = await fetch(`{{ route('check.user') }}?email=${encodeURIComponent(val)}`);
  const data = await response.json();

  if (data.exists) {
    emailError.textContent = "⚠️ Email ini sudah terdaftar.";
    emailInput.classList.add('is-invalid');
    emailInput.classList.remove('is-valid');
    alert("Email ini sudah digunakan. Silakan gunakan email lain.");
  } else {
    emailError.textContent = "";
    emailInput.classList.remove('is-invalid');
    emailInput.classList.add('is-valid');
  }
});

// === 📞 Cek Nomor HP Sudah Terdaftar ===
const phoneInput = document.getElementById('phone');
const phoneError = document.getElementById('phoneError');
phoneInput.addEventListener('blur', async () => {
  const val = phoneInput.value.trim();
  if (!val) return;

  const response = await fetch(`{{ route('check.user') }}?phone=${encodeURIComponent(val)}`);
  const data = await response.json();

  if (data.exists) {
    phoneError.textContent = "⚠️ Nomor telepon ini sudah terdaftar.";
    phoneInput.classList.add('is-invalid');
    phoneInput.classList.remove('is-valid');
    alert("Nomor telepon ini sudah digunakan. Silakan gunakan nomor lain.");
  } else {
    phoneError.textContent = "";
    phoneInput.classList.remove('is-invalid');
    phoneInput.classList.add('is-valid');
  }
});

</script>

@include('inc.footer')
