<?php

use App\Common;
Use App\Religion;
use App\School;
use App\Program;
use App\Province;
use App\Organization;

use App\Enums\Gender;
use App\Enums\EducationLevel;

use Faker\Generator as Faker;

class User_Randomizer
{
    protected static $CAMPER, $CAMPMAKER, $organizations_top, $now;

    public static $name_ths = [
        "โยธาณัฐ", "ธนพงษ์", "พสิษฐ์", "ฤทธินันท์", "นงนภัส", "กิตตินันท์", "กัญญาลักษณ์", "สุภัสสรา", "ฐิติวุฒิ", "เกวลี", "พุฒธิชัย", "ณัชชา", "กฤติยา",
        "ธนภัทร", "สาวิชญ์", "กมลลักษณ์", "พัฒนกฤษณ์", "นิรมิต", "ธนบดินทร์", "กรกมล", "วิชญ์วราภัทร์", "ณัฐยา", "ปัญณวัตน์", "ผัลย์ศุภา", "สรัล",
        "กฤติกร", "ดาวิด", "นัชชา", "กรรณสูต", "กานดา", "ณภัทร", "จิดาภา", "กิติพงษ์", "จรณพร", "ศิริณัฐ", "จิรพงษ์", "จิตรกันยา", "สโรชา", "จิรพัฒน์",
        "กัลยาณี", "ธนวัฒน์", "ชยุตพงศ์", "กรมิษฐ์", "ธนพนธ์", "ฆรวัณณ์", "จิรภาส", "ปภัสพงศ์", "ปองณัฐ", "จิรัชญา", "จุฑามาศ", "สิรภัทร", "ศุภณัฐ",
        "สรกฤศ", "ธนสร", "นภัสสร", "ณัฏฐ์พัชร์", "สรสิช", "ภควัต", "ฐิตินันท์", "พันธ์ทิพย์", "จิดาภา", "นวภรณ์", "ชัชวาล", "ศุภสิทธิ์", "พิมพ์อร", "ณัฎฐณิชา",
        "รังรักษ์", "นุชดี", "ณัฐภัทร", "ณิชา", "สมาร์ท", "มีไชย", "จักรี", "อัครินทร์", "สุดารัตน์", "ปรียพล", "กวิน", "ปิยดนย์", "พิชย", "ปุณยวีร์", "สรณ์สิริ",
        "ณัฐสิมา", "บุญปกรณ์", "พีรดนย์", "ทวิพร", "จิรัฐ", "ธนวัฒน์", "พีร์", "สุวิจักขณ์", "ธนภรณ์", "วีรวัฒน์", "วรรณกร", "กรพัชร", "ชัยภักดิ์", "ประกฤษชัย",
        "อภิสรา", "ธีรุตม์", "ธนพร", "พรรณธีรา", "ภัทรพงษ์", "นวุติ", "ธนา", "สุริยาภา", "นันทพัทธ์", "ณัฐชัย", "ปพิชญา", "คณิศร", "ณัฐชนน", "ปรินทร",
        "พีรภัทร", "มาติกา", "ภูริวัฒน์", "ปาลิตา", "กมลรักษ์", "ศุภณัฐ", "พณภณ", "ณัฐวัชร์", "สหวัชร์", "กุลพัทธ์", "มนัสนันท์", "สุชญา", "กชพรรณ", "แนนสินี",
        "ณัฐพล", "กันตวัฒน์", "วศิน", "สิรีรัชญ์", "สุชาครีย์", "ชลลดา", "ณัฐวรรณ", "พิชชาพร", "ศุภิสรา", "ฐานันท์", "ณัฐริกา", "กาญจนาพร", "ภาคภูมิ", "ภัทรพล",
        "ราณี", "กฤษดา", "นพรุจ", "นพวัฒน์", "ปารีนา", "วิชญาพร", "นภพล", "พรรณนารา", "รังสี", "หนึ่งนุช", "อธิชา", "บงกชมณี", "กนกพร", "ณพวัฒน์", "ณภัทร",
        "วรัณ", "พัชราภรณ์", "อมลณัฐ", "พชร", "ชิติพัทธ์", "ณภัทรชล", "มนสิชา", "ณัฐวิภา", "อภิปิยา", "มาธวี", "ชานน", "ประภาวรินทร์", "พิชญะ", "ปฏิพล", "ธัชพล",
        "พงศ์ศิรัส", "เพชรประกาย", "ภัทรรัตน์", "สัณห์พิชญ์", "สุภัสสรา", "ณัฐชา", "ธนกฤต", "นนทพัทธ์", "พิสิฐพงศ์", "ชัชชนิน", "กาณฑ์", "อานุภาพ", "อมรพงศ์",
        "ภัทรวดี", "ภูมิระพี", "จันทิรา", "ยอดธิดา", "ชาครัฐ", "เบญญา", "กษิดิศ", "รัชชานนท์", "ราษฎร์ชเดช", "วชิรวิทย์", "วรรณกานต์", "วิสิฐศักดิ์", "ศรณ", "ศุภณัฐ",
        "สราวรี", "สุวิจักขณ์", "อิทธิเดช", "อินทุกร",
    ];

    public static $surname_ths = [
        "มะลิวัลย์", "อนันต์ศิริขจร", "บุญกว้างโพธิวงศ์", "บุญมีทวีทรัพย์", "อารยะพงศ์", "อัคคะภิญโญ", "บุญถนอม", "บูรโสถิกุล", "จามรมาน", "จันทวงษ์",
        "ชัยธัมมะปกรณ์", "จันทร์วุฒิคุณ", "คำแพงตา", "ชนะศุภกุล", "จันทนรัตน์", "ทังสุพานิช", "เฉิน", "ชื่นประภานุสรณ์", "เชียรเจริญธนกิจ", "จิรสุภากุล",
        "โชยจอหอ", "จงวิทูกิจ", "ชูเเสง", "ชูสิทธิ์", "ชื่นภักดี", "จุฬาสถิตย์", "ชูเศรษฐการ", "เหตระกูล", "ชุตาภรณ์", "ชีวะธรรมรัตน์", "ดีศิลปกิจ",
        "สุมโนธรรม", "ดูวา", "ชาตะปัทมะ", "เอื้ออังคณากุล", "เฟื่องขจรศักดิ์", "เตียวสวัสดิ์", "เหมภูมิ", "ศรีวิภาสถิตย์", "ห้งเขียบ", "กาญจนาเวส", "อิสสระยางกูล",
        "เจนจิรวัฒน์", "จารุเกษตรพร", "เจียรมณีงาม", "ไพศาลดวงจันทร์", "จิรโรจน์อังกูร", "จิตติปัญญากุล", "สอิ้งทอง", "รัตนแสงใส", "จงศรีวัฒนพร",
        "ก้อนเงิน", "กังวานรัตนา", "แก้วพวง", "ไคบุตร", "กมลวิทย์", "กมลชัยวานิช", "กังวานวิศิษฏ์", "โชติกวณิชย์", "โกกิลานนท์", "คงสกุลวงศ์", "คนรู้",
        "โคตรสุ", "คราเคโม", "หลิมประเสริฐศิริ", "ยงภูมิพุทธา", "ไมตรีบริรักษ์", "เหล่าสุรสุนทร", "วัฒนะ", "เลิศดำรงรักษ์", "ลิลิตกุลพาณิชย์", "หล่อตจะกูล",
        "โล่พันธุ์ศิริกุล", "นาคทิม", "มั่นคง", "โอวาทสุวรรณ", "มธุรสพรวัฒนา", "มิตรสันติสุข", "ณ สงขลา", "นวลละออง", "นนทบงกช", "หนองใหญ่", "นนทลีรักษ์",
        "ปริญญาสงวน", "พัฒนกูล", "ภัทรกุลทวี", "พันธุ์มงคล", "ปัญญานนทชัย", "เพิ่มผลพัฒนา", "พิพัฒน์นรเศรษฐ์", "ภาวนาวิวัฒน์", "เพชรโลหะกุล",
        "เพ็ชรพรประภาส", "ภควัตโสภณ", "พันพิลา", "พงษ์สีดา", "พิศุทธิ์พัฒนา", "โรจน์ไพรินทร์", "พงษ์สวัสดิ์", "พูลทอง", "พูนประพันธ์", "ชัชวาลย์สายสินธ์",
        "พงศ์ไพจิตร", "ปฤษฎางค์บุตร", "พรหมธิรักษ์", "แขวงแดง", "เบ็ญจรูญ", "รัตน์วิจิตต์เวช", "ภู่ย้อย", "รอดดอน", "แซ่ลี้", "สายทองอินทร์", "ประภาพรชัยกุล",
        "สาริยาชีวะ", "เสมาชัย", "เสรีเลิศวิวัฒน์", "เผือกไพบูลย์", "สินธพเลิศชัยกุล", "ศิริโพธิ์", "สิริกุลสุนทร", "สมนิยาม", "สมสา", "สูงสิริ", "ศรอนันต์กุล",
        "รัชทาณิชย์", "ศราวณะ", "ศรีอินทร์คำ", "สว่างวงษ์", "ศรีปทุมวราภรณ์", "ศรีศักดิ์บางเตย", "สุเชาว์อินทร์", "สุขกันตะ", "ทรัพย์ธำรงค์", "ซุลศักดิ์สกุล",
        "สุมิตรเดช", "ราชตราชู", "สังขกรมานิต", "สัจเดว์", "สุนทรวุฒิไกร", "สัจจเจริญพงษ์", "สุทธิพงษ์ไกวัล", "สุรัตนาสถิตย์กุล", "สุริยัน", "ศิริพันธ์",
        "สุวรรณนิกขะ", "สุวรรณไตรภพ", "ตาลานนท์", "ธนโชติธิติกุล", "ลีลาเลอเกียรติ", "ตันกิจเจริญ", "สกุลภาพนิมิต", "ศรีเสริมโภค", "ทวีกาญจน์", "ลือคำหาญ",
        "เต็งพุฒิพงศ์", "ธีรนันทน์", "ทาบสุวรรณ", "ไทพาณิชย์", "ธรรมโชติ", "ธรรม์ทวีวุฒิ", "เทียนทรัพย์", "สุขเขตต์", "โตโพธิ์ไทย", "สุทธิรัตปัญญา", "ตั้งปวิทยา",
        "สุวรรณบล", "อั๋นประเสริฐ", "อุปถัมภานนท์", "ตัณฑ์พูนเกียรติ", "วรวรรณ", "เตชะทวีกิจกุล", "วัฒนเกรียงไกร", "วิงวอน", "ทองบริสุทธิ์", "วงศ์คำ", "วัชรเสวี",
        "ยิ้มอุดม", "ยูฮง", "อยู่พืช", "เวทวินิจ", "คันธบุษบง", "ตีระวนินทร", "วัฒนศิลเมฆินทร์", "ยอดเมือง", "อยู่ประเสริฐชัย", "อยู่สุข", "ศิริพร", "ชมภู", "ระงับพาล",
        "เจตสิกทัต", "ปรางอ่อน", "น้อยใจบุญ", "พรอนันต์ตระกูล", "ติระชูศักดิ์", "พลับพลา", "เลิศถนอมวงศ์", "สีเหมือน", "ลิมปเชวง",
    ];

    /**
     * Randomize Thai citizen ID (Only for data seeding).
     * http://kiss-hack.blogspot.com/2013/09/random-number-13.html
     *
     */
    public static function citizenID() {
        $firstNumber = rand(1, 8);
        $numberCalc = 13 * $firstNumber;
        for ($i = 12; $i > 1; $i--) {
            $m = rand(0, 9);
            $firstNumber .= $m;
            $numberCalc += ($i * $m);
        }
        $lastNumber = (11 - ($numberCalc % 11)) % 10;
        return $firstNumber.$lastNumber;
    }

    public static function camper()
    {
        if (!self::$CAMPER)
            self::$CAMPER = config('const.account.camper');
        return self::$CAMPER;
    }

    public static function campmaker()
    {
        if (!self::$CAMPMAKER)
            self::$CAMPMAKER = config('const.account.campmaker');
        return self::$CAMPMAKER;
    }

    public static function randFullNameTH(Faker &$faker)
    {
        return $faker->unique()->randomElements($array = range(0, count(self::$name_ths) - 1), $count = 2, $allowDuplicates = true);
    }

    public static function now()
    {
        if (!self::$now)
            self::$now = now();
        return self::$now;
    }

    public static function organization()
    {
        if (!self::$organizations_top)
            self::$organizations_top = Organization::where('image', '!=', '')->pluck('id', 'id')->toArray();
        return Common::randomFrequentHit() ? array_rand(array_flip(self::$organizations_top)) : rand(1, Organization::count());
    }
}

$factory->define(App\User::class, function (Faker $faker) {
    $name_en = $faker->unique()->firstName;
    $surname_en = $faker->lastName;
    $token = User_Randomizer::randFullNameTH($faker);
    $type = Common::randomMediumHit() ? User_Randomizer::camper() : User_Randomizer::campmaker();
    $dob = $type == User_Randomizer::camper() ? $faker->dateTimeBetween($startDate = '-19 years', '-10 years') : $faker->dateTimeBetween($startDate = '-40 years', '-19 years');
    $province = Province::inRandomOrder()->get()->first();
    $activated = Common::randomVeryFrequentHit();
    $data = [
        'username' => strtolower($name_en),
        'name_en' => $name_en,
        'name_th' => User_Randomizer::$name_ths[$token[0]],
        'surname_en' => $surname_en,
        'surname_th' => User_Randomizer::$surname_ths[$token[1]],
        'nickname_en' => $faker->word,
        'nationality' => rand(0, 1),
        'gender' => Gender::any(),
        'citizen_id' => User_Randomizer::citizenID(),
        'dob' => $dob,
        'street_address' => $faker->address,
        'province_id' => $province->id,
        'zipcode' => $province->zipcode_prefix.implode('', $faker->randomElements($array = range(0, 9), $count = 3, $allowDuplicates = true)),
        'mobile_no' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        'religion_id' => rand(1, Religion::count()),
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => User_Randomizer::now(),
        'type' => $type,
        'password' => '123456',
        'remember_token' => str_random(10),
        'status' => $activated ? 1 : 0,
    ];
    if ($type == User_Randomizer::camper()) {
        $data += [
            'education_level' => EducationLevel::any(),
            'blood_group' => rand(0, 3),
            'cgpa' => rand(200, 400) / 100.0, // Assume campers are not that incompetent
            'school_id' => rand(1, School::count()),
            'program_id' => rand(1, Program::count()),
            'guardian_name' => $faker->firstName,
            'guardian_surname' => $surname_en,
            'guardian_role' => Common::randomMediumHit(),
            'guardian_mobile_no' => '0'.implode('', $faker->unique()->randomElements($array = range(0, 9), $count = 9, $allowDuplicates = true)),
        ];
    } else {
        $data += [
            'organization_id' => User_Randomizer::organization(),
        ];
    }
    return $data;
});
