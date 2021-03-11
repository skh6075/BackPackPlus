<?php

namespace skh6075\backpackplus;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\ListTag;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;
use skh6075\backpackplus\lang\PluginLang;
use skh6075\backpackplus\listener\EventListener;

final class BackPackPlus extends PluginBase{
    use SingletonTrait;

    private static PluginLang $lang;

    private static Item $item;


    public function onLoad(): void{
        self::setInstance($this);
    }

    public function onEnable(): void{
        if (!class_exists(InvMenuHandler::class)) {
            $this->getLogger()->error("Could not find class InvMenuHandler.");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }

        $this->saveResource("lang/kor.yml");
        $this->saveResource("lang/eng.yml");
        self::$lang = (new PluginLang())
            ->setName($lang = $this->getServer()->getLanguage()->getLang())
            ->setTranslates(yaml_parse(file_get_contents($this->getDataFolder() . "lang/" . $lang . ".yml")));

        $this->getScheduler()->scheduleTask(new ClosureTask(function (): void{
            self::$item = ItemFactory::get((int) self::$lang->format("backpack.item.id", [], false), (int) self::$lang->format("backpack.item.meta"), 1)
                ->setCustomName("§r" . self::$lang->format("backpack.item.name", [], false))
                ->setLore(explode("(n)", self::$lang->format("backpack.item.lore", [], false)));
            self::$item->setNamedTagEntry(new ListTag("backpack", []));
            Item::addCreativeItem(self::$item);
        }));

        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function getPluginLang(): PluginLang{
        return self::$lang;
    }
}