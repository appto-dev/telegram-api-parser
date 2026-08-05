<?php

namespace TelegramApiParser\CodeGenerator\Framework;

use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use TelegramApiParser\CodeGenerator\Framework\Support\MyPrinter;
use TelegramApiParser\CodeGenerator\Framework\Support\PhpTypeChecker;
use TelegramApiParser\CodeGenerator\Framework\Support\Str;
use TelegramApiParser\CodeGenerator\Framework\Support\TelegramTypeResolver;
use TelegramApiParser\CodeGenerator\GeneratorInterface;

class Generator implements GeneratorInterface
{
    private string $build = __DIR__ .'/../../../build';
    private const string TELEGRAM_METHODS_NAMESPACE = 'Appto\\TelegramBot\\Method';
    private const string TELEGRAM_TYPES_NAMESPACE = 'Appto\\TelegramBot\\Type';
    private const string SUPPORT_NAMESPACE = 'Appto\\TelegramBot\\Support';
    private const string CASTS_NAMESPACE = 'Appto\\TelegramBot\\Support\\Casts';
    private const string CASTS_DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR . 'Casts';

    private readonly TelegramTypeResolver $typeResolver;
    private readonly PhpTypeChecker $typeChecker;
    private readonly MyPrinter $printer;

    public function __construct() {
        $this->typeResolver = new TelegramTypeResolver(self::TELEGRAM_TYPES_NAMESPACE);
        $this->typeChecker = new PhpTypeChecker();
        $this->printer = new MyPrinter();

        // make build directory
        if (!file_exists($this->build) || !is_dir($this->build)) {
            mkdir($this->build);
        }
        $this->build = realpath($this->build);
        $this->makeDirectoryForNamespace(new PhpNamespace(self::CASTS_NAMESPACE));
    }

    public function handle(string $file_source): void {
        $content = json_decode(file_get_contents($file_source), true);
        $documentation = array_slice($content['documentation'], 4);

        $implements = $this->implementations($documentation);
        $castableProperties = $this->castableProperties($documentation);

        foreach ($documentation as $doc) {
            // общий интерфейс для описания методов клиента для бота
            $docNamespace = new PhpNamespace(Str::toCamelCase(self::SUPPORT_NAMESPACE));
            $docInterface = $docNamespace
                ->addInterface(Str::toCamelCase($doc['name']))
                ->addComment($doc['description']);

            // методы и типы в секции
            foreach ($doc['sections'] as $item) {
                $namespace = new PhpNamespace(isset($item['return'])
                    ? self::TELEGRAM_METHODS_NAMESPACE
                    : self::TELEGRAM_TYPES_NAMESPACE
                );

                // interfaces: MaybeInaccessibleMessage, ChatMember, InputFile etc...
                if (!isset($item['parameters']) && !isset($item['return'])) {
                    $namespace->addInterface(ucfirst($item['name']))->addComment($item['description']);
                    $this->print($namespace);
                    continue;
                }

                $class = $namespace
                    ->addUse(Data::class)
                    ->addClass(ucfirst($item['name']))
                    ->addComment($doc['description'])
                    ->setExtends(Data::class);

                if (isset($implements[$item['name']])) {
                    $class->setImplements($implements[$item['name']]);
                }

                if (isset($item['parameters'])) {
                    $constructor = $class->addMethod('__construct');

                    foreach ($item['parameters'] as $parameter) {
                        $phpType = $this->typeResolver->toPhpType($parameter['type']);
                        $docType = $this->typeResolver->toDocType($parameter['type']);

                        // add use classes
                        $clearDocType = str_replace(['(', ')'], '', $docType);
                        if (!$this->typeChecker->isNativeType($clearDocType)) {
                            $objects = $this->typeChecker->extractClassNames($clearDocType);
                            foreach ($objects as $object) {
                                if (ucfirst($item['name']) === $object) continue;
                                $namespace->addUse(self::TELEGRAM_TYPES_NAMESPACE .'\\'. $object);
                            }
                        }

                        $comment = sprintf('@var  %s%s  %s', $docType, $parameter['required'] ? '' : '|null', $parameter['description']);

                        $props = $constructor
                            ->addPromotedParameter($parameter['name'])
                            ->setType($phpType)
                            ->addComment($comment)
                            ->setNullable(!$parameter['required'])
                            ->setPublic();

                        //
                        $isCastable = in_array($phpType, $castableProperties);
                        if ($isCastable) {
                            $castClassName = basename(str_replace('\\', '/', $phpType)) . 'Cast';
                            $castableLiteral = new Literal($castClassName . '::class');
                            $props->addAttribute(WithCast::class, [ $castableLiteral ]);

                            $namespace->addUse(WithCast::class);
                            $namespace->addUse(self::CASTS_NAMESPACE . '\\'. $castClassName);

                            $filename = self::CASTS_DIRECTORY . DIRECTORY_SEPARATOR . $castClassName .'.php';
                            $directory = $this->getDirectoryForNamespace(new PhpNamespace(self::CASTS_NAMESPACE));
                            $to = $directory . DIRECTORY_SEPARATOR . basename($filename);

                            if (file_exists($filename)) {
                                copy($filename, $to);
                            } else {
                                $castNamespace = new PhpNamespace(self::CASTS_NAMESPACE)
                                    ->addUse(Cast::class)
                                    ->addUse(DataProperty::class)
                                    ->addUse(CreationContext::class);
                                $castClass = $castNamespace->addClass($castClassName)->addImplement(Cast::class);
                                $method = $castClass->addMethod('cast')->setReturnType('mixed');
                                $method->addParameter('property')->setType(DataProperty::class);
                                $method->addParameter('value')->setType('mixed');
                                $method->addParameter('properties')->setType('array');
                                $method->addParameter('context')->setType(CreationContext::class);

                                $this->print($castNamespace);
                                copy($to, $filename);

                                // тут бы какое-то сообщение отправить, чтобы быть в курсе нового каста.

                            }
                        }
                    }
                }

                $this->print($namespace);
            }
        }
    }

    private function implementations(array $documentation): array {
        $implements = [];

        foreach ($documentation as $doc) {
            foreach ($doc['sections'] as $item) {
                if (!isset($item['parameters']) && !isset($item['return'])) {
                    $interface = $item['name'];
                    foreach (Str::linkedTypesExtractor($item['description']) as $class) {
                        if (!isset($implements[$class])) {
                            $implements[$class] = [];
                        }

                        $implements[$class][] = self::TELEGRAM_TYPES_NAMESPACE .'\\'. $interface;
                    }
                }
            }
        }

        return $implements;
    }

    private function castableProperties(array $documentation): array {
        $castableProperties = [];

        foreach ($documentation as $doc) {
            foreach ($doc['sections'] as $item) {
                if (!isset($item['parameters']) && !isset($item['return'])) {
                    $interface = $item['name'];
                    if (Str::linkedTypesExtractor($item['description'])) {
                        $castableProperties[] = self::TELEGRAM_TYPES_NAMESPACE .'\\'. $interface;
                    }
                }
            }
        }

        return array_unique($castableProperties);
    }

    private function print(PhpNamespace $namespace): string {
        $this->makeDirectoryForNamespace($namespace);

        $class = array_first($namespace->getClasses());
        $directory = $this->getDirectoryForNamespace($namespace);
        $filepath = $directory . DIRECTORY_SEPARATOR . $class->getName() . '.php';

        // make file content
        $content = '<?php' . PHP_EOL . $this->printer->printNamespace($namespace);
        $file = PhpFile::fromCode($content);
        $file->setStrictTypes();

        file_put_contents($filepath, $file);

        return $filepath;
    }

    private function makeDirectoryForNamespace(PhpNamespace $namespace): void {
        $directory = $this->getDirectoryForNamespace($namespace);

        $path = '';
        foreach (explode(DIRECTORY_SEPARATOR, $directory) as $dir) {
            $path .= DIRECTORY_SEPARATOR . $dir;
            if (!file_exists($path) || !is_dir($path)) {
                mkdir($path);
            }
        }
    }

    private function getDirectoryForNamespace(PhpNamespace $namespace): string {
        $directory = str_replace('\\', DIRECTORY_SEPARATOR, str_replace('Appto\\TelegramBot\\', '', $namespace->getName()));
        return $this->build . DIRECTORY_SEPARATOR . $directory;
    }
}
