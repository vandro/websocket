<?php

//пример реализации чата
class WebsocketWorkerHandler extends WebsocketWorker
{
    protected function onOpen($connectionId) {//вызывается при соединении с новым клиентом
        //$this->write($connectionId, $this->encode('Чтобы общаться в чате введите ник, под которым вы будете отображаться. Можно использовать английские буквы и цифры.'));
    }

    protected function onClose($connectionId) {//вызывается при закрытии соединения клиентом

    }

    protected function onMessage($connectionId, $data) {//вызывается при получении сообщения от клиента
        if (!strlen($data['payload'])) {
            return;
        }

        //var_export($data);
        //шлем всем сообщение, о том, что пишет один из клиентов
        //echo $data['payload'] . "\n";
        $message = 'пользователь #' . $connectionId . ' (' . $this->pid . '): ' . $data['payload'];
        $this->sendToMaster($message);//отправляем сообщение на мастер, чтобы он разослал его на все воркеры

        foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, $data);
        }
    }

    protected function onMasterMessage($data) {//вызывается при получении сообщения от мастера
        foreach ($this->clients as $clientId => $client) {
            $this->sendToClient($clientId, $data);
        }
    }
}