<?php
namespace App\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Workerman\Worker;
#[AsCommand(name:'websocket:server',description:'Run the authenticated chat transport on loopback.')]
final class WebSocketServerCommand extends Command
{
    public function __construct(private ChatHandler $handler,#[Autowire('%kernel.project_dir%')] private string $projectDir){parent::__construct();}
    protected function configure(): void {$this->addOption('port',null,InputOption::VALUE_REQUIRED,'Loopback port','8302');}
    protected function execute(InputInterface $input,OutputInterface $output): int {
        $port=filter_var($input->getOption('port'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1024,'max_range'=>65535]]);if(!$port)return Command::INVALID;
        $worker=new Worker('websocket://127.0.0.1:'.$port);$worker->count=1;$worker->name='universe-chat';$worker->onMessage=[$this->handler,'onMessage'];$worker->onClose=[$this->handler,'onClose'];
        Worker::$pidFile=$this->projectDir.'/var/websocket.pid';Worker::$logFile=$this->projectDir.'/var/log/websocket.log';Worker::$stdoutFile=$this->projectDir.'/var/log/websocket-output.log';
        // Workerman parses its own start/stop arguments, independently of Symfony Console.
        $GLOBALS['argv']=[__FILE__,'start'];$GLOBALS['argc']=2;
        Worker::runAll();return Command::SUCCESS;
    }
}
