use Rosterbuster\Profiler\Profiler;

class Faker
{
    /**
     * @var mixed An instanced version of the class we are faking.
     */
    private $instanced_original;

    /**
     * @var array<string, mixed> The properties from our original class that we have faked.
     */
    private array $properties;

    private \ReflectionClass $instanced_reflection;

    public function __construct(string $original)
    {
        $this->instanced_reflection = new \ReflectionClass($original);
        $this->inject();

        $this->instanced_original = app($original);
    }

    /**
     * Catch any function calls and dispatch them to the instance of the class they were supposed to go to.
     *
     * @param string $name      The name of the function.
     * @param array $arguments  The arguments of the function.
     * @return void             The result of the function.
     */
    public function __call(string $name, array $arguments)
    {
        Profiler::reportFunction($name);

        $value = $this->instanced_original->$name(...$arguments);

        Profiler::finishFunction($name);

        return $value;
    }

    private function inject()
    {
        foreach($this->instanced_reflection->getProperties() as $reflectionProperty)
        {
            $reflectionNamedType = $reflectionProperty->getType();

            // we only fake classes that are not native to php.
            if(!$reflectionNamedType->isBuiltin()) {
                fake($reflectionNamedType->getName());
            }
        }
    }
}
