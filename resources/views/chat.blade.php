<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>المحادثة مع {{ $receiver->name }}</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* ===== Background ===== */
        body {
            background: linear-gradient(135deg, #fff5f7, #ffe4ec);
            font-family: 'Poppins', sans-serif;
        }

        /* Wrapper */
        .chat-wrapper {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Card */
        .chat-card {
            width: 100%;
            max-width: 900px;
            height: 90vh;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(216, 27, 96, .15);
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .chat-header {
            background: #D81B60;
            color: white;
            padding: 18px;
            display: flex;
            align-items: center;
        }

        .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: white;
            color: #D81B60;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 10px;
        }

        /* Messages Area */
        #chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 25px;
            background: #fff0f5;
        }

        #chat-box::-webkit-scrollbar {
            width: 6px;
        }

        #chat-box::-webkit-scrollbar-thumb {
            background: #D81B60;
            border-radius: 10px;
        }

        /* Messages */
        .message {
            max-width: 75%;
            padding: 14px 18px;
            border-radius: 18px;
            margin-bottom: 12px;
            word-break: break-word;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .08);
            line-height: 1.5;
        }

        .sent {
            background: #D81B60;
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 4px;
            border-bottom-left-radius: 18px;
            border-top-right-radius: 18px;
            border-top-left-radius: 18px;
        }

        .received {
            background: #f2f2f2;
            color: #111;
            margin-right: auto;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 18px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .message-label {
            font-size: 0.78rem;
            color: #777;
            margin-bottom: 4px;
        }

        .message.sent .message-label {
            text-align: right;
        }

        .message.received .message-label {
            text-align: left;
        }

        /* Footer */
        .chat-footer {
            padding: 15px;
            background: white;
            border-top: 1px solid #eee;
        }

        .chat-input {
            border-radius: 50px;
            padding: 12px 20px;
            border: 1px solid #eee;
        }

        .chat-input:focus {
            border-color: #D81B60;
            box-shadow: 0 0 0 3px rgba(216, 27, 96, .15);
        }

        .send-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: #D81B60;
            color: white;
            transition: .3s;
        }

        .send-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 15px #D81B60;
        }
    </style>
</head>

<body>

    <div class="chat-wrapper">

        <div class="chat-card">

            <!-- HEADER -->
            <div class="chat-header">
                <div class="avatar">
                    {{ mb_substr($receiver->name, 0, 1) }}
                </div>

                <div>
                    <h5 class="mb-0">
                        التواصل مع {{ $receiver->name }}
                    </h5>
                    <small class="text-white-50">
                        أنت: {{ $currentUser->name }} · معرفك: {{ $currentUser->id }} · المحادثة مع:
                        {{ $receiver->name }} (ID {{ $receiver->id }})
                    </small>
                </div>
            </div>

            <!-- CHAT BOX -->
            <div id="chat-box">
                @foreach ($messages as $msg)
                    <div
                        class="d-flex mb-3 {{ $msg->sender_id == session('user_id') ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="message {{ $msg->sender_id == session('user_id') ? 'sent' : 'received' }}">
                            <div class="message-label">
                                {{ $msg->sender_id == session('user_id') ? 'رسالتك' : 'من ' . $msg->sender->name }}
                            </div>
                            {{ $msg->body }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- FOOTER -->
            <div class="chat-footer">

                @if ($canReply)
                    <div class="input-group" dir="ltr">

                        <button id="send-btn" type="button" class="send-btn me-2">
                            <i class="bi bi-send-fill"></i>
                        </button>

                        <input type="text" id="message-input" class="form-control chat-input text-end"
                            placeholder="اكتب رسالتك هنا..." dir="rtl" autocomplete="off">

                    </div>
                @else
                    <div class="text-center p-3">
                        <div class="alert alert-info">
                            <i class="bi bi-lock-fill me-2"></i>
                            <strong>لا يمكنك الرد على هذه المحادثة حالياً</strong>
                            <br>
                            <small class="text-muted">تواصل مع الإدمن لفتح المحادثة: 962772510076</small>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const receiverId = "{{ $receiver->id }}";
        const receiverName = "{{ $receiver->name }}";
        const myId = "{{ session('user_id') }}";
        const canReply = {{ $canReply ? 'true' : 'false' }};

        console.log('Chat loaded:', {
            currentUserId: myId,
            receiverId: receiverId,
            receiverName: receiverName,
            canReply: canReply
        });

        axios.defaults.headers.common['X-CSRF-TOKEN'] =
            document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        window.addEventListener('load', loadMessages);
        setInterval(loadMessages, 4000);

        @if ($canReply)
            document.getElementById('send-btn').addEventListener('click', sendMessage);

            document.getElementById('message-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') sendMessage();
            });
        @endif

        function loadMessages() {
            axios.get("{{ route('chat.messages', ['receiverId' => $receiver->id]) }}")
                .then(res => {
                    console.log('Loaded messages', res.data);
                    renderMessages(res.data.messages);
                })
                .catch(error => {
                    console.error('Failed to load messages', error);
                    if (error.response && error.response.status === 401) {
                        alert('لم يتم تسجيل الدخول أو انتهت الجلسة. الرجاء تسجيل الدخول مرة أخرى.');
                    }
                });
        }

        function sendMessage() {

            if (!canReply) {
                alert('لا يمكنك الرد على هذه المحادثة حالياً - تواصل مع الإدمن لفتح المحادثة: 962772510076');
                return;
            }

            let input = document.getElementById('message-input');
            let message = input.value;

            if (message.trim() == "") return;

            axios.post("{{ route('chat.send') }}", {
                    message: message,
                    receiver_id: receiverId
                })
                .then(() => {
                    appendMessage(message);
                    input.value = "";
                    loadMessages();
                })
                .catch(error => {
                    let messageText = 'فشل إرسال الرسالة.';
                    if (error.response && error.response.data && error.response.data.error) {
                        messageText = error.response.data.error;
                    }
                    alert(messageText);
                });
        }

        function renderMessages(messages) {

            let chatBox = document.getElementById('chat-box');
            chatBox.innerHTML = '';

            if (!canReply) {
                let alertDiv = document.createElement('div');
                alertDiv.className = "alert alert-warning text-center mx-3 mb-3";
                alertDiv.innerHTML = `
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>لا يمكنك الرد على هذه المحادثة حالياً</strong>
                    <br>
                    <small class="text-muted">تواصل مع الإدمن لفتح المحادثة: 962772510076</small>
                `;
                chatBox.appendChild(alertDiv);
            }

            messages.forEach(msg => {
                let type = (msg.sender_id == myId) ? 'sent' : 'received';
                let alignment = (type === 'sent') ? 'justify-content-end' : 'justify-content-start';
                let label = (type === 'sent') ? 'رسالتك' : 'من ' + msg.sender.name;

                let div = document.createElement('div');
                div.className = `d-flex mb-3 ${alignment}`;
                div.innerHTML = `
                    <div class="message ${type}">
                        <div class="message-label">${label}</div>
                        ${msg.body}
                    </div>
                `;

                chatBox.appendChild(div);
            });

            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function appendMessage(text) {
            let chatBox = document.getElementById('chat-box');

            let div = document.createElement('div');
            div.className = "d-flex mb-3 justify-content-end";
            div.innerHTML = `
                <div class="message sent">
                    <div class="message-label">رسالتك</div>
                    ${text}
                </div>
            `;

            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>