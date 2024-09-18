**Projeyi Başlatma Adımları**

<p>Proje için Docker imajlarını oluşturun<br>
docker compose build</p>
<p>Docker Compose ile konteynerleri başlatın<br>
docker compose up -d</p>

<p>PHP konteynerine bağlanın ve Composer güncellemesi yapın<br>
docker compose exec php bash<br>
composer update</p>

Queueleri aktif hale getirmek için<br>
php bin/console messenger:consume async

Developer eklemek için
http://localhost:8080/developers

<p>**Note: Localinizde messenger ack and nack hatası verir ise şöyle bir müdahale de bulunmak durumunda kaldım :/<br>
case-todo-planning/app/vendor/symfony/messenger/Transport/AmqpExt/Connection.php<br>

public function ack(\AMQPEnvelope $message, string $queueName): bool<br>
{<br>
    return $this->queue($queueName)->ack($message->getDeliveryTag()) ?? false;<br>
}<br>

public function nack(\AMQPEnvelope $message, string $queueName, int $flags = \AMQP_NOPARAM): bool<br>
{<br>
    return $this->queue($queueName)->nack($message->getDeliveryTag(), $flags) ?? false;<br>
}<br>
</p>

<p>Taskları providerlardan çekmek için<br>
php bin/console app:fetch-tasks</p>

<p>Taskları Developerlara dağıtmak için<br>
php bin/console app:distribute-jobs</p>
