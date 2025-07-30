<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chatbot UI</title>
  <style>
    body { margin: 0; font-family: Arial, sans-serif; }
    #chat-widget {
      position: fixed;
      bottom: 20px; right: 20px;
      width: 350px; height: 500px;
      display: none;
      flex-direction: column;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.3);
      background: #fff;
      z-index: 9999;
    }
    #chat-header {
      display: flex; align-items: center;
      background: #ec6504; color: #fff; padding: 10px;
      border-radius: 8px 8px 0 0;
    }
    #chat-header img { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
    #close-chat { background: none; border: none; color: #fff; font-size: 28px; cursor: pointer; margin-left: auto; }
    #chat-body {
      flex: 1; overflow-y: auto; padding: 10px; background: #f9f9f9;
      display: flex; flex-direction: column; gap: 12px;
    }
    .bot-message, .user-message {
      margin: 4px 0; padding: 10px; border-radius: 15px;
      max-width: 85%; word-wrap: break-word;
    }
    .bot-message { background: #fff; align-self: flex-start; border: 1px solid #e0e0e0; }
    .user-message { background: #ec6504; color: #fff; align-self: flex-end; }
    #chat-input-container { display: flex; border-top: 1px solid #ccc; padding: 8px; }
    #chat-input { flex: 1; border: none; padding: 10px; font-size: 14px; border-radius: 20px; margin-right: 8px; }
    #chat-input:focus { outline: none; box-shadow: 0 0 0 2px #ec650455; }
    #send-btn {
      border: none; background: #ec6504; color: #fff;
      padding: 0 16px; cursor: pointer; border-radius: 20px;
      transition: background 0.3s ease;
    }
    #send-btn:hover { background: #d35400; }
    #send-btn:disabled { background: #ec650455; cursor: not-allowed; }

    /* Typing Animation */
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-4px); }
    }
    .typing-indicator {
      display: flex; align-items: center;
      padding: 12px; gap: 8px;
    }
    .typing-dot {
      width: 8px; height: 8px;
      background: #888; border-radius: 50%;
      animation: bounce 1.4s infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    /* Optimized Product Carousel */
    .product-carousel {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      gap: 15px;
      padding: 12px 0;
      margin: 10px -10px;
      min-height: 240px;
      -webkit-overflow-scrolling: touch;
      min-width: 100%;
      width: 100vw;
      max-width: 100vw;
    }
    .product-carousel::-webkit-scrollbar {
      height: 5px;
    }
    .product-carousel::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }
    .product-carousel::-webkit-scrollbar-thumb {
      background: #ec6504;
      border-radius: 3px;
    }
    .product-card {
      flex: 0 0 220px;
      scroll-snap-align: start;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 10px;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: visible;
    }
    .product-card img {
      width: 100%;
      height: 140px;
      object-fit: contain;
      margin-bottom: 12px;
      border-radius: 4px;
      object-position: center;
      background: #fff;
      display: block;
    }
    .product-name {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 8px;
      color: #333;
      line-height: 1.3;
    }
    .product-price {
      font-size: 15px;
      color: #ec6504;
      font-weight: 700;
      margin-bottom: 12px;
    }
    .add-btn {
      background: #27ae60;
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 10px 15px;
      font-size: 13px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .add-btn:hover { background: #219a52; }

    @media (max-width: 480px) {
      .product-card {
        flex: 0 0 200px;
      }
      .product-carousel {
        min-width: 100vw;
        width: 100vw;
        max-width: 100vw;
      }
    }
    
    .single-product-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      padding: 20px;
      margin: 15px 0;
      text-align: center;
      max-width: 280px;
      margin-left: auto;
      margin-right: auto;
    }

    .single-product-image {
      width: 100%;
      height: 180px;
      object-fit: contain;
      margin-bottom: 15px;
      border-radius: 8px;
    }

    .single-product-name {
      font-size: 16px;
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 8px;
      line-height: 1.3;
    }

    .single-product-price {
      font-size: 18px;
      color: #ec6504;
      font-weight: 800;
      margin-bottom: 15px;
    }

    .single-product-add-btn {
      background: #27ae60;
      color: #fff;
      padding: 12px 25px;
      border-radius: 25px;
      font-size: 14px;
      font-weight: 600;
      width: 100%;
      transition: all 0.3s ease;
    }

    .single-product-add-btn:hover {
      background: #219a52;
      transform: translateY(-2px);
    }

    /* Updated Product Carousel */
    .product-carousel {
      display: flex;
      overflow-x: auto;
      scroll-snap-type: x mandatory;
      gap: 15px;
      padding: 12px 0;
      margin: 10px -10px;
      min-height: 240px;
      -webkit-overflow-scrolling: touch;
      min-width: 100%;
      width: 100vw;
      max-width: 100vw;
    }
  </style>
</head>
<body>
<div id="chat-icon" style="position: fixed; bottom: 20px; right: 20px; cursor: pointer; width: 60px; height: 60px; border-radius: 50%;">
    <img src="image/doc.png" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%;">

    <!-- Red notification badge -->
    <span id="chat-notification" style="
        position: absolute;
        top: 0;
        right: 0;
        background-color: red;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        font-size: 12px;
        text-align: center;
        line-height: 20px;
        font-weight: bold;
    ">1</span>

    <!-- Green online indicator -->
    <span id="chat-online" style="
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: #0BDA51;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    "></span>
</div>


  <div id="chat-widget">
    <div id="chat-header">
      <img src="image/doc.png" alt="Expert" class="avatar">
      <span>My Nutrify Herbal & Ayurveda</span>
      <button id="close-chat">&times;</button>
    </div>
    
    <div id="chat-body">
      <div class="bot-message">Hello! I'm your Ayurvedic Expert. How can I help?</div>
    </div>

    <div id="chat-input-container">
      <input type="text" id="chat-input" placeholder="Type your message...">
      <button id="send-btn">Send</button>
    </div>
  </div>

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
      } finally {
        chatInput.disabled = false;
        sendBtn.disabled = false;
        chatInput.focus();
      }
    }
    

    function displayMessage(text, sender) {
      const chatBody = document.getElementById("chat-body");
      const messageDiv = document.createElement("div");
      
      messageDiv.className = `${sender}-message`;
      messageDiv.innerHTML = `<p>${text}</p>`;
      chatBody.appendChild(messageDiv);
      
      chatBody.scrollTo({
        top: chatBody.scrollHeight,
        behavior: 'smooth'
      });
    }

    function showTypingIndicator() {
      const chatBody = document.getElementById("chat-body");
      const typingDiv = document.createElement("div");
      
      typingDiv.className = "bot-message typing-indicator";
      typingDiv.innerHTML = `
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      `;
      
      chatBody.appendChild(typingDiv);
      chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: 'smooth' });
      
      return typingDiv;
    }

 function addProductCarousel(products) {
      const chatBody = document.getElementById("chat-body");
      
      if(products.length === 1) {
        // Single product layout
        const container = document.createElement('div');
        const product = products[0];
        
        container.innerHTML = `
          <div class="single-product-card">
            <img src="${product.image_url}" alt="${product.name}" class="single-product-image">
            <div class="single-product-name">${product.name}</div>
            <div class="single-product-price">${product.price}</div>
            <a href="${product.url}" class="single-product-add-btn" role="button">Buy Now</a>
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
            <img src="${product.image_url}" alt="${product.name}">
            <div class="product-name">${product.name}</div>
            <div class="product-price">${product.price}</div>
            <a href="${product.url}" class="single-product-add-btn" role="button">Buy Now</a>
          `;
          carousel.appendChild(card);
        });
        chatBody.appendChild(carousel);
      }
      
      chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: 'smooth' });
    }
  </script>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-XXXXXX');</script>
</body>
</html>