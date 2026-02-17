<?php

namespace TelegramApiParser\CodeGenerator\NewPHP;

use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use Symfony\Component\Console\Output\OutputInterface;
use TelegramApiParser\CodeGenerator\GeneratorInterface;

class Generator implements GeneratorInterface
{
    protected const NAMESPACE = 'Appto\\TelegramBot';
    const WRAP_LENGTH = 110;
    private array $resolved = [];

    private array $temp_use = [];

    public static array $namespaces = [
        'interfaces' => self::NAMESPACE . '\\Interfaces',
        'types' => self::NAMESPACE . '\\Data',
        'methods' => self::NAMESPACE . '\\Dto'
    ];

    private ParserDocumentation $parser;

    public function __construct(
        protected string $outputDirectory,
        private OutputInterface $output
    ) {
    }

    public function handle(string $file_source, string|array|null $extends = null): void
    {
        if (is_array($extends)) {
            if (isset($extends['methods']) && is_array($extends['types'])) {
                throw new \Exception('The $extends array must contain the keys "methods" and "types".');
            }
        }

        $content = json_decode(file_get_contents($file_source), true);

        $this->output->writeln('------------------------------------------------');
        $this->output->writeln(sprintf('<info>      %s</info>', $content['version_string']));
        $this->output->writeln('------------------------------------------------');

        $this->parser = new ParserDocumentation();

        $documentation = $this->parser->handle($content['documentation']);

        // make interfaces
        $this->output->writeln('Creating interfaces...');
        $this->creatingInterfaces($this->parser->getInterfaces());

        // make available types
        $this->output->writeln('Creating types...');
        $types = array_filter($documentation, fn($item) => in_array(ParserDocumentation::INTERFACE_NAMES['type'], $item['interfaces']));
        $this->creatingClasses($types, self::$namespaces['types'], is_array($extends) ? $extends['types'] : $extends);

        // make available methods
        $this->output->writeln('Creating methods...');
        $methods = array_filter($documentation, fn($item) => in_array(ParserDocumentation::INTERFACE_NAMES['method'], $item['interfaces']));
        $this->creatingClasses($methods, self::$namespaces['methods'], is_array($extends) ? $extends['methods'] : $extends);

        // make telegram bot interface
        $this->output->writeln('Creating TelegramBotInterface...');
        $this->makeTelegramBotInterface($methods);

        $this->output->writeln('<info>Done!</info>');
    }

    private function creatingInterfaces(array $interfaces): void
    {
        $namespace_name = self::$namespaces['interfaces'];

        // interfaces for available types
        foreach (array_keys($interfaces) as $name) {
            $data = $interfaces[$name];

            $namespace = new PhpNamespace($namespace_name);

            $class = $namespace->addInterface($name);

            if (! empty($data['comment'])) {
                $class->addComment(wordwrap($data['comment'], self::WRAP_LENGTH));
            }

            $this->writeToFile($namespace);
        }

        // base interfaces
        foreach (ParserDocumentation::INTERFACE_NAMES as $name => $interface_name) {
            $namespace = new PhpNamespace($namespace_name);
            $namespace->addInterface($interface_name);
            $this->writeToFile($namespace);
        }
    }

    private function creatingClasses(array $types, string $namespace_name, ?string $extends = null): void
    {
        $this->temp_use = [];
        foreach ($types as $type) {
            $this->temp_use = [];

            $namespace = new PhpNamespace($namespace_name);

            $class = $namespace->addClass(ucfirst($type['name']))
                ->setFinal()
                ->addComment(wordwrap($type['comment'], self::WRAP_LENGTH));

            if ($extends) {
                $class->setExtends($extends);
                $this->addUse($namespace, $extends);
            }

            foreach ($type['interfaces'] as $interface) {
                $class->addImplement($this->resolved[$interface]);
                $this->addUse($namespace, $this->resolved[$interface]);
            }

            if ($type['properties']) {
                $method = $class->addMethod('__construct');

                foreach ($type['properties'] as $property) {
                    $type_string = Types::convertToBuiltinType($property['type'], self::$namespaces['types']);

                    $clear_type = str_replace('[]', '', $property['type']);
                    if ($clear_type == $type['name']) continue;

                    if (isset($this->parser->getInterfaces()[$clear_type])) {
                        $type_string = $this->resolved[$clear_type];
                    }

                    $this->addUse($namespace, $type_string);

                    $method
                        ->addPromotedParameter($property['name'])
                        ->addComment(wordwrap($property['comment'], self::WRAP_LENGTH))
                        ->setType($type_string)
                        ->setNullable(!$property['required']);
                }
            }

            $this->writeToFile($namespace);
        }
    }

    private function makeTelegramBotInterface(array $methods): void
    {
        $namespace = new PhpNamespace(self::$namespaces['interfaces']);
        $interface = $namespace->addInterface('TelegramBotInterface');

        foreach ($methods as $method) {
            $docblock = DocBlock::make($method['properties']);

            $method_interface = $interface->addMethod($method['name'])->setPublic();

            if ($method['comment']) {
                $method_interface->addComment(wordwrap($method['comment'], self::WRAP_LENGTH) . PHP_EOL);
            }

            if ($method['properties']) {
                $parameter_dto = Types::convertToBuiltinType(ucfirst($method['name']), self::$namespaces['methods']);
                $parameter = $this->parser->class_basename($parameter_dto).'|'.($docblock ?? 'array');

                $method_interface->addComment('@param '.$parameter .' $dto');

                $this->addUse($namespace, $parameter_dto);
            }

            $comment_return = $method['return'];
            if (str_contains($method['return'], '[]')) {
                $clear_comment_return = str_replace('[]', '', $method['return']);
                if (isset($this->resolved[$clear_comment_return])) {
                    $this->addUse($namespace, $this->resolved[$clear_comment_return]);
                }
            }

            $method_interface->addComment('@return ' . $comment_return);

            $return_type = str_contains($method['return'], '[]')
                ? 'array'
                : $this->resolved[$method['return']] ?? Types::convertToBuiltinType($method['return'], self::$namespaces['types']);

            $method_interface->setReturnType($return_type);

            if (! Types::isBuiltinType($return_type)) {
                $this->addUse($namespace, $return_type);
            }

            if ($method['properties']) {
                $method_interface->addParameter('dto')->setType($parameter_dto.'|array');
            }

            $bot_method_types = DocBlock::getRecurseTypes($method['properties']);
            if ($bot_method_types) {
                sort($bot_method_types);
                foreach ($bot_method_types as $type) {
                    $this->addUse($namespace, $this->resolved[$this->parser->class_basename($type)] ?? $type);
                }
            }
        }

        $this->writeToFile($namespace);
    }

    private function writeToFile(PhpNamespace $namespace): void
    {
        $path = sprintf('%s/%s', $this->outputDirectory, str_replace('\\', '/', $namespace->getName()));

        if (!file_exists($path)) mkdir($path, 0755, true);

        $printer = $this->getPrinter();

        $content = '<?php' . PHP_EOL . $printer->printNamespace($namespace);

        $name = array_key_first($namespace->getClasses());

        $filepath = sprintf('%s/%s.php', realpath($path), $name);

        if (file_exists($filepath)) {
            unlink($filepath);
        }

        file_put_contents($filepath, $content);

        $this->resolved[$name] = $namespace->getName() .'\\'. $name;
    }

    private function addUse(PhpNamespace $namespace, string $type): void
    {
        $types = [];

        if (str_contains($type, '|')) {
            foreach (explode('|', $type) as $item) {
                $clear = str_replace('[]', '', $item);
                if (Types::isBuiltinType($clear)) continue;

                $types[] = $item;
            }
        } else {
            $clear = str_replace('[]', '', $type);
            if (Types::isBuiltinType($clear)) return;

            $types = [$type];
        }

        if (! $types) return;

        foreach ($types as $type) {
            if (! str_contains($type, '\\')) {
                $type = Types::convertToBuiltinType($type, self::$namespaces['types']);
            }

            if (in_array($type, $this->temp_use)) continue;

            $namespace->addUse($type);
            $this->temp_use[] = $type;
        }
    }

    private function getPrinter(): PsrPrinter
    {
        $printer = new PsrPrinter();
        $printer->indentation = '    ';

        return $printer;
    }
}
