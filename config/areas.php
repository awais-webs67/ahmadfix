<?php
/**
 * Mr Fix KSA — Service Area (City) Pages
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
        'wikidata'  => 'https://www.wikidata.org/wiki/Q269488',
        'title_en'  => 'Appliance Repair Dammam – AC, Fridge & Washer | ' . SITE_NAME,
        'title_ar'  => 'صيانة أجهزة منزلية بالدمام – مكيفات وثلاجات | ' . SITE_NAME_AR,
        'desc_en'   => 'Same-day appliance repair in Dammam for AC, fridge, washer & oven across all districts. Certified technicians, genuine parts & warranty. Call {phone}',
        'desc_ar'   => 'صيانة أجهزة منزلية في الدمام بنفس اليوم: مكيفات، ثلاجات، غسالات وأفران في جميع الأحياء. فنيون معتمدون، قطع أصلية وضمان. اتصل الآن {phone}',
        'h1_en'     => 'Home Appliance Repair in Dammam',
        'h1_ar'     => 'صيانة الأجهزة المنزلية في الدمام',
        'lead_en'   => 'AC, refrigerator, washing machine and oven repair at your door — anywhere in Dammam, usually the same day.',
        'lead_ar'   => 'صيانة مكيفات، ثلاجات، غسالات وأفران عند باب منزلك — في أي حي بالدمام، وغالباً في نفس اليوم.',
        'intro_en'  => [
            'Dammam is our home base, so our technicians are never far away. Whether you live in a villa in Al Faisaliyah, an apartment in Ash Shati or a family house in Al Mazruiyah, ' . SITE_NAME . ' dispatches a certified technician equipped with diagnostic gauges and genuine OEM spare parts to complete your repair on the very first visit.',
            'Summer in Dammam regularly passes 45°C with intense coastal humidity, putting extreme thermal load on split and central air conditioners as well as domestic refrigerators. We handle everything from R410A and R22 Freon leak detection and high-pressure coil washing to inverter fridge compressor replacement, front-load washing machine drain pump repair, and gas oven thermocouple replacement for Samsung, LG, Gree, Carrier, Midea, Bosch, and Whirlpool.',
            'Every service call in Dammam starts with a complete electronic and mechanical inspection and a clear upfront price quote starting from 50 SAR before any work begins. Once completed, your appliance is tested under load and backed by a written 30-to-90-day workmanship and spare-parts warranty.',
        ],
        'intro_ar'  => [
            'الدمام هي مقرنا الرئيسي، لذلك فنيونا دائماً قريبون منك. سواء كنت تسكن في فيلا بحي الفيصلية أو شقة في حي الشاطئ أو منزل عائلي في المزروعية أو النخيل، يرسل لك ' . SITE_NAME_AR . ' فنياً معتمداً مجهزاً بأحدث أجهزة الفحص وقطع الغيار الأصلية لإتمام الإصلاح من الزيارة الأولى.',
            'يتجاوز صيف الدمام 45 درجة مئوية مع رطوبة عالية تضع ضغطاً مستمراً على المكيفات السبليت والمركزية والثلاجات المنزلية. نتعامل مع كافة الأعطال بدءاً من فحص تسريب الفريون وتعبئة غاز R410A والتنظيف العميق للوحدات الداخلية والخارجية، وصولاً إلى تغيير كمبروسر الثلاجات الإنفرتر، وإصلاح طرمبات وبوردات الغسالات الأوتوماتيك، وصيانة حساسات وشعلات أفران الغاز والكهرباء لماركات سامسونج، إل جي، جري، كاريير، ميديا، بوش وويرلبول.',
            'تبدأ كل زيارة صيانة في الدمام بفحص فني شامل وتسعير واضح ومسبق يبدأ من 50 ريال سعودي قبل البدء بأي إصلاح. وبعد الانتهاء نختبر كفاءة الجهاز بالكامل ونقدم لك فاتورة وضماناً مكتوباً لمدة تتراوح بين 30 و90 يوماً على قطع الغيار والعمل.',
        ],
        'benefits_en' => [
            ['title' => 'Rapid Dispatch from Our Dammam Hub', 'desc' => 'Technicians stationed along King Fahd Road and Al Faisaliyah reach most Dammam neighborhoods within 60 to 90 minutes.'],
            ['title' => 'Upfront SAR Pricing Before Work Starts', 'desc' => 'Full fault diagnosis starting from 50 SAR (waived upon repair approval) with a clear quote before turning a wrench.'],
            ['title' => 'Genuine Parts for Extreme Heat', 'desc' => 'OEM compressors, capacitors, fan motors, and PCBs rated for Dammam’s 45°C+ summer operating conditions.'],
            ['title' => 'Written 30–90 Day Repair Warranty', 'desc' => 'Every AC, refrigerator, washer, and oven repair in Dammam includes a documented guarantee on parts and labor.'],
        ],
        'benefits_ar' => [
            ['title' => 'وصول سريع من مقرنا الرئيسي بالدمام', 'desc' => 'فرقنا المتمركزة قرب طريق الملك فهد وحي الفيصلية تصل إلى معظم أحياء الدمام خلال 60 إلى 90 دقيقة.'],
            ['title' => 'تسعير واضح بالريال قبل بدء العمل', 'desc' => 'فحص وتشخيص شامل يبدأ من 50 ريال (يُخصم عند الموافقة على الإصلاح) مع تحديد التكلفة بدقة قبل البدء.'],
            ['title' => 'قطع غيار أصلية تتحمل حرارة الصيف', 'desc' => 'كمبروسرات ومكثفات ومراوح وبوردات أصلية مصممة لتحمل درجات الحرارة المرتفعة في صيف الدمام.'],
            ['title' => 'ضمان مكتوب لمدة 30 إلى 90 يوماً', 'desc' => 'كل عملية صيانة للمكيفات والثلاجات والغسالات والأفران في الدمام مشمولة بضمان رسمي على القطع والعمل.'],
        ],
        'districts_en' => ['Al Faisaliyah', 'Ash Shati', 'Al Mazruiyah', 'Al Nakheel', 'Al Muhammadiyah', 'Al Jalawiyah', 'Al Adamah', 'Al Badiyah', 'Al Rayyan', 'Al Noor', 'Uhud', 'Al Manar', 'Al Shulah', 'Taybah', 'Al Hamra', 'Al Zuhur', 'Al Rawdah'],
        'districts_ar' => ['الفيصلية', 'الشاطئ', 'المزروعية', 'النخيل', 'المحمدية', 'الجلوية', 'العدامة', 'البادية', 'الريان', 'النور', 'أحد', 'المنار', 'الشعلة', 'طيبة', 'الحمراء', 'الزهور', 'الروضة'],
        'faq_en' => [
            ['q' => 'Do you cover all districts of Dammam?', 'a' => 'Yes. We serve every district in Dammam, including Al Faisaliyah, Ash Shati, Al Mazruiyah, Al Nakheel, Al Muhammadiyah, Al Rayyan, Uhud, Al Noor and Al Jalawiyah, for homes, villas, apartments and commercial offices.'],
            ['q' => 'How fast can a technician reach me in Dammam?', 'a' => 'Most Dammam bookings made before 2 PM receive a same-day technician visit within 60 to 120 minutes. For urgent summer AC breakdowns or refrigerator cooling failures, we prioritize immediate dispatch.'],
            ['q' => 'How much does appliance inspection and repair cost in Dammam?', 'a' => 'On-site inspection and fault diagnosis in Dammam starts at 50 SAR and is credited toward your repair when you proceed. Typical repairs range from 120 SAR to 450 SAR depending on the appliance and spare part required.'],
            ['q' => 'Which appliances and brands do you repair in Dammam?', 'a' => 'We repair split, window, and central air conditioners, refrigerators and freezers, front-load and top-load washing machines, and gas and electric ovens across all major brands including Samsung, LG, Gree, Carrier, Daikin, Midea, Bosch, and Whirlpool.'],
            ['q' => 'Do you provide a warranty on Dammam repairs?', 'a' => 'Yes. All repairs carried out in Dammam come with a 30-to-90-day warranty covering both the genuine replacement parts installed and our technician’s workmanship.'],
        ],
        'faq_ar' => [
            ['q' => 'هل تغطون جميع أحياء الدمام؟', 'a' => 'نعم. نخدم جميع أحياء الدمام بما فيها الفيصلية، الشاطئ، المزروعية، النخيل، المحمدية، الريان، أحد، النور والجلوية، للمنازل والفلل والشقق والمكاتب التجارية.'],
            ['q' => 'كم يستغرق وصول الفني إليّ في الدمام؟', 'a' => 'معظم الحجوزات في الدمام قبل الساعة 2 ظهراً يصلها فني في نفس اليوم خلال 60 إلى 120 دقيقة. وفي أعطال المكيفات والثلاجات العاجلة صيفاً نعطي الأولوية لأقرب موعد فوري.'],
            ['q' => 'كم تكلفة الكشف وصيانة الأجهزة في الدمام؟', 'a' => 'تبدأ رسوم الزيارة والفحص الفني في الدمام من 50 ريال سعودي ويتم خصمها من تكلفة الإصلاح عند الموافقة. وتتراوح معظم الإصلاحات بين 120 و450 ريال حسب نوع العطل وقطعة الغيار.'],
            ['q' => 'ما الأجهزة والماركات التي تصلحونها في الدمام؟', 'a' => 'نصلح المكيفات السبليت والشباك والمركزية، الثلاجات والفريزرات، الغسالات الأوتوماتيك والأمامية والعلوية، وأفران الغاز والكهرباء لجميع الماركات مثل سامسونج، إل جي، جري، كاريير، دايكن، ميديا، بوش وويرلبول.'],
            ['q' => 'هل تقدمون ضماناً على الصيانة في الدمام؟', 'a' => 'نعم، جميع خدمات الإصلاح في الدمام مشمولة بضمان مكتوب لمدة تتراوح بين 30 و90 يوماً يشمل قطع الغيار الأصلية التي تم تركيبها وجودة العمل.'],
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
        'wikidata'  => 'https://www.wikidata.org/wiki/Q244232',
        'title_en'  => 'Appliance Repair Al Khobar – AC, Fridge & Oven | ' . SITE_NAME,
        'title_ar'  => 'صيانة أجهزة منزلية بالخبر – مكيفات وثلاجات | ' . SITE_NAME_AR,
        'desc_en'   => 'Fast appliance repair in Al Khobar for AC, fridge, washer & oven in Al Thuqbah, Al Rakah & all areas. Certified technicians & warranty. Call {phone}',
        'desc_ar'   => 'صيانة أجهزة منزلية سريعة في الخبر: مكيفات، ثلاجات، غسالات وأفران في الثقبة، العقربية، الراكة وجميع الأحياء. خدمة نفس اليوم وضمان. اتصل {phone}',
        'h1_en'     => 'Home Appliance Repair in Al Khobar',
        'h1_ar'     => 'صيانة الأجهزة المنزلية في الخبر',
        'lead_en'   => 'Certified technicians for AC, fridge, washing machine and oven repair across Al Khobar — homes, villas, compounds and offices.',
        'lead_ar'   => 'فنيون معتمدون لصيانة المكيفات والثلاجات والغسالات والأفران في جميع أنحاء الخبر — منازل، فلل، مجمعات سكنية ومكاتب.',
        'intro_en'  => [
            'From the Corniche and Al Ulaya to Al Thuqbah, Al Aqrabiyah, and Al Rakah, ' . SITE_NAME . ' covers every residential neighborhood and compound in Al Khobar. Our bilingual technicians arrive on schedule, explain the technical fault clearly, and provide a fixed quote before any repair begins.',
            'High coastal humidity along the Arabian Gulf in Al Khobar accelerates condenser coil corrosion, refrigerant micro-leaks, clogged AC drain lines, and refrigerator defrost sensor failures. We pressure-wash and recharge split and ducted AC units, replace faulty fridge thermostats and inverter boards, resolve front-load washer spin and drainage errors, and restore gas and built-in electric ovens safely.',
            'Whether we are visiting a residential compound in Al Rakah, a seafront apartment near the Corniche, or a family villa in Al Aziziyah, our mobile workshops carry genuine spare parts for Samsung, LG, Carrier, Daikin, Bosch, Siemens, and Whirlpool — backed by a 30-to-90-day service warranty.',
        ],
        'intro_ar'  => [
            'من الكورنيش والعليا إلى الثقبة والعقربية والراكة، يغطي ' . SITE_NAME_AR . ' كافة الأحياء والمجمعات السكنية في الخبر. يصل فنيونا في الموعد المحدد، ويشرحون سبب العطل بوضوح، ويقدمون لك السعر النهائي قبل البدء بأي إصلاح — بدون أي تكاليف مخفية.',
            'تؤثر الرطوبة الساحلية العالية في الخبر بشكل مباشر على المكيفات والثلاجات، حيث تسبب تآكل المكثفات وتسريب الفريون وانسداد مواسير التصريف وتلف حساسات التبريد. نقوم بغسيل وتعبئة فريون المكيفات السبليت والدكت، وإصلاح الثلاجات التي توقفت عن التبريد، ومعالجة اهتزاز ومشاكل تصريف الغسالات الأوتوماتيك، وصيانة أفران الغاز والكهرباء البلت إن بأعلى معايير الأمان.',
            'سواء كنت في مجمع سكني بحي الراكة أو شقة قريبة من الكورنيش أو فيلا في العزيزية أو الحزام الأخضر، فإن سياراتنا المجهزة تحمل قطع غيار أصلية لماركات سامسونج، إل جي، كاريير، دايكن، بوش، سيمنس وويرلبول مع ضمان مكتوب يصل إلى 90 يوماً.',
        ],
        'benefits_en' => [
            ['title' => 'Compound & Villa Access Across Al Khobar', 'desc' => 'Experienced bilingual technicians serving residential compounds, high-rise apartments, and villas from Al Rakah to Al Aziziyah.'],
            ['title' => 'Coastal Humidity & Corrosion Protection', 'desc' => 'Specialized AC coil chemical cleaning, drain line flushing, and moisture-resistant PCB replacements suited for Al Khobar’s climate.'],
            ['title' => 'Transparent Pricing & Instant Diagnosis', 'desc' => 'Clear 50 SAR diagnostic check (credited toward repair) and fixed quotation before replacing any component.'],
            ['title' => 'Genuine European, Korean & Japanese Parts', 'desc' => 'Original components for Samsung, LG, Bosch, Siemens, Carrier, and Daikin backed by up to 90 days warranty.'],
        ],
        'benefits_ar' => [
            ['title' => 'خدمة سريعة للفلل والمجمعات السكنية بالخبر', 'desc' => 'فنيون محترفون لخدمة المجمعات السكنية والشقق والفلل في الراكة، العقربية، الثقبة، العليا والعزيزية.'],
            ['title' => 'حلول مخصصة لرطوبة الخبر الساحلية', 'desc' => 'تنظيف كيميائي لمكثفات المكيفات وتسليك مجاري الصرف وتركيب قطع أصلية مقاومة للرطوبة والأملاح.'],
            ['title' => 'فحص دقيق وتسعير مسبق واضح', 'desc' => 'كشف فني يبدأ من 50 ريال (يُخصم عند الإصلاح) مع عرض سعر ثابت وواضح قبل تغيير أي قطعة.'],
            ['title' => 'قطع غيار أصلية بضمان حتى 90 يوماً', 'desc' => 'قطع غيار معتمدة لماركات سامسونج، إل جي، بوش، سيمنس، كاريير ودايكن مع ضمان مكتوب على العمل.'],
        ],
        'districts_en' => ['Al Thuqbah', 'Al Aqrabiyah', 'Al Khobar Al Shamaliyah', 'Al Khobar Al Janubiyah', 'Al Rakah', 'Al Ulaya', 'Al Yarmouk', 'Al Aziziyah', 'Al Hizam Al Akhdar', 'Al Bandariyah', 'Al Corniche', 'Al Jisr', 'Al Hamra', 'Madinat Al Ummal', 'Al Andalus'],
        'districts_ar' => ['الثقبة', 'العقربية', 'الخبر الشمالية', 'الخبر الجنوبية', 'الراكة', 'العليا', 'اليرموك', 'العزيزية', 'الحزام الأخضر', 'البندرية', 'الكورنيش', 'الجسر', 'الحمراء', 'مدينة العمال', 'الأندلس'],
        'faq_en' => [
            ['q' => 'Do you serve residential compounds and villas in Al Khobar?', 'a' => 'Yes. We regularly service residential compounds, family villas, and apartment towers across Al Khobar, including Al Rakah, Al Aqrabiyah, Al Ulaya, Al Jisr, Al Aziziyah and Al Thuqbah.'],
            ['q' => 'Can I get same-day appliance repair in Al Khobar?', 'a' => 'Yes. Call or WhatsApp us before 2 PM and we will dispatch a technician to your Al Khobar address the same day, often within 60 to 90 minutes.'],
            ['q' => 'How much does AC or appliance repair cost in Al Khobar?', 'a' => 'Our Al Khobar visit and diagnostic fee is 50 SAR, which is waived when you approve the repair. AC cleaning and gas top-ups start from 120–180 SAR, and major appliance repairs come with an upfront quote.'],
            ['q' => 'Do you offer preventive AC maintenance in Al Khobar?', 'a' => 'Yes. We provide seasonal and annual AC maintenance in Al Khobar, including high-pressure coil washing, drain pan sanitization, and refrigerant pressure checks to combat coastal humidity.'],
            ['q' => 'What warranty do I receive on repairs in Al Khobar?', 'a' => 'Every repair in Al Khobar includes a 30-to-90-day warranty covering both the genuine spare parts installed and the labor performed.'],
        ],
        'faq_ar' => [
            ['q' => 'هل تخدمون المجمعات السكنية والفلل في الخبر؟', 'a' => 'نعم. نعمل يومياً في المجمعات السكنية والفلل والأبراج السكنية في جميع أنحاء الخبر، بما فيها الراكة، العقربية، العليا، الجسر، العزيزية والثقبة.'],
            ['q' => 'هل يمكنني الحصول على صيانة في نفس اليوم في الخبر؟', 'a' => 'نعم. اتصل بنا أو راسلنا واتساب قبل الساعة 2 ظهراً وسنرسل فنياً إلى عنوانك في الخبر في نفس اليوم، وغالباً خلال 60 إلى 90 دقيقة.'],
            ['q' => 'كم تكلفة فحص وصيانة الأجهزة في الخبر؟', 'a' => 'رسوم الزيارة والفحص في الخبر 50 ريال تُخصم عند الموافقة على الإصلاح. ويبدأ تنظيف المكيفات وفحص الفريون من 120 إلى 180 ريال مع تسعير مسبق لأي قطعة غيار.'],
            ['q' => 'هل تقدمون صيانة وقائية وعقود مكيفات في الخبر؟', 'a' => 'نعم. نوفر خدمات الغسيل الموسمي وعقود الصيانة السنوية للمكيفات في الخبر لحمايتها من الرطوبة الساحلية وانسداد التصريف وضعف التبريد.'],
            ['q' => 'ما مدة الضمان على الصيانة في الخبر؟', 'a' => 'نمنح ضماناً مكتوباً لمدة 30 إلى 90 يوماً على جميع أعمال الصيانة وقطع الغيار الأصلية التي نركبها في الخبر.'],
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
        'wikidata'  => 'https://www.wikidata.org/wiki/Q269062',
        'title_en'  => 'Appliance Repair Dhahran – AC, Fridge & Washer | ' . SITE_NAME,
        'title_ar'  => 'صيانة أجهزة منزلية بالظهران – مكيفات وثلاجات | ' . SITE_NAME_AR,
        'desc_en'   => 'Reliable home appliance repair in Dhahran for AC, fridge, washer & oven in Al Doha, Al Qusur & all areas. Genuine parts & warranty. Call {phone}',
        'desc_ar'   => 'صيانة أجهزة منزلية موثوقة في الظهران: مكيفات، ثلاجات، غسالات وأفران في الدوحة، القصور وجميع الأحياء. قطع غيار أصلية وضمان مكتوب. اتصل {phone}',
        'h1_en'     => 'Home Appliance Repair in Dhahran',
        'h1_ar'     => 'صيانة الأجهزة المنزلية في الظهران',
        'lead_en'   => 'Professional AC, fridge, washing machine and oven repair for homes and businesses across Dhahran.',
        'lead_ar'   => 'صيانة احترافية للمكيفات والثلاجات والغسالات والأفران للمنازل والشركات في جميع أنحاء الظهران.',
        'intro_en'  => [
            SITE_NAME . ' serves all residential districts and compounds of Dhahran, including Al Doha North, Al Doha South, Al Dana, Al Qusur, and the neighborhoods surrounding KFUPM and Saudi Aramco. Book by phone or WhatsApp and a certified technician arrives at your doorstep with genuine spare parts.',
            'Many homes in Dhahran rely on high-capacity central and split air conditioning systems, side-by-side American and Korean refrigerators, front-load washer-dryers, and built-in electric or gas ovens. Our technicians are trained to diagnose inverter control boards, seal refrigerant leaks, replace worn washing machine bearings, and calibrate oven thermostats cleanly and safely inside your home.',
            'We respect your schedule and your property: every Dhahran visit includes punctual arrival, shoe covers and clean workspaces, a transparent 50 SAR diagnostic check (waived upon repair), and a written 30-to-90-day warranty on all installed parts and labor.',
        ],
        'intro_ar'  => [
            'يخدم ' . SITE_NAME_AR . ' جميع الأحياء والمناطق السكنية في الظهران، بما فيها الدوحة الشمالية، الدوحة الجنوبية، الدانة، القصور، غرب الظهران والأحياء المحيطة بجامعة الملك فهد للبترول والمعادن وأرامكو. احجز بالاتصال أو عبر الواتساب ليصلك فني معتمد مجهز بقطع الغيار الأصلية.',
            'تعتمد معظم الفلل والمنازل في الظهران على أنظمة التكييف المركزي والسبليت عالية الكفاءة، والثلاجات الكبيرة (الدولابي والإنفرتر)، والغسالات الأوتوماتيك المزودة بمجفف، والأفران البلت إن الغازية والكهربائية. يمتلك فنيونا خبرة دقيقة في فحص اللوحات الإلكترونية وإصلاح تسريبات الفريون وتغيير رومان بلي وطرمبات الغسالات ومعايرة منظمات حرارة الأفران بأمان تام داخل منزلك.',
            'نلتزم في الظهران بالدقة التامة في المواعيد والمحافظة على نظافة المنزل، مع تقديم فحص فني شامل يبدأ من 50 ريال (يُخصم عند الإصلاح)، وتسعير واضح قبل البدء، وضمان مكتوب لمدة 30 إلى 90 يوماً على كافة القطع والعمل.',
        ],
        'benefits_en' => [
            ['title' => 'Specialists in Villa & Built-In Appliances', 'desc' => 'Expert diagnostics for central/split ACs, side-by-side refrigerators, washer-dryers, and built-in ovens common in Dhahran villas.'],
            ['title' => 'Fast Coverage in Al Doha, Al Dana & Al Qusur', 'desc' => 'Dedicated mobile teams serving Dhahran neighborhoods with same-day appointments for bookings before 2 PM.'],
            ['title' => 'Upfront Quotation & Clean Home Guarantee', 'desc' => 'Fixed pricing approved by you before work starts, with careful, mess-free repair inside your kitchen or laundry room.'],
            ['title' => 'OEM Spare Parts & 30–90 Day Warranty', 'desc' => 'Genuine manufacturer components for LG, Samsung, Whirlpool, GE, Bosch, Carrier, and Daikin with a written guarantee.'],
        ],
        'benefits_ar' => [
            ['title' => 'خبرة في أجهزة الفلل والأجهزة البلت إن بالظهران', 'desc' => 'صيانة متخصصة للمكيفات المركزية والسبليت والثلاجات الدولابي والغسالات المجففة والأفران المدمجة في فلل الظهران.'],
            ['title' => 'تغطية سريعة للدوحة والدانة والقصور', 'desc' => 'فرق متنقلة تخدم أحياء الظهران في نفس اليوم للحجوزات قبل الساعة 2 ظهراً.'],
            ['title' => 'سعر واضح مسبقاً ونظافة تامة أثناء العمل', 'desc' => 'لا نبدأ أي إصلاح إلا بعد موافقتك على التكلفة، مع الحفاظ التام على نظافة وسلامة منزلك.'],
            ['title' => 'قطع غيار أصلية وضمان 30 إلى 90 يوماً', 'desc' => 'نستخدم قطعاً أصلية لماركات إل جي، سامسونج، ويرلبول، جنرال إلكتريك، بوش وكاريير مع ضمان مكتوب.'],
        ],
        'districts_en' => ['Al Doha North', 'Al Doha South', 'Al Qusur', 'Al Dana', 'KFUPM area', 'Gharb Al Dhahran'],
        'districts_ar' => ['الدوحة الشمالية', 'الدوحة الجنوبية', 'القصور', 'الدانة', 'حي الجامعة', 'غرب الظهران'],
        'faq_en' => [
            ['q' => 'Do you provide appliance repair in all areas of Dhahran?', 'a' => 'Yes. We cover all residential and commercial areas of Dhahran, including Al Doha North, Al Doha South, Al Dana, Al Qusur, Gharb Al Dhahran and the KFUPM area.'],
            ['q' => 'How much does a repair visit cost in Dhahran?', 'a' => 'Our diagnostic visit in Dhahran is 50 SAR, which is waived when you proceed with the repair. Our technician inspects the appliance and gives you the exact total price before starting any work.'],
            ['q' => 'Do you repair built-in ovens and American-style appliances in Dhahran?', 'a' => 'Yes. Our technicians are experienced with American, European, and Korean appliances including Whirlpool, General Electric, Bosch, Siemens, Samsung, and LG.'],
            ['q' => 'How quickly can you visit my home in Dhahran?', 'a' => 'Bookings placed before 2 PM are typically scheduled for the same day across Al Doha, Al Dana, and Al Qusur.'],
            ['q' => 'Do you give a warranty on repairs in Dhahran?', 'a' => 'Yes. Every repair in Dhahran comes with a written 30-to-90-day service warranty, and we install genuine, manufacturer-approved spare parts.'],
        ],
        'faq_ar' => [
            ['q' => 'هل تقدمون صيانة الأجهزة في جميع مناطق الظهران؟', 'a' => 'نعم. نغطي جميع المناطق السكنية والتجارية في الظهران، بما فيها الدوحة الشمالية، الدوحة الجنوبية، الدانة، القصور، غرب الظهران وحي الجامعة.'],
            ['q' => 'كم تكلفة زيارة الصيانة في الظهران؟', 'a' => 'تكلفة الزيارة والفحص الفني في الظهران 50 ريال يتم خصمها عند الموافقة على الإصلاح. يشخص الفني العطل ويخبرك بالسعر الإجمالي الدقيق قبل البدء.'],
            ['q' => 'هل تصلحون الأفران البلت إن والأجهزة الأمريكية والأوروبية في الظهران؟', 'a' => 'نعم. فنيونا متمرسون في صيانة الأجهزة الأمريكية والأوروبية والكورية مثل ويرلبول، جنرال إلكتريك، بوش، سيمنس، سامسونج وإل جي.'],
            ['q' => 'متى يمكن للفني زيارتي في الظهران؟', 'a' => 'الحجوزات التي تتم قبل الساعة 2 ظهراً تحصل غالباً على زيارة في نفس اليوم في الدوحة والدانة والقصور وباقي أحياء الظهران.'],
            ['q' => 'هل تعطون ضماناً على الإصلاح في الظهران؟', 'a' => 'نعم. كل إصلاح في الظهران يأتي مع ضمان خدمة مكتوب لمدة 30 إلى 90 يوماً، ونستخدم قطع غيار أصلية معتمدة من الشركات المصنعة.'],
        ],
    ],
];
