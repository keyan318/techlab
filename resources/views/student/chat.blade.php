<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>TechLab · Chat with Astro</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Fonts: match landing page (Space Grotesk + Inter + Space Mono) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

    {{-- Tailwind (Play CDN) + TechLab landing-page tokens --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Alpine.js for interactive components --}}
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {
            --void: #06061a;
            --cosmic-1: #120a33;
            --cosmic-2: #1e1259;
            --nebula: #2d1b69;
            --blue: #73b6ff;
            --violet: #9b6bff;
            --cyan: #5be1ff;
            --text: #eaeeff;
            --muted: #98a2d4;
            --glass: rgba(123, 142, 220, 0.07);
            --glass-border: rgba(150, 170, 255, 0.18);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            color: var(--text); background: var(--void);
            overflow-x: hidden; -webkit-font-smoothing: antialiased; line-height: 1.6;
        }
        a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>
    @include('student.chat.partials.chat-panel')

    <script>
        document.addEventListener('alpine:init', () => {
            // Shared conversation ID store for Studio actions (Quiz, PPT, etc.)
            Alpine.store('conversation', {
                currentId: null,

                set(id) {
                    this.currentId = id;
                },

                clear() {
                    this.currentId = null;
                },
            });

            // Quiz store
            Alpine.store('quiz', {
                isOpen: false,
                // configuring | generating | answering | results | error
                state: 'idle',
                quiz: null,               // normalized { title, description, topic, question_count, questions }
                error: '',
                errorKind: '',
                conversationId: null,     // synced from astroChat
                // answering state
                current: 0,
                answers: {},              // { qId: 'A'|'B'|'C'|'D' }
                revealed: false,          // whether current question's correctness has been revealed (for review flow, not enforced)
                generating: false,

                open() {
                    if (this.isOpen) return;
                    // sync conversationId from astroChat if not yet set
                    const ctrl = document.querySelector('[x-data="astroChat()"]');
                    // fallback: try to read from the astroChat component via Alpine
                    try {
                        const el = document.querySelector('[x-data="astroChat()"]');
                        if (el && el._x_dataStack) {
                            const d = el._x_dataStack[0];
                            if (d && d.conversationId) this.conversationId = d.conversationId;
                        }
                    } catch(e) {}
                    this.isOpen = true;
                    if (!this.quiz) this.state = 'configuring';
                    else if (Object.keys(this.answers).length > 0 && this.current >= this.total) this.state = 'results';
                    else if (this.quiz) this.state = 'answering';
                    else this.state = 'configuring';
                    this.error = '';
                    this.errorKind = '';
                },

                close() {
                    this.isOpen = false;
                },

                reset() {
                    this.quiz = null;
                    this.current = 0;
                    this.answers = {};
                    this.revealed = false;
                    this.error = '';
                    this.errorKind = '';
                    this.state = 'configuring';
                },

                get total() { return this.quiz && Array.isArray(this.quiz.questions) ? this.quiz.questions.length : 0; },
                get score() {
                    if (!this.quiz || !Array.isArray(this.quiz.questions)) return 0;
                    let s = 0;
                    for (const q of this.quiz.questions) {
                        const a = this.answers[q.id];
                        if (a && a === q.correct_key) s += (q.points || 1);
                    }
                    return s;
                },
                get maxScore() {
                    if (!this.quiz || !Array.isArray(this.quiz.questions)) return 0;
                    return this.quiz.questions.reduce((n,q)=> n + (q.points||1), 0);
                },
                get answeredCount() { return Object.keys(this.answers).length; },
                get canGenerate() { return !!this.conversationId; },

                selectAnswer(key) {
                    const q = this.quiz && this.quiz.questions ? this.quiz.questions[this.current] : null;
                    if (!q) return;
                    this.answers[q.id] = key;
                },

                next() {
                    if (!this.quiz) return;
                    if (this.current < this.total - 1) {
                        this.current++;
                        this.revealed = false;
                    } else {
                        this.state = 'results';
                    }
                },

                prev() {
                    if (this.current > 0) { this.current--; this.revealed = false; }
                },

                retry() {
                    this.reset();
                    this.generate();
                },

                async generate() {
                    if (!this.conversationId) {
                        this.error = 'Chat with Astro first — the quiz is built from your current conversation.';
                        this.errorKind = 'insufficient_content';
                        this.state = 'error';
                        return;
                    }
                    this.generating = true;
                    this.state = 'generating';
                    this.error = '';
                    this.errorKind = '';
                    this.quiz = null;
                    this.current = 0;
                    this.answers = {};
                    this.revealed = false;
                    const csrf = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
                    try {
                        const res = await fetch('/chat/quiz', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                            body: JSON.stringify({ conversation_id: this.conversationId }),
                        });
                        const j = await res.json().catch(()=>({}));
                        this.generating = false;
                        if (!res.ok || !j.ok) {
                            this.error = (j && j.error) ? j.error : "Astro couldn't build your quiz just now. Please try again.";
                            this.errorKind = (j && j.kind) ? j.kind : 'generation';
                            this.state = 'error';
                            return;
                        }
                        this.quiz = j.quiz;
                        this.current = 0;
                        this.answers = {};
                        this.state = 'answering';
                    } catch(e) {
                        this.generating = false;
                        this.error = "Astro couldn't connect right now. Please try again in a moment.";
                        this.errorKind = 'network';
                        this.state = 'error';
                    }
                },
            });

            // Infographic store (simplified version for main chat)
            Alpine.store('infographic', {
                isOpen: false,
                state: 'idle',            // idle | configuring | generating | ready | error
                error: '',
                errorKind: '',

                open() {
                    if (this.isOpen) return;
                    this.isOpen = true;
                    this.state = 'configuring';
                    this.error = '';
                    this.errorKind = '';
                },

                close() {
                    this.isOpen = false;
                    this.state = 'idle';
                    this.error = '';
                    this.errorKind = '';
                },

                async generate() {
                    // In a real implementation, this would connect to the backend
                    // For now, we'll just show a placeholder
                    this.state = 'generating';
                    setTimeout(() => {
                        this.state = 'ready';
                    }, 2000);
                },

                retry() { this.generate(); },
            });

            Alpine.data('astroChat', () => ({
                // State
                messages: [],
                input: '',
                sending: false,
                sourceCount: 0,
                bannerDismissed: false,
                studioOpen: false,
                conversationId: null,

                // Lifecycle
                init() {
                    // Load existing conversations or start fresh
                    this.loadConversations();
                },

                // Load conversations from server
                async loadConversations() {
                    try {
                        const response = await fetch('/chat/conversations');
                        const data = await response.json();
                        if (data.conversations && data.conversations.length > 0) {
                            // Load the most recent conversation
                            await this.loadConversation(data.conversations[0].id);
                        }
                    } catch (error) {
                        console.error('Failed to load conversations:', error);
                    }
                },

                // Load a specific conversation
                async loadConversation(conversationId) {
                    try {
                        const response = await fetch(`/chat/conversations/${conversationId}`);
                        const data = await response.json();
                        if (data.conversation && data.messages) {
                            this.messages = data.messages.map(msg => ({
                                ...msg,
                                html: this.convertToHtml(msg.content) // Simple conversion for now
                            }));
                            // Set conversation ID in store
                            this.conversationId = conversationId;
                            const convStore = this.$store.conversation;
                            if (convStore) convStore.set(conversationId);
                            const quizStore = this.$store.quiz;
                            if (quizStore) quizStore.conversationId = conversationId;
                        }
                    } catch (error) {
                        console.error('Failed to load conversation:', error);
                    }
                },

                // Send a message
                async send() {
                    if (!this.input.trim() || this.sending) return;

                    const message = this.input.trim();
                    this.input = '';
                    this.sending = true;

                    // Add user message to chat
                    const userMessage = {
                        id: Date.now(),
                        role: 'user',
                        content: message,
                        created_at: new Date().toISOString(),
                        html: this.convertToHtml(message)
                    };
                    this.messages.push(userMessage);
                    this.scrollToBottom();

                    try {
                        // Send message to backend
                        const response = await fetch('/chat/message', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                message: message
                            })
                        });

                        const data = await response.json();

                        // Add placeholder for Astro's response
                        const astroMessage = {
                            id: Date.now() + 1,
                            role: 'assistant',
                            content: '',
                            html: '',
                            pending: true
                        };
                        this.messages.push(astroMessage);
                        this.scrollToBottom();

                        // Simulate streaming response (in reality, this would come from an endpoint)
                        // For now, we'll just add a simple response after a delay
                        setTimeout(async () => {
                            try {
                                const astroResponse = await this.getAstroResponse(message);
                                astroMessage.content = astroResponse;
                                astroMessage.html = this.convertToHtml(astroResponse);
                                astroMessage.pending = false;
                                // Update conversation stores with new ID if provided
                                if (data.conversation_id) {
                                    this.conversationId = data.conversation_id;
                                    const convStore = this.$store.conversation;
                                    if (convStore) convStore.set(data.conversation_id);
                                    const quizStore = this.$store.quiz;
                                    if (quizStore) quizStore.conversationId = data.conversation_id;
                                }
                                this.scrollToBottom();
                            } catch (error) {
                                console.error('Failed to get Astro response:', error);
                                astroMessage.content = "I'm having trouble connecting right now. Please try again.";
                                astroMessage.html = this.convertToHtml(astroMessage.content);
                                astroMessage.pending = false;
                            }
                        }, 500);

                    } catch (error) {
                        console.error('Failed to send message:', error);
                        // Remove the user message if send failed
                        this.messages.pop();
                    } finally {
                        this.sending = false;
                    }
                },

                // Get response from Astro (placeholder - would connect to actual AI service)
                async getAstroResponse(userMessage) {
                    // This is a placeholder response
                    // In a real implementation, this would connect to your AI service (NVIDIA NIM via Astro)
                    return `I received your message: "${userMessage}". This is a placeholder response while we work on connecting to the actual AI service.`;
                },

                // Convert plain text to HTML (simple implementation)
                convertToHtml(text) {
                    // Simple markdown-like conversion for demo purposes
                    // In reality, you might use a library like marked.js
                    return text
                        .replace(/\n/g, '<br>')
                        .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
                        .replace(/`([^`]+)`/g, '<code>$1</code>');
                },

                // Scroll to bottom of chat
                scrollToBottom() {
                    const chatScroll = document.getElementById('chat-scroll');
                    if (chatScroll) {
                        chatScroll.scrollTop = chatScroll.scrollHeight;
                    }
                },

                // Start a new chat
                newChat() {
                    this.messages = [];
                    this.input = '';
                    this.bannerDismissed = false;
                    this.conversationId = null;
                    // Clear stores
                    const convStore = this.$store.conversation;
                    if (convStore) convStore.clear();
                    const quizStore = this.$store.quiz;
                    if (quizStore) {
                        quizStore.conversationId = null;
                        quizStore.reset();
                    }
                    const infoStore = this.$store.infographic;
                    if (infoStore) infoStore.close();
                },

                // Toggle sources panel
                toggleSources() {
                    // Implementation would toggle a sources sidebar
                    console.log('Toggle sources');
                },

                // Toggle studio panel
                toggleStudio() {
                    this.studioOpen = !this.studioOpen;
                },

                // Send a suggestion chip message
                sendSuggestion(suggestion) {
                    this.input = suggestion;
                    this.send();
                },

                // Copy text to clipboard
                async copy(text) {
                    try {
                        await navigator.clipboard.writeText(text);
                        return true;
                    } catch (err) {
                        console.error('Failed to copy:', err);
                        return false;
                    }
                }
            }));
        });
    </script>
</body>
</html>