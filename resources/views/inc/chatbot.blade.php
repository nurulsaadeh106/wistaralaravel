<!-- ================== WISTARABOT CHAT ================== -->
<style>
:root {
  --wistara-navy: #071739;
  --wistara-gold: #d4af37;
}

/* === Floating Chat Button === */
.chat-toggle {
  position: fixed;
  bottom: 25px;
  right: 25px;
  width: 65px;
  height: 65px;
  border-radius: 50%;
  border: none;
  outline: none;
  background: linear-gradient(135deg, #071739, #1d315d);
  color: #fff;
  font-size: 1.6rem;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  cursor: pointer;
  z-index: 1000;
  transition: all .25s ease;
  animation: pulse 2s infinite;
}
.chat-toggle:hover {
  transform: translateY(-4px) scale(1.05);
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}
@keyframes pulse {
  0% { box-shadow: 0 0 0 0 rgba(212,175,55,0.6); }
  70% { box-shadow: 0 0 0 15px rgba(212,175,55,0); }
  100% { box-shadow: 0 0 0 0 rgba(212,175,55,0); }
}

/* Chatbox */
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
<button class="chat-toggle" id="chatToggle">
  💬
</button>

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
const chatBox = document.getElementById("chatBox");
const chatBody = document.getElementById("chatBody");
const chatInput = document.getElementById("chatInput");

document.getElementById("chatToggle").onclick = () => {
  chatBox.classList.toggle("active");
  if (!localStorage.getItem("wistara_started")) {
    sendToBot("menu");
    localStorage.setItem("wistara_started", "1");
  }
};
document.getElementById("closeChat").onclick = () => chatBox.classList.remove("active");
document.getElementById("sendBtn").onclick = handleSend;
chatInput.addEventListener("keypress", e => e.key === "Enter" && handleSend());

async function handleSend() {
  const text = chatInput.value.trim();
  if (!text) return;
  appendMessage("user", text);
  chatInput.value = "";
  sendToBot(text);
}

function appendMessage(sender, text, quickReplies = []) {
  const msg = document.createElement("div");
  msg.className = `message ${sender}`;
  const bubble = document.createElement("div");
  bubble.className = "bubble";
  bubble.innerHTML = text;
  if (sender === "bot" && quickReplies?.length) {
    const wrap = document.createElement("div");
    wrap.className = "quick-replies";
    quickReplies.forEach(r => {
      const btn = document.createElement("button");
      btn.className = "quick-btn";
      btn.textContent = r;
      btn.onclick = () => {
        appendMessage("user", r);
        sendToBot(r);
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

    // 🔥 Format produk & berita (jika ada HTML dari API)
    appendMessage("bot", data.reply, data.quick_replies || []);

  } catch (err) {
    removeTyping();
    appendMessage("bot", "⚠️ Gagal menghubungi server chatbot.");
  }
}
</script>
