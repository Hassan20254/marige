# 🔧 كيفية إعداد Google OAuth

## الحالة الحالية:

⚠️ **Google Login معطل** - بيانات وهمية قيد الاستخدام للاختبار

---

## 📋 الخطوات السريعة:

### 1️⃣ انتقل إلى Google Cloud Console

👉 https://console.cloud.google.com

### 2️⃣ أنشئ مشروع جديد

```
اضغط على "Select a Project"
→ "NEW PROJECT"
→ أدخل اسم "Marriage App"
→ اضغط "CREATE"
```

### 3️⃣ فعّل Google+ API

```
APIs & Services
→ Enable APIs and Services
→ ابحث عن "Google+ API"
→ اضغط "ENABLE"
```

### 4️⃣ أنشئ OAuth Credentials

```
APIs & Services
→ Credentials
→ Create Credentials
→ OAuth client ID
→ Web application
```

### 5️⃣ ضع Redirect URI

```
أضف في "Authorized redirect URIs":
http://localhost:8000/auth/google/callback
```

### 6️⃣ احفظ البيانات

ستحصل على:

- **Client ID** (مثال: 123456789-xyz.apps.googleusercontent.com)
- **Client Secret** (مثال: GOCSPX-abcxyz123)

### 7️⃣ حديّث ملف .env

ضع البيانات في `g:\project\marriage-project\marige\.env`:

```env
GOOGLE_CLIENT_ID=YOUR_CLIENT_ID_HERE
GOOGLE_CLIENT_SECRET=YOUR_CLIENT_SECRET_HERE
GOOGLE_CLIENT_REDIRECT=http://localhost:8000/auth/google/callback
```

**مثال صحيح:**

```env
GOOGLE_CLIENT_ID=123456789-abcxyz.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abcxyz123
GOOGLE_CLIENT_REDIRECT=http://localhost:8000/auth/google/callback
```

### 8️⃣ امسح الـ Cache

```bash
php artisan config:cache
php artisan cache:clear
```

### 9️⃣ اختبر الآن!

- افتح: http://127.0.0.1:8000/login
- اضغط على زر "دخول عبر Google"
- سيأخذك للتحقق من Google

---

## ✅ إذا عملت بنجاح:

- ✓ ستظهر صفحة Google Login
- ✓ بعد التحقق ستُطلب إكمال البيانات (جنس، عمر، دولة، مدينة)
- ✓ حساب جديد سينشأ تلقائياً

---

## ❓ الأسئلة الشائعة:

**س: ما بيانات الاختبار (Dummy) الحالية؟**
ج: بيانات مؤقتة لا تعمل مع Google الحقيقي. استخدم البريد الإلكتروني وكلمة المرور للدخول حالياً.

**س: هل يمكن تسجيل الدخول بدون Google؟**
ج: ✓ نعم! استخدم:

- البريد الإلكتروني: admin@example.com
- كلمة المرور: admin123

**س: سحبت النموذج الخاطئ من Google؟**
ج: تأكد من اختيار:

- ✓ **Web application** (وليس Desktop، Android، إلخ)
- ✓ **Add Authorized redirect URIs** مع URL صحيح

---

## 🚀 للإنتاج:

عند نشر التطبيق على server حقيقي:

```env
GOOGLE_CLIENT_REDIRECT=https://yourdomain.com/auth/google/callback
```

وأضف الـ URL الجديد في Google Cloud Console.

---

**حاجة مساعدة؟** 📞
راجع الملف الأصلي: `GOOGLE_OAUTH_SETUP.md`
