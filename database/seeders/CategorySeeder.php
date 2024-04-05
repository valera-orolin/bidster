<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Real Estate',
                'subcategories' => ['New Buildings', 'Apartments', 'Rooms', 'Houses, Cottages', 'Garages and Parking', 'Plots', 'Commercial Real Estate'],
            ],
            [
                'name' => 'Auto and Parts',
                'subcategories' => ['Cars', 'Spare Parts', 'Trucks and Buses', 'Motorcycles', 'Agricultural Machinery', 'Special Equipment', 'Trailers', 'Water Transport', 'Accessories', 'Tires, Disks', 'Tools, Equipment', 'Auto Chemicals and Auto Oils'],
            ],
            [
                'name' => 'Household Appliances',
                'subcategories' => ['Kitchen Appliances', 'Large Kitchen Appliances', 'Cleaning Appliances', 'Clothing Care, Sewing', 'Climate Equipment', 'Beauty and Health Appliances'],
            ],
            [
                'name' => 'Computer Equipment',
                'subcategories' => ['Laptops', 'Computers / System Units', 'Monitors', 'Components', 'Office Equipment', 'Peripherals and Accessories', 'Networking Equipment', 'Other Computer Goods'],
            ],
            [
                'name' => 'Phones and Tablets',
                'subcategories' => ['Mobile Phones', 'Phone Components', 'Phone Accessories', 'Telephony and Communication', 'Tablets', 'Graphic Tablets', 'E-books', 'Smart Watches and Fitness Bracelets', 'Accessories for Tablets, Books, Watches'],
            ],
            [
                'name' => 'Electronics',
                'subcategories' => ['Audio Equipment', 'Headphones', 'TV and Video Equipment', 'Photo Equipment and Optics', 'Games and Consoles', 'Security and Smart Home'],
            ],
            [
                'name' => 'Women\'s Wardrobe',
                'subcategories' => ['Women\'s Clothing', 'Women\'s Shoes', 'Women\'s Accessories'],
            ],
            [
                'name' => 'Men\'s Wardrobe',
                'subcategories' => ['Men\'s Clothing', 'Men\'s Shoes', 'Men\'s Accessories'],
            ],            
            [
                'name' => 'Beauty and Health',
                'subcategories' => ['Decorative Cosmetics', 'Care Cosmetics', 'Perfumery', 'Manicure, Pedicure', 'Hair Products', 'Hygiene Products, Depilation', 'Eyelashes and Eyebrows, Tattooing', 'Cosmetic Accessories', 'Medical Goods'],
            ],   
            [
                'name' => 'Everything for Children and Moms',
                'subcategories' => ['Clothing up to 1 year', 'Girls\' Clothing', 'Boys\' Clothing', 'Children\'s Accessories', 'Children\'s Shoes', 'Walkers, Loungers, Swings', 'Strollers', 'Car Seats and Boosters', 'Feeding and Care', 'Children\'s Textiles', 'Baby Carriers and Slings', 'Toys and Books', 'Children\'s Transport', 'Goods for Moms', 'Maternity Clothes', 'Other Goods for Children'],
            ],
            [
                'name' => 'Furniture',
                'subcategories' => ['Benches, Poufs', 'Hangers, Hallways', 'Children\'s Furniture', 'Chests of Drawers', 'Beds, Mattresses', 'Kitchens', 'Kitchen Corners', 'Upholstered Furniture', 'Shelves, Racks, Cabinets', 'Bedroom Sets', 'Walls, Sections, Modules', 'Tables and Dining Groups', 'Chairs', 'Cabinets, Buffets', 'Wardrobes', 'Furniture Fittings and Components', 'Other Furniture'],
            ],
            [
                'name' => 'Everything for Home',
                'subcategories' => ['Interior Items, Mirrors', 'Curtains, Blinds, Cornices', 'Textiles and Carpets', 'Lighting', 'Household Goods', 'Dishes and Kitchen Accessories', 'Indoor Plants'],
            ],
            [
                'name' => 'Repair and Construction',
                'subcategories' => ['Construction Tool', 'Construction Equipment', 'Plumbing and Heating', 'Building Materials', 'Finishing Materials', 'Windows and Doors', 'Houses, Log Houses and Structures', 'Gates, Fences', 'Electrical Supply', 'Personal Protective Equipment', 'Other for Repair and Construction'],
            ],
            [
                'name' => 'Garden and Vegetable Garden',
                'subcategories' => ['Garden Furniture and Pools', 'Barbecues, Accessories, Fuel', 'Motoblocks and Cultivators', 'Garden Equipment', 'Garden Tools', 'Greenhouses and Greenhouses', 'Plants, Seedlings and Seeds', 'Fertilizers and Agrochemicals', 'Everything for the Beekeeper', 'Baths, Utility Blocks, Toilets', 'Other for the Garden and Vegetable Garden'],
            ],
            [
                'name' => 'Hobbies, Sports and Tourism',
                'subcategories' => ['CD, DVD, Records', 'Antiques and Collections', 'Tickets', 'Books and Magazines', 'Metal Detectors', 'Musical Instruments', 'Board Games and Puzzles', 'Hunting and Fishing', 'Tourist Goods', 'Radio Controlled Models', 'Handicraft', 'Sports Goods', 'Bicycles', 'Electric Transport', 'Other in Hobbies, Sports and Tourism'],
            ],
            [
                'name' => 'Wedding and Holidays',
                'subcategories' => ['Wedding Dresses', 'Wedding Suits', 'Wedding Shoes', 'Wedding Accessories', 'Gifts and Holiday Goods', 'Carnival Costumes'],
            ],
            [
                'name' => 'Animals',
                'subcategories' => ['Pets', 'Farm Animals', 'Goods for Animals', 'Animal Mating'],
            ],
            [
                'name' => 'Ready Business and Equipment',
                'subcategories' => ['Ready Business', 'Equipment for Business'],
            ],
            [
                'name' => 'Work',
                'subcategories' => ['Vacancies', 'Looking for Work'],
            ],
            [
                'name' => 'Services',
                'subcategories' => ['Services for Auto', 'Household Services', 'Computer Services, Internet', 'Repair of Equipment and Electronics', 'Beauty and Health', 'Nannies and Babysitters', 'Educational Services', 'Translator, Secretary Services', 'Passenger and Cargo Transportation', 'Advertising, Printing', 'Construction Works', 'Apartment, House Repair', 'Repair, Assembly, Upholstery of Furniture', 'Repair and Sewing of Clothes', 'Garden, Landscaping', 'Services for Animals', 'Photo and Video Shooting', 'Legal Services', 'Tourist Services', 'Services for Celebrations', 'Rent of Baths and Gazebos', 'Other Services'],
            ],
            [
                'name' => 'Other',
                'subcategories' => ['Lost and Found', 'Hookahs', 'Stationery', 'Food', 'Electronic Steam Generators', 'Demand', 'All the Rest'],
            ]                                                                                                                                                         
        ];

        foreach ($categories as $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
            ]);

            foreach ($categoryData['subcategories'] as $subcategoryName) {
                Subcategory::create([
                    'name' => $subcategoryName,
                    'category_id' => $category->id,
                ]);
            }
        }
    }
}
