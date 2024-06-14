<?php

namespace Modules\VCard\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\LandingPage\Entities\MarketplacePageSetting;


class MarketPlaceSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $module = 'VCard';

        $data['product_main_banner'] = '';
        $data['product_main_status'] = 'on';
        $data['product_main_heading'] = 'vCard';
        $data['product_main_description'] = '<p>Designing your business card has never been this easier. With vCard, you can compose your business card within minutes, and it’s effortless, elegant, and free. vCard is always in your pocket without running or tearing out. Easily update your digital business card with our user-friendly dashboard, so you won’t need to re-print your business card again.</p>';
        $data['product_main_demo_link'] = '#';
        $data['product_main_demo_button_text'] = 'View Live Demo';
        $data['dedicated_theme_heading'] = '<h2>Digital <b>Business Card</b>  Builder</h2>';
        $data['dedicated_theme_description'] = '<p>vCard create a digital business card that helps you reach more customers</p>';
        $data['dedicated_theme_sections'] = '[{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Stop wasting money on Expensive Hard Copy Business Cards.","dedicated_theme_section_description":"<p>A vCard is an electronic business or personal card and also the name of an industry specification for the kind of communication exchange that is done on business or personal cards. You may have seen a vCard attached to an email note someone has sent you.<\/p>","dedicated_theme_section_cards":{"1":{"title":"Self-manageable and intuitive","description":"vCard is fast, easy and intuitive! You only need a few steps to have your own virtual card. Manage your business` cards in seconds and modify any content with immediate application through an easy and intuitive platform."},"2":{"title":"Send and save contacts as vCards","description":"VCF (Virtual Card Format) is a digital file format for storing contact information. The format is widely used for data interchange among popular information exchange applications. A VCF file usually contains information such as contact’s name, phone number, email. Being supported by email clients and services, there is no loss of data during the transfer of contacts via using the vCard format. The media type for VCF file format is text/vcard."}}},{"dedicated_theme_section_image":"","dedicated_theme_section_heading":"Smarten your business cards.","dedicated_theme_section_description":"<p>Digital business cards also known as virtual business cards and e business cards are like the digital makeover that the paper cards have needed for a long time.<\/p>","dedicated_theme_section_cards":{"1":{"title":"Choose what to share!","description":"Our vcards are self-manageable, therefore, you’ll always be in control of the information you’re sharing. Centralize all your social media profiles and make your community grow."},"2":{"title":"The most cost-effective way to expand your reach","description":"Customize your digital business card to match your brand.Choose from 21 beautiful themes with it`s unique colors.Add,modify update your business information quickly and easily."}}}]';
        $data['dedicated_theme_sections_heading'] = '';
        $data['screenshots'] = '[{"screenshots":"","screenshots_heading":"vCard"},{"screenshots":"","screenshots_heading":"vCard"},{"screenshots":"","screenshots_heading":"vCard"},{"screenshots":"","screenshots_heading":"vCard"},{"screenshots":"","screenshots_heading":"vCard"}]';
        $data['addon_heading'] = '<h2>Why choose dedicated modules<b> for Your Business?</b></h2>';
        $data['addon_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['addon_section_status'] = 'on';
        $data['whychoose_heading'] = 'Why choose dedicated modules for Your Business?';
        $data['whychoose_description'] = '<p>With Dash, you can conveniently manage all your business functions from a single location.</p>';
        $data['pricing_plan_heading'] = 'Empower Your Workforce with DASH';
        $data['pricing_plan_description'] = '<p>Access over Premium Add-ons for Accounting, HR, Payments, Leads, Communication, Management, and more, all in one place!</p>';
        $data['pricing_plan_demo_link'] = '#';
        $data['pricing_plan_demo_button_text'] = 'View Live Demo';
        $data['pricing_plan_text'] = '{"1":{"title":"Pay-as-you-go"},"2":{"title":"Unlimited installation"},"3":{"title":"Secure cloud storage"}}';
        $data['whychoose_sections_status'] = 'on';
        $data['dedicated_theme_section_status'] = 'on';

        foreach($data as $key => $value){
            if(!MarketplacePageSetting::where('name', '=', $key)->where('module', '=', $module)->exists()){
                MarketplacePageSetting::updateOrCreate(
                [
                    'name' => $key,
                    'module' => $module

                ],
                [
                    'value' => $value
                ]);
            }
        }
    }
}
