<?php

namespace skh6075\backpackplus\lang;

use pocketmine\utils\SingletonTrait;

final class PluginLang{
    use SingletonTrait;

    private string $lang;

    private array $translates = [];


    public function __construct() {
        self::setInstance($this);
    }

    public function getName(): string{
        return $this->lang;
    }

    public function setName(string $lang): self{
        $this->lang = $lang;
        return $this;
    }

    public function getTranslates(): array{
        return $this->translates;
    }

    public function setTranslates(array $translates = []): self{
        $this->translates = $translates;
        return $this;
    }

    public function format(string $key, array $replaces = [], bool $pushPrefix = true): string{
        $format = $pushPrefix ? $this->translates["prefix"] ?? "" : "";
        $format .= $this->translates[$key] ?? "";
        foreach ($replaces as $old => $new) {
            $format = str_replace($old, $new, $format);
        }

        return $format;
    }
}