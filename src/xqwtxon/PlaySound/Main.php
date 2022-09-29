<?php

declare(strict_types=1);

namespace xqwtxon\PlaySound;

use pocketmine\plugin\PluginBase;
use xqwtxon\PlaySound\PlaySoundCommand;
use xqwtxon\PlaySound\StopSoundCommand;

class Main extends PluginBase
{
    public function onEnable(): void
    {
        $this->getServer()->getCommandMap()->register($this->getDescription()->getName(), new PlaySoundCommand($this));
        $this->getServer()->getCommandMap()->register($this->getDescription()->getName(), new StopSoundCommand($this));
    }
}
