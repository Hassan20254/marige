@extends('layouts.app')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #fff5f7, #ffe4ec);
            height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        /* Wrapper */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Card */
        .login-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 45px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 40px rgba(216, 27, 96, 0.15);
        }

        /* Title */
        .login-title {
            color: #D81B60;
            font-weight: 700;
            margin-bottom: 25px;
        }

        /* Labels */
        label {
            font-weight: 500;
            margin-bottom: 6px;
        }

        /* Inputs */
        .form-control {
            border-radius: 10px;
            padding: 14px;
            border: 1px solid #eee;
            transition: .3s;
        }

        .form-control:focus {
            border-color: #D81B60;
            box-shadow: 0 0 0 3px rgba(216, 27, 96, 0.15);
        }

        /* Button */
        .login-btn {
            background: #D81B60;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            padding: 12px;
            transition: .3s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(216, 27, 96, 0.25);
        }

        .admin-link {
            margin-top: 15px;
            text-align: center;
        }

        .admin-link a {
            color: #D81B60;
            text-decoration: none;
            font-weight: 500;
        }

        .admin-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #d32f2f;
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #ffebee;
            border-radius: 5px;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #ddd;
        }

        .divider span {
            background: white;
            padding: 0 10px;
            position: relative;
            color: #999;
            font-size: 12px;
        }

        
    </style>

    <div class="login-wrapper" dir="rtl">

        <div class="notification-toggle">
            <button class="btn btn-sm btn-outline-primary" onclick="showNotificationArea()">
                <i class="bi bi-bell"></i>
                <span class="badge bg-danger" id="notification-count" style="display: none;">0</span>
            </button>
        </div>

        <div class="login-card text-center">

            <h3 class="login-title">🔑 تسجيل الدخول - المستخدم</h3>

            @if ($errors->any())
                <div class="error-message">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session()->has('info'))
                <div class="error-message" style="background-color: #e3f2fd; color: #1976d2;">
                    ℹ️ {{ session('info') }}
                </div>
            @endif

            
            

            <form action="{{ route('user.login.submit') }}" method="POST">
                @csrf

                <div class="mb-3 text-end">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                </div>

                <div class="mb-4 text-end">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn login-btn w-100">
                    دخول
                </button>

            </form>

            <div class="admin-link">
                هل أنت مسؤول؟ <a href="{{ route('admin.login') }}">تسجيل دخول مسؤول</a>
            </div>

            

        </div>

    </div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
// Notification functions
function showNotificationArea() {
    document.getElementById('notification-area').style.display = 'block';
}

function hideNotificationArea() {
    document.getElementById('notification-area').style.display = 'none';
}

function renderNotifications(notifications) {
    let notificationList = document.getElementById('notification-list');
    notificationList.innerHTML = '';
    
    if (notifications.length === 0) {
        notificationList.innerHTML = '<div class="text-muted p-3 text-center">لا توجد إشعارات</div>';
        return;
    }
    
    notifications.forEach(notification => {
        let notificationItem = document.createElement('div');
        notificationItem.className = `notification-item ${notification.is_read ? '' : 'unread'}`;
        notificationItem.onclick = () => markAsRead(notification.message_id);
        
        notificationItem.innerHTML = `
            <div class="notification-content">
                <div>
                    <strong>${notification.sender_id == '{{ session('user_id') }}' ? 'أنت' : 'مستخدم آخر'}</strong>
                    <div class="notification-time">${notification.created_at}</div>
                </div>
                <div>${notification.message_preview}</div>
            </div>
            ${!notification.is_read ? '<div class="notification-badge">جديد</div>' : ''}
        `;
        
        notificationList.appendChild(notificationItem);
    });
}

function updateNotificationCount(notifications) {
    const unreadCount = notifications.filter(n => !n.is_read).length;
    const badge = document.getElementById('notification-count');
    if (unreadCount > 0) {
        badge.textContent = unreadCount;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

function markAsRead(notificationId) {
    axios.post('/mark-notification-read', {
        notification_id: notificationId
    }).then(() => {
        // Remove unread styling
        let notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach(item => {
            if (item.innerHTML.includes(notificationId)) {
                item.classList.remove('unread');
            }
        });
    }).catch(error => {
        console.error('Failed to mark notification as read', error);
    });
}

function clearNotifications() {
    axios.post('/clear-notifications')
        .then(() => {
            document.getElementById('notification-list').innerHTML = '<div class="text-muted p-3 text-center">تم مسح جميع الإشعارات</div>';
        })
        .catch(error => {
            console.error('Failed to clear notifications', error);
        });
}

// Load notifications on page load
window.addEventListener('load', function() {
    // Load notifications from server
    axios.get('/get-notifications')
        .then(response => {
            renderNotifications(response.data.notifications || []);
            updateNotificationCount(response.data.notifications || []);
        })
        .catch(error => {
            console.error('Failed to load notifications', error);
        });
});
</script>
