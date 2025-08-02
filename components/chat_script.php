<script>
// Chat Toggle
document.getElementById("chat-icon").addEventListener("click", () => {
  document.getElementById("chat-widget").style.display = "flex";
  document.getElementById("chat-icon").style.display = "none";
});

document.getElementById("close-chat").addEventListener("click", () => {
  document.getElementById("chat-widget").style.display = "none";
  document.getElementById("chat-icon").style.display = "block";
});

// Message Handling
document.getElementById("send-btn").addEventListener("click", sendMessage);
document.getElementById("chat-input").addEventListener("keypress", (e) => {
  if (e.key === "Enter") sendMessage();
});

// Suggestion handling
document.querySelectorAll('.suggestion').forEach(button => {
  button.addEventListener('click', function() {
    const message = this.textContent;
    document.getElementById('chat-input').value = message;
    sendMessage();
  });
});

async function sendMessage() {
  const chatInput = document.getElementById("chat-input");
  const sendBtn = document.getElementById("send-btn");
  const userMsg = chatInput.value.trim();
  
  if (!userMsg) return;

  displayMessage(userMsg, "user");
  chatInput.value = "";
  
  chatInput.disabled = true;
  sendBtn.disabled = true;

  const typingElement = showTypingIndicator();

  try {
    // Create AbortController for aggressive timeout
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout (Groq is super fast)

    // Use lightning-fast Groq API
    const response = await fetch("chatbot_api_groq.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: userMsg }),
      signal: controller.signal
    });

    clearTimeout(timeoutId);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    typingElement.remove();

    // Process API response
    if (data.response) {
      displayMessage(data.response, "bot");
    }
    if (data.products?.length > 0) {
      addProductCarousel(data.products);
    }
  } catch (error) {
    // Handle timeout and other errors
    typingElement.remove();

    let errorMessage = "Sorry, I'm having trouble responding right now. Please try again in a moment.";

    if (error.name === 'AbortError') {
      errorMessage = "Sorry, that took too long to process. Please try asking a shorter question.";
    } else if (error.message.includes('HTTP error')) {
      errorMessage = "Sorry, I'm experiencing technical difficulties. Please try again later.";
    }

    displayMessage(errorMessage, "bot");

    // Add a suggestion to try again
    setTimeout(() => {
      const chatBody = document.getElementById("chat-body");
      const suggestionDiv = document.createElement("div");
      suggestionDiv.className = "bot-message";
      suggestionDiv.innerHTML = `
        <div style="margin-top: 10px;">
          <small>You can try asking about:</small><br>
          <button class="suggestion-btn" onclick="document.getElementById('chat-input').value='What products do you have for immunity?'; sendMessage();" style="background: #28a745; color: white; border: none; padding: 5px 10px; margin: 2px; border-radius: 15px; font-size: 12px; cursor: pointer;">Immunity products</button>
          <button class="suggestion-btn" onclick="document.getElementById('chat-input').value='I need help with digestion'; sendMessage();" style="background: #28a745; color: white; border: none; padding: 5px 10px; margin: 2px; border-radius: 15px; font-size: 12px; cursor: pointer;">Digestive health</button>
        </div>
      `;
      chatBody.appendChild(suggestionDiv);
      chatBody.scrollTop = chatBody.scrollHeight;
    }, 1000);
  } finally {
    chatInput.disabled = false;
    sendBtn.disabled = false;
    chatInput.focus();
  }
}

function displayMessage(message, sender) {
  const chatBody = document.getElementById("chat-body");
  const messageDiv = document.createElement("div");
  messageDiv.className = sender === "user" ? "user-message" : "bot-message";
  messageDiv.textContent = message;
  chatBody.appendChild(messageDiv);
  chatBody.scrollTop = chatBody.scrollHeight;
}

function showTypingIndicator() {
  const chatBody = document.getElementById("chat-body");
  const typingDiv = document.createElement("div");
  typingDiv.className = "typing-indicator";
  typingDiv.innerHTML = `
    <div class="typing-dot"></div>
    <div class="typing-dot"></div>
    <div class="typing-dot"></div>
  `;
  chatBody.appendChild(typingDiv);
  chatBody.scrollTop = chatBody.scrollHeight;
  return typingDiv;
}

function addProductCarousel(products) {
  const chatBody = document.getElementById("chat-body");

  if(products.length === 1) {
    // Single product layout
    const container = document.createElement('div');
    const product = products[0];

    container.innerHTML = `
      <div class="single-product-card" onclick="window.location.href='${product.url}'" style="cursor: pointer;">
        <img src="${product.image_url}" alt="${product.name}" class="single-product-image" onerror="this.src='image/placeholder.png'">
        <div class="single-product-name">${product.name}</div>
        <div class="single-product-price">${product.price}</div>
        <a href="${product.url}" class="single-product-add-btn" role="button" onclick="event.stopPropagation();">Buy Now</a>
      </div>
    `;
    chatBody.appendChild(container);
  } else {
    // Multiple products carousel
    const carousel = document.createElement('div');
    carousel.className = 'product-carousel';

    products.forEach(product => {
      const card = document.createElement("div");
      card.className = "product-card";
      card.innerHTML = `
        <img src="${product.image_url}" alt="${product.name}" onerror="this.src='image/placeholder.png'">
        <div class="product-name">${product.name}</div>
        <div class="product-price">${product.price}</div>
        <a href="${product.url}" class="single-product-add-btn" role="button" onclick="event.stopPropagation();">Buy Now</a>
      `;
      card.onclick = () => window.location.href = product.url;
      card.style.cursor = 'pointer';
      carousel.appendChild(card);
    });
    chatBody.appendChild(carousel);
  }

  chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: 'smooth' });
}

// Auto-open chat if URL parameter is present
document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('open_chat') === '1') {
    setTimeout(() => {
      document.getElementById("chat-widget").style.display = "flex";
      document.getElementById("chat-icon").style.display = "none";
    }, 1000);
  }
});
</script>
