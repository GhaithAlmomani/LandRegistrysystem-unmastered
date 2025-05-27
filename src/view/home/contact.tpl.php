<style>
    .contact {
        padding: 2rem;
    }

    .contact .row {
        display: flex;
        align-items: center;
        gap: 3rem;
        margin-bottom: 4rem;
    }

    .contact .row .image {
        flex: 1;
        text-align: center;
    }

    .contact .row .image img {
        width: 100%;
        max-width: 500px;
    }

    .contact .row form {
        flex: 1;
        background: var(--white);
        padding: 3rem;
        border-radius: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .contact .row form h3 {
        font-size: 2.5rem;
        color: var(--black);
        margin-bottom: 2rem;
        text-align: center;
        text-transform: capitalize;
    }

    .contact .row form .box {
        width: 100%;
        margin-bottom: 1.5rem;
        padding: 1.2rem;
        font-size: 1.6rem;
        color: var(--black);
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
    }

    .contact .row form .box:focus {
        border-color: var(--main-color);
        box-shadow: 0 0 0 2px rgba(0, 86, 15, 0.1);
    }

    .contact .row form textarea {
        resize: vertical;
        min-height: 150px;
    }

    .contact .row form .inline-btn {
        width: 100%;
        padding: 1.2rem;
        font-size: 1.6rem;
        background: var(--main-color);
        color: white;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .contact .row form .inline-btn:hover {
        background: #004408;
        transform: translateY(-2px);
    }

    .contact .box-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(25rem, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .contact .box-container .box {
        background: var(--white);
        padding: 2rem;
        border-radius: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .contact .box-container .box:hover {
        transform: translateY(-5px);
    }

    .contact .box-container .box i {
        font-size: 3rem;
        color: var(--main-color);
        margin-bottom: 1.5rem;
    }

    .contact .box-container .box h3 {
        font-size: 1.8rem;
        color: var(--black);
        margin-bottom: 1rem;
    }

    .contact .box-container .box a {
        display: block;
        font-size: 1.6rem;
        color: var(--light-color);
        margin: 0.5rem 0;
        transition: all 0.3s ease;
    }

    .contact .box-container .box a:hover {
        color: var(--main-color);
    }

    /* Map Styles */
    .map-container {
        margin-top: 3rem;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .map-container iframe {
        width: 100%;
        height: 400px;
        border: none;
    }

    /* Live Chat Styles */
    .chat-widget {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1000;
    }

    .chat-button {
        width: 6rem;
        height: 6rem;
        background: var(--main-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    .chat-button i {
        font-size: 2.5rem;
        color: white;
    }

    .chat-button:hover {
        transform: scale(1.1);
        background: #004408;
    }

    .chat-window {
        position: fixed;
        bottom: 9rem;
        right: 2rem;
        width: 350px;
        height: 500px;
        background: white;
        border-radius: 1rem;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
        display: none;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-window.active {
        display: flex;
    }

    .chat-header {
        background: var(--main-color);
        color: white;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-header h3 {
        font-size: 1.8rem;
        margin: 0;
    }

    .chat-header .close-chat {
        background: none;
        border: none;
        color: white;
        font-size: 2rem;
        cursor: pointer;
    }

    .chat-messages {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        max-width: 80%;
        padding: 1rem;
        border-radius: 1rem;
        font-size: 1.4rem;
        line-height: 1.4;
    }

    .message.user {
        background: #e3f2fd;
        align-self: flex-end;
        border-bottom-right-radius: 0.2rem;
    }

    .message.support {
        background: #f5f5f5;
        align-self: flex-start;
        border-bottom-left-radius: 0.2rem;
    }

    .message.system {
        background: #fff3e0;
        align-self: center;
        text-align: center;
        font-style: italic;
    }

    .chat-input {
        padding: 1.5rem;
        border-top: 1px solid #eee;
        display: flex;
        gap: 1rem;
    }

    .chat-input input {
        flex: 1;
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        font-size: 1.4rem;
    }

    .chat-input button {
        background: var(--main-color);
        color: white;
        border: none;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .chat-input button:hover {
        background: #004408;
    }

    .typing-indicator {
        display: none;
        padding: 1rem;
        color: #666;
        font-style: italic;
        font-size: 1.2rem;
    }

    .typing-indicator.active {
        display: block;
    }

    @media (max-width: 768px) {
        .contact .row {
            flex-direction: column;
        }

        .contact .row .image {
            order: -1;
        }

        .contact .row form {
            width: 100%;
        }

        .chat-window {
            width: 100%;
            height: 100%;
            bottom: 0;
            right: 0;
            border-radius: 0;
        }
    }
</style>

<section class="contact">
    <div class="row">
        <div class="image">
            <img src="Vids/Contact us.svg" alt="">
        </div>

        <form action="" method="post">
            <h3>get in touch</h3>
            <input type="text" placeholder="Your name" name="name" required maxlength="50" class="box">
            <input type="email" placeholder="Your email" name="email" required maxlength="50" class="box">
            <input type="number" placeholder="Your number" name="number" required maxlength="50" class="box">
            <textarea name="msg" class="box" placeholder="Your message" required maxlength="1000" cols="30" rows="10"></textarea>
            <input type="Submit" value="Send message" class="inline-btn" name="submit">
        </form>
    </div>

    <div class="box-container">
        <div class="box">
            <i class="fas fa-phone"></i>
            <h3>Phone number</h3>
            <a href="tel:062006006">062006006</a>
            <a href="tel:062004040">062004040</a>
        </div>

        <div class="box">
            <i class="fas fa-envelope"></i>
            <h3>Email address</h3>
            <a href="mailto:dls@dls.gov.jo">dls@dls.gov.jo</a>
            <a href="mailto:support@dls.gov.jo">support@dls.gov.jo</a>
        </div>

        

        <div class="box">
            <i class="fa-solid fa-clock"></i>
            <h3>Working hours</h3>
            <a href="#">Starting from 8:30 am until 3:30 pm from Sunday to Thursday</a>
        </div>

        <div class="box">
            <i class="fa-brands fa-whatsapp"></i>
            <h3>Whatsapp</h3>
            <a href="https://wa.me/+962780318811">0780318811</a>
        </div>
    </div>

    <!-- Map Integration -->
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3382.123456789012!2d35.92345678901234!3d31.98765432109876!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151ca2e8c0c0c0c1%3A0x1234567890abcdef!2sAbdul%20Monem%20Samarah%20St.%2C%20Amman%2C%20Jordan!5e0!3m2!1sen!2sjo!4v1234567890123!5m2!1sen!2sjo" allowfullscreen="" loading="lazy"></iframe>
    </div>

    <!-- Live Chat Widget -->
    <div class="chat-widget">
        <div class="chat-button" onclick="toggleChat()">
            <i class="fas fa-comments"></i>
        </div>
        <div class="chat-window" id="chatWindow">
            <div class="chat-header">
                <h3>Live Chat Support</h3>
                <button class="close-chat" onclick="toggleChat()">×</button>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="message system">
                    Welcome to DLS Support! How can we help you today?
                </div>
            </div>
            <div class="typing-indicator" id="typingIndicator">
                Support agent is typing...
            </div>
            <div class="chat-input">
                <input type="text" id="messageInput" placeholder="Type your message...">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>
</section>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    let chatHistory = [];
    let isTyping = false;
    let typingTimeout;
    const AI_API_ENDPOINT = 'https://openrouter.ai/api/v1/chat/completions';
    const AI_API_KEY = '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';

    async function getAIResponse(message) {
        try {
            const response = await fetch(AI_API_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${AI_API_KEY}`,
                    'HTTP-Referer': window.location.origin,
                    'X-Title': 'DLS Support Chat'
                },
                body: JSON.stringify({
                    model: "meta-llama/llama-4-maverick",
                    messages: [
                        {
                            role: "system",
                            content: "You are a helpful support agent for the Department of Land and Survey (DLS). You help users with property registration, land management, and related services. Keep responses concise and professional. Focus on providing accurate information about land registration, property transfer, and related government services."
                        },
                        ...chatHistory.map(msg => ({
                            role: msg.type === 'user' ? 'user' : 'assistant',
                            content: msg.message
                        })),
                        {
                            role: 'user',
                            content: message
                        }
                    ],
                    temperature: 0.7,
                    max_tokens: 150
                })
            });

            if (!response.ok) {
                throw new Error('AI API request failed');
            }

            const data = await response.json();
            return data.choices[0].message.content;
        } catch (error) {
            console.error('Error getting AI response:', error);
            return 'I apologize, but I\'m having trouble connecting to our support system. Please try again in a moment or contact us through our other support channels.';
        }
    }

    function toggleChat() {
        const chatWindow = document.getElementById('chatWindow');
        chatWindow.classList.toggle('active');
        if (chatWindow.classList.contains('active')) {
            document.getElementById('messageInput').focus();
        }
    }

    function addMessage(message, type = 'user') {
        const chatMessages = document.getElementById('chatMessages');
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        messageDiv.textContent = message;
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Store message in history
        chatHistory.push({ type, message });
    }

    function showTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        indicator.classList.add('active');
    }

    function hideTypingIndicator() {
        const indicator = document.getElementById('typingIndicator');
        indicator.classList.remove('active');
    }

    async function handleSupportResponse(message) {
        showTypingIndicator();
        
        try {
            // Get response from AI API
            const response = await getAIResponse(message);
            hideTypingIndicator();
            addMessage(response, 'support');
        } catch (error) {
            hideTypingIndicator();
            addMessage('I apologize, but I\'m having trouble connecting to our support system. Please try again in a moment or contact us through our other support channels.', 'system');
        }
    }

    async function sendMessage() {
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        
        if (message) {
            addMessage(message);
            input.value = '';
            
            // Get AI response
            await handleSupportResponse(message);
        }
    }

    // Allow sending message with Enter key
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        const chatWindow = document.getElementById('chatWindow');
        if (window.innerWidth <= 768) {
            chatWindow.style.width = '100%';
            chatWindow.style.height = '100%';
            chatWindow.style.bottom = '0';
            chatWindow.style.right = '0';
            chatWindow.style.borderRadius = '0';
        } else {
            chatWindow.style.width = '350px';
            chatWindow.style.height = '500px';
            chatWindow.style.bottom = '9rem';
            chatWindow.style.right = '2rem';
            chatWindow.style.borderRadius = '1rem';
        }
    });
</script>

