<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diputados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('agrupacion')->nullable();
            $table->integer('orden')->nullable();
            $table->timestamps();
        });

        $ahora = now();

        // Nómina alfabética oficial de 80 diputados (período legislativo 2023-2028)
        $diputados = [
            ['María Rocío Abed de Zacarías', 'ANR', 1],
            ['Liz Cristina Acosta Brítez', 'ANR', 2],
            ['José Domingo Adorno Mazacotte', 'ANR', 3],
            ['Alejandro Darío Aguilera Elizaúr', 'ANR', 4],
            ['Bettina Rosmary Aguilera Paniagua', 'ANR', 5],
            ['Diosnel Aguilera Rojas', 'ATJPÑ', 6],
            ['Graciela Aguilera Ruiz Díaz', 'CN2023', 7],
            ['Juan Manuel Añazco Vera', 'ANR', 8],
            ['Carlos María Arrechea Ortiz', 'ANR', 9],
            ['Jorge Ramón Ávalos Mariño', 'APUNP', 10],
            ['Juan Manuel Acevedo Ayala', 'PLRA', 11],
            ['Jorge Sebastián Barressi Mancia', 'CN2023', 12],
            ['María Constancia Benítez de Benítez', 'APUNG', 13],
            ['Santiago Alfredo Benítez Cáceres', 'ANR', 14],
            ['Raúl Antonio Benítez Talavera', 'AEN', 15],
            ['Rodrigo Daniel Blanco Amarilla', 'APLP', 16],
            ['Christian Gabriel Brunaga Rotela', 'ANR', 17],
            ['Enrique Antonio Buzarquis Cáceres', 'CN2023', 18],
            ['Diego Crispín Candia Melgarejo', 'ANR', 19],
            ['Benjamín Cantero Ramírez', 'ANR', 20],
            ['Néstor David Castellano Escobar', 'ANR', 21],
            ['María Ida Cattebeke de Ortiz', 'ANR', 22],
            ['Daniel Fernando Centurión González', 'ANR', 23],
            ['César Ladislao Cerini Benítez', 'ANR', 24],
            ['Edgar Milciades Chávez Brizuela', 'ANR', 25],
            ['Avelino Dávalos Estigarribia', 'ANR', 26],
            ['Miguel Ángel Del Puerto Silva', 'ANR', 27],
            ['Yamil Esgaib Mansia', 'ANR', 28],
            ['Mauricio Fabián Espínola Núñez', 'ANR', 29],
            ['Dalia Marlene Estigarribia', 'APLP', 30],
            ['Jatar Eduardo Fernández Safuán', 'PCN', 31],
            ['Héctor Rubén Figueredo Notario', 'ANR', 32],
            ['Luis Federico Franco Alfaro', 'APLP', 33],
            ['Leidy Paola Galeano Chávez', 'PCN', 34],
            ['Rodrigo Daniel Gamarra Krayacich', 'ANR', 35],
            ['Walter Hugo García Méndez', 'CDN', 36],
            ['Carmen de Jesús Giménez de Ovando', 'ANR', 37],
            ['Cleto Marcelino Giménez Giménez', 'AC', 38],
            ['Carlos Marcial Godoy García', 'ANR', 39],
            ['Pedro José Gómez Silva', 'APLP', 40],
            ['Saúl González Rojas', 'ANR', 41],
            ['Roberto Eudes González Segovia', 'ANR', 42],
            ['Luis María González Vaesken', 'ANR', 43],
            ['David Manuel Jara Espinoza', 'ANR', 44],
            ['Raúl Luis Latorre Martínez', 'ANR', 45],
            ['Carlos María López López', 'CN2023', 46],
            ['Juan Ramón Maciel Merlo', 'ANR', 47],
            ['Miguel Martínez Núñez', 'PCN', 48],
            ['Hugo Joel Meza', 'ANR', 49],
            ['Jazmín Narváez Osorio', 'ANR', 50],
            ['Carlos Alberto Núñez Salinas', 'ANR', 51],
            ['Edgar Gustavo Olmedo Silva', 'ANR', 52],
            ['Johanna Paola Ortega Ghiringhelli', 'APAD', 53],
            ['Pedro Ortiz Torres', 'ANR', 54],
            ['Emilio Pavón Doldán', 'CN2023', 55],
            ['Carlos Alberto Pereira Rieve', 'CN2023', 56],
            ['Alejo Ríos Medina', 'CN2023', 57],
            ['Francisco Osvaldo Manuel Petersen Veiluva', 'ANR', 58],
            ['Sebastián Emilio Remesowski Squef', 'ANR', 59],
            ['Guillermo Ariel Rodríguez Duré', 'CDN', 60],
            ['José Ramón Rodríguez Maciel', 'ANR', 61],
            ['Rubén Antonio Roussillon Blaires', 'ANR', 62],
            ['Rubén Isaac Rubín Orrego', 'AEN', 63],
            ['Leonardo Saiz Arce', 'ANR', 64],
            ['Marcelo Rafael Salinas González', 'APLP', 65],
            ['Esteban Martín Samaniego Álvarez', 'ANR', 66],
            ['Germán Solinger Santander', 'ANR', 67],
            ['Fabiana Souto de Alliana', 'ANR', 68],
            ['Roya Nigsa Torres Báez', 'CN2023', 69],
            ['Arturo René Urbieta Cuevas', 'ANR', 70],
            ['Adrián Darío Vaesken Vázquez', 'APLP', 71],
            ['Arnaldo Agustín Valdez Noguera', 'CN2023', 72],
            ['Hilda María del Rocío Vallejo Ávalos', 'PPQ', 73],
            ['Del Pilar Vázquez Cabrera', 'CN2023', 74],
            ['Pastor Alberto Vera Bejarano', null, 75],
            ['Gloria María Johana Vega Insfrán', 'ANR', 76],
            ['Ariel Villagra Sosa', 'CN2023', 77],
            ['María Cristina Villalba de Abente', 'ANR', 78],
            ['Virina Mauricia Villanueva viuda de Peña', 'ANR', 79],
            ['Patricia Alexandra Zena Oviedo', 'PCN', 80],
        ];

        $filas = array_map(fn ($d) => [
            'nombre' => $d[0],
            'agrupacion' => $d[1],
            'orden' => $d[2],
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ], $diputados);

        DB::table('diputados')->insertOrIgnore($filas);
    }

    public function down(): void
    {
        Schema::dropIfExists('diputados');
    }
};
