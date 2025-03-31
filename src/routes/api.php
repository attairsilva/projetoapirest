<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServidorEfetivoController;
use App\Http\Controllers\ServidorTemporarioController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\LotacaoController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UploadController;

use Illuminate\Http\Request;

Route::get('/login', function () {
    return response()->json(['error' => 'Faça login para continuar'], 401);
})->name('login');



Route::prefix('auth')->group(function () {
    Route::post('/registrar', [AuthController::class, 'registrar']);
    Route::post('/obter-token', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum','check.token'])->group(function () {
        Route::post('/desconectar', [AuthController::class, 'logout']);
        Route::post('/renovar-periodo-token', [AuthController::class,  'RenovarPeriodoToken']);
    });
});


# Proteger todas as rotas com autenticação Sanctum
Route::middleware('auth:sanctum','check.token')->group(function () {

    Route::prefix('ServidorEfetivo')->group(function () {
        # mostrar lista de Servidores usando o metodo Get
        Route::get('/', [ServidorEfetivoController::class, 'index']);
        # lançar um novo Servidor usando o metodo Post
        Route::post('/inserir', [ServidorEfetivoController::class, 'store']);
        # mostrar um de Servidor usando o metodo Get, passando o ID do servidor na rota
        Route::get('/exibir', [ServidorEfetivoController::class, 'show']);
        # atualizar a foto de um Servidor usando o metodo Put, passando o ID do servidor na rota
        Route::put('/atualizar', [ServidorEfetivoController::class, 'update']);
        # deletar Servidor usando o metodo Delete, passando o ID do servidor na rota
        Route::delete('/deletar', [ServidorEfetivoController::class, 'destroy']);
        #Buscar endereço funcioanal pelo nome do servidor efetivo
        Route::get('/busca-endereco-funcional', [ServidorEfetivoController::class, 'buscarEnderecoPorNome']);
    });

    Route::prefix('ServidorTemporario')->group(function () {
        # mostrar lista de Servidores usando o metodo Get
        Route::get('/', [ServidorTemporarioController::class, 'index']);
        # lançar um novo Servidor usando o metodo Post
        Route::post('/inserir', [ServidorTemporarioController::class, 'store']);
        # mostrar um de Servidor usando o metodo Get
        Route::get('/exibir', [ServidorTemporarioController::class, 'show']);
        # atualizarum Servidor usando o metodo Put
        Route::put('/atualizar', [ServidorTemporarioController::class, 'update']);
        # deletar Servidor usando o metodo Delete
        Route::delete('/deletar', [ServidorTemporarioController::class, 'destroy']);
    });

    #Uploads
    Route::prefix('uploads')->group(function () {
        # mostrar todas as foto de um Servidor usando o metodo Get, passando o ID do Servidor
        Route::get('/exibir', [UploadController::class, 'getTemporaryUrl']);
        # Enviar a foto de um Servidor usando o metodo
        Route::post('/inserir', [UploadController::class, 'store']);
       # deletar foto especifica pelo ID da foto
        Route::delete('/deletar', [UploadController::class, 'apagarFoto']); #fotos peassoas
        # deletar todas as fotos de um Servidor, passando o ID do Servidor
        Route::delete('/deletartodos', [UploadController::class, 'apagarFotos']); #fotos peassoas

   });

    Route::prefix('Unidade')->group(function () {
        Route::get('/', [UnidadeController::class, 'index']);
        Route::post('/inserir', [UnidadeController::class, 'store']);
        Route::get('/exibir', [UnidadeController::class, 'show']);
        Route::put('/atualizar', [UnidadeController::class, 'update']);
        Route::delete('/deletar', [UnidadeController::class, 'destroy']);
        # servidores efetivos da unidade Nome, idade, unidade de lotação e fotografia
        Route::get('/servidores-efetivos', [ServidorEfetivoController::class, 'listarPorUnidade']);
    });

    Route::prefix('Lotacao')->group(function () {
        Route::get('/', [LotacaoController::class, 'index']);
        Route::post('/inserir', [LotacaoController::class, 'store']);
        Route::get('/exibir', [LotacaoController::class, 'show']);
        Route::put('/atualizar', [LotacaoController::class, 'update']);
        Route::delete('/deletar', [LotacaoController::class, 'destroy']);
    });


});
