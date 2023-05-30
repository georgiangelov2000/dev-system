<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;
use App\Models\Product;
class ProductFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $price = $this->faker->randomFloat(2, 0, 1000);
        $quantity = $this->faker->numberBetween(0, 100);
        $totalPrice = $price * $quantity;

        return [
            'name' => $this->faker->name,
            'supplier_id' => Supplier::inRandomOrder()->first()->id,
            'quantity' => $quantity,
            'price' => $price,
            'total_price' => $totalPrice,
            'initial_quantity' => $this->faker->numberBetween(0, 100),
            'notes' => $this->faker->text,
            'code' => $this->faker->unique()->regexify('[A-Za-z0-9]{20}'),
            'status' => $this->faker->randomElement(['enabled', 'disabled']),
            'is_paid' => 0,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
