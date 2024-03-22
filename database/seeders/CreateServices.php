<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceCategory;

class CreateServices extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = ServiceCategory::pluck('id')->toArray();

        $services = [
            [
                'service_name' => 'Solo Package 4',
                'category_id' => $category[0],
                'description' => 'Explore Solo Package 4: three curated poses in 3R size. Express yourself uniquely.',
                'price' => 200.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'Ii5M38sU12SpOoAUDW0OkYXhbouuRY-metaazk0U0NQUUZKU0k0eHhMeDFrQkU3dks0WXdWSkdmLW1ldGFVMjlzYnlCd1lXTnJZV2RsTFRRdWNHNW4tLnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Solo Package 3',
                'category_id' => $category[1],
                'description' => 'Discover your essence with Solo Package 3: 3 poses, 6 prints. Effortless booking, diverse options, personalized expression.',
                'price' => 170.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'tlzGFtTnKoJWwK6k35Ow9mKgQZJuiO-metaVlpBSHpWVlROSnhSNXdwdU9zQkc1Snc4T1pVQkFxLW1ldGFVMjlzYnlCd1lXTnJZV2RsTFRNdWNHNW4tLnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Solo Package 3',
                'category_id' => $category[1],
                'description' => 'Discover your essence with Solo Package 3: 3 poses, 6 prints. Effortless booking, diverse options, personalized expression.',
                'price' => 170.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'tlzGFtTnKoJWwK6k35Ow9mKgQZJuiO-metaVlpBSHpWVlROSnhSNXdwdU9zQkc1Snc4T1pVQkFxLW1ldGFVMjlzYnlCd1lXTnJZV2RsTFRNdWNHNW4tLnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Solo Package 2',
                'category_id' => $category[1],
                'description' => 'Engage in Solo Package 2: 2 poses, 3 prints. Effortless booking, varied formats, personalized storytelling.',
                'price' => 130.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'SOfSen39YhOB0ZWrHCqCn8OvqPQjym-metac25LMjR0ckdvQXlROXo2SG13VTNxcU1PazNlaWE2LW1ldGFVMjlzYnlCd1lXTnJZV2RsTFRJdWNHNW4tLnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Solo Package 1',
                'category_id' => $category[1],
                'description' => 'Explore Solo Package 1: 2 poses, 4 prints. Easy booking, customizable, capture your unique style.',
                'price' => 120.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'b4ojAQRSKhbAMIuK0Rf2KiG5QsDwba-metaUllpb0JZS21FTElSMVppUnU2ckpKcmFjeHlPalNNLW1ldGFVMjlzYnlCd1lXTnJZV2RsTFRFdWNHNW4tLnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Solo Package 5',
                'category_id' => $category[1],
                'description' => 'Embrace Solo Package 5: 3 poses, 9 prints. Easy booking, diverse formats, showcase your essence.',
                'price' => 200.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'vQ8okbdjw5B5LRS7aNe5fMpwMiAToO-metaZkdjMXpVS2dPbFEyTFpnYmJ2cmc5V3lUWGRNYTF2LW1ldGFVMjlzYnlCd1lXTnJZV2RsTFRVdWNHNW4tLnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Solo Package 6',
                'category_id' => $category[1],
                'description' => 'Discover Solo Package 6: 4 poses, 9 prints. Effortless booking, diverse formats, stunning self-expression.',
                'price' => 250.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'pzbtRCYEDYlcNSuJSXORiDPMTPTw1I-metaTEVIb0VCN1ZkUnN0Q0Z1MDVjdk0xcUlmWTVUTkJxLW1ldGFVMjlzYnlCd1lXTnJZV2RsTFRZdWNHNW4tLnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Solo Package 3',
                'category_id' => $category[1],
                'description' => '4 2x2 Pictures & 6 1x1 Pictures',
                'price' => 80.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => '6CKDqG0ErxqKHnr02uWObLu3xT4FAn-metaYUZwSkkwUU1OYldpN1FkRng1cEFkZDFjd0RFTkxJLW1ldGFOQzB5V0RJZ05pMHhXREV1Y0c1bi0ucG5n-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'ID Package 2',
                'category_id' => $category[1],
                'description' => '6 pcs 2x2 Pictures',
                'price' => 80.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'RbdGCfNwNVIUMJb5BtQqB1FxCjuW4t-metaOTBJQTNlTUczR2Rja2l5eWlIRTlvUTZmS1ZnWU1MLW1ldGFOaTF3WTNNZ0xTQXlXREl1Y0c1bi0ucG5n-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'ID Package 1',
                'category_id' => $category[1],
                'description' => '6 pcs Passport size pictures.',
                'price' => 90.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'iVPPkukVkec1LUQzbrOp81yscDuvHS-metaM1VabHBPN2YxQW5rdzhTUVdOMUtqTjF3anZoeHNlLW1ldGFOaTF3WTNNZ2NHRnpjM0J2Y25RZ2MybDZaUzV3Ym1jPS0ucG5n-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'ID Size Lamination',
                'category_id' => $category[2],
                'description' => 'Protect your IDs with our lamination service. Add to cart, choose your payment method, and bring your ID on the appointment date for efficient lamination.',
                'price' => 15.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => '8AAQZEtLKpUwaxEP4LsAeDh1ZnUVZB-metaRkYyQ3h4MHc2b3VYYk5HRjdUOER5MXpNbEIxZWVVLW1ldGFTV1FnYzJsNlpTQnNZVzFwYm1GMGFXOXVJQzV3Ym1jPS0ucG5n-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 2ft x 3ft',
                'category_id' => $category[1],
                'description' => 'Compact yet impactful: 2ft x 3ft tarpaulin printing for personalized messages or designs.',
                'price' => 72.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'u2BnYqKacLAzt1FlUIaph13nQreXTo-metaMmNtX18xXy1yZW1vdmViZy1wcmV2aWV3LnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 3ft x 4ft',
                'category_id' => $category[0],
                'description' => 'Versatile 3ft x 4ft tarpaulin printing: perfect for events, promotions, balanced size for standout visuals.',
                'price' => 144.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => '7L6047wfWYX7c31TsnUlgp5uvPOuve-metaMmNtX18yXy1yZW1vdmViZy1wcmV2aWV3LnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 3ft x 5ft',
                'category_id' => $category[0],
                'description' => 'Ideal for ads and displays: 3ft x 5ft tarpaulin printing. Ample space for vibrant graphics, high visibility.',
                'price' => 180.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'IMZWVfPzasa8HhvfQranq2cz9WPzpJ-metaMmNtX18zXy1yZW1vdmViZy1wcmV2aWV3LnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 3ft x 6ft',
                'category_id' => $category[0],
                'description' => 'Extended space, impactful designs: 3ft x 6ft tarpaulin printing for larger events or storefronts.',
                'price' => 240.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'mBdjzwy6L3xGhlrbV5KYpo2IFYdtpy-metaNC1yZW1vdmViZy1wcmV2aWV3ICgxKS5wbmc=-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 4ft x 6ft',
                'category_id' => $category[0],
                'description' => 'Ideal for trade shows and impactful signage: 4ft x 6ft tarpaulin printing for substantial display area.',
                'price' => 288.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'szGFEz5TZypKfjIgvRerKnXqOPVqmk-metaNi1yZW1vdmViZy1wcmV2aWV3LnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 4ft x 8ft',
                'category_id' => $category[0],
                'description' => 'Statement-making size, versatile setting: 4ft x 8ft tarpaulin printing for detailed graphics and info.',
                'price' => 340.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => '0EyPzs0b8YyDnuZ6klP9VXKYSymyGl-metaNy1yZW1vdmViZy1wcmV2aWV3LnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 5ft x 6ft',
                'category_id' => $category[0],
                'description' => 'Landscape versatility: 5ft x 6ft tarpaulin printing for panoramics or broad messaging.',
                'price' => 360.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'EAQq5BVGZFXYAfvcjhrG1808BYklr6-metaOC1yZW1vdmViZy1wcmV2aWV3LnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 5ft x 10ft',
                'category_id' => $category[0],
                'description' => 'Max impact, expansive canvas: 5ft x 10ft tarpaulin printing for elaborate designs or info.',
                'price' => 600.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'vcKBVgSXjsoNtB1GDYtBGsat18xpQT-metaOS1yZW1vdmViZy1wcmV2aWV3LnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Tarpaulin Printing - 6ft x 10ft',
                'category_id' => $category[0],
                'description' => 'Ultimate grand canvas: 6ft x 10ft tarpaulin printing for bold, detailed displays.',
                'price' => 720.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'lgUXlvQviLzvuczslpVTnpyZIBz3BQ-metaMTEtcmVtb3ZlYmctcHJldmlldy5wbmc=-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Mug Printing - White',
                'category_id' => $category[2],
                'description' => '""
                Introducing our timeless Normal Mugs – classic design, versatile 15 oz capacity, and durable construction for your daily sipping pleasure. Enjoy your favorite hot or cold beverages with a comfortable grip. Personalize them to make each sip uniquely yours. Elevate simplicity with our enduring Normal Mugs.\n
                \n
                *Note: Enjoy special pricing with 10 pieces or more at 100 pesos each.
                ""',
                'price' => 150.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'i9wV8GvNd4Z0pEt85KHVtjFQi4JqTX-metaU2NyZWVuc2hvdF8yMDI0LTAyLTE5LTEzLTE1LTI3LTc4Xzk5YzA0ODE3YzBkZTU2NTIzOTdmYzhiNTZjM2IzODE3LmpwZw==-.jpg',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Mug Printing - Inner Color',
                'category_id' => $category[2],
                'description' => '""
                Introducing our Mug Printing with Inner Color 15 oz – surprise yourself with a burst of vibrant color as you sip. Personalize the outer design, combining style with functionality for both hot and cold beverages. Crafted for durability, these mugs elevate your daily ritual with a delightful touch.\n
                \n
                *Note: Enjoy special pricing with 10 pieces or more at 110 pesos each.
                ""',
                'price' => 160.00,
                'duration_in_days' => 3,
                'availability_status' => 'Available',
                'service_avatar' => 'NImJWrNYqwPgShPIKpPIqW0Hb5f6M2-metacmVjZWl2ZWRfMjQ3Nzc4ODQyMDE4MjU5ODcuanBlZw==-.jpg',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'White T-shirt Printing - A4 Size',
                'category_id' => $category[2],
                'description' => 'Explore our Personalized White T-shirt Printing with an A4 canvas (21.0 x 29.7 cm) for your unique designs. We accept T-shirts of any size provided by you. Please double-check your order, as we are not responsible for inaccuracies. Elevate your style with tailor-made creations expressing your individuality!',
                'price' => 80.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'x1k4HWcoZNejZQQDY0erxUYxxLR8Cg-metaMy1yZW1vdmViZy1wcmV2aWV3ICgxKS5wbmc=-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'White T-shirt Printing - Half A4 Size',
                'category_id' => $category[2],
                'description' => 'Introducing our Personalized White T-shirt Printing service, where your creativity takes center stage! Our A4 size canvas of 14.8 x 21.0 cm provides the perfect space for your unique designs. Whether its a bold statement or a subtle expression, we welcome any size of T-shirt provided by the customer. All personalized designs are meticulously crafted to order, with T-shirts provided by you, our valued customer. We kindly urge you to double-check your order before completing the checkout process, as we cannot be held liable for any inaccuracies. Elevate your style and make a statement with custom creations that reflect your individuality – because your uniqueness deserves to be worn!',
                'price' => 40.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'qKD8v8gTrVKz4PKPokUPGvWQlkzaHH-metaNC1yZW1vdmViZy1wcmV2aWV3ICgyKS5wbmc=-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Colored T-shirt Printing - A4 Size',
                'category_id' => $category[2],
                'description' => 'Introducing our Personalized Colored T-shirt Printing service, featuring an A4 size canvas of 21 x 29.7 cm for your unique designs. We accept any size of T-shirt, and all personalized designs are made to order with T-shirts provided by you, the customer. Please double-check your order before checking out, as we are not liable for any wrong item ordered.',
                'price' => 100.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'TbCtPwZ3ZME45FUW9qdBZ5bvusrYle-metaQTQgU0laRSBDT0xPUkVELnBuZw==-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Colored T-Shirt Printing -Half A4 size',
                'category_id' => $category[2],
                'description' => 'Introducing our Personalized Colored T-shirt Printing service, featuring an A4 size canvas of 21 x 29.7 cm for your unique designs. We accept any size of T-shirt, and all personalized designs are made to order with T-shirts provided by you, the customer. Please double-check your order before checking out, as we are not liable for any wrong item ordered.',
                'price' => 40.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'X8cdx1e8Z9FTVw1sMpMaPw5FcUIOSp-metaNS1yZW1vdmViZy1wcmV2aWV3ICgxKS5wbmc=-.png',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Personalized Magic Mug',
                'category_id' => $category[0],
                'description' => '""
                Discover the enchantment with our Personalized 15 oz Magic Mugs! This heat-changing coffee mug appears ordinary until you pour in hot liquid – watch as your cherished memories or favorite photo magically emerge before your eyes. Transform your beverage experience, add a personal touch, and relish your favorite drinks in a whole new way with our Magic Mugs!\n
                \n
                *Note: Enjoy special pricing with 10 pieces or more at 150 pesos each.
                ""',
                'price' => 200.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'iKjcnGMBym2oyqEIffz6joV56qcwUE-metaTUFHSUMtTVVHLVlPVVItREVTSUdOLUhFUkUuanBn-.jpg',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'Photocopy (Short, Long, A4) per copy',
                'category_id' => $category[0],
                'description' => '""
                Discover the enchantment with our Personalized 15 oz Magic Mugs! This heat-changing coffee mug appears ordinary until you pour in hot liquid – watch as your cherished memories or favorite photo magically emerge before your eyes. Transform your beverage experience, add a personal touch, and relish your favorite drinks in a whole new way with our Magic Mugs!\n
                \n
                *Note: Enjoy special pricing with 10 pieces or more at 150 pesos each.
                ""',
                'price' => 3.00,
                'duration_in_days' => 1,
                'availability_status' => 'Available',
                'service_avatar' => 'bM9FAtMelTMGjKxNi0iLoYQNn1FSNh-metaMTU5NjA5ODM2XzczMDgwODMwNzU5NjQ3OF82MjI1MDg4MDQzNDc4ODI0NzJfbi5qcGc=-.jpg',
                'deleted_at' => null,
            ],
            [
                'service_name' => 'ID Package 4',
                'category_id' => $category[1],
                'description' => 'Passport size 6 pieces',
                'price' => 80.00,
                'duration_in_days' => null,
                'availability_status' => 'Available',
                'service_avatar' => 'yUxA6AhwW2TVEyWeKfUFAu04wY8qKC-metaMzEyNDY0NzE2XzY1MjA4ODI3MzEwOTc2Nl83NTUyMTA1MjEyMDU5MzE1MjY4X24uanBn-.jpg',
                'deleted_at' => null,
            ],
        ];

        foreach($services as $newService){
            Service::create($newService);
        }
    }
}
