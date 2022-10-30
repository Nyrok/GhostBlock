<?php

namespace Nyrok\GhostBlock;

use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\player\GameMode;

final class EventListener implements Listener
{
    public function onInteract(PlayerInteractEvent $event){
        if($event->isCancelled()) return;
        if($event->getAction() !== $event::RIGHT_CLICK_BLOCK) return;
        if(($item = $event->getPlayer()->getInventory()->getItemInHand())->getId() !== Main::$block ?? ItemIds::BONE_BLOCK) return;
        $old = clone $event->getBlock();
        $entity = new GhostBlock(
            Location::fromObject(match ($event->getFace()){
                Facing::DOWN => $event->getBlock()->getPosition()->add(0.5, -0.5, 0.5),
                Facing::UP => $event->getBlock()->getPosition()->add(0.5, 1, 0.5),
                Facing::NORTH => $event->getBlock()->getPosition()->add(0.5, 0.5, 0),
                Facing::SOUTH => $event->getBlock()->getPosition()->add(0.5, 0.5, +1.5),
                Facing::WEST => $event->getBlock()->getPosition()->add(0, 0.5, 0.5),
                Facing::EAST => $event->getBlock()->getPosition()->add(+1.5, 0.5, 0.5),
            }, $old->getPosition()->getWorld(), 1, 1),
            $old,
            CompoundTag::create()->setTag('TileID', new IntTag($old->getId()))->setTag('Data', new IntTag($old->getMeta()))
        );
        $entity->spawnToAll();
        $event->cancel();
        if($event->getPlayer()->getGamemode() === GameMode::CREATIVE()) return;
        $event->getPlayer()->getInventory()->setItemInHand(match ($item->getCount()){
            1 => VanillaItems::AIR(),
            default => $item->setCount($item->getCount() - 1),
        });
    }
}