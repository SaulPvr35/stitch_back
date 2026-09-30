<?php

namespace Database\Seeders;

use App\Models\ShopItem;
use Illuminate\Database\Seeder;

class ShopItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['slug' => 'skin-explorador-solar', 'name' => 'Traje solar', 'category' => 'skins', 'description' => 'Un traje brillante para explorar con energía.', 'price' => 35, 'icon' => 'user', 'metadata' => ['effect' => 'skin', 'value' => 'solar']],
            ['slug' => 'skin-explorador-oceano', 'name' => 'Traje oceánico', 'category' => 'skins', 'description' => 'Colores del mar para tu próximo viaje.', 'price' => 45, 'icon' => 'user', 'metadata' => ['effect' => 'skin', 'value' => 'oceano']],
            ['slug' => 'theme-bosque-estelar', 'name' => 'Bosque estelar', 'category' => 'temas', 'description' => 'Un tema nocturno para el mapa de aventuras.', 'price' => 30, 'icon' => 'tree', 'metadata' => ['effect' => 'theme', 'value' => 'estelar']],
            ['slug' => 'theme-oceano-aventura', 'name' => 'Océano aventurero', 'category' => 'temas', 'description' => 'Una interfaz fresca con colores del mar.', 'price' => 35, 'icon' => 'tree', 'metadata' => ['effect' => 'theme', 'value' => 'oceano']],
            ['slug' => 'theme-pradera-viva', 'name' => 'Pradera viva', 'category' => 'temas', 'description' => 'Verdes luminosos para estudiar con calma.', 'price' => 35, 'icon' => 'tree', 'metadata' => ['effect' => 'theme', 'value' => 'pradera']],
            ['slug' => 'theme-candy', 'name' => 'Dulce aventura', 'category' => 'temas', 'description' => 'Un tema alegre con tonos dulces y brillantes.', 'price' => 40, 'icon' => 'star', 'metadata' => ['effect' => 'theme', 'value' => 'dulce']],
            ['slug' => 'theme-volcan', 'name' => 'Volcán de energía', 'category' => 'temas', 'description' => 'Colores cálidos para una aventura intensa.', 'price' => 45, 'icon' => 'rocket', 'metadata' => ['effect' => 'theme', 'value' => 'volcan']],
            ['slug' => 'sticker-cohete', 'name' => 'Sticker cohete', 'category' => 'decoraciones', 'description' => 'Decora tu colección con una insignia espacial.', 'price' => 20, 'icon' => 'rocket', 'metadata' => ['effect' => 'decoration', 'value' => 'cohete']],
            ['slug' => 'pet-zorro', 'name' => 'Mascota zorro', 'category' => 'mascotas', 'description' => 'Un compañero curioso que crece contigo.', 'price' => 60, 'icon' => 'paw', 'metadata' => ['effect' => 'pet', 'value' => 'zorro']],
            ['slug' => 'pet-buho', 'name' => 'Mascota búho', 'category' => 'mascotas', 'description' => 'Un compañero sabio para tus misiones.', 'price' => 70, 'icon' => 'paw', 'metadata' => ['effect' => 'pet', 'value' => 'buho']],
            ['slug' => 'mini-memoria', 'name' => 'Reto de memoria', 'category' => 'mini-juegos', 'description' => 'Desbloquea un reto extra de parejas.', 'price' => 50, 'icon' => 'game', 'metadata' => ['effect' => 'mini_game', 'value' => 'memory']],
            ['slug' => 'coleccion-dinosaurios', 'name' => 'Colección dinosaurios', 'category' => 'colecciones', 'description' => 'Completa un set de criaturas prehistóricas.', 'price' => 40, 'icon' => 'star', 'metadata' => ['effect' => 'collection', 'value' => 'dinosaurios']],
            ['slug' => 'pista-especial', 'name' => 'Pista especial', 'category' => 'pistas', 'description' => 'Una ayuda limitada para una actividad difícil.', 'price' => 15, 'icon' => 'sparkle', 'metadata' => ['effect' => 'hint', 'value' => 'tip']],
            ['slug' => 'titulo-maestro-sumas', 'name' => 'Maestro de las sumas', 'category' => 'titulos', 'description' => 'Un título para presumir tus logros matemáticos.', 'price' => 80, 'icon' => 'trophy', 'metadata' => ['effect' => 'title', 'value' => 'Maestro de las sumas']],
            ['slug' => 'nivel-secreto', 'name' => 'Mapa secreto', 'category' => 'niveles secretos', 'description' => 'Abre un camino oculto lleno de desafíos.', 'price' => 100, 'icon' => 'lock', 'metadata' => ['effect' => 'secret_level', 'value' => 'mapa-secreto']],
        ];

        foreach ($items as $item) {
            ShopItem::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}