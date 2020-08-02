<?php

declare(strict_types=1);

namespace DinoVNOwO\Utils;

use pocketmine\math\Vector3;
use pocketmine\entity\Skin;
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
	
	public function particle(string $particlename, Vector3 $pos, int $xoffset = 0, int $yoffset = 0, int $zoffset = 0, array $players = []) : bool{
		if($players === []){
			$players = $this->getServer()->getOnlinePlayers();
		}
		$pk = new SpawnParticleEffectPacket();
		$random = new Random((int) (microtime(true) * 1000) + mt_rand());
		if($xoffset !== 0){
			$pos->add($random->nextSignedFloat() * $xoffset);
		}
		if($yoffset !== 0){
			$pos->add(0, $random->nextSignedFloat() * $yoffset);
		}
		if($zoffset !== 0){
			$pos->add(0, 0, $random->nextSignedFloat() * $zoffset);
		}
		$pk->position = $pos;
		$pk->particleName = $particlename;
		$this->getServer()->broadcastPacket($players, $pk);
		return true;
	}

	public function convertToByte(string $path) : string{
		$imagesize = getimagesize($path);
		$width = $imagesize[0];
		$height = $imagesize[1];
		$img = imagecreatefrompng($path);
		$bytes = "";
		for ($y = 0; $y < $height; ++$y) {
			for ($x = 0; $x < $width; ++$x) {
				$argb = imagecolorat($img, $x, $y);
				$bytes .= chr(($argb >> 16) & 0xff) . chr(($argb >> 8) & 0xff) . chr($argb & 0xff) . chr((~($argb >> 24) << 1) & 0xff);
			}	
		}
		imagedestroy($img);
		return $bytes;
	}

	public function convertToSkin(string $path, string $geometryname, string $geometrypath) : Skin{
		return new Skin("Standard_Custom", $this->convertToByte($path), $geometryname, file_get_contents($geometrypath));		
	}
	
	public function imageResize(string $path, int $width, int $height){
		$imagesize = getimagesize($path);
		$imagewidth = $imagesize[0];
		$imageheight = $imagesize[1];

		$imager = $imagewidth / $imageheight;

		if($width / $height > $imager) {
			$newimagewidth = $height * $imager;
			$newimageheight = $height;
        	}else{
            		$newimageheight = $width / $imager;
            		$newimagewidth = $width;
		}
		$dst = imagecreatetruecolor($newimagewidth, $newimageheight);
		imagecopyresampled(imagecreatetruecolor($newimagewidth, $newimageheight), imagecreatefrompng($path), 0, 0, 0, 0, $newimageheight, $newimageheight, $width, $height);
		return $dst;
	}
}
