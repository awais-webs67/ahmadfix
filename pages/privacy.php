<?php
/**
 * Ahmad Fix — Privacy Policy
 * Plain-language policy. Review it and adjust to how you actually handle data.
 */
$updated = '2026-10-26';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo url(); ?>"><?php echo __('nav_home'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span aria-current="page"><?php echo __('footer_privacy'); ?></span>
        </nav>
        <h1><?php echo __('footer_privacy'); ?></h1>
        <p><?php echo __('last_updated'); ?>: <time datetime="<?php echo $updated; ?>"><?php echo $updated; ?></time></p>
    </div>
</section>

<section class="section">
    <div class="container legal-content service-main-content">
<?php if ($lang === 'ar'): ?>
        <p>يحترم أحمد فيكس خصوصيتك. توضح هذه السياسة المعلومات التي نجمعها عند تواصلك معنا عبر هذا الموقع، وكيف نستخدمها ونحميها، وفقاً لنظام حماية البيانات الشخصية في المملكة العربية السعودية.</p>
        <h2>المعلومات التي نجمعها</h2>
        <ul>
            <li><strong>بيانات نموذج التواصل:</strong> الاسم، رقم الجوال، البريد الإلكتروني (اختياري)، الخدمة المطلوبة، ووصف المشكلة.</li>
            <li><strong>التواصل المباشر:</strong> عند الاتصال بنا أو مراسلتنا عبر واتساب نستلم رقمك والرسائل التي ترسلها.</li>
            <li><strong>بيانات تقنية:</strong> معلومات أساسية مثل نوع المتصفح والصفحات التي تزورها، تُستخدم لتحسين الموقع.</li>
        </ul>
        <h2>كيف نستخدم معلوماتك</h2>
        <ul>
            <li>للرد على طلبك وترتيب زيارة الفني.</li>
            <li>لإرسال تأكيد الطلب أو متابعة الخدمة.</li>
            <li>لتحسين خدماتنا وموقعنا.</li>
        </ul>
        <p>لا نبيع معلوماتك ولا نشاركها مع أي جهة لأغراض تسويقية.</p>
        <h2>ملفات تعريف الارتباط (الكوكيز)</h2>
        <p>نستخدم ملف تعريف ارتباط ضرورياً لحماية نموذج التواصل وتذكر لغتك. قد نستخدم أدوات إحصائية مثل Google Analytics لفهم كيفية استخدام الموقع.</p>
        <h2>حفظ البيانات وحمايتها</h2>
        <p>نحفظ طلبات التواصل بشكل آمن ولا يطّلع عليها إلا فريقنا، ونحذفها عندما لا تعود هناك حاجة إليها.</p>
        <h2>حقوقك</h2>
        <p>يمكنك طلب الاطلاع على بياناتك أو تصحيحها أو حذفها في أي وقت بالتواصل معنا.</p>
        <h2>تواصل معنا</h2>
        <p>الهاتف: <a href="tel:<?php echo formatPhone(PHONE); ?>" dir="ltr"><?php echo e(PHONE); ?></a> — البريد الإلكتروني: <a href="mailto:<?php echo e(EMAIL); ?>"><?php echo e(EMAIL); ?></a></p>
<?php else: ?>
        <p>Ahmad Fix respects your privacy. This policy explains what information we collect when you contact us through this website, and how we use and protect it, in line with Saudi Arabia’s Personal Data Protection Law (PDPL).</p>
        <h2>Information We Collect</h2>
        <ul>
            <li><strong>Contact form details:</strong> your name, phone number, email (optional), the service you need and a description of the problem.</li>
            <li><strong>Direct contact:</strong> when you call or WhatsApp us, we receive your number and the messages you send.</li>
            <li><strong>Technical data:</strong> basic information such as browser type and pages visited, used to improve the website.</li>
        </ul>
        <h2>How We Use Your Information</h2>
        <ul>
            <li>To reply to your request and arrange a technician visit.</li>
            <li>To send booking confirmations or follow up on a repair.</li>
            <li>To improve our services and website.</li>
        </ul>
        <p>We do not sell your information or share it with anyone for marketing.</p>
        <h2>Cookies</h2>
        <p>We use one essential cookie to protect the contact form and remember your language. We may use analytics tools such as Google Analytics to understand how the website is used.</p>
        <h2>Storage and Security</h2>
        <p>Contact requests are stored securely and are only seen by our team. We delete them when they are no longer needed.</p>
        <h2>Your Rights</h2>
        <p>You can ask to see, correct or delete your information at any time by contacting us.</p>
        <h2>Contact Us</h2>
        <p>Phone: <a href="tel:<?php echo formatPhone(PHONE); ?>" dir="ltr"><?php echo e(PHONE); ?></a> — Email: <a href="mailto:<?php echo e(EMAIL); ?>"><?php echo e(EMAIL); ?></a></p>
<?php endif; ?>
    </div>
</section>
