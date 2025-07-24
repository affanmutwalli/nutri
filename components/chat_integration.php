<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/687cd82326d3e2191999a21f/1j0jqoipl';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

<style>
#chat-icon {
    position: fixed;
    bottom: 50px;
    right: 20px;
    cursor: pointer;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    z-index: 9999; /* Ensures it stays above other elements */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

#chat-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: red;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    font-size: 12px;
    text-align: center;
    line-height: 20px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

#chat-online {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background-color: #32CD32;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

#chat-widget {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    flex-direction: column;
    z-index: 10000;
    overflow: hidden;
}

#chat-header {
    background: linear-gradient(135deg, #ff8c00, #2d5016);
    color: white;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
}

#chat-header .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid white;
}

#close-chat {
    position: absolute;
    right: 15px;
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

#close-chat:hover {
    background: rgba(255, 255, 255, 0.2);
}

#chat-body {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
}

.bot-message {
    background: white;
    padding: 12px 15px;
    border-radius: 15px 15px 15px 5px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    font-size: 14px;
    line-height: 1.4;
}

.user-message {
    background: #ff8c00;
    color: white;
    padding: 12px 15px;
    border-radius: 15px 15px 5px 15px;
    margin-bottom: 15px;
    margin-left: 50px;
    font-size: 14px;
    line-height: 1.4;
}

#suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 15px;
}

.suggestion {
    background: #ff8c00;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 20px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.3s ease;
    text-align: left;
}

.suggestion:hover {
    background: #2d5016;
    transform: translateY(-2px);
}

#chat-input-container {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}

#chat-input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 10px 15px;
    font-size: 14px;
    outline: none;
}

#chat-input:focus {
    border-color: #ff8c00;
}

#send-btn {
    background: #ff8c00;
    color: white;
    border: none;
    border-radius: 20px;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

#send-btn:hover {
    background: #2d5016;
}

#send-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 12px 15px;
    background: white;
    border-radius: 15px 15px 15px 5px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ff8c00;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

.product-carousel {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding: 10px 0;
    margin-top: 10px;
}

.product-card-chat {
    min-width: 150px;
    background: white;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.product-card-chat img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 8px;
}

.product-card-chat h6 {
    font-size: 12px;
    margin: 5px 0;
    color: #333;
}

.product-card-chat .price {
    color: #ff8c00;
    font-weight: bold;
    font-size: 14px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    #chat-widget {
        width: 90%;
        height: 70%;
        bottom: 10px;
        right: 5%;
    }
    
    #chat-icon {
        bottom: 30px;
        right: 15px;
        width: 50px;
        height: 50px;
    }
}
</style>

<!-- Chat Icon -->
<div id="chat-icon">
    <img src="image/doc.png" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%;">
    <span id="chat-notification">1</span>
    <span id="chat-online"></span>
</div>

<!-- Chat Widget -->
<div id="chat-widget">
    <div id="chat-header">
        <img src="image/doc.png" alt="Expert" class="avatar">
        <span>My Nutrify Herbal & Ayurveda</span>
        <button id="close-chat">&times;</button>
    </div>
    
    <div id="chat-body">
        <div class="bot-message">Hello!👋 I'm your Ayurvedic Expert.<b> How can I take care of your health?</b></div>
        <div id="suggestions">
            <button class="suggestion">Immunity kaise badhaye?</button>
            <button class="suggestion">Diabetes ko kaise manage karein?</button>
            <button class="suggestion">PCOS Ko Kaise Control Kare.</button>
            <button class="suggestion">Khoon ki kami kaise thik karein?</button>
            <button class="suggestion">Cholestrol Kaise control karein?</button>
        </div>
    </div>

    <div id="chat-input-container">
        <input type="text" id="chat-input" placeholder="Type your message...">
        <button id="send-btn">Send</button>
    </div>
</div>
