<?php
/**
 * Ahmad Fix — Service Area (City) Pages
 *
 * Every service is available in every city. Each city gets its own page so
 * people searching "appliance repair Khobar" / "صيانة أجهزة الخبر" land on a
 * page written for their city.
 *
 * Edit text or district lists here — the pages, sitemap and links update automatically.
 * {phone} is replaced with your phone number.
 */

if (!defined('FIXORA_APP')) {
    die('Direct access not allowed.');
}

return [
    'dammam' => [
        'match_en'  => 'Dammam', // must match the name in the service_areas table (homepage cards)
        'slug_en'   => 'appliance-repair-dammam',
        'slug_ar'   => 'صيانة-اجهزة-منزلية-الدمام',
        'name_en'   => 'Dammam',
        'name_ar'   => 'الدمام',
        'lat'       => 26.4207,
        'lng'       => 50.0888,
        'title_en'  => 'Appliance Repair in Dammam – AC, Fridge, Washer & Oven | Ahmad Fix',
        'title_ar'  => 'صيانة أجهزة منزلية بالدمام – مكيفات، ثلاجات، غسالات، أفران | أحمد فيكس',
        'desc_en'   => 'Same-day home appliance repair in Dammam. AC, refrigerator, washing machine & oven repair in every district. Certified technicians, genuine parts, warranty. Call {phone}',
        'desc_ar'   => 'صيانة أجهزة منزلية في الدمام بنفس اليوم: مكيفات، ثلاجات، غسالات وأفران في جميع الأحياء. فنيون معتمدون، قطع أصلية، وضمان. اتصل {phone}',
        'h1_en'     => 'Home Appliance Repair in Dammam',
        'h1_ar'     => 'صيانة الأجهزة المنزلية في الدمام',
        'lead_en'   => 'AC, refrigerator, washing machine and oven repair at your door — anywhere in Dammam, usually the same day.',
        'lead_ar'   => 'صيانة مكيفات، ثلاجات، غسالات وأفران عند باب منزلك — في أي حي بالدمام، وغالباً في نفس اليوم.',
        'intro_en'  => [
            'Dammam is our home base, so our technicians are never far away. Whether you live in a villa in Al Faisaliyah, an apartment in Ash Shati or a house in Al Mazruiyah, Ahmad Fix sends a certified technician with the tools and genuine spare parts to fix your appliance on the first visit.',
            'Summer in Dammam regularly passes 45°C, which puts heavy load on air conditioners and refrigerators. We handle everything from AC gas refilling and deep cleaning to fridge compressor repair, washing machine drainage problems and gas oven ignition faults — for all major brands.',
        ],
        'intro_ar'  => [
            'الدمام هي مقرنا الرئيسي، لذلك فنيونا دائماً قريبون منك. سواء كنت تسكن في فيلا بالفيصلية أو شقة في الشاطئ أو منزل في المزروعية، يرسل لك أحمد فيكس فنياً معتمداً مع الأدوات وقطع الغيار الأصلية لإصلاح جهازك من الزيارة الأولى.',
            'صيف الدمام يتجاوز 45 درجة مئوية، مما يضع ضغطاً كبيراً على المكيفات والثلاجات. نتعامل مع كل شيء من تعبئة فريون المكيفات والتنظيف العميق إلى إصلاح كمبروسر الثلاجة ومشاكل صرف الغسالة وأعطال إشعال أفران الغاز — لجميع الماركات الكبرى.',
        ],
        'districts_en' => ['Al Faisaliyah', 'Ash Shati', 'Al Mazruiyah', 'Al Nakheel', 'Al Muhammadiyah', 'Al Jalawiyah', 'Al Adamah', 'Al Badiyah', 'Al Rayyan', 'Al Noor', 'Uhud', 'Al Manar', 'Al Shulah', 'Taybah', 'Al Hamra', 'Al Zuhur', 'Al Rawdah'],
        'districts_ar' => ['الفيصلية', 'الشاطئ', 'المزروعية', 'النخيل', 'المحمدية', 'الجلوية', 'العدامة', 'البادية', 'الريان', 'النور', 'أحد', 'المنار', 'الشعلة', 'طيبة', 'الحمراء', 'الزهور', 'الروضة'],
        'faq_en' => [
            ['q' => 'Do you cover all districts of Dammam?', 'a' => 'Yes. We serve every district in Dammam, including Al Faisaliyah, Ash Shati, Al Mazruiyah, Al Nakheel, Al Muhammadiyah and Al Jalawiyah, for homes, villas, apartments and offices.'],
            ['q' => 'How fast can a technician reach me in Dammam?', 'a' => 'Most Dammam bookings made before 2 PM get a technician the same day. For urgent AC breakdowns in summer we prioritise the fastest available slot.'],
            ['q' => 'Which appliances do you repair in Dammam?', 'a' => 'We repair split and central air conditioners, refrigerators and freezers, automatic and front-load washing machines, and gas and electric ovens and stoves — all brands.'],
        ],
        'faq_ar' => [
            ['q' => 'هل تغطون جميع أحياء الدمام؟', 'a' => 'نعم. نخدم جميع أحياء الدمام بما فيها الفيصلية، الشاطئ، المزروعية، النخيل، المحمدية والجلوية، للمنازل والفلل والشقق والمكاتب.'],
            ['q' => 'كم يستغرق وصول الفني إليّ في الدمام؟', 'a' => 'معظم الحجوزات في الدمام قبل الساعة 2 ظهراً يصلها فني في نفس اليوم. وفي أعطال المكيفات العاجلة صيفاً نعطي الأولوية لأقرب موعد متاح.'],
            ['q' => 'ما الأجهزة التي تصلحونها في الدمام؟', 'a' => 'نصلح المكيفات السبليت والمركزية، الثلاجات والفريزرات، الغسالات الأوتوماتيك والأمامية، وأفران الغاز والكهرباء والبوتاجاز — لجميع الماركات.'],
        ],
    ],

    'khobar' => [
        'match_en'  => 'Al Khobar',
        'slug_en'   => 'appliance-repair-khobar',
        'slug_ar'   => 'صيانة-اجهزة-منزلية-الخبر',
        'name_en'   => 'Al Khobar',
        'name_ar'   => 'الخبر',
        'lat'       => 26.2794,
        'lng'       => 50.2083,
        'title_en'  => 'Appliance Repair in Al Khobar – AC, Fridge, Washer & Oven | Ahmad Fix',
        'title_ar'  => 'صيانة أجهزة منزلية بالخبر – مكيفات، ثلاجات، غسالات، أفران | أحمد فيكس',
        'desc_en'   => 'Fast home appliance repair in Al Khobar. AC, fridge, washing machine & oven repair in Al Thuqbah, Al Aqrabiyah, Al Rakah & all districts. Same-day service. Call {phone}',
        'desc_ar'   => 'صيانة أجهزة منزلية سريعة في الخبر: مكيفات، ثلاجات، غسالات وأفران في الثقبة، العقربية، الراكة وجميع الأحياء. خدمة نفس اليوم. اتصل {phone}',
        'h1_en'     => 'Home Appliance Repair in Al Khobar',
        'h1_ar'     => 'صيانة الأجهزة المنزلية في الخبر',
        'lead_en'   => 'Certified technicians for AC, fridge, washing machine and oven repair across Al Khobar — homes, villas, compounds and offices.',
        'lead_ar'   => 'فنيون معتمدون لصيانة المكيفات والثلاجات والغسالات والأفران في جميع أنحاء الخبر — منازل، فلل، مجمعات سكنية ومكاتب.',
        'intro_en'  => [
            'From the Corniche to Al Thuqbah and Al Rakah, Ahmad Fix covers every part of Al Khobar. Our technicians arrive on time, explain the problem clearly, and give you the price before any work starts — no surprises.',
            'Coastal humidity in Al Khobar is hard on air conditioners and refrigerators: it causes rust, gas leaks and blocked drain lines. We clean, refill and repair AC units, fix fridges that stop cooling, sort out washing machines that leak or won’t spin, and repair gas and electric ovens safely.',
        ],
        'intro_ar'  => [
            'من الكورنيش إلى الثقبة والراكة، يغطي أحمد فيكس كل أنحاء الخبر. يصل فنيونا في الموعد، ويشرحون المشكلة بوضوح، ويعطونك السعر قبل بدء أي عمل — بدون مفاجآت.',
            'رطوبة الساحل في الخبر تؤثر على المكيفات والثلاجات: تسبب الصدأ وتسريب الغاز وانسداد خطوط التصريف. نقوم بتنظيف وتعبئة وإصلاح المكيفات، وإصلاح الثلاجات التي لا تبرد، وحل مشاكل الغسالات التي تسرب أو لا تدور، وإصلاح أفران الغاز والكهرباء بأمان.',
        ],
        'districts_en' => ['Al Thuqbah', 'Al Aqrabiyah', 'Al Khobar Al Shamaliyah', 'Al Khobar Al Janubiyah', 'Al Rakah', 'Al Ulaya', 'Al Yarmouk', 'Al Aziziyah', 'Al Hizam Al Akhdar', 'Al Bandariyah', 'Al Corniche', 'Al Jisr', 'Al Hamra', 'Madinat Al Ummal', 'Al Andalus'],
        'districts_ar' => ['الثقبة', 'العقربية', 'الخبر الشمالية', 'الخبر الجنوبية', 'الراكة', 'العليا', 'اليرموك', 'العزيزية', 'الحزام الأخضر', 'البندرية', 'الكورنيش', 'الجسر', 'الحمراء', 'مدينة العمال', 'الأندلس'],
        'faq_en' => [
            ['q' => 'Do you serve compounds and villas in Al Khobar?', 'a' => 'Yes. We regularly work in residential compounds, villas and apartment buildings across Al Khobar, including Al Rakah, Al Aziziyah and Al Thuqbah.'],
            ['q' => 'Can I get same-day appliance repair in Al Khobar?', 'a' => 'Yes. Call or WhatsApp us before 2 PM and we will usually send a technician to your Al Khobar address the same day.'],
            ['q' => 'Do you offer AC maintenance contracts in Al Khobar?', 'a' => 'Yes. Our annual maintenance contracts include scheduled AC cleaning and check-ups, which is especially useful with Al Khobar’s coastal humidity.'],
        ],
        'faq_ar' => [
            ['q' => 'هل تخدمون المجمعات السكنية والفلل في الخبر؟', 'a' => 'نعم. نعمل باستمرار في المجمعات السكنية والفلل والعمائر في جميع أنحاء الخبر، بما فيها الراكة والعزيزية والثقبة.'],
            ['q' => 'هل يمكنني الحصول على صيانة في نفس اليوم في الخبر؟', 'a' => 'نعم. اتصل بنا أو راسلنا واتساب قبل الساعة 2 ظهراً وغالباً نرسل فنياً إلى عنوانك في الخبر في نفس اليوم.'],
            ['q' => 'هل تقدمون عقود صيانة مكيفات في الخبر؟', 'a' => 'نعم. عقود الصيانة السنوية تشمل تنظيف وفحص المكيفات بشكل دوري، وهذا مهم جداً مع رطوبة الخبر الساحلية.'],
        ],
    ],

    'dhahran' => [
        'match_en'  => 'Dhahran',
        'slug_en'   => 'appliance-repair-dhahran',
        'slug_ar'   => 'صيانة-اجهزة-منزلية-الظهران',
        'name_en'   => 'Dhahran',
        'name_ar'   => 'الظهران',
        'lat'       => 26.2361,
        'lng'       => 50.0393,
        'title_en'  => 'Appliance Repair in Dhahran – AC, Fridge, Washer & Oven | Ahmad Fix',
        'title_ar'  => 'صيانة أجهزة منزلية بالظهران – مكيفات، ثلاجات، غسالات، أفران | أحمد فيكس',
        'desc_en'   => 'Reliable home appliance repair in Dhahran. AC, refrigerator, washing machine & oven repair in Al Doha, Al Qusur & all areas. Genuine parts, warranty. Call {phone}',
        'desc_ar'   => 'صيانة أجهزة منزلية موثوقة في الظهران: مكيفات، ثلاجات، غسالات وأفران في الدوحة، القصور وجميع المناطق. قطع أصلية وضمان. اتصل {phone}',
        'h1_en'     => 'Home Appliance Repair in Dhahran',
        'h1_ar'     => 'صيانة الأجهزة المنزلية في الظهران',
        'lead_en'   => 'Professional AC, fridge, washing machine and oven repair for homes and businesses across Dhahran.',
        'lead_ar'   => 'صيانة احترافية للمكيفات والثلاجات والغسالات والأفران للمنازل والشركات في جميع أنحاء الظهران.',
        'intro_en'  => [
            'Ahmad Fix serves all residential and commercial areas of Dhahran, including Al Doha North and South, Al Qusur and the neighbourhoods around KFUPM. Book by phone or WhatsApp and a certified technician comes to you with genuine parts.',
            'We repair every major appliance in your home: split and central AC systems, refrigerators and freezers, top-load and front-load washing machines, and gas or electric ovens. Every repair comes with a service warranty and an upfront price.',
        ],
        'intro_ar'  => [
            'يخدم أحمد فيكس جميع المناطق السكنية والتجارية في الظهران، بما فيها الدوحة الشمالية والجنوبية والقصور والأحياء المحيطة بجامعة الملك فهد للبترول والمعادن. احجز بالاتصال أو الواتساب ويأتيك فني معتمد مع قطع غيار أصلية.',
            'نصلح جميع الأجهزة الرئيسية في منزلك: المكيفات السبليت والمركزية، الثلاجات والفريزرات، الغسالات العلوية والأمامية، وأفران الغاز أو الكهرباء. كل إصلاح يأتي مع ضمان خدمة وسعر واضح مسبقاً.',
        ],
        'districts_en' => ['Al Doha North', 'Al Doha South', 'Al Qusur', 'Al Dana', 'KFUPM area', 'Gharb Al Dhahran'],
        'districts_ar' => ['الدوحة الشمالية', 'الدوحة الجنوبية', 'القصور', 'الدانة', 'حي الجامعة', 'غرب الظهران'],
        'faq_en' => [
            ['q' => 'Do you provide appliance repair in all areas of Dhahran?', 'a' => 'Yes. We cover all residential and commercial areas of Dhahran, including Al Doha North, Al Doha South and Al Qusur.'],
            ['q' => 'How much does a repair visit cost in Dhahran?', 'a' => 'The price depends on the appliance and the fault. Our technician diagnoses the problem and tells you the exact price before starting — you only pay if you agree.'],
            ['q' => 'Do you give a warranty on repairs in Dhahran?', 'a' => 'Yes. Every repair comes with a service warranty, and we use genuine, manufacturer-approved spare parts.'],
        ],
        'faq_ar' => [
            ['q' => 'هل تقدمون صيانة الأجهزة في جميع مناطق الظهران؟', 'a' => 'نعم. نغطي جميع المناطق السكنية والتجارية في الظهران، بما فيها الدوحة الشمالية والدوحة الجنوبية والقصور.'],
            ['q' => 'كم تكلفة زيارة الصيانة في الظهران؟', 'a' => 'يعتمد السعر على الجهاز ونوع العطل. يشخص الفني المشكلة ويخبرك بالسعر الدقيق قبل البدء — ولا تدفع إلا إذا وافقت.'],
            ['q' => 'هل تعطون ضماناً على الإصلاح في الظهران؟', 'a' => 'نعم. كل إصلاح يأتي مع ضمان خدمة، ونستخدم قطع غيار أصلية معتمدة من الشركات المصنعة.'],
        ],
    ],
];
