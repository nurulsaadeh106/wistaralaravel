<!-- ================== WISTARABOT CHAT ================== -->
<style>
:root {
  --wistara-navy: #071739;
  --wistara-gold: #d4af37;
}

/* === Floating Chat Button Improved === */
.chat-toggle {
  position: fixed;
  bottom: 25px;
  right: 25px;
  width: 65px;
  height: 65px;
  border-radius: 50%;
  border: none;
  outline: none;
  background: linear-gradient(135deg, #071739, #1d315dff);
  color: #071739;
  font-size: 1.6rem;
  font-weight: bold;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  cursor: pointer;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all .25s ease;
  animation: pulse 2s infinite;
}

.chat-icon  
/* Hover & Active */
.chat-toggle:hover {
  transform: translateY(-4px) scale(1.05);
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
.chat-toggle:active {
  transform: scale(0.96);
  animation: none;
}

/* Inner icon (smooth movement) */
.chat-toggle .chat-icon {
  transition: transform .3s ease;

}
.chat-toggle:hover .chat-icon {
  transform: rotate(-10deg);
}

/* Badge indicator (notifikasi chat baru) */
.chat-badge {
  position: absolute;
  top: 8px;
  right: 10px;
  background: #ff4757;
  color: #fff;
  font-size: 0.7rem;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.25);
  display: none; /* bisa diaktifkan nanti */
}

/* Pulse animation */
@keyframes pulse {
  0% { box-shadow: #071739; }
  70% { box-shadow: 0 0 0 15px rgba(212, 175, 55, 0); }
  100% { box-shadow: 0 0 0 0 rgba(212, 175, 55, 0); }
}

/* Responsif untuk mobile */
@media (max-width: 768px) {
  .chat-toggle {
    width: 58px;
    height: 58px;
    bottom: 20px;
    right: 20px;
    font-size: 1.4rem;
  }
}
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

@keyframes pop {
  from { transform: scale(.9); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.chatbox-header {
  background: var(--wistara-navy);
  color: white;
  padding: 12px 18px;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.chatbox-header .close-chat {
  background: none;
  border: none;
  color: white;
  font-size: 1.2rem;
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

.chatbox-input {
  display: flex;
  border-top: 1px solid #ddd;
}
.chatbox-input input {
  flex: 1;
  border: none;
  padding: 12px 14px;
  outline: none;
  font-size: 0.9rem;
}
.chatbox-input button {
  background: var(--wistara-navy);
  color: #fff;
  border: none;
  padding: 0 20px;
  font-weight: 600;
  transition: background .2s;
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
  line-height: 1.4;
  font-size: 0.9rem;
  word-break: break-word;
  background: #e9eef5;
  color: #000;
  border-bottom-left-radius: 5px;
  display: inline-block;
  position: relative;
}
.message.user .bubble {
  background: var(--wistara-gold);
  color: #071739;
  border-bottom-right-radius: 5px;
}

/* Inline button area inside bubble */
.bubble .quick-replies {
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
  font-size: 0.85rem;
  cursor: pointer;
  text-align: left;
  transition: background .2s;
}
.quick-btn:hover { background: #0a214f; }

/* Typing indicator */
.typing-indicator {
  display: inline-flex;
  align-items: center;
  height: 20px;
}
.typing-dot {
  height: 7px;
  width: 7px;
  background-color: #999;
  border-radius: 50%;
  margin: 0 3px;
  animation: blink 1.4s infinite both;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes blink {
  0%,80%,100%{opacity:0;} 40%{opacity:1;}
}

@keyframes fadeInUp {
  0% { opacity: 0; transform: translateY(10px); }
  100% { opacity: 1; transform: translateY(0); }
}
.fade-in {
  animation: fadeInUp 0.4s ease both;
}

</style>

<!-- Floating Chat Button -->
<button class="chat-toggle" id="chatToggle" aria-label="Buka Chatbot Wistara">
  <div class="chat-icon">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="28" fill="white">
        <path d="M2 4.75A2.75 2.75 0 0 1 4.75 2h14.5A2.75 2.75 0 0 1 22 4.75v9.5A2.75 2.75 0 0 1 19.25 17H8.414l-3.707 3.707A1 1 0 0 1 3 19.999V17H4.75A2.75 2.75 0 0 1 2 14.25v-9.5Z"/>
    </svg>
  </div>
  <span class="chat-badge">1</span>
</button>

<!-- Chat Box -->
<div class="chatbox" id="chatBox">
  <div class="chatbox-header">
    <span>🤖 WistaraBot</span>
    <button class="close-chat" id="closeChat">&times;</button>
  </div>
  <div class="chatbox-body" id="chatBody"></div>
  <div class="chatbox-input">
    <input type="text" id="chatInput" placeholder="Ketik pesan..." />
    <button id="sendBtn">Kirim</button>
  </div>
</div>

<script>
const API_URL = "{{ url('/api/chatbot') }}";
const chatBox = document.getElementById('chatBox');
const toggleBtn = document.getElementById('chatToggle');
const closeBtn = document.getElementById('closeChat');
const chatBody = document.getElementById('chatBody');
const chatInput = document.getElementById('chatInput');
const sendBtn = document.getElementById('sendBtn');

let chatState = localStorage.getItem('wistara_state') || 'menu';
let chatHistory = JSON.parse(localStorage.getItem('wistara_history') || '[]');

// Init
toggleBtn.onclick = () => {
  chatBox.classList.toggle('active');
  if (chatHistory.length === 0) sendToBot('menu');
};
closeBtn.onclick = () => chatBox.classList.remove('active');
sendBtn.onclick = () => handleSend();
chatInput.addEventListener('keypress', e => e.key === 'Enter' && handleSend());

// Load history
chatHistory.forEach(msg => appendMessage(msg.from, msg.text));
if (chatHistory.length === 0) sendToBot('menu');

function handleSend() {
  const text = chatInput.value.trim();
  if (!text) return;
  appendMessage('user', text);
  saveToHistory('user', text);
  chatInput.value = '';
  sendToBot(text);
}

async function sendToBot(message) {
  appendTyping();
  try {
    const res = await fetch(API_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json','Accept':'application/json'},
      body: JSON.stringify({ message, state: chatState })
    });
    removeTyping();
    const data = await res.json();

    appendMessage('bot', data.reply, data.quick_replies);
    chatState = data.next_state || 'menu';
    localStorage.setItem('wistara_state', chatState);
    saveToHistory('bot', data.reply);

  } catch (err) {
    removeTyping();
    appendMessage('bot', '⚠️ Tidak dapat terhubung ke server.');
  }
}

function appendMessage(sender, text, quickReplies = []) {
  const msg = document.createElement('div');
  msg.className = `message ${sender}`;
  const bubble = document.createElement('div');
  bubble.className = 'bubble';
  bubble.innerHTML = text;

  // ✅ Quick replies inside bubble (with descriptive labels)
  if (sender === 'bot' && quickReplies.length) {
    const qWrap = document.createElement('div');
    qWrap.className = 'quick-replies';
    const labelMap = {
      '1': '1️⃣ Katalog Produk',
      '2': '2️⃣ Cek Stok Produk',
      '3': '3️⃣ Berita Terbaru',
      '4': '4️⃣ Alamat & Jam Buka',
      '0': '0️⃣ Hubungi Admin',
      'menu': '🔙 Kembali ke Menu Utama'
    };
    quickReplies.forEach(rep => {
      const btn = document.createElement('button');
      btn.className = 'quick-btn';
      btn.textContent = labelMap[rep] || rep;
      btn.onclick = () => {
        appendMessage('user', labelMap[rep] || rep);
        saveToHistory('user', rep);
        sendToBot(rep);
      };
      qWrap.appendChild(btn);
    });
    bubble.appendChild(qWrap);
  }

  msg.appendChild(bubble);
  chatBody.appendChild(msg);
  chatBody.scrollTop = chatBody.scrollHeight;
}

function appendTyping() {
  const typing = document.createElement('div');
  typing.className = 'message bot typing';
  typing.innerHTML = `
    <div class="bubble">
      <div class="typing-indicator">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      </div>
    </div>`;
  chatBody.appendChild(typing);
  chatBody.scrollTop = chatBody.scrollHeight;
}
function removeTyping() {
  const t = chatBody.querySelector('.typing');
  if (t) t.remove();
}
function saveToHistory(from, text) {
  chatHistory.push({ from, text });
  if (chatHistory.length > 50) chatHistory.shift();
  localStorage.setItem('wistara_history', JSON.stringify(chatHistory));
}

// 🎵 Suara notifikasi (gunakan file pop.mp3 di public/)
const botSound = new Audio("{{ asset('sounds/pop.mp3') }}");

// 🕓 Kirim ke bot dengan delay & efek realistis
async function sendToBot(message) {
  appendTyping();
  try {
    const res = await fetch(API_URL, {
      method: 'POST',
      headers: {'Content-Type': 'application/json','Accept':'application/json'},
      body: JSON.stringify({ message, state: chatState })
    });

    const data = await res.json();

    // Simulasi delay realistis (1–2 detik)
    const delay = 1000 + Math.random() * 1000;
    setTimeout(() => {
      removeTyping();
      appendMessage('bot', data.reply, data.quick_replies);
      chatState = data.next_state || 'menu';
      localStorage.setItem('wistara_state', chatState);
      saveToHistory('bot', data.reply);
      playNotification();
    }, delay);

  } catch (err) {
    removeTyping();
    appendMessage('bot', '⚠️ Tidak dapat terhubung ke server.');
  }
}

// 🔊 Notifikasi suara + getar
function playNotification() {
  botSound.currentTime = 0;
  botSound.play().catch(() => {}); // aman kalau browser block autoplay
  if (navigator.vibrate) navigator.vibrate(50);
}

// 💬 Tambahkan animasi fade-in tiap bubble
function appendMessage(sender, text, quickReplies = []) {
  const msg = document.createElement('div');
  msg.className = `message ${sender} fade-in`;
  const bubble = document.createElement('div');
  bubble.className = 'bubble';
  bubble.innerHTML = text;

  if (sender === 'bot' && quickReplies.length) {
    const qWrap = document.createElement('div');
    qWrap.className = 'quick-replies';
    const labelMap = {
      '1': '1️⃣ Katalog Produk',
      '2': '2️⃣ Cek Stok Produk',
      '3': '3️⃣ Berita Terbaru',
      '4': '4️⃣ Alamat & Jam Buka',
      '0': '0️⃣ Hubungi Admin',
      'menu': '🔙 Kembali ke Menu Utama'
    };
    quickReplies.forEach(rep => {
      const btn = document.createElement('button');
      btn.className = 'quick-btn';
      btn.textContent = labelMap[rep] || rep;
      btn.onclick = () => {
        appendMessage('user', labelMap[rep] || rep);
        saveToHistory('user', rep);
        sendToBot(rep);
      };
      qWrap.appendChild(btn);
    });
    bubble.appendChild(qWrap);
  }

  msg.appendChild(bubble);
  chatBody.appendChild(msg);
  chatBody.scrollTop = chatBody.scrollHeight;
}
</script>
