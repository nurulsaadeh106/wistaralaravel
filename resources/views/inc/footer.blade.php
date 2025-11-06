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

<!-- 💬 WistaraBot -->
<div id="chatbot-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
  <!-- Toggle Button -->
  <button id="chatbot-toggle" class="btn shadow-lg rounded-circle"
    style="width: 60px; height: 60px; background-color:#c7a946; color:#071739; font-size:22px; border:none;">
    💬
  </button>

  <!-- Chatbox -->
  <div id="chatbot-box" class="card shadow-lg border-0 rounded-4 overflow-hidden d-none"
       style="width: 340px; height: 470px; background-color: #fefefe;">
    <!-- Header -->
    <div class="card-header d-flex justify-content-between align-items-center py-2"
         style="background-color:#071739; color:#fff;">
      <div>
        <h6 class="mb-0 fw-bold">WistaraBot 🤖</h6>
        <small class="opacity-75">Online</small>
      </div>
      <button id="chatbot-close" class="btn btn-sm btn-outline-light border-0">✖</button>
    </div>

    <!-- Body -->
    <div id="chatbot-body" class="card-body overflow-auto p-3"
         style="font-size:14px; background:linear-gradient(180deg,#f8f9fa,#fff 90%); height:340px;">
    </div>

    <!-- Footer -->
    <div class="card-footer bg-light p-2 border-0">
      <form id="chatbot-form" class="d-flex">
        <input type="text" id="chatbot-input" class="form-control border-0 shadow-sm rounded-pill me-2"
               placeholder="Ketik pesan..." autocomplete="off">
        <button type="submit" class="btn rounded-pill px-3"
                style="background-color:#c7a946; color:#071739; font-weight:bold;">Kirim</button>
      </form>
    </div>
  </div>
</div>

<style>
  /* === Bubble Chat === */
  #chatbot-body .bot-msg, #chatbot-body .user-msg {
    max-width: 85%;
    padding: 9px 13px;
    border-radius: 15px;
    margin-bottom: 8px;
    display: inline-block;
    word-wrap: break-word;
    line-height: 1.4;
  }
  .bot-msg {
    background: #e9ecef;
    color: #071739;
    border-top-left-radius: 0;
  }
  .user-msg {
    background: #071739;
    color: #fff;
    border-top-right-radius: 0;
  }

  /* === Typing Animation === */
  .typing-dots {
    display: inline-block;
    width: 30px;
    text-align: center;
  }
  .typing-dots span {
    display: inline-block;
    width: 6px;
    height: 6px;
    margin: 0 1px;
    background-color: #c7a946;
    border-radius: 50%;
    animation: blink 1.4s infinite both;
  }
  .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
  .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

  @keyframes blink {
    0%, 80%, 100% { opacity: 0; }
    40% { opacity: 1; }
  }
</style>

<script>
  const toggleBtn = document.getElementById("chatbot-toggle");
  const closeBtn = document.getElementById("chatbot-close");
  const chatBox = document.getElementById("chatbot-box");
  const chatBody = document.getElementById("chatbot-body");
  const form = document.getElementById("chatbot-form");
  const input = document.getElementById("chatbot-input");

  // 🧠 Load chat history
  document.addEventListener("DOMContentLoaded", () => {
    const saved = localStorage.getItem("wistara_chat");
    if (saved) {
      chatBody.innerHTML = saved;
    } else {
      addMessage("👋 Halo! Saya <b>WistaraBot</b> siap membantu kamu.<br>Coba ketik <i>‘berita terbaru’</i> atau nama produk batik.", "bot");
    }
  });

  // 🎚️ Toggle window
  toggleBtn.addEventListener("click", () => chatBox.classList.toggle("d-none"));
  closeBtn.addEventListener("click", () => chatBox.classList.add("d-none"));

  // ✉️ Kirim pesan
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const message = input.value.trim();
    if (!message) return;

    addMessage(message, "user");
    input.value = "";

    // 🕓 Tampilkan animasi mengetik
    const typing = document.createElement("div");
    typing.className = "bot-msg";
    typing.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
    chatBody.appendChild(typing);
    chatBody.scrollTop = chatBody.scrollHeight;

    try {
      const res = await fetch("{{ url('/api/chatbot') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
        },
        body: JSON.stringify({ message }),
      });

      const data = await res.json();
      typing.remove();

      if (data.reply) {
        addMessage(data.reply, "bot");
      } else {
        fallbackToWhatsApp(message);
      }
    } catch (err) {
      typing.remove();
      fallbackToWhatsApp(message);
    }
  });

  // 💬 Tambah pesan ke tampilan
  function addMessage(text, sender) {
    const msg = document.createElement("div");
    msg.className = sender === "bot" ? "bot-msg" : "user-msg";
    msg.innerHTML = text;
    const wrapper = document.createElement("div");
    wrapper.className = sender === "bot" ? "text-start" : "text-end";
    wrapper.appendChild(msg);
    chatBody.appendChild(wrapper);
    chatBody.scrollTop = chatBody.scrollHeight;

    // Simpan ke localStorage
    localStorage.setItem("wistara_chat", chatBody.innerHTML);
  }

  // 💡 Fallback ke WhatsApp
  function fallbackToWhatsApp(message) {
    const reply = `Maaf, saya belum paham pertanyaan itu 😔<br>
      Kamu bisa hubungi admin langsung di <a href='https://wa.me/62895381110035?text=${encodeURIComponent(message)}' target='_blank'>
      WhatsApp Batik Wistara 📞</a>`;
    addMessage(reply, "bot");
  }
</script>

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