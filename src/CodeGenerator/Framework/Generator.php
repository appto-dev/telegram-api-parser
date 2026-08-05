<?php

namespace TelegramApiParser\CodeGenerator\Framework;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\InterfaceType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
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
    private string $build = __DIR__ . '/../../../build';
    private const string TELEGRAM_METHODS_NAMESPACE = 'Appto\\TelegramBot\\Method';
    private const string TELEGRAM_TYPES_NAMESPACE = 'Appto\\TelegramBot\\Type';
    private const string SUPPORT_NAMESPACE = 'Appto\\TelegramBot\\Support';
    private const string CASTS_NAMESPACE = 'Appto\\TelegramBot\\Support\\Casts';
    private const string CASTS_DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR . 'Casts';

    private readonly TelegramTypeResolver $typeResolver;
    private readonly PhpTypeChecker $typeChecker;
    private readonly MyPrinter $printer;

    public function __construct()
    {
        $this->typeResolver = new TelegramTypeResolver(self::TELEGRAM_TYPES_NAMESPACE);
        $this->typeChecker = new PhpTypeChecker();
        $this->printer = new MyPrinter();

        if (!file_exists($this->build) || !is_dir($this->build)) {
            mkdir($this->build);
        }
        $this->build = realpath($this->build);
        $this->makeDirectoryForNamespace(new PhpNamespace(self::CASTS_NAMESPACE));
    }

    public function handle(string $file_source): void
    {
        $content = json_decode(file_get_contents($file_source), true);
        $documentation = array_slice($content['documentation'], 4);

        [$implements, $castableProperties] = $this->scanSections($documentation);

        $this->emitBaseInterfaces();

        foreach ($documentation as $doc) {
            $docNamespace = new PhpNamespace(Str::toCamelCase(self::SUPPORT_NAMESPACE));
            $docInterface = $docNamespace
                ->addInterface(Str::toCamelCase($doc['name']))
                ->addComment($doc['description']);

            foreach ($doc['sections'] as $item) {
                $namespace = $this->namespaceForItem($item);

                if ($this->isInterfaceOnly($item)) {
                    $this->emitInterface($namespace, $item);
                    continue;
                }

                $class = $this->emitDataClassSkeleton($namespace, $item, $doc, $implements);

                if ($this->isMethodItem($item)) {
                    $this->emitMethodDoc($docInterface, $docNamespace, $item);
                }

                if (isset($item['parameters'])) {
                    $this->emitConstructor($namespace, $class, $item, $castableProperties);
                }

                $this->print($namespace);
            }

            if (count($docInterface->getMethods())) {
                $this->print($docNamespace);
            }
        }
    }

    // ------------------------------------------------------------------
    // Сканирование документации: интерфейсы -> кто их implements + какие типы кастуемые
    // ------------------------------------------------------------------

    /**
     * Один проход по всей документации вместо двух одинаковых (implementations()+castableProperties()).
     *
     * @return array{0: array<string, list<string>>, 1: list<string>}
     */
    private function scanSections(array $documentation): array
    {
        $implements = [];
        $castableProperties = [];

        foreach ($documentation as $doc) {
            foreach ($doc['sections'] as $item) {
                if (!$this->isInterfaceOnly($item)) {
                    continue;
                }

                $linkedTypes = Str::linkedTypesExtractor($item['description']);

                if ($linkedTypes === []) {
                    continue;
                }

                $interfaceFqcn = self::TELEGRAM_TYPES_NAMESPACE . '\\' . $item['name'];

                foreach ($linkedTypes as $class) {
                    $implements[$class][] = $interfaceFqcn;
                }

                $castableProperties[] = $interfaceFqcn;
            }
        }

        return [$implements, array_unique($castableProperties)];
    }

    private function isInterfaceOnly(array $item): bool
    {
        return !isset($item['parameters']) && !isset($item['return']);
    }

    private function isMethodItem(array $item): bool
    {
        return isset($item['return']);
    }

    // ------------------------------------------------------------------
    // Namespace / interface / class skeleton
    // ------------------------------------------------------------------

    private function namespaceForItem(array $item): PhpNamespace
    {
        return new PhpNamespace(
            $this->isMethodItem($item) ? self::TELEGRAM_METHODS_NAMESPACE : self::TELEGRAM_TYPES_NAMESPACE
        );
    }

    /**
     * Генерирует два "корневых" интерфейса с общим контрактом (toArray/from),
     * которые реализует каждый сгенерированный Data-класс - методов и типов отдельно.
     */
    private function emitBaseInterfaces(): void
    {
        $this->emitBaseInterface(self::TELEGRAM_METHODS_NAMESPACE, 'TelegramMethod', 'методов');
        $this->emitBaseInterface(self::TELEGRAM_TYPES_NAMESPACE, 'TelegramType', 'типов');
    }

    private function emitBaseInterface(string $namespaceName, string $interfaceName, string $description): void
    {
        $namespace = new PhpNamespace($namespaceName);

        $interface = $namespace
            ->addInterface($interfaceName)
            ->addComment("Базовый контракт для сгенерированных {$description} Telegram Bot API.");

        $interface->addMethod('toArray')->setReturnType('array');

        $from = $interface->addMethod('from')->setStatic()->setReturnType('static')->setVariadic();
        $from->addParameter('payload')->setType('mixed');

        $this->print($namespace);
    }

    private function emitInterface(PhpNamespace $namespace, array $item): void
    {
        $namespace->addInterface(ucfirst($item['name']))->addComment($item['description']);
        $this->print($namespace);
    }

    private function emitDataClassSkeleton(PhpNamespace $namespace, array $item, array $doc, array $implements): ClassType
    {
        $class = $namespace
            ->addUse(Data::class)
            ->addClass(ucfirst($item['name']))
            ->addComment($doc['description'])
            ->setExtends(Data::class);

        $additionalInterfaces = $implements[$item['name']] ?? [];
        $class->setImplements(array_unique([$this->baseInterfaceFqcn($item), ...$additionalInterfaces]));

        return $class;
    }

    private function baseInterfaceFqcn(array $item): string
    {
        return $this->isMethodItem($item)
            ? self::TELEGRAM_METHODS_NAMESPACE . '\\TelegramMethod'
            : self::TELEGRAM_TYPES_NAMESPACE . '\\TelegramType';
    }

    // ------------------------------------------------------------------
    // Метод клиента (docInterface): сигнатура + докблок
    // ------------------------------------------------------------------

    private function emitMethodDoc(InterfaceType $docInterface, PhpNamespace $docNamespace, array $item): void
    {
        $returnPhpType = $this->typeResolver->toPhpType($item['return']);
        $returnDocType = $this->typeResolver->toDocType($item['return']);

        $docMethod = $docInterface
            ->addMethod($item['name'])
            ->setReturnType($returnPhpType)
            ->addComment($item['description'] . PHP_EOL);

        $this->addUseForReturnType($docNamespace, $returnPhpType, $returnDocType);

        if (isset($item['parameters'])) {
            foreach ($this->sortByRequired($item['parameters']) as $parameter) {
                $this->emitMethodParameter($docMethod, $docNamespace, $parameter);
            }
        }

        $docMethod->addComment('')->addComment('@return ' . $returnDocType);
    }

    private function addUseForReturnType(PhpNamespace $docNamespace, string $returnPhpType, string $returnDocType): void
    {
        if (!$this->typeChecker->isNativeType($returnPhpType)) {
            foreach ($this->typeChecker->extractClassNames($returnPhpType) as $class) {
                $docNamespace->addUse($class);
            }

            return;
        }

        if ($returnPhpType === 'array') {
            foreach ($this->typeChecker->extractClassNames($returnDocType) as $class) {
                $docNamespace->addUse(self::TELEGRAM_TYPES_NAMESPACE . '\\' . $class);
            }
        }
    }

    private function emitMethodParameter(Method $docMethod, PhpNamespace $docNamespace, array $parameter): void
    {
        [$phpType, $docType] = $this->resolveParameterTypes($parameter);

        if (!$this->typeChecker->isNativeType($docType)) {
            $this->addUse($docNamespace, $docType);
        }

        $docMethod->addComment(sprintf(
            '@param  %s%s $%s %s',
            $docType,
            $parameter['required'] ? '' : '|null',
            $parameter['name'],
            $parameter['description']
        ));

        $docMethod->addParameter($parameter['name'])
            ->setType($phpType)
            ->setNullable(!$parameter['required']);
    }

    // ------------------------------------------------------------------
    // Data-класс: __construct с промотированными свойствами
    // ------------------------------------------------------------------

    private function emitConstructor(PhpNamespace $namespace, ClassType $class, array $item, array $castableProperties): void
    {
        $constructor = $class->addMethod('__construct');

        foreach ($item['parameters'] as $parameter) {
            [$phpType, $docType] = $this->resolveParameterTypes($parameter);

            $this->addUse($namespace, $docType, $item['name']);

            $comment = sprintf(
                '@var  %s%s  %s',
                $docType,
                $parameter['required'] ? '' : '|null',
                $parameter['description']
            );

            $props = $constructor
                ->addPromotedParameter($parameter['name'])
                ->setType($phpType)
                ->addComment($comment)
                ->setNullable(!$parameter['required'])
                ->setPublic();

            if (in_array($phpType, $castableProperties, true)) {
                $this->makeOrMoveCastableClass($namespace, $props);
            }
        }
    }

    /** @return array{0: string, 1: string} [phpType, docType] */
    private function resolveParameterTypes(array $parameter): array
    {
        return [
            $this->typeResolver->toPhpType($parameter['type']),
            $this->typeResolver->toDocType($parameter['type']),
        ];
    }

    private function sortByRequired(array $parameters): array
    {
        usort($parameters, fn ($a, $b) => $b['required'] <=> $a['required']);

        return $parameters;
    }

    // ------------------------------------------------------------------
    // Cast-классы
    // ------------------------------------------------------------------

    private function makeOrMoveCastableClass(PhpNamespace $namespace, Parameter $props): void
    {
        $phpType = $props->getType();

        $castClassName = basename(str_replace('\\', '/', $phpType)) . 'Cast';
        $castableLiteral = new Literal($castClassName . '::class');
        $props->addAttribute(WithCast::class, [$castableLiteral]);

        $namespace->addUse(WithCast::class);
        $namespace->addUse(self::CASTS_NAMESPACE . '\\' . $castClassName);

        $filename = self::CASTS_DIRECTORY . DIRECTORY_SEPARATOR . $castClassName . '.php';
        $directory = $this->getDirectoryForNamespace(new PhpNamespace(self::CASTS_NAMESPACE));
        $to = $directory . DIRECTORY_SEPARATOR . basename($filename);

        if (file_exists($filename)) {
            copy($filename, $to);

            return;
        }

        $castNamespace = (new PhpNamespace(self::CASTS_NAMESPACE))
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

    // ------------------------------------------------------------------
    // Печать файлов / directory helpers
    // ------------------------------------------------------------------

    private function print(PhpNamespace $namespace): string
    {
        $this->makeDirectoryForNamespace($namespace);

        $class = array_first($namespace->getClasses());
        $directory = $this->getDirectoryForNamespace($namespace);
        $filepath = $directory . DIRECTORY_SEPARATOR . $class->getName() . '.php';

        $content = '<?php' . PHP_EOL . $this->printer->printNamespace($namespace);
        $file = PhpFile::fromCode($content);
        $file->setStrictTypes();

        file_put_contents($filepath, $this->printer->printFile($file));

        return $filepath;
    }

    private function makeDirectoryForNamespace(PhpNamespace $namespace): void
    {
        $directory = $this->getDirectoryForNamespace($namespace);

        $path = '';
        foreach (explode(DIRECTORY_SEPARATOR, $directory) as $dir) {
            $path .= DIRECTORY_SEPARATOR . $dir;
            if (!file_exists($path) || !is_dir($path)) {
                mkdir($path);
            }
        }
    }

    private function getDirectoryForNamespace(PhpNamespace $namespace): string
    {
        $directory = str_replace('\\', DIRECTORY_SEPARATOR, str_replace('Appto\\TelegramBot\\', '', $namespace->getName()));

        return $this->build . DIRECTORY_SEPARATOR . $directory;
    }

    private function addUse(PhpNamespace $namespace, string $docType, ?string $classname = null): void
    {
        $clearDocType = str_replace(['(', ')'], '', $docType);

        if ($this->typeChecker->isNativeType($clearDocType)) {
            return;
        }

        foreach ($this->typeChecker->extractClassNames($clearDocType) as $object) {
            if ($classname && ucfirst($classname) === $object) {
                continue;
            }

            $namespace->addUse(self::TELEGRAM_TYPES_NAMESPACE . '\\' . $object);
        }
    }
}
