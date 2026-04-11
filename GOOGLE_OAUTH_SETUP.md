# 🔧 تعليمات إعداد Google OAuth

هذا الملف يشرح كيفية إعداد تسجيل الدخول عبر Google في التطبيق.

## الخطوة 1: إنشاء Google Cloud Project

1. انتقل إلى [Google Cloud Console](https://console.cloud.google.com/)
2. سجل الدخول باستخدام حسابك على Google
3. أنشئ مشروع جديد:
    - انقر على "Select a Project" في الأعلى
    - انقر على "NEW PROJECT"
    - أدخل اسم المشروع (مثل: "Marriage App")
    - انقر على "CREATE"

## الخطوة 2: تفعيل Google+ API

1. في لوحة التحكم، انتقل إلى "APIs & Services"
2. انقر على "Enable APIs and Services"
3. ابحث عن "Google+ API"
4. انقر عليها وثم انقر على "ENABLE"

## الخطوة 3: إنشاء OAuth 2.0 Credentials

1. انتقل إلى "APIs & Services" → "Credentials"
2. انقر على "Create Credentials" → "OAuth client ID"
3. إذا كان هذا أول مرة، قد تحتاج إلى إنشاء OAuth consent screen أولاً:
    - انقر على "Configure Consent Screen"
    - اختر "External" ثم انقر "CREATE"
    - ملء النموذج بـ:
        - **App name**: Marriage App
        - **User support email**: your-email@gmail.com
        - **Developer contact**: your-email@gmail.com
    - انقر "SAVE AND CONTINUE" عدة مرات حتى تنتهي

4. بعد إنشاء Consent Screen، عد إلى Credentials وانقر "Create Credentials" → "OAuth client ID"
5. اختر "Web application"
6. أضف "Authorized redirect URIs":
    ```
    http://localhost:8000/auth/google/callback
    ```
    و إذا كان لديك domain للإنتاج:
    ```
    https://yourdomain.com/auth/google/callback
    ```
7. انقر "CREATE"
8. سيظهر لك **Client ID** و **Client Secret** - احفظهما

## الخطوة 4: تحديث ملف .env

ضع القيم التي حصلت عليها في ملف `.env`:

```env
GOOGLE_CLIENT_ID=your_client_id_here
GOOGLE_CLIENT_SECRET=your_client_secret_here
GOOGLE_CLIENT_REDIRECT=http://localhost:8000/auth/google/callback
```

مثال:

```env
GOOGLE_CLIENT_ID=123456789-abcxyz.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-abcxyz123
GOOGLE_CLIENT_REDIRECT=http://localhost:8000/auth/google/callback
```

## الخطوة 5: اختبار التطبيق

1. افتح المتصفح على `http://localhost:8000`
2. جرب:
    - **تسجيل حساب جديد عبر Google**: اضغط على زر "📧 إنشاء حساب عبر Google"
    - **تسجيل دخول عبر Google**: اذهب إلى `/login` واضغط على زر "دخول عبر Google"

## 🎯 الميزات المطبقة

✅ تسجيل دخول عبر Google  
✅ تسجيل حساب جديد عبر Google  
✅ إكمال البيانات المطلوبة (الجنس، العمر، الدولة، المدينة)  
✅ جعل الاسم والجنس والعمر مطلوبة في صفحة التسجيل العادية  
✅ حفظ البريد الأصلي من Google

## 📋 الحقول المطلوبة

عند إنشاء حساب جديد (سواء عادي أو عبر Google):

**مطلوب:**

- 👤 **الاسم** (Name)
- 👥 **الجنس** (Gender - ذكر/أنثى)
- 🎂 **العمر** (Age - من 18 إلى 80 سنة)
- 🌍 **الدولة** (Country)
- 🏙️ **المدينة** (City)
- 📧 **البريد الإلكتروني** (Email)
- 🔐 **كلمة المرور** (Password - في التسجيل العادي فقط)

**اختياري:**

- 📏 الطول
- ⚖️ الوزن
- 🎨 لون البشرة
- 👰 الحالة الاجتماعية
- 🎓 المؤهل التعليمي
- 💼 الوظيفة

## ❓ استكشاف الأخطاء

**مشكلة**: "Redirect URI mismatch"
**الحل**: تأكد من أن الـ URL في Google Console يطابق بدقة الـ URL في `GOOGLE_CLIENT_REDIRECT`

**مشكلة**: "Access Denied"
**الحل**: يمكن أنك تحاول تسجيل بريد إلكتروني مسجل بالفعل. استخدم بريد أخر.

**مشكلة**: "Client ID is required"
**الحل**: تأكد من ملء `GOOGLE_CLIENT_ID` و `GOOGLE_CLIENT_SECRET` في `.env`

## 🚀 للإنتاج

عند نشر التطبيق:

1. استبدل `GOOGLE_CLIENT_REDIRECT` بـ URL الفعلي:

    ```
    GOOGLE_CLIENT_REDIRECT=https://yourdomain.com/auth/google/callback
    ```

2. أضف الـ URL الجديد في Google Cloud Console تحت "Authorized redirect URIs"

3. استخدم بيانات الإنتاج من Google (Client ID و Secret)
