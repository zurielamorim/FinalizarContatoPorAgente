<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "init.php";

use FuturofoneCore\Futurofone\Service\Cache\ChatAtendimentoCacheService;
use FuturofoneCore\Futurotec\Exception\SistemaException;
use FuturofoneManager\Chat\Futurofone\ChatEventoPublish;

echo "\n";

$codigoAgente = $argv[1];

if (!is_numeric($codigoAgente)) {
    echo "É esperado o CÓDIGO DO AGENTE como parametro!\n";
    die();
}

$contFinalizados=0;
try {
    foreach (ChatAtendimentoCacheService::getChatAtendimentos() as $chatAtendimentoDTO) {

        if ($chatAtendimentoDTO["agente"]["codigo"] == $codigoAgente) {
            if($contFinalizados == 0){
                echo "\nIniciando processo de finalização dos chats do agente ".$chatAtendimentoDTO['agente']['nome'];
                echo "\n ID  |  Contato";
            }
            ChatEventoPublish::finalizarContatoInatividade($chatAtendimentoDTO["chatContato"]["id"]);
            echo "\n".$chatAtendimentoDTO['chatContato']['id']." | ".$chatAtendimentoDTO['chatContato']['hash']." ==> Finalizado!";
            $contFinalizados++;
        }
    }
} catch (SistemaException $ex) {
    echo $ex->getMessage() . "\n\n";
    die();
}


if ($contFinalizados == 0) {
    echo "NENHUM CONTATO FINALIZADO!\n";
} else {
    echo "\nQUANTIDADE DE CONTATOS FINALIZADOS: " . $contFinalizados . "\n";
}

echo "\n";

########################################################################################################################
