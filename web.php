<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;

/*
|--------------------------------------------------------------------------
| HERO QUEST RPG
| Tudo em um único web.php
|--------------------------------------------------------------------------
*/

$classes = [

    'Guerreiro' => [
        'hp' => 140,
        'mp' => 40,
        'attack' => 18,
        'defence' => 12,
        'speed' => 8,
        'crit' => 10
    ],

    'Mago' => [
        'hp' => 90,
        'mp' => 120,
        'attack' => 25,
        'defence' => 5,
        'speed' => 10,
        'crit' => 15
    ],

    'Arqueiro' => [
        'hp' => 110,
        'mp' => 60,
        'attack' => 20,
        'defence' => 8,
        'speed' => 15,
        'crit' => 20
    ]

];

$monsters = [

    ['name'=>'Slime Verde','level'=>1,'hp'=>50,'attack'=>8,'defence'=>2,'gold'=>10,'xp'=>15],
    ['name'=>'Slime Azul','level'=>2,'hp'=>65,'attack'=>10,'defence'=>3,'gold'=>15,'xp'=>20],
    ['name'=>'Goblin','level'=>3,'hp'=>80,'attack'=>12,'defence'=>5,'gold'=>20,'xp'=>25],
    ['name'=>'Lobo Selvagem','level'=>4,'hp'=>95,'attack'=>15,'defence'=>6,'gold'=>25,'xp'=>30],
    ['name'=>'Esqueleto','level'=>5,'hp'=>120,'attack'=>18,'defence'=>8,'gold'=>35,'xp'=>40],
    ['name'=>'Orc','level'=>6,'hp'=>140,'attack'=>22,'defence'=>10,'gold'=>45,'xp'=>50],
    ['name'=>'Mago Sombrio','level'=>8,'hp'=>180,'attack'=>28,'defence'=>12,'gold'=>60,'xp'=>70],
    ['name'=>'Troll','level'=>10,'hp'=>250,'attack'=>35,'defence'=>15,'gold'=>90,'xp'=>90]

];

$bosses = [

    ['name'=>'Rei Goblin','level'=>5,'hp'=>300,'attack'=>25,'defence'=>10,'gold'=>100,'xp'=>120],
    ['name'=>'Dragao Rubro','level'=>10,'hp'=>600,'attack'=>40,'defence'=>18,'gold'=>250,'xp'=>250],
    ['name'=>'Lorde das Sombras','level'=>20,'hp'=>1200,'attack'=>70,'defence'=>30,'gold'=>500,'xp'=>500],
    ['name'=>'Titã Ancestral','level'=>35,'hp'=>2500,'attack'=>120,'defence'=>60,'gold'=>1000,'xp'=>1200]

];

$shop = [

    [
        'id'=>1,
        'name'=>'Poção Pequena',
        'price'=>30,
        'heal'=>50
    ],

    [
        'id'=>2,
        'name'=>'Poção Grande',
        'price'=>80,
        'heal'=>150
    ],

    [
        'id'=>3,
        'name'=>'Espada de Ferro',
        'price'=>150,
        'attack'=>8
    ],

    [
        'id'=>4,
        'name'=>'Armadura de Aço',
        'price'=>180,
        'defence'=>6
    ],

    [
        'id'=>5,
        'name'=>'Anel do Poder',
        'price'=>250,
        'attack'=>12
    ]

];

$quests = [

    [
        'name'=>'Caçar Slimes',
        'reward_gold'=>50,
        'reward_xp'=>40
    ],

    [
        'name'=>'Eliminar Goblins',
        'reward_gold'=>80,
        'reward_xp'=>60
    ],

    [
        'name'=>'Patrulhar Floresta',
        'reward_gold'=>100,
        'reward_xp'=>80
    ],

    [
        'name'=>'Defender a Vila',
        'reward_gold'=>150,
        'reward_xp'=>120
    ],

    [
        'name'=>'Explorar Ruínas',
        'reward_gold'=>220,
        'reward_xp'=>160
    ]

];

Route::get('/', function () use (
    $classes,
    $monsters,
    $bosses,
    $shop,
    $quests
) {

    if (!Session::has('player')) {

        return '
        <html>
        <head>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        </head>

        <body class="bg-slate-950 text-white flex justify-center items-center min-h-screen">

            <div class="bg-slate-900 p-8 rounded-xl w-full max-w-lg">

                <h1 class="text-3xl font-black text-center mb-6">
                    HERO QUEST RPG
                </h1>

                <form method="POST" action="/create">

                    '.csrf_field().'

                    <input
                    name="name"
                    class="w-full bg-slate-800 p-3 rounded mb-4"
                    placeholder="Nome do Herói">

                    <select
                    name="class"
                    class="w-full bg-slate-800 p-3 rounded mb-4">

                        <option>Guerreiro</option>
                        <option>Mago</option>
                        <option>Arqueiro</option>

                    </select>

                    <button
                    class="bg-indigo-600 w-full p-3 rounded font-bold">

                        Iniciar Jornada

                    </button>

                </form>

            </div>

        </body>
        </html>
        ';
    }

    $player = Session::get('player');

    if (!Session::has('enemy')) {

        $enemy = $monsters[array_rand($monsters)];

        Session::put('enemy', $enemy);

        Session::put('enemy_hp', $enemy['hp']);

        Session::put('battle_logs', [
            'Um '.$enemy['name'].' apareceu!'
        ]);
    }

    $enemy = Session::get('enemy');

    $enemyHp = Session::get('enemy_hp');

    $logs = array_reverse(
        Session::get('battle_logs', [])
    );

    $inventory = $player['inventory'] ?? [];

    $gold = $player['gold'];

    $xpNeed = $player['xp_needed'];

    $xpPercent = min(
        100,
        ($player['xp'] / $xpNeed) * 100
    );

    $hpPercent = ($player['hp'] / $player['max_hp']) * 100;

    $enemyPercent = ($enemyHp / $enemy['hp']) * 100;

    $html = '
    <!DOCTYPE html>

    <html>

    <head>

        <meta charset="UTF-8">

        <title>Hero Quest RPG</title>

        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    </head>

    <body class="bg-slate-950 text-white p-4">

    <div class="max-w-6xl mx-auto">

        <h1 class="text-4xl font-black text-center mb-6 text-indigo-400">
            HERO QUEST RPG
        </h1>

        <div class="grid md:grid-cols-3 gap-4">

            <div class="bg-slate-900 p-4 rounded-xl">

                <h2 class="font-black text-xl mb-2">
                    '.$player['name'].'
                </h2>

                <p>Classe: '.$player['class'].'</p>
                <p>Nível: '.$player['level'].'</p>
                <p>Ouro: '.$gold.' 🪙</p>

                <div class="mt-3">
                    HP '.$player['hp'].'/'.$player['max_hp'].'

                    <div class="bg-slate-800 h-3 rounded">

                        <div
                        class="bg-green-500 h-3 rounded"
                        style="width:'.$hpPercent.'%"></div>

                    </div>
                </div>

                <div class="mt-3">
                    XP '.$player['xp'].'/'.$xpNeed.'

                    <div class="bg-slate-800 h-3 rounded">

                        <div
                        class="bg-indigo-500 h-3 rounded"
                        style="width:'.$xpPercent.'%"></div>

                    </div>
                </div>

                <hr class="my-4 border-slate-700">

                <p>Ataque: '.$player['attack'].'</p>
                <p>Defesa: '.$player['defence'].'</p>
                <p>MP: '.$player['mp'].'</p>

            </div>

            <div class="bg-slate-900 p-4 rounded-xl">

                <h2 class="font-black text-xl text-red-400">
                    '.$enemy['name'].'
                </h2>

                <p>Nível '.$enemy['level'].'</p>

                <div class="mt-3">

                    HP '.$enemyHp.'/'.$enemy['hp'].'

                    <div class="bg-slate-800 h-3 rounded">

                        <div
                        class="bg-red-500 h-3 rounded"
                        style="width:'.$enemyPercent.'%"></div>

                    </div>

                </div>

                <div class="grid grid-cols-2 gap-2 mt-6">

                    <form method="POST" action="/battle">
                        '.csrf_field().'
                        <input type="hidden" name="action" value="attack">
                        <button class="w-full bg-red-600 p-2 rounded">
                            Atacar
                        </button>
                    </form>

                    <form method="POST" action="/battle">
                        '.csrf_field().'
                        <input type="hidden" name="action" value="skill">
                        <button class="w-full bg-cyan-600 p-2 rounded">
                            Skill
                        </button>
                    </form>

                    <form method="POST" action="/battle">
                        '.csrf_field().'
                        <input type="hidden" name="action" value="heal">
                        <button class="w-full bg-green-600 p-2 rounded">
                            Curar
                        </button>
                    </form>

                    <form method="POST" action="/quest">
                        '.csrf_field().'
                        <button class="w-full bg-amber-600 p-2 rounded">
                            Missão
                        </button>
                    </form>

                </div>

            </div>

            <div class="bg-slate-900 p-4 rounded-xl">

                <h2 class="font-black mb-3">
                    Log de Combate
                </h2>
    ';                foreach ($logs as $log) {

                    $html .= '
                    <div class="text-xs border-l-2 border-slate-700 pl-2 py-1">
                        '.$log.'
                    </div>';
                }

                $html .= '

            </div>

        </div>

        <div class="grid md:grid-cols-2 gap-4 mt-4">

            <div class="bg-slate-900 p-4 rounded-xl">

                <h2 class="font-black mb-3">
                    Inventário
                </h2>
        ';

        if(empty($inventory)){

            $html .= '
            <p class="text-slate-500 text-sm">
                Inventário vazio.
            </p>';
        }
        else{

            foreach($inventory as $key => $item){

                $html .= '

                <div class="flex justify-between items-center bg-slate-800 rounded p-2 mb-2">

                    <span>'.$item['name'].'</span>

                    <form method="POST" action="/use-item">

                        '.csrf_field().'

                        <input
                        type="hidden"
                        name="slot"
                        value="'.$key.'">

                        <button class="bg-indigo-600 px-2 py-1 rounded text-xs">

                            Usar

                        </button>

                    </form>

                </div>
                ';
            }
        }

        $html .= '

            </div>

            <div class="bg-slate-900 p-4 rounded-xl">

                <h2 class="font-black mb-3">
                    Loja do Mercador
                </h2>
        ';

        foreach($shop as $item){

            $html .= '

            <div class="flex justify-between items-center bg-slate-800 rounded p-2 mb-2">

                <div>

                    <div>'.$item['name'].'</div>

                    <div class="text-xs text-yellow-400">

                        '.$item['price'].' ouro

                    </div>

                </div>

                <form method="POST" action="/buy">

                    '.csrf_field().'

                    <input
                    type="hidden"
                    name="item"
                    value="'.$item['id'].'">

                    <button
                    class="bg-green-600 px-3 py-1 rounded text-xs">

                        Comprar

                    </button>

                </form>

            </div>

            ';
        }

        $html .= '

            </div>

        </div>

        <div class="mt-4">

            <form method="POST" action="/boss">

                '.csrf_field().'

                <button
                class="w-full bg-purple-700 p-3 rounded-xl font-black">

                    Procurar Chefe Mundial

                </button>

            </form>

        </div>

    </div>

    </body>
    </html>
    ';

    return response($html);
});

Route::post('/create', function () use ($classes) {

    $class = Request::input('class');

    $base = $classes[$class];

    Session::put('player', [

        'name' => Request::input('name','Herói'),

        'class' => $class,

        'level' => 1,

        'xp' => 0,

        'xp_needed' => 100,

        'gold' => 50,

        'hp' => $base['hp'],

        'max_hp' => $base['hp'],

        'mp' => $base['mp'],

        'max_mp' => $base['mp'],

        'attack' => $base['attack'],

        'defence' => $base['defence'],

        'speed' => $base['speed'],

        'crit' => $base['crit'],

        'inventory' => []

    ]);

    return redirect('/');
});

Route::post('/battle', function () {

    $player = Session::get('player');

    $enemy = Session::get('enemy');

    $enemyHp = Session::get('enemy_hp');

    $logs = Session::get('battle_logs', []);

    $action = Request::input('action');

    if($action == 'attack'){

        $damage = max(
            1,
            ($player['attack'] + rand(1,8))
            - $enemy['defence']
        );

        if(rand(1,100) <= $player['crit']){

            $damage *= 2;

            $logs[] = 'CRÍTICO!';
        }

        $enemyHp -= $damage;

        $logs[] = 'Você causou '.$damage.' de dano.';
    }

    elseif($action == 'skill'){

        if($player['mp'] >= 10){

            $player['mp'] -= 10;

            $damage =
                ($player['attack'] * 2)
                + rand(5,15);

            $enemyHp -= $damage;

            $logs[] =
            'Habilidade especial causou '.$damage.' de dano.';
        }
        else{

            $logs[] =
            'Mana insuficiente.';
        }
    }

    elseif($action == 'heal'){

        if($player['mp'] >= 8){

            $player['mp'] -= 8;

            $heal =
                intval($player['max_hp'] * 0.25);

            $player['hp'] = min(
                $player['max_hp'],
                $player['hp'] + $heal
            );

            $logs[] =
            'Você recuperou '.$heal.' HP.';
        }
    }    if($enemyHp > 0){

        $enemyDamage = max(
            1,
            ($enemy['attack'] + rand(1,5))
            - $player['defence']
        );

        $player['hp'] -= $enemyDamage;

        $logs[] =
        $enemy['name'].
        ' causou '.$enemyDamage.
        ' de dano.';
    }

    if($enemyHp <= 0){

        $enemyHp = 0;

        $logs[] =
        'Você derrotou '.$enemy['name'].'!';

        $player['gold'] += $enemy['gold'];

        $player['xp'] += $enemy['xp'];

        $logs[] =
        '+'.$enemy['gold'].' ouro';

        $logs[] =
        '+'.$enemy['xp'].' XP';

        if(rand(1,100) <= 35){

            $loot = [
                'name' => 'Poção Pequena',
                'heal' => 50
            ];

            $player['inventory'][] = $loot;

            $logs[] =
            'Loot encontrado: Poção Pequena';
        }

        while(
            $player['xp']
            >=
            $player['xp_needed']
        ){

            $player['xp']
            -=
            $player['xp_needed'];

            $player['level']++;

            $player['xp_needed']
            =
            intval(
                $player['xp_needed']
                * 1.4
            );

            $player['max_hp'] += 25;
            $player['max_mp'] += 10;
            $player['attack'] += 4;
            $player['defence'] += 3;

            $player['hp']
            =
            $player['max_hp'];

            $player['mp']
            =
            $player['max_mp'];

            $logs[] =
            'LEVEL UP! Agora nível '
            .$player['level'];
        }

        Session::forget('enemy');
        Session::forget('enemy_hp');
    }

    if($player['hp'] <= 0){

        $player['hp'] = 1;

        $goldLost =
            min(
                $player['gold'],
                intval($player['gold'] * 0.10)
            );

        $player['gold']
        -=
        $goldLost;

        $logs[] =
        'Você foi derrotado.';

        $logs[] =
        'Perdeu '.$goldLost.' ouro.';

        Session::forget('enemy');
        Session::forget('enemy_hp');
    }

    Session::put('player',$player);
    Session::put('enemy_hp',$enemyHp);
    Session::put('battle_logs',$logs);

    return redirect('/');
});

Route::post('/quest', function () use ($quests) {

    $player = Session::get('player');

    $quest =
        $quests[array_rand($quests)];

    $gold =
        rand(
            $quest['reward_gold'],
            $quest['reward_gold'] + 50
        );

    $xp =
        rand(
            $quest['reward_xp'],
            $quest['reward_xp'] + 30
        );

    $player['gold'] += $gold;
    $player['xp'] += $xp;

    $logs =
        Session::get('battle_logs',[]);

    $logs[] =
    'Missão concluída: '
    .$quest['name'];

    $logs[] =
    '+'.$gold.' ouro';

    $logs[] =
    '+'.$xp.' XP';

    while(
        $player['xp']
        >=
        $player['xp_needed']
    ){

        $player['xp']
        -=
        $player['xp_needed'];

        $player['level']++;

        $player['xp_needed']
        =
        intval(
            $player['xp_needed']
            * 1.4
        );

        $player['max_hp'] += 25;
        $player['max_mp'] += 10;
        $player['attack'] += 4;
        $player['defence'] += 3;

        $logs[] =
        'LEVEL UP! Nível '
        .$player['level'];
    }

    Session::put('player',$player);
    Session::put('battle_logs',$logs);

    return redirect('/');
});

Route::post('/buy', function () use ($shop) {

    $id =
        intval(
            Request::input('item')
        );

    $player =
        Session::get('player');

    $logs =
        Session::get('battle_logs',[]);

    foreach($shop as $item){

        if($item['id'] != $id){
            continue;
        }

        if(
            $player['gold']
            <
            $item['price']
        ){

            $logs[] =
            'Ouro insuficiente.';

            Session::put(
                'battle_logs',
                $logs
            );

            return redirect('/');
        }

        $player['gold']
        -=
        $item['price'];

        if(isset($item['heal'])){

            $player['inventory'][] = [

                'name' => $item['name'],

                'heal' => $item['heal']

            ];
        }

        if(isset($item['attack'])){

            $player['attack']
            +=
            $item['attack'];

            $logs[] =
            'Ataque aumentado!';
        }

        if(isset($item['defence'])){

            $player['defence']
            +=
            $item['defence'];

            $logs[] =
            'Defesa aumentada!';
        }

        $logs[] =
        'Comprou '.$item['name'];

        break;
    }

    Session::put('player',$player);
    Session::put('battle_logs',$logs);

    return redirect('/');
});

Route::post('/use-item', function(){

    $slot =
        intval(
            Request::input('slot')
        );

    $player =
        Session::get('player');

    $logs =
        Session::get('battle_logs',[]);

    if(
        !isset(
            $player['inventory'][$slot]
        )
    ){
        return redirect('/');
    }

    $item =
        $player['inventory'][$slot];

    if(isset($item['heal'])){

        $player['hp'] =
        min(
            $player['max_hp'],
            $player['hp']
            +
            $item['heal']
        );

        $logs[] =
        'Usou '.$item['name'];

        unset(
            $player['inventory'][$slot]
        );

        $player['inventory']
        =
        array_values(
            $player['inventory']
        );
    }

    Session::put('player',$player);
    Session::put('battle_logs',$logs);

    return redirect('/');
});Route::post('/boss', function () use ($bosses) {

    $boss =
        $bosses[array_rand($bosses)];

    Session::put('enemy', $boss);

    Session::put(
        'enemy_hp',
        $boss['hp']
    );

    $logs =
        Session::get(
            'battle_logs',
            []
        );

    $logs[] =
    '⚔️ CHEFE ENCONTRADO: '
    .$boss['name'];

    $logs[] =
    'Prepare-se para uma batalha épica!';

    Session::put(
        'battle_logs',
        $logs
    );

    return redirect('/');
});

Route::post('/new-game', function () {

    Session::flush();

    return redirect('/');
});

Route::get('/stats', function () {

    $player =
        Session::get('player');

    if(!$player){

        return response()->json([
            'error' => 'Nenhum jogador'
        ]);
    }

    return response()->json($player);
});

/*
|--------------------------------------------------------------------------
| BÔNUS: recompensa especial para chefes
|--------------------------------------------------------------------------
|
| Adicione este bloco DENTRO da rota /battle,
| logo após:
|
| if($enemyHp <= 0){
|
| e antes do Session::forget('enemy');
|
*/

if(
    isset($enemy['hp'])
    &&
    $enemy['hp'] >= 300
){

    $bonusGold =
        intval(
            $enemy['gold'] * 2
        );

    $bonusXp =
        intval(
            $enemy['xp'] * 2
        );

    $player['gold']
    +=
    $bonusGold;

    $player['xp']
    +=
    $bonusXp;

    $logs[] =
    '🏆 Recompensa de Chefe!';

    $logs[] =
    '+'.$bonusGold.' ouro bônus';

    $logs[] =
    '+'.$bonusXp.' XP bônus';

    if(rand(1,100) <= 50){

        $epicItems = [

            [
                'name' =>
                'Poção Suprema',
                'heal' => 300
            ],

            [
                'name' =>
                'Poção Suprema',
                'heal' => 300
            ],

            [
                'name' =>
                'Poção Suprema',
                'heal' => 300
            ]

        ];

        $drop =
            $epicItems[
                array_rand(
                    $epicItems
                )
            ];

        $player['inventory'][]
        =
        $drop;

        $logs[] =
        '✨ Item épico encontrado!';
    }
}

/*
|--------------------------------------------------------------------------
| OPCIONAL - TÍTULOS POR NÍVEL
|--------------------------------------------------------------------------
|
| Coloque dentro do GET /
| depois de carregar o player
|
*/

$title = 'Aventureiro';

if($player['level'] >= 5){
    $title = 'Caçador de Monstros';
}

if($player['level'] >= 10){
    $title = 'Cavaleiro Lendário';
}

if($player['level'] >= 20){
    $title = 'Herói do Reino';
}

if($player['level'] >= 35){
    $title = 'Mestre das Lendas';
}

if($player['level'] >= 50){
    $title = 'Deus da Guerra';
}

/*
|--------------------------------------------------------------------------
| Exibição opcional
|--------------------------------------------------------------------------
|
| Mostrar abaixo do nome:
|
| <p>'.$title.'</p>
|
*/

/*
|--------------------------------------------------------------------------
| Botão Novo Jogo (opcional)
|--------------------------------------------------------------------------
|
| Adicione no HTML:
|
| <form method="POST" action="/new-game">
|     '.csrf_field().'
|     <button class="bg-red-700 p-2 rounded">
|         Reiniciar
|     </button>
| </form>
|
*/

/*
|--------------------------------------------------------------------------
| FIM
|--------------------------------------------------------------------------
*/
