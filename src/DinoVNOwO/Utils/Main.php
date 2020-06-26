<?php

declare(strict_types=1);

namespace DinoVNOwO\Utils;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\SpawnParticleEffectPacket;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase{
	
	private static $instance;

	public function onEnable() : void{
		self::$instance = $this;
	}

	public static function getInstance() : Main{
		return self::$instance;
	}
	
	public function sound(Vector3 $pos, string $soundname, int $volume = 1, int $pitch = 1, array $players = []) : bool{
		if($players === []){
			$players = $this->getServer()->getOnlinePlayers();
		}
		$pk = new PlaySoundPacket();
		$pk->soundName = $soundname;
		$pk->x = $pos->x;
		$pk->y = $pos->y;
		$pk->z = $pos->z;
		$pk->volume = 1;
		$pk->pitch = 1;
		$this->getServer()->broadcastPacket($players, $pk);
		return true;
	}

	public function particle(Vector3 $pos, string $particlename, array $players = []) : bool{
		if($players === []){
			$players = $this->getServer()->getOnlinePlayers();
		}
		$pk = new SpawnParticleEffectPacket();
		$pk->position = $pos->asVector3();
		$pk->particleName = $particlename;
		$this->getServer()->broadcastPacket($players, $pk);
		return true;
	}
}
