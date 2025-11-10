<!-- ================== WISTARABOT CHAT ================== -->
<style>
:root {
  --wistara-navy: #071739;
  --wistara-gold: #d4af37;
}

/* Tombol Chat Mengambang */
.chat-toggle {
  position: fixed;
  bottom: 25px;
  right: 25px;
  width: 65px;
  height: 65px;
  border-radius: 50%;
  border: none;
  background: linear-gradient(135deg, #071739, #1d315d);
  color: #fff;
  font-size: 1.6rem;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  cursor: pointer;
  z-index: 1000;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(212,175,55,0.6); }
  70% { box-shadow: 0 0 0 15px rgba(212,175,55,0); }
  100% { box-shadow: 0 0 0 0 rgba(212,175,55,0); }
}

/* Kotak Chat */
.chatbox {
  position: fixed;
  bottom: 95px;
  right: 25px;
  width: 360px;
  max-height: 520px;
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.25);
  display: none;
  flex-direction: column;
  overflow: hidden;
  z-index: 1001;
}
.chatbox.active { display: flex; animation: pop .25s ease-out; }
@keyframes pop { from{transform:scale(.9);opacity:0;} to{transform:scale(1);opacity:1;} }

.chatbox-header {
  background: var(--wistara-navy);
  color: #fff;
  padding: 12px 18px;
  font-weight: 600;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.chatbox-header .close-chat {
  background: none;
  border: none;
  color: white;
  font-size: 1.3rem;
  cursor: pointer;
}

.chatbox-body {
  flex: 1;
  padding: 14px;
  overflow-y: auto;
  background: #f8f9fb;
  display: flex;
  flex-direction: column;
}

/* Input */
.chatbox-input {
  display: flex;
  border-top: 1px solid #ddd;
}
.chatbox-input input {
  flex: 1;
  border: none;
  padding: 12px 14px;
  outline: none;
}
.chatbox-input button {
  background: var(--wistara-navy);
  color: #fff;
  border: none;
  padding: 0 20px;
  font-weight: 600;
}
.chatbox-input button:hover { background: #0a214f; }

/* Chat bubbles */
.message { margin-bottom: 10px; display: flex; flex-direction: column; }
.message.user { align-items: flex-end; }
.message.bot { align-items: flex-start; }
.bubble {
  max-width: 80%;
  padding: 10px 13px;
  border-radius: 15px;
  background: #e9eef5;
  font-size: 0.9rem;
  line-height: 1.4;
}
.message.user .bubble {
  background: var(--wistara-gold);
  color: #071739;
}

/* Quick replies */
.quick-replies {
  margin-top: 10px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.quick-btn {
  background: var(--wistara-navy);
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 7px 10px;
  cursor: pointer;
  text-align: left;
  transition: background .2s;
}
.quick-btn:hover { background: #0a214f; }

/* Typing animation */
.typing-dot {
  width: 7px;
  height: 7px;
  background: #999;
  border-radius: 50%;
  margin: 0 2px;
  animation: blink 1.4s infinite both;
}
@keyframes blink { 0%,80%,100%{opacity:0;} 40%{opacity:1;} }
</style>

<!-- Floating Chat Button -->
<button class="chat-toggle" id="chatToggle">💬</button>

<!-- Chatbox -->
<div class="chatbox" id="chatBox">
  <div class="chatbox-header">
    🤖 WistaraBot
    <button class="close-chat" id="closeChat">&times;</button>
  </div>
  <div class="chatbox-body" id="chatBody"></div>
  <div class="chatbox-input">
    <input type="text" id="chatInput" placeholder="Ketik pesan..." />
    <button id="sendBtn">Kirim</button>
  </div>
</div>

<script>
const API_URL = "https://chatbot.batikwistara.com/api/chat";
const ADMIN_WA = "62895381110035"; // nomor admin kamu
const chatBox = document.getElementById("chatBox");
const chatBody = document.getElementById("chatBody");
const chatInput = document.getElementById("chatInput");
const toggleBtn = document.getElementById("chatToggle");
const closeBtn = document.getElementById("closeChat");
const sendBtn = document.getElementById("sendBtn");

// Saat halaman dibuka, bot langsung aktif
window.addEventListener("load", () => {
  chatBox.classList.add("active");
  showWelcomeMessage();
});

toggleBtn.onclick = () => chatBox.classList.toggle("active");
closeBtn.onclick = () => chatBox.classList.remove("active");
sendBtn.onclick = handleSend;
chatInput.addEventListener("keypress", e => e.key === "Enter" && handleSend());

// Pesan awal
function showWelcomeMessage() {
  appendMessage("bot", "✨ Selamat datang di <b>Batik Wistara</b>!<br>Pilih layanan di bawah ini 👇", [
    { label: "🛍️ Katalog Produk", value: "produk" },
    { label: "📰 Berita Terbaru", value: "berita" },
    { label: "📍 Alamat & Jam Buka", value: "alamat" },
    { label: "💬 Hubungi Admin", value: "admin" }
  ]);
}

// Kirim pesan user
async function handleSend() {
  const text = chatInput.value.trim();
  if (!text) return;
  appendMessage("user", text);
  chatInput.value = "";

  // langsung ke WA jika ketik admin / 0
  if (text === "0" || text.toLowerCase().includes("admin")) {
    openAdminWhatsApp();
    return;
  }

  await sendToBot(text);
}

// kirim pesan ke server chatbot
async function sendToBot(message) {
  appendTyping();
  try {
    const res = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message })
    });
    const data = await res.json();
    removeTyping();

    // kalau pesan balasan berisi quick_replies = admin, langsung buka WA
    if (Array.isArray(data.quick_replies)) {
      const adminBtn = data.quick_replies.find(qr =>
        typeof qr.value === "string" && qr.value.toLowerCase().includes("admin")
      );
      if (adminBtn) {
        appendMessage("bot", data.reply || "📞 Klik tombol di bawah untuk chat admin:", data.quick_replies);
        return;
      }
    }

    appendMessage("bot", data.reply || "⚠️ Tidak ada balasan.", data.quick_replies || []);
  } catch (err) {
    removeTyping();
    appendMessage("bot", "⚠️ Gagal menghubungi server chatbot.");
  }
}

// Tambah bubble chat
function appendMessage(sender, text, quickReplies = []) {
  const msg = document.createElement("div");
  msg.className = `message ${sender}`;
  const bubble = document.createElement("div");
  bubble.className = "bubble";
  bubble.innerHTML = text;

  // quick replies
  if (sender === "bot" && quickReplies.length) {
    const wrap = document.createElement("div");
    wrap.className = "quick-replies";
    quickReplies.forEach(qr => {
      const label = typeof qr === "object" ? qr.label : qr;
      const value = typeof qr === "object" ? qr.value : qr;
      const btn = document.createElement("button");
      btn.className = "quick-btn";
      btn.textContent = label;

      btn.onclick = () => {
        appendMessage("user", label);
        // kalau tombol admin ditekan, langsung ke WA
        if (value === "admin" || value.toLowerCase().includes("wa.me")) {
          openAdminWhatsApp();
        } else if (value.startsWith("http")) {
          window.open(value, "_blank");
        } else {
          sendToBot(value);
        }
      };
      wrap.appendChild(btn);
    });
    bubble.appendChild(wrap);
  }

  msg.appendChild(bubble);
  chatBody.appendChild(msg);
  chatBody.scrollTop = chatBody.scrollHeight;
}

function appendTyping() {
  const el = document.createElement("div");
  el.className = "message bot typing";
  el.innerHTML = `<div class="bubble"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>`;
  chatBody.appendChild(el);
  chatBody.scrollTop = chatBody.scrollHeight;
}

function removeTyping() {
  const t = chatBody.querySelector(".typing");
  if (t) t.remove();
}

// buka WA admin langsung
function openAdminWhatsApp() {
  const url = `https://wa.me/${ADMIN_WA}?text=Halo%20admin%2C%20saya%20ingin%20bertanya.`;
  window.open(url, "_blank");
  appendMessage("bot", "📞 Membuka WhatsApp Admin...");
}
</script>

