<?php

declare(strict_types=1);

namespace xqwtxon\PlaySound;

use xqwtxon\PlaySound\Main;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\plugin\PluginOwned;
use pocketmine\plugin\Plugin;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class PlaySoundCommand extends Command implements PluginOwned
{
    private Main $plugin;

    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("playsound", "Play sound to the player or your self.", "/playsound <player: string> <sound: string> [volume: float] [minimumVolume: float] [pitch: float]");
        $this->setPermission("pocketmine.command.playsound");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if (!$this->testPermission($sender)) {
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage("Usage: ".$this->usageMessage);
            return;
        }

        if (!isset($args[1])) {
            $this->playSound($sender, $args[0]);
            $sender->sendMessage("Successfully played: {$args[0]} to {$sender->getName()}!");
            return;
        } else {
            $target = $this->getOwningPlugin()->getServer()->getPlayerExact($args[1]);
            if ($target === null) {
                $sender->sendMessage("Sorry, we cant find that player!");
                return;
            } else {
                if (!isset($args[2])) {
                    $this->playSound($target, $args[0]);
                    $sender->sendMessage("Successfully played: {$args[0]} to {$target->getName()}!");
                } else {
                    if (!isset($args[3])) {
                        $this->playSound($target, $args[0], $args[2]);
                        $sender->sendMessage("Successfully played: {$args[0]} to {$target->getName()}!");
                    } else {
                        if (!isset($args[4])) {
                            $sender->sendMessage("Usage: ".$this->usageMessage);
                            return;
                        }
                        if (!isset($args[5])) {
                            $sender->sendMessage("Usage: ".$this->usageMessage);
                            return;
                        }
                        $this->playSound($target, $args[0], $args[2], $args[3], $args[4], $args[5]);
                        $sender->sendMessage("Successfully played: {$args[0]} to {$target->getName()}!");
                    }
                }
            }
        }
    }

    private function playSound(Player $player, string $sound, float $minimumVolume = 1.0, float $volume = 1.0, float $pitch = 1.0)
    {
        $position = null;
        $pos = $player->getPosition();
        $pk = new PlaySoundPacket();
        $pk->soundName = $sound;
        $pk->volume = $volume > $minimumVolume ? $minimumVolume : $volume;
        $pk->pitch = $pitch;
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
}
