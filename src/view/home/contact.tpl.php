<div class="contact-page">
    <h1 class="heading">Contact the Department of Land and Survey</h1>
    <p class="contact-lead">Reach us by message, phone, email, or in person. Our team can guide you on property registration, transfers, and general land-survey services.</p>

    <section class="contact-hero" aria-labelledby="contact-form-heading">
        <div class="contact-hero-grid">
            <div class="contact-hero-media">
                <img src="<?= htmlspecialchars(url('Vids/Contact%20us.svg')) ?>" width="400" height="320" alt="Illustration of a person contacting support online">
            </div>
            <form class="contact-form" action="<?= htmlspecialchars(url('contact')) ?>" method="post" id="contact-form">
                <h2 class="h3" id="contact-form-heading">Send a message</h2>
                <label for="contact-name">Full name</label>
                <input type="text" id="contact-name" name="name" placeholder="Your name" required maxlength="50" class="box" autocomplete="name">

                <label for="contact-email">Email</label>
                <input type="email" id="contact-email" name="email" placeholder="name@example.com" required maxlength="80" class="box" autocomplete="email">

                <label for="contact-phone">Phone number</label>
                <input type="tel" id="contact-phone" name="number" placeholder="e.g. 079 000 0000" required maxlength="20" class="box" autocomplete="tel">

                <label for="contact-msg">Message</label>
                <textarea id="contact-msg" name="msg" class="box" placeholder="How can we help you?" required maxlength="1000" rows="5"></textarea>

                <input type="submit" class="btn" name="submit" value="Send message">
            </form>
        </div>
    </section>

    <section class="contact-details" aria-labelledby="contact-channels-heading">
        <h2 class="heading" id="contact-channels-heading">Ways to reach us</h2>
        <div class="contact-info-grid">
            <div class="contact-info-item">
                <div class="icon" aria-hidden="true"><i class="fas fa-phone"></i></div>
                <div class="contact-info-body">
                    <h5>Phone</h5>
                    <div class="contact-info-links">
                        <a href="tel:062006006">062006006</a>
                        <a href="tel:062004040">062004040</a>
                    </div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="icon" aria-hidden="true"><i class="fas fa-envelope"></i></div>
                <div class="contact-info-body">
                    <h5>Email</h5>
                    <div class="contact-info-links">
                        <a href="mailto:dls@dls.gov.jo">dls@dls.gov.jo</a>
                        <a href="mailto:support@dls.gov.jo">support@dls.gov.jo</a>
                    </div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="icon" aria-hidden="true"><i class="fas fa-clock"></i></div>
                <div class="contact-info-body">
                    <h5>Working hours</h5>
                    <p class="contact-info-text">Sunday to Thursday, 8:30 am – 3:30 pm</p>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="icon" aria-hidden="true"><i class="fab fa-whatsapp"></i></div>
                <div class="contact-info-body">
                    <h5>WhatsApp</h5>
                    <div class="contact-info-links">
                        <a href="https://wa.me/962780318811" rel="noopener noreferrer" target="_blank">0780318811</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="contact-map-section" aria-labelledby="contact-map-heading">
        <h2 class="heading" id="contact-map-heading">Location</h2>
        <p class="contact-map-link-wrap">
            <a href="https://maps.app.goo.gl/SzaSUKCCUhmkoEhD8" class="contact-map-external" rel="noopener noreferrer" target="_blank">Open this location in Google Maps</a>
        </p>
        <div class="contact-map-card">
            <iframe
                title="Department of Land and Survey on the map"
                src="https://www.google.com/maps?q=31.9565926%2C35.9290259&amp;hl=en&amp;z=17&amp;output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>
        </div>
    </section>

    <div class="page-actions">
        <a href="home" class="btn btn-outline">Back to home</a>
    </div>
</div>

<div class="contact-chat-root" id="contactChatRoot">
    <button type="button" class="contact-chat-fab" id="contactChatFab" aria-expanded="false" aria-controls="contactChatPanel">
        <i class="fas fa-comments" aria-hidden="true"></i>
        <span class="visually-hidden">Open help chat</span>
    </button>
    <div class="contact-chat-panel" id="contactChatPanel" role="dialog" aria-modal="true" aria-labelledby="contact-chat-title" hidden>
        <div class="contact-chat-header">
            <h3 id="contact-chat-title">Help &amp; information</h3>
            <button type="button" class="contact-chat-close" id="contactChatClose" aria-label="Close chat">&times;</button>
        </div>
        <div class="contact-chat-messages" id="contactChatMessages">
            <div class="contact-chat-msg system">Welcome. Ask a short question about DLS services, or use the form above for official enquiries.</div>
        </div>
        <div class="contact-chat-typing" id="contactChatTyping" aria-live="polite">Preparing a reply…</div>
        <div class="contact-chat-input-row">
            <input type="text" id="contactChatInput" class="contact-chat-input" placeholder="Type your message…" maxlength="500" autocomplete="off">
            <button type="button" class="btn" id="contactChatSend">Send</button>
        </div>
        <p class="contact-chat-footnote">This assistant shares general information only. For binding decisions, use official channels listed on this page.</p>
    </div>
</div>

<script>
(function () {
    const canned = [
        'For property registration, transfers, or title information, use the message form on this page or visit a DLS office during working hours.',
        'You can call us on 062006006 or 062004040, Sunday–Thursday, 8:30 am–3:30 pm.',
        'Written enquiries: dls@dls.gov.jo or support@dls.gov.jo. Response times follow standard government service procedures.',
        'WhatsApp 0780318811 is available for quick questions; complex cases may still require an office visit or formal application.'
    ];

    function replyFor(text) {
        const q = text.toLowerCase();
        if (/hour|open|close|time|when/.test(q)) {
            return 'We are open Sunday through Thursday, 8:30 am to 3:30 pm.';
        }
        if (/phone|call|number|tel/.test(q)) {
            return 'Main lines: 062006006 and 062004040.';
        }
        if (/email|mail|@/.test(q)) {
            return 'Email dls@dls.gov.jo or support@dls.gov.jo for official correspondence.';
        }
        if (/whatsapp|wa\b|chat/.test(q)) {
            return 'WhatsApp: 0780318811 — https://wa.me/962780318811';
        }
        if (/register|registration|title|deed|land|property|transfer|survey/.test(q)) {
            return canned[0];
        }
        return canned[Math.floor(Math.random() * canned.length)];
    }

    const root = document.getElementById('contactChatRoot');
    const fab = document.getElementById('contactChatFab');
    const panel = document.getElementById('contactChatPanel');
    const closeBtn = document.getElementById('contactChatClose');
    const messagesEl = document.getElementById('contactChatMessages');
    const typingEl = document.getElementById('contactChatTyping');
    const input = document.getElementById('contactChatInput');
    const sendBtn = document.getElementById('contactChatSend');

    if (!root || !fab || !panel || !messagesEl || !typingEl || !input || !sendBtn) return;

    function setOpen(open) {
        panel.hidden = !open;
        panel.classList.toggle('is-open', open);
        fab.setAttribute('aria-expanded', open ? 'true' : 'false');
        root.classList.toggle('is-panel-open', open);
        if (open) {
            input.focus();
        }
    }

    function addMsg(text, role) {
        const div = document.createElement('div');
        div.className = 'contact-chat-msg ' + role;
        div.textContent = text;
        messagesEl.appendChild(div);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function showTyping(show) {
        typingEl.classList.toggle('is-active', show);
    }

    async function sendUserMessage() {
        const text = input.value.trim();
        if (!text) return;
        addMsg(text, 'user');
        input.value = '';
        showTyping(true);
        await new Promise(function (r) { setTimeout(r, 450 + Math.random() * 400); });
        showTyping(false);
        addMsg(replyFor(text), 'assistant');
    }

    fab.addEventListener('click', function () {
        setOpen(panel.hidden);
    });
    closeBtn.addEventListener('click', function () {
        setOpen(false);
    });
    sendBtn.addEventListener('click', sendUserMessage);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendUserMessage();
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !panel.hidden) setOpen(false);
    });
})();
</script>
