<?php

namespace skh6075\backpackplus\listener;

use muqsit\invmenu\InvMenu;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\NamedTag;
use skh6075\backpackplus\BackPackPlus;

final class EventListener implements Listener{

    private BackPackPlus $plugin;


    public function __construct(BackPackPlus $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerInteractEvent $event
     *
     * @priority MONITOR
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void{
        if (!in_array($event->getAction(), [PlayerInteractEvent::RIGHT_CLICK_AIR, PlayerInteractEvent::RIGHT_CLICK_BLOCK]))
            return;

        $item_ = $event->getItem();
        if (!($namedTag = $item_->getNamedTagEntry("backpack")) instanceof NamedTag)
            return;

        $player = $event->getPlayer();
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        /** @var CompoundTag $item */
        foreach ($namedTag as $i => $item) {
            $menu->getInventory()->setItem($item->getByte("Slot"), Item::nbtDeserialize($item));
        }

        $menu->setInventoryCloseListener(function () use ($menu, $player, $item_): void{
            $listTag = new ListTag("backpack", []);
            foreach ($menu->getInventory()->getContents() as $slot => $item) {
                $listTag->push($item->nbtSerialize($slot));
            }

            $item_->setNamedTagEntry($listTag);
            $player->getInventory()->setItemInHand($item_);
        });
        $menu->send($player, $this->plugin->getPluginLang()->format("backpack.item.name"));
    }
}