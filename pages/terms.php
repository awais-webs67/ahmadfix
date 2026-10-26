<?php
/**
 * Ahmad Fix — Terms of Service
 * Plain-language terms. Review and adjust (warranty length, fees) to match your business.
 */
$updated = '2026-10-26';
?>

<section class="page-header">
    <div class="container">
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="<?php echo url(); ?>"><?php echo __('nav_home'); ?></a>
            <span class="breadcrumb-separator">/</span>
            <span aria-current="page"><?php echo __('footer_terms'); ?></span>
        </nav>
        <h1><?php echo __('footer_terms'); ?></h1>
        <p><?php echo __('last_updated'); ?>: <time datetime="<?php echo $updated; ?>"><?php echo $updated; ?></time></p>
    </div>
</section>

<section class="section">
    <div class="container legal-content service-main-content">
<?php if ($lang === 'ar'): ?>
        <p>باستخدامك لموقع أحمد فيكس أو حجزك لإحدى خدماتنا، فإنك توافق على الشروط التالية.</p>
        <h2>الخدمات</h2>
        <p>نقدم خدمات صيانة وإصلاح الأجهزة المنزلية (المكيفات، الثلاجات، الغسالات، الأفران) في الدمام والخبر والظهران بالمنطقة الشرقية.</p>
        <h2>الحجز والمواعيد</h2>
        <ul>
            <li>يمكن الحجز عبر الهاتف أو واتساب أو نموذج التواصل.</li>
            <li>نسعى للزيارة في نفس اليوم للحجوزات قبل الساعة 2 ظهراً، وقد يختلف ذلك حسب الضغط والموقع.</li>
            <li>يرجى إبلاغنا مسبقاً إذا رغبت في إلغاء الموعد أو تغييره.</li>
        </ul>
        <h2>الأسعار والدفع</h2>
        <ul>
            <li>يشخص الفني العطل ويخبرك بالسعر قبل بدء العمل، ولا نبدأ إلا بموافقتك.</li>
            <li>قد تُضاف تكلفة قطع الغيار حسب الحاجة، ويتم توضيحها مسبقاً.</li>
            <li>يتم الدفع بعد إتمام الخدمة.</li>
        </ul>
        <h2>الضمان</h2>
        <p>تشمل أعمال الإصلاح ضمان خدمة على العمل المنجز وقطع الغيار المركبة. تُوضَّح مدة الضمان عند إتمام الخدمة. لا يشمل الضمان الأعطال الناتجة عن سوء الاستخدام أو تدخل جهة أخرى أو انقطاع الكهرباء.</p>
        <h2>المسؤولية</h2>
        <p>نلتزم بالعمل بعناية واحترافية. لا نتحمل مسؤولية الأعطال القائمة مسبقاً غير المرتبطة بالإصلاح الذي قمنا به.</p>
        <h2>تواصل معنا</h2>
        <p>لأي استفسار: <a href="tel:<?php echo formatPhone(PHONE); ?>" dir="ltr"><?php echo e(PHONE); ?></a> — <a href="mailto:<?php echo e(EMAIL); ?>"><?php echo e(EMAIL); ?></a></p>
<?php else: ?>
        <p>By using the Ahmad Fix website or booking one of our services, you agree to the following terms.</p>
        <h2>Services</h2>
        <p>We provide home appliance repair and maintenance (air conditioners, refrigerators, washing machines and ovens) in Dammam, Al Khobar and Dhahran in the Eastern Province.</p>
        <h2>Bookings and Appointments</h2>
        <ul>
            <li>You can book by phone, WhatsApp or the contact form.</li>
            <li>We aim for same-day visits for bookings made before 2 PM; this can vary with demand and location.</li>
            <li>Please let us know in advance if you need to cancel or change an appointment.</li>
        </ul>
        <h2>Prices and Payment</h2>
        <ul>
            <li>The technician diagnoses the fault and tells you the price before starting. Work only begins with your approval.</li>
            <li>Spare parts may be added where needed and are explained beforehand.</li>
            <li>Payment is made after the service is completed.</li>
        </ul>
        <h2>Warranty</h2>
        <p>Repairs include a service warranty on the work done and the parts installed. The warranty period is confirmed when the job is completed. It does not cover faults caused by misuse, work by another party, or power surges.</p>
        <h2>Liability</h2>
        <p>We work carefully and professionally. We are not responsible for pre-existing faults unrelated to the repair we carried out.</p>
        <h2>Contact Us</h2>
        <p>Questions: <a href="tel:<?php echo formatPhone(PHONE); ?>" dir="ltr"><?php echo e(PHONE); ?></a> — <a href="mailto:<?php echo e(EMAIL); ?>"><?php echo e(EMAIL); ?></a></p>
<?php endif; ?>
    </div>
</section>
