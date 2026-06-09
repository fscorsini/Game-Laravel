<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// --- 1. ROTA PRINCIPAL: GERENCIA AS TELAS ---
Route::get('/', function () {
    // Uso do recurso nativo session() do Laravel
    if (!session()->has('player')) {
        return '
        <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <title>Laravel Quest - Criar Personagem</title>
            <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        </head>
        <body class="bg-gray-950 text-gray-100 flex items-center justify-center min-h-screen">
            <div class="bg-gray-900 p-8 rounded-lg border border-gray-800 w-full max-w-md">
                <h1 class="text-2xl font-bold text-center mb-6 text-blue-500">LARAVEL QUEST</h1>
                <form method="POST" action="/criar">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Nome do Heroi:</label>
                        <input type="text" name="name" required class="w-full bg-gray-800 border border-gray-750 p-2 rounded text-white focus:outline-none focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium mb-1">Classe:</label>
                        <select name="class" class="w-full bg-gray-800 border border-gray-750 p-2 rounded text-white focus:outline-none focus:border-blue-500">
                            <option value="Guerreiro">Guerreiro (Mais defesa)</option>
                            <option value="Mago">Mago (Mais dano)</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded transition cursor-pointer">
                        Iniciar Jogo
                    </button>
                </form>
            </div>
        </body>
        </html>';
    }

    $player = session('player');

    // Usando as Collections nativas do Laravel para estruturar os dados
    $monstersCollection = collect([
        ['name' => 'Slime', 'hp' => 40, 'max_hp' => 40, 'atk' => 8, 'def' => 2, 'gold' => 10, 'xp' => 40],
        ['name' => 'Goblin', 'hp' => 60, 'max_hp' => 60, 'atk' => 12, 'def' => 5, 'gold' => 15, 'xp' => 60],
        ['name' => 'Orc', 'hp' => 90, 'max_hp' => 90, 'atk' => 18, 'def' => 8, 'gold' => 25, 'xp' => 90],
        ['name' => 'Golem', 'hp' => 150, 'max_hp' => 150, 'atk' => 22, 'def' => 15, 'gold' => 50, 'xp' => 100],
        ['name' => 'Dragao', 'hp' => 250, 'max_hp' => 250, 'atk' => 35, 'def' => 20, 'gold' => 100, 'xp' => 200]
    ]);

    $monstersJson = $monstersCollection->toJson();

    return '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Laravel Quest</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>
    <body class="bg-gray-950 text-gray-100 p-6">
        <div class="max-w-4xl mx-auto">
            
            <div class="mb-6 border-b border-gray-800 pb-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-blue-500">LARAVEL QUEST</h1>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gray-900 p-5 rounded-lg border border-gray-800">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold text-green-400" id="p-name"></h2>
                            <p class="text-xs text-gray-400 uppercase tracking-wider" id="p-class"></p>
                        </div>
                        <span class="bg-blue-900 text-blue-200 text-xs font-bold px-2 py-1 rounded" id="p-level"></span>
                    </div>

                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Vida (HP):</span>
                            <span id="p-hp-text"></span>
                        </div>
                        <div class="w-full bg-gray-800 h-2 rounded overflow-hidden">
                            <div id="p-hp-bar" class="bg-green-500 h-2 transition-all duration-200" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>Experiencia (XP):</span>
                            <span id="p-xp-text"></span>
                        </div>
                        <div class="w-full bg-gray-800 h-1.5 rounded overflow-hidden">
                            <div id="p-xp-bar" class="bg-blue-500 h-1.5 transition-all duration-200" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-800 grid grid-cols-2 gap-2 text-sm text-gray-400">
                        <div>Ataque: <span class="text-white font-semibold" id="p-atk"></span></div>
                        <div>Defesa: <span class="text-white font-semibold" id="p-def"></span></div>
                        <div class="col-span-2 mt-2 text-yellow-400 font-bold">Ouro: <span id="p-gold"></span> moedas</div>
                    </div>
                </div>

                <div class="bg-gray-900 p-5 rounded-lg border border-gray-800 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <h2 class="text-xl font-bold text-red-400" id="m-name"></h2>
                            <span id="m-modifier" class="hidden text-xs font-bold px-2 py-0.5 rounded"></span>
                        </div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Inimigo Ativo</p>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Vida do Inimigo:</span>
                                <span id="m-hp-text"></span>
                            </div>
                            <div class="w-full bg-gray-800 h-2 rounded overflow-hidden">
                                <div id="m-hp-bar" class="bg-red-500 h-2 transition-all duration-200" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 mt-6">
                        <button id="btn-attack" onclick="jogarTurno(\'attack\')" class="w-full bg-red-600 hover:bg-red-500 disabled:bg-gray-800 disabled:text-gray-500 py-2 rounded text-sm font-bold transition cursor-pointer disabled:cursor-not-allowed">Atacar</button>
                        <button id="btn-heal" onclick="jogarTurno(\'heal\')" class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-800 disabled:text-gray-500 py-2 rounded text-sm font-bold transition cursor-pointer disabled:cursor-not-allowed">Usar Cura</button>
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 p-4 rounded-lg border border-gray-800 mt-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Relatorio de Turno</h3>
                <div id="log-console" class="bg-gray-950 p-3 rounded font-mono text-xs space-y-1 text-gray-300 max-h-90 overflow-y-auto"></div>
            </div>
        </div>

        <script>
            let backupPlayer = ' . json_encode($player) . ';
            let player = Object.assign({}, backupPlayer);
            let monsterPool = ' . $monstersJson . ';
            let activeMonster = {};
            let emEspera = false; // Controla a trava de tempo dos botões

            function spawnMonster() {
                let rng = Math.random();
                
                if (rng < 0.05) {
                    activeMonster = { name: "Rei Slime", hp: 350, max_hp: 350, atk: 45, def: 25, gold: 150, xp: 400, type: "boss" };
                } else {
                    let randomIdx = Math.floor(Math.random() * monsterPool.length);
                    activeMonster = Object.assign({}, monsterPool[randomIdx]);
                    
                    if (rng >= 0.05 && rng < 0.20) {
                        activeMonster.name = "Campeão " + activeMonster.name;
                        activeMonster.hp = Math.floor(activeMonster.hp * 1.5);
                        activeMonster.max_hp = activeMonster.hp;
                        activeMonster.atk = Math.floor(activeMonster.atk * 1.3);
                        activeMonster.gold = Math.floor(activeMonster.gold * 1.5);
                        activeMonster.xp = Math.floor(activeMonster.xp * 1.5);
                        activeMonster.type = "champion";
                    } else {
                        activeMonster.type = "normal";
                    }
                }

                if (activeMonster.type === "boss") {
                    adicionarLog("ALERTA: O Chefe " + activeMonster.name + " bloqueia seu caminho!");
                } else if (activeMonster.type === "champion") {
                    adicionarLog("Um perigoso " + activeMonster.name + " se aproxima!");
                } else {
                    adicionarLog("Um " + activeMonster.name + " apareceu.");
                }

                atualizarInterface();
            }

            function atualizarInterface() {
                document.getElementById("p-name").innerText = player.name;
                document.getElementById("p-class").innerText = "Classe: " + player.class;
                document.getElementById("p-level").innerText = "Nivel " + player.level;
                document.getElementById("p-hp-text").innerText = player.hp + " / " + player.max_hp;
                document.getElementById("p-xp-text").innerText = player.xp + " / " + player.xp_needed;
                document.getElementById("p-atk").innerText = player.atk;
                document.getElementById("p-def").innerText = player.def;
                document.getElementById("p-gold").innerText = player.gold;
                
                document.getElementById("p-hp-bar").style.width = ((player.hp / player.max_hp) * 100) + "%";
                document.getElementById("p-xp-bar").style.width = ((player.xp / player.xp_needed) * 100) + "%";

                document.getElementById("m-name").innerText = activeMonster.name;
                document.getElementById("m-hp-text").innerText = activeMonster.hp + " / " + activeMonster.max_hp;
                document.getElementById("m-hp-bar").style.width = ((activeMonster.hp / activeMonster.max_hp) * 100) + "%";

                let badge = document.getElementById("m-modifier");
                if (activeMonster.type === "boss") {
                    badge.innerText = "CHEFE";
                    badge.className = "bg-purple-900 text-purple-200 text-xs font-bold px-2 py-0.5 rounded block";
                } else if (activeMonster.type === "champion") {
                    badge.innerText = "CAMPEÃO";
                    badge.className = "bg-orange-900 text-orange-200 text-xs font-bold px-2 py-0.5 rounded block";
                } else {
                    badge.className = "hidden";
                }

                // Desabilita os botões visualmente se "emEspera" for verdadeiro
                document.getElementById("btn-attack").disabled = emEspera;
                document.getElementById("btn-heal").disabled = emEspera;
            }

            function adicionarLog(texto) {
                let consoleDiv = document.getElementById("log-console");
                consoleDiv.innerHTML += "<p class=\'border-l-2 border-gray-700 pl-2\'>" + texto + "</p>";
                consoleDiv.scrollTop = consoleDiv.scrollHeight;
            }

            function limparConsole() {
                document.getElementById("log-console").innerHTML = "";
            }

            function resetarPersonagemPorMorte() {
                limparConsole();
                adicionarLog("RELIQUIA DE RESSUREICAO ATIVADA!");
                adicionarLog("Voce voltou ao Nivel 1. Tente novamente!");
                
                player = Object.assign({}, backupPlayer);
                emEspera = false;
                spawnMonster();
            }

            function jogarTurno(acao) {
                if (emEspera || player.hp <= 0 || activeMonster.hp <= 0) return;

                // Ativa a trava de clique imediatamente
                emEspera = true;
                atualizarInterface();

                let acaoExecutada = false;

                // --- FLUXO DO JOGADOR ---
                if (acao === "attack") {
                    if (Math.random() < 0.10) {
                        adicionarLog("O " + activeMonster.name + " esquivou do seu ataque!");
                    } else {
                        let variacao = Math.floor(Math.random() * 5) + 1;
                        let danoBase = (player.atk + variacao) - activeMonster.def;
                        let danoFinal = Math.max(1, danoBase);
                        
                        if (Math.random() < 0.15) {
                            danoFinal = danoFinal * 2;
                            adicionarLog("ACERTO CRITICO! Voce causou " + danoFinal + " de dano no " + activeMonster.name);
                        } else {
                            adicionarLog("Voce causou " + danoFinal + " de dano no " + activeMonster.name);
                        }

                        activeMonster.hp -= danoFinal;
                    }
                    acaoExecutada = true;

                    if (activeMonster.hp <= 0) {
                        activeMonster.hp = 0;
                        player.gold += activeMonster.gold;
                        player.xp += activeMonster.xp;
                        
                        let curaVitoria = Math.floor(player.max_hp * 0.15);
                        player.hp = Math.min(player.max_hp, player.hp + curaVitoria);
                        
                        adicionarLog("Voce derrotou o " + activeMonster.name + ". Recebeu " + activeMonster.gold + " moedas, " + activeMonster.xp + " XP e recuperou " + curaVitoria + " de HP.");
                        
                        if (player.xp >= player.xp_needed) {
                            player.xp -= player.xp_needed;
                            player.level += 1;
                            player.xp_needed = Math.floor(player.xp_needed * 1.5);
                            
                            player.max_hp += 20;
                            player.hp = player.max_hp;
                            player.atk += 4;
                            player.def += 3;
                            player.gold += 30; 
                            
                            adicionarLog("LEVEL UP! Voce alcancou o Nivel " + player.level + " e recebeu 30 moedas de bonus!");
                        }

                        // Próximo monstro surge após 1.5 segundos, liberando o botão
                        setTimeout(() => {
                            emEspera = false;
                            spawnMonster();
                        }, 1500);
                        return;
                    }
                } else if (acao === "heal") {
                    if (player.gold >= 10) {
                        player.gold -= 10;
                        let valorCura = Math.floor(player.max_hp * 0.30);
                        player.hp = Math.min(player.max_hp, player.hp + valorCura);
                        adicionarLog("Voce usou poção por 10 moedas e curou " + valorCura + " de HP.");
                        acaoExecutada = true;
                    } else {
                        adicionarLog("Moedas insuficientes. Custo da cura: 10 moedas.");
                    }
                }

                // --- FLUXO DE CONTRA-ATAQUE DO MONSTRO ---
                if (acaoExecutada && activeMonster.hp > 0) {
                    let chanceEsquivaPlayer = (player.class === "Mago") ? 0.15 : 0.08;
                    
                    if (Math.random() < chanceEsquivaPlayer) {
                        adicionarLog("Voce conseguiu esquivar do ataque do " + activeMonster.name + "!");
                    } else {
                        let variacaoM = Math.floor(Math.random() * 3) + 1;
                        let danoInimigo = Math.max(1, (activeMonster.atk + variacaoM) - player.def);
                        player.hp -= danoInimigo;
                        adicionarLog("O " + activeMonster.name + " revidou causando " + danoInimigo + " de dano.");

                        if (player.hp <= 0) {
                            player.hp = 0;
                            atualizarInterface();
                            setTimeout(resetarPersonagemPorMorte, 1500);
                            return;
                        }
                    }
                }

                // Libera a trava de tempo dos botões após 1.5 segundos (1500ms)
                setTimeout(() => {
                    emEspera = false;
                    atualizarInterface();
                }, 500);
            }

            window.onload = spawnMonster;
        </script>
    </body>
    </html>';
});

// --- 2. ROTA DE PROCESSAMENTO: CRIA O MODELO DO HEROI ---
Route::post('/criar', function (Request $request) {
    $name = $request->input('name', 'Heroi');
    $class = $request->input('class');

    // Usando as Collections nativas do Laravel para organizar os atributos
    $attributes = collect([
        'Guerreiro' => ['hp' => 120, 'max_hp' => 120, 'atk' => 16, 'def' => 12],
        'Mago'      => ['hp' => 85,  'max_hp' => 85,  'atk' => 26, 'def' => 5],
    ]);

    $playerData = $attributes->get($class);
    $playerData['name'] = $name;
    $playerData['class'] = $class;
    $playerData['gold'] = 50; 
    $playerData['level'] = 1;
    $playerData['xp'] = 0;
    $playerData['xp_needed'] = 100;

    // Uso do helper nativo session() global do Laravel
    session(['player' => $playerData]);

    return redirect('/');
});

// --- 3. ROTA DE RESET MANUAL ---
Route::post('/reiniciar', function () {
    session()->forget('player');
    return redirect('/');
});
