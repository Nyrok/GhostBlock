<?php

namespace Nyrok\GhostBlock;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\World;

class Main extends PluginBase
{
    use SingletonTrait;

    public static int $block = ItemIds::BONE_BLOCK;

    protected function onLoad(): void
    {
        $this::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        EntityFactory::getInstance()->register(GhostBlock::class, function (World $world, CompoundTag $nbt): GhostBlock {
            return new GhostBlock(EntityDataHelper::parseLocation($nbt, $world),
                BlockFactory::getInstance()->get($nbt->getTag('TileID')?->getValue() ?? $nbt->getTag("Tile")?->getValue() ?? 0, $nbt->getTag('Data')?->getValue() ?? 0),
                $nbt
            );
        }, ['GhostBlock']);
        self::$block = (new Config($this->getDataFolder() . 'config.yml', Config::YAML))->get('block', BlockLegacyIds::BONE_BLOCK);
    }
}