<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "init.php";

use FuturofoneCore\Futurofone\Service\Cache\ChatAtendimentoCacheService;
use FuturofoneCore\Futurotec\Exception\SistemaException;
use FuturofoneManager\Chat\Futurofone\ChatEventoPublish;

echo "\n";

$codigoMidiaSocial = $argv[1];

if (!is_numeric($codigoMidiaSocial)) {
    echo "É esperado o ID da Midia como parametro!\n";
    die();
}

$contFinalizados=0;
try {
    foreach (ChatAtendimentoCacheService::getChatAtendimentos() as $chatAtendimentoDTO) {


        if ($chatAtendimentoDTO["chatContato"]["idMidiaSocial"] == $codigoMidiaSocial) {
            if($contFinalizados == 0){
                echo "\nIniciando processo de finalização dos chats da midia ".$chatAtendimentoDTO['chatContato']['nomeMidiaSocial'];
                echo "\n ID  |  Contato";
            }
            ChatEventoPublish::finalizarContatoInatividade($chatAtendimentoDTO["chatContato"]["id"], null); // add the second argument here
            echo "\n".$chatAtendimentoDTO['chatContato']['id']." | ".$chatAtendimentoDTO['chatContato']['hash']." ==> Finalizado!";
            $contFinalizados++;

            // call the finalizarContato.php script for each finalized chat
            $command = "php /home/futurofone/web/core/cmd/chat/finalizarContato.php " . $chatAtendimentoDTO["chatContato"]["id"];
            exec($command, $output, $return_var);

            // check if the command was successful
            if ($return_var !== 0) {
                echo "Error finalizing chat with ID: " . $chatAtendimentoDTO["chatContato"]["id"] . "\n";
            }
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
