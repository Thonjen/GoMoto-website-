<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MotorcycleSeeder extends Seeder
{
    public function run()
    {
        $motorcycles = [
            ['make' => 'Honda', 'model' => 'Giorno+', 'year' => '2025', 'transmission' => 'CVT (scooter)', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'CUV e:', 'year' => '2025 (Electric)', 'transmission' => 'CVT (electric)', 'fuel_type' => 'Electric'],
            ['make' => 'Honda', 'model' => 'ADV350', 'year' => '2025', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'CB1000 Hornet', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'Gold Wing', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'Navi', 'year' => '2025', 'transmission' => 'V-Matic (CVT)', 'fuel_type' => 'Gasoline'],
            ['make' => 'SYM', 'model' => 'Sport Rider 125i', 'year' => '2016–present', 'transmission' => '4-speed rotary', 'fuel_type' => 'Gasoline'],
            ['make' => 'Yamaha', 'model' => 'SZ‑x', 'year' => '2015–present (PH)', 'transmission' => '5-speed manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Yamaha', 'model' => 'RS‑100T', 'year' => '1977–2005', 'transmission' => '4-speed manual', 'fuel_type' => 'Gasoline (2-stroke)'],
            ['make' => 'Yamaha', 'model' => 'NMAX', 'year' => '2015–present', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'NWOW', 'model' => 'V15', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'F 800 GS', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'CE 02', 'year' => '2025', 'transmission' => 'Electric (EV)', 'fuel_type' => 'Electric'],
            ['make' => 'Suzuki', 'model' => 'Gixxer SF 155', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Aprilia', 'model' => 'RS 457', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Hero', 'model' => 'Hunk', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'CFMoto', 'model' => '250SR Lite', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'CFMoto', 'model' => '250NK Lite', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Triumph', 'model' => 'Bobber Icon', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Triumph', 'model' => 'Tiger Sport 800', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Triumph', 'model' => 'Speed Twin 1200 RS', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Bristol/Zontes', 'model' => '400E/D/G/M, 703F', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'KTM', 'model' => '390 Adv R / Enduro R / Adv X / SMC R', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'KTM', 'model' => '890 SMT', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'ZEEHO', 'model' => 'MO.1, EZ3, AE4 SE', 'year' => '2025', 'transmission' => 'CVT (electric)', 'fuel_type' => 'Electric'],
            ['make' => 'Royal Enfield', 'model' => 'Bear 650', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Royal Enfield', 'model' => 'Classic 650', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Royal Enfield', 'model' => 'Guerrilla 450', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Royal Enfield', 'model' => 'Shotgun 650', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Voge', 'model' => 'CU525', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'R12', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'R12 S / R12 nineT', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'BMW', 'model' => 'M 1000 XR', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Indian', 'model' => 'FTR series', 'year' => '2025', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Honda', 'model' => 'Click 125', 'year' => 'Current', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Suzuki', 'model' => 'Burgman Street EX', 'year' => 'Current', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Vespa', 'model' => 'S125', 'year' => 'Current', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Suzuki', 'model' => 'Skydrive', 'year' => 'Current', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Suzuki', 'model' => 'Avenis', 'year' => 'Current', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Haojue', 'model' => 'Lindy', 'year' => 'Current', 'transmission' => 'CVT', 'fuel_type' => 'Gasoline'],
            ['make' => 'Keeway', 'model' => 'CR152', 'year' => 'Modified custom', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Rusi', 'model' => 'Classic 250i', 'year' => 'Classic', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
            ['make' => 'Motorstar', 'model' => 'Cafe 400', 'year' => 'Classic', 'transmission' => 'Manual', 'fuel_type' => 'Gasoline'],
        ];

        DB::table('motorcycles')->insert($motorcycles);
    }
}
