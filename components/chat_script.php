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
    // First try to use actual API
    const response = await fetch("chatbot_api.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message: userMsg })
    });
    
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
    // Fallback to static mock data if API fails
    typingElement.remove();
    
    const mockResponse = {
      response: "For weakness, I recommend My Nutrify Herbal & Ayurveda's Pure Shilajit Resin. It boosts immunity and energy levels.",
      products: [
        {
          name: "Pure Shilajit Resin",
          price: 1499,
          image_url: "images/shilajit.png"
        },
        {
          name: "Ashwagandha Capsules",
          price: 899,
          image_url: "images/ashwagandha.png"
        },
        {
          name: "Triphala Powder",
          price: 599,
          image_url: "images/triphala.png"
        }
      ]
    };

    if (mockResponse.response) {
      displayMessage(mockResponse.response, "bot");
    }
    if (mockResponse.products?.length > 0) {
      addProductCarousel(mockResponse.products);
    }
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
  const carouselDiv = document.createElement("div");
  carouselDiv.className = "product-carousel";
  
  products.forEach(product => {
    const productCard = document.createElement("div");
    productCard.className = "product-card-chat";
    productCard.innerHTML = `
      <img src="${product.image_url}" alt="${product.name}" onerror="this.src='image/placeholder.png'">
      <h6>${product.name}</h6>
      <div class="price">₹${product.price}</div>
    `;
    carouselDiv.appendChild(productCard);
  });
  
  chatBody.appendChild(carouselDiv);
  chatBody.scrollTop = chatBody.scrollHeight;
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
