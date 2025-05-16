<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Printer>
 */
class PrinterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['network', 'usb', 'serial', 'windows']);

        $attributes = [
            'title' => $this->faker->company() . ' Printer',
            'type' => $type,
            'characters_per_line' => $this->faker->randomElement([32, 40, 42, 48, 56]),
            'path' => null,
            'ip_address' => null,
            'port' => null,
        ];

        if ($type === 'network') {
            $attributes['ip_address'] = $this->faker->ipv4();
            $attributes['port'] = $this->faker->randomElement([9100, 631]);
        } elseif ($type === 'usb') {
            $attributes['path'] = '/dev/usb/lp' . $this->faker->numberBetween(0, 3);
        } elseif ($type === 'serial') {
            $attributes['path'] = 'COM' . $this->faker->numberBetween(1, 8);
        } elseif ($type === 'windows') {
            $attributes['path'] = $this->faker->slug(3, false); // e.g., "shared-printer-name" or just "Printer Name"
        }

        return $attributes;
    }
}
