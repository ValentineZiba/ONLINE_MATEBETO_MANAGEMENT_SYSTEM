<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedUsers();
        $this->seedTables();
        $this->seedCategories();
        $this->seedMenuItems();
        $this->seedCoupons();
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'restaurant_name', 'value' => 'Matebeto Restaurant', 'group' => 'general'],
            ['key' => 'restaurant_tagline', 'value' => 'Where Every Meal is a Memory', 'group' => 'general'],
            ['key' => 'restaurant_email', 'value' => 'info@matebeto.com', 'group' => 'general'],
            ['key' => 'restaurant_phone', 'value' => '+260 977 123 456', 'group' => 'general'],
            ['key' => 'restaurant_address', 'value' => '123 Cairo Road, Lusaka, Zambia', 'group' => 'general'],
            ['key' => 'restaurant_latitude', 'value' => '-15.4167', 'group' => 'general'],
            ['key' => 'restaurant_longitude', 'value' => '28.2833', 'group' => 'general'],
            ['key' => 'opening_hours', 'value' => 'Mon-Sun: 7:00 AM - 11:00 PM', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'K', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => 'K', 'group' => 'general'],
            ['key' => 'tax_rate', 'value' => '16', 'group' => 'billing'],
            ['key' => 'delivery_fee', 'value' => '150', 'group' => 'billing'],
            ['key' => 'delivery_base_fee', 'value' => '30', 'group' => 'billing'],
            ['key' => 'delivery_rate_per_km', 'value' => '15', 'group' => 'billing'],
            ['key' => 'min_order_amount', 'value' => '500', 'group' => 'billing'],
            ['key' => 'max_reservation_party', 'value' => '20', 'group' => 'reservations'],
            ['key' => 'reservation_lead_time', 'value' => '2', 'group' => 'reservations'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/matebeto', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/matebeto', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/matebeto', 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }

    private function seedUsers(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'admin@matebeto.com', 'role' => 'admin', 'phone' => '+260977000001'],
            ['name' => 'Restaurant Manager', 'email' => 'manager@matebeto.com', 'role' => 'manager', 'phone' => '+260977000002'],
            ['name' => 'Head Chef', 'email' => 'kitchen@matebeto.com', 'role' => 'kitchen', 'phone' => '+260977000003'],
            ['name' => 'John Waiter', 'email' => 'waiter@matebeto.com', 'role' => 'waiter', 'phone' => '+260977000004'],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['password' => Hash::make('password'), 'email_verified_at' => now()])
            );
        }
    }

    private function seedTables(): void
    {
        $tables = [
            ['number' => 'T01', 'capacity' => 2, 'location' => 'indoor', 'status' => 'available'],
            ['number' => 'T02', 'capacity' => 2, 'location' => 'indoor', 'status' => 'available'],
            ['number' => 'T03', 'capacity' => 4, 'location' => 'indoor', 'status' => 'available'],
            ['number' => 'T04', 'capacity' => 4, 'location' => 'indoor', 'status' => 'occupied'],
            ['number' => 'T05', 'capacity' => 4, 'location' => 'indoor', 'status' => 'reserved'],
            ['number' => 'T06', 'capacity' => 6, 'location' => 'indoor', 'status' => 'available'],
            ['number' => 'T07', 'capacity' => 6, 'location' => 'outdoor', 'status' => 'available'],
            ['number' => 'T08', 'capacity' => 4, 'location' => 'outdoor', 'status' => 'available'],
            ['number' => 'T09', 'capacity' => 4, 'location' => 'outdoor', 'status' => 'available'],
            ['number' => 'T10', 'capacity' => 8, 'location' => 'outdoor', 'status' => 'available'],
            ['number' => 'B01', 'capacity' => 2, 'location' => 'bar', 'status' => 'available'],
            ['number' => 'B02', 'capacity' => 2, 'location' => 'bar', 'status' => 'available'],
            ['number' => 'B03', 'capacity' => 2, 'location' => 'bar', 'status' => 'available'],
            ['number' => 'P01', 'capacity' => 12, 'location' => 'private', 'status' => 'available'],
            ['number' => 'P02', 'capacity' => 20, 'location' => 'private', 'status' => 'available'],
        ];

        foreach ($tables as $table) {
            RestaurantTable::updateOrCreate(['number' => $table['number']], $table);
        }
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Breakfast', 'slug' => 'breakfast', 'description' => 'Start your day right with our hearty breakfast options', 'icon' => '🍳', 'sort_order' => 1],
            ['name' => 'Appetizers', 'slug' => 'appetizers', 'description' => 'Perfect starters to awaken your appetite', 'icon' => '🥗', 'sort_order' => 2],
            ['name' => 'Soups & Salads', 'slug' => 'soups-salads', 'description' => 'Fresh, healthy and delicious soups and salads', 'icon' => '🥣', 'sort_order' => 3],
            ['name' => 'Main Course', 'slug' => 'main-course', 'description' => 'Our signature main dishes prepared with love', 'icon' => '🍽️', 'sort_order' => 4],
            ['name' => 'Grills & BBQ', 'slug' => 'grills-bbq', 'description' => 'Flame-grilled to perfection', 'icon' => '🥩', 'sort_order' => 5],
            ['name' => 'Seafood', 'slug' => 'seafood', 'description' => 'Fresh catch from the ocean to your plate', 'icon' => '🦞', 'sort_order' => 6],
            ['name' => 'Vegetarian', 'slug' => 'vegetarian', 'description' => 'Plant-based delights for every palate', 'icon' => '🥦', 'sort_order' => 7],
            ['name' => 'Pasta & Rice', 'slug' => 'pasta-rice', 'description' => 'Comfort foods done right', 'icon' => '🍝', 'sort_order' => 8],
            ['name' => 'Desserts', 'slug' => 'desserts', 'description' => 'Sweet endings to a perfect meal', 'icon' => '🍰', 'sort_order' => 9],
            ['name' => 'Beverages', 'slug' => 'beverages', 'description' => 'Refreshing drinks to complement your meal', 'icon' => '🥤', 'sort_order' => 10],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }
    }

    private function seedMenuItems(): void
    {
        $items = [
            // Breakfast
            ['category' => 'breakfast', 'name' => 'Full English Breakfast', 'description' => 'Eggs, bacon, sausages, toast, baked beans, grilled tomatoes and mushrooms', 'price' => 850, 'calories' => 750, 'prep' => 20, 'featured' => true],
            ['category' => 'breakfast', 'name' => 'Avocado Toast', 'description' => 'Sourdough toast topped with smashed avocado, poached eggs and cherry tomatoes', 'price' => 650, 'calories' => 420, 'prep' => 12, 'vegetarian' => true],
            ['category' => 'breakfast', 'name' => 'Pancake Stack', 'description' => 'Fluffy pancakes with maple syrup, fresh berries and whipped cream', 'price' => 550, 'calories' => 680, 'prep' => 15, 'vegetarian' => true],
            ['category' => 'breakfast', 'name' => 'Eggs Benedict', 'description' => 'Poached eggs on English muffins with Canadian bacon and hollandaise sauce', 'price' => 750, 'calories' => 580, 'prep' => 18],
            ['category' => 'breakfast', 'name' => 'Acai Bowl', 'description' => 'Blended acai with banana, topped with granola, fresh fruits and honey', 'price' => 700, 'calories' => 380, 'prep' => 10, 'vegan' => true, 'featured' => true],

            // Appetizers
            ['category' => 'appetizers', 'name' => 'Chicken Wings (6 pcs)', 'description' => 'Crispy wings in your choice of BBQ, buffalo or honey garlic sauce', 'price' => 850, 'calories' => 520, 'prep' => 20, 'spicy' => true, 'featured' => true],
            ['category' => 'appetizers', 'name' => 'Calamari Rings', 'description' => 'Golden fried squid rings with tartar sauce and lemon wedge', 'price' => 750, 'calories' => 380, 'prep' => 15],
            ['category' => 'appetizers', 'name' => 'Bruschetta', 'description' => 'Toasted baguette with diced tomatoes, basil, garlic and olive oil', 'price' => 550, 'calories' => 280, 'prep' => 10, 'vegetarian' => true, 'vegan' => true],
            ['category' => 'appetizers', 'name' => 'Loaded Nachos', 'description' => 'Tortilla chips with melted cheese, jalapeños, sour cream, guacamole and salsa', 'price' => 900, 'calories' => 650, 'prep' => 15, 'vegetarian' => true, 'spicy' => true],
            ['category' => 'appetizers', 'name' => 'Spinach & Artichoke Dip', 'description' => 'Creamy dip served warm with tortilla chips and pita bread', 'price' => 700, 'calories' => 420, 'prep' => 18, 'vegetarian' => true],

            // Soups & Salads
            ['category' => 'soups-salads', 'name' => 'Cream of Tomato Soup', 'description' => 'Velvety smooth tomato bisque with fresh cream and basil oil', 'price' => 450, 'calories' => 220, 'prep' => 10, 'vegetarian' => true, 'gluten_free' => true],
            ['category' => 'soups-salads', 'name' => 'Caesar Salad', 'description' => 'Romaine lettuce, parmesan, croutons and caesar dressing', 'price' => 650, 'calories' => 350, 'prep' => 10],
            ['category' => 'soups-salads', 'name' => 'Greek Salad', 'description' => 'Fresh tomatoes, cucumber, olives, red onion and feta cheese', 'price' => 600, 'calories' => 280, 'prep' => 8, 'vegetarian' => true, 'gluten_free' => true],
            ['category' => 'soups-salads', 'name' => 'Oxtail Soup', 'description' => 'Rich slow-cooked oxtail broth with vegetables and herbs', 'price' => 950, 'calories' => 380, 'prep' => 15, 'gluten_free' => true],
            ['category' => 'soups-salads', 'name' => 'Quinoa Power Bowl', 'description' => 'Quinoa with roasted vegetables, avocado, chickpeas and tahini dressing', 'price' => 850, 'calories' => 420, 'prep' => 12, 'vegan' => true, 'gluten_free' => true, 'featured' => true],

            // Main Course
            ['category' => 'main-course', 'name' => 'Nyama Choma Platter', 'description' => 'Traditional Zambian roasted goat meat served with nshima and kachumbari', 'price' => 1800, 'calories' => 850, 'prep' => 45, 'gluten_free' => true, 'featured' => true],
            ['category' => 'main-course', 'name' => 'Beef Burger', 'description' => '250g beef patty with cheese, lettuce, tomato and special sauce in a brioche bun', 'price' => 1200, 'calories' => 780, 'prep' => 20],
            ['category' => 'main-course', 'name' => 'Chicken Tikka Masala', 'description' => 'Tender chicken in a rich spiced tomato cream sauce served with naan', 'price' => 1400, 'calories' => 620, 'prep' => 30, 'spicy' => true, 'featured' => true],
            ['category' => 'main-course', 'name' => 'Fish & Chips', 'description' => 'Beer-battered tilapia fillet with crispy chips and mushy peas', 'price' => 1200, 'calories' => 720, 'prep' => 25],
            ['category' => 'main-course', 'name' => 'Roast Chicken', 'description' => 'Half roasted chicken with roasted potatoes, seasonal vegetables and gravy', 'price' => 1500, 'calories' => 680, 'prep' => 35, 'gluten_free' => true],

            // Grills & BBQ
            ['category' => 'grills-bbq', 'name' => 'Ribeye Steak 300g', 'description' => 'Premium ribeye steak grilled to your preference with garlic butter and fries', 'price' => 2800, 'calories' => 780, 'prep' => 25, 'gluten_free' => true, 'featured' => true],
            ['category' => 'grills-bbq', 'name' => 'BBQ Pork Ribs', 'description' => 'Slow-cooked baby back ribs with our signature BBQ glaze', 'price' => 2200, 'calories' => 920, 'prep' => 30],
            ['category' => 'grills-bbq', 'name' => 'Mixed Grill Platter', 'description' => 'Beef steak, chicken, lamb chops and sausages with grilled vegetables', 'price' => 3500, 'calories' => 1100, 'prep' => 35, 'featured' => true],
            ['category' => 'grills-bbq', 'name' => 'Lamb Chops', 'description' => 'Herb-marinated lamb chops with mint jelly and roasted vegetables', 'price' => 2500, 'calories' => 680, 'prep' => 30, 'gluten_free' => true],
            ['category' => 'grills-bbq', 'name' => 'Chicken Skewers', 'description' => 'Marinated chicken breast skewers with pilau rice and tzatziki', 'price' => 1600, 'calories' => 520, 'prep' => 25],

            // Seafood
            ['category' => 'seafood', 'name' => 'Grilled Nile Perch', 'description' => 'Fresh Nile perch grilled with lemon butter, herbs and served with fries', 'price' => 1800, 'calories' => 480, 'prep' => 25, 'gluten_free' => true, 'featured' => true],
            ['category' => 'seafood', 'name' => 'Prawn Curry', 'description' => 'Tiger prawns in a coconut cream curry with steamed rice', 'price' => 2200, 'calories' => 520, 'prep' => 30, 'spicy' => true],
            ['category' => 'seafood', 'name' => 'Seafood Pasta', 'description' => 'Linguine with prawns, calamari, mussels in a white wine cream sauce', 'price' => 1900, 'calories' => 680, 'prep' => 25],
            ['category' => 'seafood', 'name' => 'Lobster Thermidor', 'description' => 'Whole lobster with cream sauce, gruyère cheese, gratinated', 'price' => 4500, 'calories' => 580, 'prep' => 35, 'gluten_free' => true],
            ['category' => 'seafood', 'name' => 'Swordfish Steak', 'description' => 'Grilled swordfish with Mediterranean salsa and roasted potatoes', 'price' => 2400, 'calories' => 440, 'prep' => 25, 'gluten_free' => true],

            // Vegetarian
            ['category' => 'vegetarian', 'name' => 'Mushroom Risotto', 'description' => 'Creamy arborio rice with wild mushrooms, truffle oil and parmesan', 'price' => 1200, 'calories' => 580, 'prep' => 30, 'vegetarian' => true, 'gluten_free' => true, 'featured' => true],
            ['category' => 'vegetarian', 'name' => 'Veggie Buddha Bowl', 'description' => 'Brown rice, roasted vegetables, tofu, edamame and sesame dressing', 'price' => 1100, 'calories' => 420, 'prep' => 20, 'vegan' => true, 'gluten_free' => true],
            ['category' => 'vegetarian', 'name' => 'Eggplant Parmesan', 'description' => 'Breaded eggplant with marinara sauce, mozzarella and parmesan', 'price' => 1000, 'calories' => 520, 'prep' => 25, 'vegetarian' => true],
            ['category' => 'vegetarian', 'name' => 'Falafel Wrap', 'description' => 'Crispy falafel in a warm flatbread with hummus, salad and tahini', 'price' => 850, 'calories' => 480, 'prep' => 15, 'vegan' => true],

            // Pasta & Rice
            ['category' => 'pasta-rice', 'name' => 'Spaghetti Bolognese', 'description' => 'Classic meat sauce with spaghetti, parmesan and garlic bread', 'price' => 1100, 'calories' => 720, 'prep' => 20, 'featured' => true],
            ['category' => 'pasta-rice', 'name' => 'Chicken Alfredo', 'description' => 'Fettuccine in a rich cream sauce with grilled chicken and parmesan', 'price' => 1300, 'calories' => 780, 'prep' => 20],
            ['category' => 'pasta-rice', 'name' => 'Biryani', 'description' => 'Fragrant basmati rice with spiced chicken, raita and papadum', 'price' => 1400, 'calories' => 650, 'prep' => 35, 'spicy' => true, 'featured' => true],
            ['category' => 'pasta-rice', 'name' => 'Coconut Rice & Beans', 'description' => 'Creamy coconut rice served with spiced black beans and plantains', 'price' => 800, 'calories' => 420, 'prep' => 20, 'vegan' => true, 'gluten_free' => true],

            // Desserts
            ['category' => 'desserts', 'name' => 'Chocolate Lava Cake', 'description' => 'Warm chocolate cake with molten center, served with vanilla ice cream', 'price' => 650, 'calories' => 520, 'prep' => 15, 'vegetarian' => true, 'featured' => true],
            ['category' => 'desserts', 'name' => 'Cheesecake', 'description' => 'New York style cheesecake with seasonal berry compote', 'price' => 600, 'calories' => 480, 'prep' => 5, 'vegetarian' => true],
            ['category' => 'desserts', 'name' => 'Tiramisu', 'description' => 'Classic Italian dessert with espresso-soaked ladyfingers and mascarpone', 'price' => 650, 'calories' => 450, 'prep' => 5, 'vegetarian' => true],
            ['category' => 'desserts', 'name' => 'Mango Sorbet', 'description' => 'Refreshing house-made mango sorbet with fresh mint', 'price' => 450, 'calories' => 180, 'prep' => 5, 'vegan' => true, 'gluten_free' => true],
            ['category' => 'desserts', 'name' => 'Crème Brûlée', 'description' => 'Classic French custard with caramelized sugar topping', 'price' => 700, 'calories' => 420, 'prep' => 5, 'vegetarian' => true, 'gluten_free' => true],

            // Beverages
            ['category' => 'beverages', 'name' => 'Fresh Mango Juice', 'description' => 'Cold-pressed fresh mango juice, no added sugar', 'price' => 350, 'calories' => 140, 'prep' => 5, 'vegan' => true, 'gluten_free' => true, 'featured' => true],
            ['category' => 'beverages', 'name' => 'Dawa Cocktail', 'description' => 'East African classic: vodka, fresh lime, honey and crushed ice', 'price' => 800, 'calories' => 220, 'prep' => 5, 'gluten_free' => true],
            ['category' => 'beverages', 'name' => 'Passion Fruit Lemonade', 'description' => 'Fresh lemonade blended with passion fruit, mint and ice', 'price' => 400, 'calories' => 160, 'prep' => 5, 'vegan' => true, 'gluten_free' => true],
            ['category' => 'beverages', 'name' => 'Zambian Chai', 'description' => 'Spiced masala tea brewed with fresh ginger and cardamom', 'price' => 200, 'calories' => 80, 'prep' => 8, 'vegetarian' => true, 'gluten_free' => true],
            ['category' => 'beverages', 'name' => 'Cold Brew Coffee', 'description' => '24-hour cold-brewed Zambian coffee over ice with milk', 'price' => 450, 'calories' => 120, 'prep' => 3, 'vegetarian' => true, 'gluten_free' => true],
            ['category' => 'beverages', 'name' => 'Tropical Smoothie', 'description' => 'Mango, pineapple, banana, coconut milk and chia seeds', 'price' => 500, 'calories' => 280, 'prep' => 5, 'vegan' => true, 'gluten_free' => true],
        ];

        foreach ($items as $item) {
            $category = Category::where('slug', $item['category'])->first();
            if (!$category) continue;

            MenuItem::updateOrCreate(
                ['name' => $item['name']],
                [
                    'category_id' => $category->id,
                    'slug' => Str::slug($item['name']) . '-' . Str::random(4),
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'calories' => $item['calories'],
                    'preparation_time' => $item['prep'],
                    'is_available' => true,
                    'is_featured' => $item['featured'] ?? false,
                    'is_vegetarian' => $item['vegetarian'] ?? false,
                    'is_vegan' => $item['vegan'] ?? false,
                    'is_gluten_free' => $item['gluten_free'] ?? false,
                    'is_spicy' => $item['spicy'] ?? false,
                ]
            );
        }
    }

    private function seedCoupons(): void
    {
        $coupons = [
            ['code' => 'WELCOME20', 'description' => '20% off your first order', 'type' => 'percentage', 'value' => 20, 'min_order_amount' => 1000, 'max_discount' => 500, 'max_uses' => 500],
            ['code' => 'FLAT200', 'description' => 'Flat K 200 off any order', 'type' => 'fixed', 'value' => 200, 'min_order_amount' => 1500],
            ['code' => 'WEEKEND15', 'description' => '15% off weekend orders', 'type' => 'percentage', 'value' => 15, 'min_order_amount' => 800, 'max_discount' => 300],
            ['code' => 'BIRTHDAY50', 'description' => '50% off on your birthday', 'type' => 'percentage', 'value' => 50, 'min_order_amount' => 2000, 'max_discount' => 1000, 'max_uses' => 1],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(['code' => $coupon['code']], array_merge($coupon, [
                'is_active' => true,
                'expires_at' => now()->addYear(),
            ]));
        }
    }
}
