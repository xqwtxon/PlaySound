<?php

declare(strict_types=1);

namespace xqwtxon\PlaySound;

use xqwtxon\PlaySound\Main;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\StopSoundPacket; 

class StopSoundCommand extends Command implements PluginOwned {
    
    private Main $plugin;
    
    public function getOwningPlugin() : Plugin{
        return $this->plugin;
    }
    
    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct("stopsound", "Stop sound to the player or on yourself.", "/stopsound <sound: string>|all <player: string>");
        $this->setPermission("pocketmine.command.stopsound");
    }
    
    public function execute(CommandSender $sender, string $label, array $args) : void{
        if(!$this->testPermission($sender)) return;
        
        if(!isset($args[0])){
            $this->stopSound($sender);
            $sender->sendMessage("Stopped all sound.");
            return;
        } else {
            $this->stopSound($sender, $args[0]);
            $sender->sendMessage("Stopped sound: ", $args[0]);
            return;
        }
        
        if(!isset($args[1])){
            $sender->sendMessage("Usage: ". $this->usageMessage);
            return;
        } else {
            $target = $this->getOwningPlugin()->getServer()->getPlayerExact($args[1]);
            if($target === null){
                $sender->sendMessage("Sorry, we couldnt find player: " . $args[1]);
                return;
            } else {
                if($args[0] === "all"){
                    $this->stopSound($target);
                    $sender->sendMessage("Stopped all sound to " . $args[1]);
                    return;
                } else {
                    $this->stopSound($target, $args[0]);
                    $sender->sendMessage("Stopped ". $args[0] ." sound to " . $args[1]);
                    return;
                }
            }
        }
    }
    
    public function stopSound(Player $player, string $soundName = "") : void{
        $packet = new StopSoundPacket();
        $packet->soundName = $soundName;
        $packet->stopAll = true;
        $player->dataPacket($packet);
    }
}