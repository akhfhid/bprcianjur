@extends('layouts.global')

@section('title')
    Asisten Sikap AI
@endsection

@section('content')
    <!-- FontAwesome and Google Fonts for Rich UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Marked.js to parse AI Markdown responses to HTML -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        /* Custom Premium Theme */
        .ai-assistant-body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .chat-container {
            display: flex;
            height: calc(100vh - 200px);
            min-height: 550px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* Sidebar Suggestions styling */
        .chat-sidebar {
            width: 320px;
            background: rgba(248, 250, 252, 0.8);
            border-right: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            flex-direction: column;
            padding: 24px;
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.4;
        }

        .suggestion-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .suggestion-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 0.875rem;
            font-weight: 500;
            color: #334155;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .suggestion-card i {
            color: #4f46e5;
            margin-top: 3px;
        }

        .suggestion-card:hover {
            border-color: #818cf8;
            background: #f5f3ff;
            color: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.05);
        }

        /* Main Chat Window styling */
        .chat-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
        }

        /* Chat Header */
        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        .chat-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        .ai-status-indicator {
            width: 10px;
            height: 10px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #10b981;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }
            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }
            100% {
                transform: scale(0.9);
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        .ai-name {
            font-weight: 700;
            font-size: 1.05rem;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .ai-status-text {
            font-size: 0.8rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-reset-chat {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            color: #64748b;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-reset-chat:hover {
            background-color: #fef2f2;
            border-color: #fca5a5;
            color: #ef4444;
        }

        /* Message area */
        .chat-messages {
            flex-grow: 1;
            padding: 24px;
            overflow-y: auto;
            background-color: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .message-bubble {
            max-width: 75%;
            padding: 14px 18px;
            border-radius: 16px;
            font-size: 0.925rem;
            line-height: 1.5;
            position: relative;
            animation: fadeInMessage 0.3s ease-out;
        }

        @keyframes fadeInMessage {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* User Bubble */
        .message-user {
            background: linear-gradient(135deg, #4f46e5, #5a52e6);
            color: #ffffff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
        }

        /* Assistant Bubble */
        .message-assistant {
            background: #ffffff;
            color: #1e293b;
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        /* Markdown styles inside Assistant bubble */
        .message-assistant p {
            margin-bottom: 8px;
        }
        .message-assistant p:last-child {
            margin-bottom: 0;
        }

        /* Link badges */
        .message-assistant a {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            padding: 2px 8px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            margin: 2px 0;
            text-decoration: none;
            transition: all 0.2s;
        }

        .message-assistant a:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
        }

        /* Input Panel */
        .chat-input-panel {
            padding: 16px 24px;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            background: #ffffff;
        }

        .chat-input-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 6px 12px;
            transition: all 0.2s;
        }

        .chat-input-wrapper:focus-within {
            background: #ffffff;
            border-color: #818cf8;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .chat-input-field {
            flex-grow: 1;
            border: none;
            background: transparent;
            outline: none;
            padding: 8px 4px;
            font-size: 0.95rem;
            color: #1e293b;
            resize: none;
            height: 38px;
            line-height: 22px;
        }

        .btn-send-message {
            background-color: #4f46e5;
            color: #ffffff;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.2);
        }

        .btn-send-message:hover:not(:disabled) {
            background-color: #4338ca;
            transform: scale(1.05);
        }

        .btn-send-message:disabled {
            background-color: #94a3b8;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Typing Indicator */
        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            align-self: flex-start;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            animation: fadeInMessage 0.2s ease-out;
        }

        .typing-dot {
            width: 7px;
            height: 7px;
            background: #64748b;
            border-radius: 50%;
            animation: bounce-dot 1.4s infinite ease-in-out both;
        }

        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes bounce-dot {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1.0); }
        }

        /* Empty State */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            text-align: center;
            padding: 40px 20px;
            color: #64748b;
        }

        .empty-state-icon {
            font-size: 3.5rem;
            color: #c7d2fe;
            margin-bottom: 16px;
            animation: hover-icon 3s ease-in-out infinite;
        }

        @keyframes hover-icon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .empty-state-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .empty-state-desc {
            font-size: 0.875rem;
            max-width: 400px;
            line-height: 1.5;
        }
        
        .chat-time {
            display: block;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: right;
            margin-top: 6px;
        }
        
        .chat-time-assistant {
            display: block;
            font-size: 0.7rem;
            color: #94a3b8;
            text-align: right;
            margin-top: 6px;
        }

        /* Scrollbar styles */
        .chat-messages::-webkit-scrollbar,
        .suggestion-list::-webkit-scrollbar {
            width: 6px;
        }
        .chat-messages::-webkit-scrollbar-track,
        .suggestion-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .chat-messages::-webkit-scrollbar-thumb,
        .suggestion-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .chat-messages::-webkit-scrollbar-thumb:hover,
        .suggestion-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chat-sidebar {
                display: none;
            }
        }
    </style>

    <div class="ai-assistant-body">
        <div class="chat-container">
            <!-- Sidebar with suggestions -->
            <div class="chat-sidebar">
                <div class="sidebar-title">
                    <i class="fa-solid fa-lightbulb"></i>
                    <span>Saran Pertanyaan</span>
                </div>
                <div class="sidebar-subtitle">
                    Pilih pertanyaan cepat di bawah ini untuk memulai pencarian peraturan:
                </div>
                <div class="suggestion-list">
                    <div class="suggestion-card" onclick="sendSuggestion('Bagaimana peraturan tentang Cuti Wajib?')">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>Peraturan Cuti Wajib Pegawai</span>
                    </div>
                    <div class="suggestion-card" onclick="sendSuggestion('Tolong jelaskan SOP Brankas Penyimpanan Uang')">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>SOP Brankas Penyimpanan Uang</span>
                    </div>
                    <div class="suggestion-card" onclick="sendSuggestion('Tampilkan info tentang SOP Deposito')">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>Kebijakan / SOP Deposito</span>
                    </div>
                    <div class="suggestion-card" onclick="sendSuggestion('Apa saja SOP Kredit Modal Kerja?')">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>SOP Kredit Modal Kerja</span>
                    </div>
                    <div class="suggestion-card" onclick="sendSuggestion('Jelaskan sanksi disiplin pegawai')">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>Aturan Sanksi Disiplin Pegawai</span>
                    </div>
                </div>
            </div>

            <!-- Main Chat Window -->
            <div class="chat-main">
                <!-- Header -->
                <div class="chat-header">
                    <div class="chat-header-info">
                        <div class="ai-avatar">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div>
                            <div class="ai-name">Asisten Sikap AI</div>
                            <div class="ai-status-text">
                                <span class="ai-status-indicator"></span>
                                <span>Aktif | Berbasis Peraturan BPR Cianjur</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn-reset-chat" id="btn-reset">
                            <i class="fa-solid fa-trash-can"></i>
                            <span>Bersihkan Obrolan</span>
                        </button>
                    </div>
                </div>

                <!-- Message List -->
                <div class="chat-messages" id="chat-messages-container">
                    @if(empty($history))
                        <div class="empty-state" id="chat-empty-state">
                            <div class="empty-state-icon">
                                <i class="fa-solid fa-comments"></i>
                            </div>
                            <div class="empty-state-title">Halo, saya Asisten Sikap AI!</div>
                            <div class="empty-state-desc">
                                Tanyakan apa saja tentang peraturan internal BPR Cianjur, kebijakan OJK/LPS, atau SOP perusahaan. Saya akan mencari dokumen yang relevan untuk Anda.
                            </div>
                        </div>
                    @else
                        @foreach($history as $chat)
                            <!-- User Message -->
                            <div class="message-bubble message-user">
                                {!! nl2br(e($chat['user'])) !!}
                                <span class="chat-time">{{ isset($chat['timestamp']) ? \Carbon\Carbon::parse($chat['timestamp'])->format('H:i') : '' }}</span>
                            </div>
                            <!-- AI Message -->
                            <div class="message-bubble message-assistant markdown-content">
                                {!! $chat['assistant'] !!}
                                <span class="chat-time-assistant">{{ isset($chat['timestamp']) ? \Carbon\Carbon::parse($chat['timestamp'])->format('H:i') : '' }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Input area -->
                <div class="chat-input-panel">
                    <form id="chat-form" style="margin: 0;">
                        @csrf
                        <div class="chat-input-wrapper">
                            <input type="text" 
                                   class="chat-input-field" 
                                   id="message-input" 
                                   placeholder="Tulis pesan atau pertanyaan Anda di sini..." 
                                   autocomplete="off"
                                   required>
                            <button type="submit" class="btn-send-message" id="btn-send">
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    <script>
        $(document).ready(function() {
            const $chatContainer = $('#chat-messages-container');
            const $chatForm = $('#chat-form');
            const $messageInput = $('#message-input');
            const $btnSend = $('#btn-send');
            const $btnReset = $('#btn-reset');
            const $emptyState = $('#chat-empty-state');

            // Set CSRF token for AJAX setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Parse existing assistant messages using marked.js
            $('.markdown-content').each(function() {
                const rawMarkdown = $(this).html();
                // Replace decoded entities if any, or just parse directly
                const parsedHtml = marked.parse(rawMarkdown.replace(/&gt;/g, '>').replace(/&lt;/g, '<'));
                $(this).html(parsedHtml);
            });

            // Scroll to the bottom of chat history initially
            scrollToBottom();

            // Handle suggestions clicks
            window.sendSuggestion = function(text) {
                $messageInput.val(text);
                $chatForm.submit();
            };

            // Form Submit
            $chatForm.on('submit', function(e) {
                e.preventDefault();
                
                const userText = $messageInput.val().trim();
                if (!userText) return;

                // Disable input and button
                $messageInput.val('');
                $messageInput.prop('disabled', true);
                $btnSend.prop('disabled', true);

                // Hide empty state if present
                if ($emptyState.length) {
                    $emptyState.hide();
                }

                // Append user message bubble
                const now = new Date();
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
                
                const userBubbleHtml = `
                    <div class="message-bubble message-user">
                        ${escapeHtml(userText).replace(/\n/g, '<br>')}
                        <span class="chat-time">${timeString}</span>
                    </div>
                `;
                $chatContainer.append(userBubbleHtml);
                scrollToBottom();

                // Append typing indicator
                const typingIndicatorId = 'typing-' + Date.now();
                const typingHtml = `
                    <div class="typing-indicator" id="${typingIndicatorId}">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                `;
                $chatContainer.append(typingHtml);
                scrollToBottom();

                // AJAX Request to send message to controller
                $.ajax({
                    url: "{{ route('admin.asisten-sikap.chat') }}",
                    type: "POST",
                    data: {
                        message: userText
                    },
                    success: function(response) {
                        // Remove typing indicator
                        $('#' + typingIndicatorId).remove();

                        if (response.success) {
                            // Parse markdown response using marked
                            const aiHtml = marked.parse(response.message);
                            const assistantBubbleHtml = `
                                <div class="message-bubble message-assistant">
                                    ${aiHtml}
                                    <span class="chat-time-assistant">${timeString}</span>
                                </div>
                            `;
                            $chatContainer.append(assistantBubbleHtml);
                        } else {
                            appendErrorBubble('Gagal mendapatkan respon. Silakan coba kembali.');
                        }
                    },
                    error: function(xhr) {
                        $('#' + typingIndicatorId).remove();
                        const errorMsg = xhr.responseJSON && xhr.responseJSON.message 
                            ? xhr.responseJSON.message 
                            : 'Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.';
                        appendErrorBubble(errorMsg);
                    },
                    complete: function() {
                        // Re-enable inputs
                        $messageInput.prop('disabled', false);
                        $btnSend.prop('disabled', false);
                        $messageInput.focus();
                        scrollToBottom();
                    }
                });
            });

            // Reset Chat Action
            $btnReset.on('click', function() {
                if (confirm('Apakah Anda yakin ingin menghapus seluruh riwayat obrolan ini?')) {
                    $.ajax({
                        url: "{{ route('admin.asisten-sikap.reset') }}",
                        type: "POST",
                        success: function(response) {
                            if (response.success) {
                                // Clear message list
                                $chatContainer.html(`
                                    <div class="empty-state" id="chat-empty-state">
                                        <div class="empty-state-icon">
                                            <i class="fa-solid fa-comments"></i>
                                        </div>
                                        <div class="empty-state-title">Halo, saya Asisten Sikap AI!</div>
                                        <div class="empty-state-desc">
                                            Tanyakan apa saja tentang peraturan internal BPR Cianjur, kebijakan OJK/LPS, atau SOP perusahaan. Saya akan mencari dokumen yang relevan untuk Anda.
                                        </div>
                                    </div>
                                `);
                            }
                        },
                        error: function() {
                            alert('Gagal membersihkan obrolan. Silakan coba lagi.');
                        }
                    });
                }
            });

            function scrollToBottom() {
                $chatContainer.animate({ scrollTop: $chatContainer[0].scrollHeight }, 300);
            }

            function escapeHtml(text) {
                return text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            function appendErrorBubble(msg) {
                const errorBubbleHtml = `
                    <div class="message-bubble message-assistant" style="background-color: #fef2f2; border-color: #fca5a5; color: #b91c1c;">
                        <i class="fa-solid fa-triangle-exclamation" style="margin-right: 6px;"></i> ${escapeHtml(msg)}
                    </div>
                `;
                $chatContainer.append(errorBubbleHtml);
            }
        });
    </script>
@endsection
