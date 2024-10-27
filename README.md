# Парсер документации Telegram
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Мне с периодичностью требуется создать бота в Telegram для моих клиентов.
Я понимаю, что есть достаточно библиотек, для их создания, 
но я всегда испытывал некоторые проблемы используя их.

 - Мне хотелось иметь минимальный набор функций, а именно описание Telegram типов и методов. Чтобы моя IDE (PHPStorm) могла мне подсказывать какие параметры нужно заполнить, для того или иного метода Telegram.
 - Все библиотеки которые я использовал до этого, не могли обновляться часто, новые функции из API приходили слишком поздно.
 - Некоторые библиотеки содержат кучу не нужного мне функционала или логики.

В ручную отслеживать, что изменилось с момента последнего обновления Telegram Bot API, сложно, или невозможно.
Изучив HTML-разметку документации, мне пришла идея создать парсер, который решит проблему поиска изменений в API.
Благодаря которой, я теперь могу получать документированную последнюю версию Bot API, буквально за несколько команд в консоли.

🎁 Пример работы генератора: [mahlenko/telegram-bot-casts](https://github.com/mahlenko/telegram-bot-casts) - вы можете использовать его в своем проекте.

## Установка и использование
Просто склонируйте репозиторий себе, и запустите несколько команд:
 - `php console telegram:parse` - для парсинга актуальной документации в JSON файл.
 - `php console telegram:generate` - для генерации PHP файлов.

✅ Готово! Заберите сгенерированные файлы из директории `build` себе в проект.

## Расширение сгенерированных классов
Для простой работы с классами, может понадобится их расширить собственным классом.
Например, для работы в Laravel, я использую библиотеку [Spatie Laravel Data](https://github.com/spatie/laravel-data), по сути библиотека делает удобной работу с DTO.
Также библиотека дает подсказки IDE по ключам, при создании класса.

```php
\Method\SendMessage::from([
    'chat_id' => 0000000,
    'text' => 'Your message'
])
```

Для генерации расширенных классов используйте аргумент `--extends`:
```shell
php console telegram:generate --extends=\\Spatie\\LaravelData\\Data
```

После чего, сгенерированные классы получат ваше расширение и все возможности нового класса:
```php
final class SendMessage extends \Spatie\LaravelData\Data {
    // ...
}
```

Важно! Ваш класс должен реализовывать метод `toArray()`.

## Зависимости

- [imangazaliev/didom](https://github.com/nette/php-generatorhttps://github.com/Imangazaliev/DiDOM)
- [nette/php-generator](https://github.com/nette/php-generator)
- [symfony/console](https://symfony.com/components/Console)

## ✨ Благодарности

![TonBlockchainLogo](/ton_logo_dark_background.svg#gh-dark-mode-only)
![TonBlockchainLogo](/ton_logo_light_background.svg#gh-light-mode-only)

Вы всегда можете отправить благодарность на чашку кофе или больше 😉 с помощью TON на мой кошелек
`appto-wallet.ton`