<?php

namespace Nyrok\GhostBlock;

use JetBrains\PhpStorm\Pure;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\object\FallingBlock;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\Pickaxe;
use pocketmine\player\Player;

final class GhostBlock extends FallingBlock
{
    protected $gravityEnabled = false;
    protected $invisible = false;
    protected $immobile = true;
    public $fallDistance = 0.0;
    public $canCollide = false;
    protected bool $networkPropertiesDirty = true;

    public function attack(EntityDamageEvent $source): void
    {
        if(!$source instanceof EntityDamageByEntityEvent) return;
        if(!($player = $source->getDamager()) instanceof Player) return;
        if(!$player->isSneaking()) return;
        if(($item = $player->getInventory()->getItemInHand()) instanceof Pickaxe) {
            $item->applyDamage(1);
            $player->getInventory()->setItemInHand($item);
            $this->flagForDespawn();
            $this->getWorld()->dropItem($this->getLocation(), ItemFactory::getInstance()->get(Main::$block ?? BlockLegacyIds::BONE_BLOCK, 0));
        }
    }

    protected function entityBaseTick(int $tickDiff = 1): bool { return true; }

    public function isOnGround(): bool { return true; }

    #[Pure] protected function getInitialSizeInfo() : EntitySizeInfo { return new EntitySizeInfo(1, 1, 1); }

}