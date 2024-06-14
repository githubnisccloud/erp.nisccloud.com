<?php

namespace Modules\VCard\Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class EmailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
        $emailTemplate = [
            'Appointment Created',
        ];

        $defaultTemplate = [
            'Appointment Created' => [
                'subject' => 'Appointment Created',
                'variables' => '{"App Name":"app_name","Appointment Name":"appointment_name","Appointment Email":"appointment_email","Appointment Phone":"appointment_phone","Appointment Date":"appointment_date","Appointment Time":"appointment_time"
                  }',
                'lang' => [
                    'ar' => '<p>مرحبا عزيزتي</p><p>قام {appointment_name} بحجز تعيين ل ـ {appointment_date} في{appointment_time}.</p><p>البريد الالكتروني : {appointment_email}</p><p>رقم التليفون : {appointment_phone}</p><p>يعتبر،</p><p>{app_url}</p>',
                    'da' => '<p>Hej, kære.</p><p>{ appointment_name } har bestilt en aftale for { appointment_date} kl. {appointment_time}.</p><p>E-mail: { appointment_email }</p><p>Telefonnummer: { appointment_phone }</p><p>Med venlig hilsen</p><p>{ app_name }.</p>',
                    'de' => '<p>Hallo Lieber,</p><p>{appointment_name} hat einen Termin für {appointment_date} gebucht um {appointment_time}.</p><p>E-Mail: {appointment_email}</p><p>Telefonnummer: {appointment_phone}</p><p>Betrachtet,</p><p>{app_name}.</p>',
                    'en' => '<p>Hi Dear,</p><p>{appointment_name} has booked an appointment for {appointment_date} at {appointment_time}.</p><p>Email: {appointment_email}</p><p>Phone Number: {appointment_phone}</p><p>Regards,</p><p>{app_name}.</p>',
                    'es' => '<p>Hola Querido,</p><p>{appointment_name} ha reservado una cita para {appointment_date}a las {appointment_time}.</p><p>Correo electrónico: {appointment_email}</p><p>Número de teléfono: {appointment_phone}</p><p>Considerando,</p><p>{app_name}.</p>',
                    'fr' => '<p>Salut, Chère,</p><p>{ appointment_name} a réservé un rendez-vous pour { appointment_date } à {appointment_time}.</p><p>Adresse électronique: {appointment_email}</p><p>Numéro de téléphone: { appointment_phone }</p><p>Regards,</p><p>{ app_name }.</p>',
                    'it' => '<p>Ciao Caro,</p><p>{appointment_name} ha prenotato un appuntamento per {appointment_date} a {appointment_time}.</p><p>Email: {appointment_email}</p><p>Numero di telefono: {appointment_phone}</p><p>Riguardo,</p><p>{app_name}.</p>',
                    'ja' => '<p>こんにちは、</p><p>{appointment_name} は {appointment_date} の {appointment_time} に予約を入れました。</p><p>メール: {appointment_email}</p><p>電話番号: {appointment_phone}</p><p>よろしくお願いします</p><p>{app_name}.</p>',
                    'nl' => '<p>Hallo, lieverd.</p><p>{ appointment_name } heeft een afspraak voor { appointment_date } geboekt Bij {appointment_time}.</p><p>E-mail: { appointment_email }</p><p>Telefoonnummer: { appointment_phone }</p><p>Betreft:</p><p>{ app_name }.</p>',
                    'pl' => '<p>Witam Szanowny Panie,</p><p>Użytkownik {appointment_name } zarezerwował termin dla {appointment_date } W {appointment_time}.</p><p>E-mail: {appointment_email }</p><p>Numer telefonu: {appointment_phone }</p><p>W odniesieniu do</p><p>{app_name }.</p>',
                    'ru' => '<p>Привет, дорогой.</p><p>Пользователь { appointment_name } забронировал назначение на { appointment_date } в {appointment_time}.</p><p>Электронная почта: { appointment_email }</p><p>Номер телефона: { appointment_phone }</p><p>С уважением,</p><p>{ app_name }.</p>',
                    'pt' => '<p>Oi Querida,</p><p>{appointment_name} marcou um compromisso para {appointment_date} no {appointment_time}.</p><p>E-mail: {appointment_email}</p><p>Número do Telefone: {appointment_phone}</p><p>Considera,</p><p>{app_name}.</p>',
                    'tr' => '<p>Merhaba canım,</p><p>{appointment_name} için randevu aldı  {appointment_date} de {appointment_time}.</p><p>E-posta: {appointment_email}</p><p>Telefon numarası: {appointment_phone}</p><p>Saygılarımızla,</p><p>{app_name}.</p>',
                    'he' => '<p>הי יקירי</p><p>{appointment_name} קבע תור ל {appointment_date} ב-{appointment_time}.</p><p>דואר אלקטרוני: {appointment_email}</p><p>מספר טלפון: {appointment_phone}</p><p>ברכות</p><p>{app_name}.</p>',
                    'pt-br' => '<p>Oi querido</p><p>{appointment_name} marcou uma consulta para {appointment_date} em {appointment_time}.</p><p>Email: {appointment_email}</p><p>Telefone: {appointment_phone}</p><p>Relação,</p><p>{app_name}.</p>',
                    'zh' => '<p>嗨，亲爱的</p><p>{appointment_name} 已预约 {appointment_date} 在 {appointment_time}.</p><p>电子邮件： {appointment_email}</p><p>电话号码： {appointment_phone}</p><p>问候</p><p>{app_name}.</p>',
                    
                ],
            ],
        ];
        foreach($emailTemplate as $eTemp)
        {
            $table = EmailTemplate::where('name',$eTemp)->where('module_name','VCard')->exists();
            if(!$table)
            {
                $emailtemplate=  EmailTemplate::create(
                    [
                        'name' => $eTemp,
                        'from' => 'VCard',
                        'module_name' => 'VCard',
                        'created_by' => 1,
                        'workspace_id' => 0
                        ]
                    );
                    foreach($defaultTemplate[$eTemp]['lang'] as $lang => $content)
                    {
                        
                        EmailTemplateLang::create(
                            [
                                'parent_id' => $emailtemplate->id,
                                'lang' => $lang,
                                'subject' => $defaultTemplate[$eTemp]['subject'],
                                'variables' => $defaultTemplate[$eTemp]['variables'],
                                'content' => $content,
                            ]
                        );
                    }
            }
        }
    }
}
