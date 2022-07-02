<?php

namespace lazyperson0710\commandItem;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    private array $data;

    /**
     * @return void
     */
    public function onEnable(): void {
        $this->saveResource("config.yml");
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
            "CommandItem" => [
                [
                    "name" => "空にするとそのまま処理が通ります(id,metaのみの検索)",
                    "itemId" => 0,
                    "itemMeta" => 0,
                    "eventCancel" => false,//trueでイベントをキャンセルします
                    "command" => [
                        "say test1",
                        "say test2",
                    ],
                ],
            ]
        ]);
        $this->data = $config->get("CommandItem");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $this->onProcessing($event);
    }

    public function onItemUse(PlayerItemUseEvent $event): void {
        $this->onProcessing($event);
    }

    /**
     * @param PlayerInteractEvent|PlayerItemUseEvent $event
     * @return void
     */
    public function onProcessing(PlayerInteractEvent|PlayerItemUseEvent $event): void {
        $item = $event->getPlayer()->getInventory()->getItemInHand();
        foreach ($this->data as $key => $data) {
            if ($item->getId() === $this->data[$key]["itemId"] && $item->getMeta() === $this->data[$key]["itemMeta"]) {
                if (!empty($this->data[$key]["name"])) {
                    if ($item->getCustomName() === $this->data[$key]["name"]) {
                        if ($this->data[$key]["eventCancel"] === true) {
                            $event->cancel();
                        }
                        foreach ($this->data[$key]["command"] as $command) {
                            $event->getPlayer()->getServer()->dispatchCommand($event->getPlayer(), $command);
                        }
                    }
                } else {
                    if ($this->data[$key]["eventCancel"] === true) {
                        $event->cancel();
                    }
                    foreach ($this->data[$key]["command"] as $command) {
                        $event->getPlayer()->getServer()->dispatchCommand($event->getPlayer(), $command);
                    }
                    return;
                }
            }
        }
    }
}
