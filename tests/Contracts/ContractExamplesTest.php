<?php

declare(strict_types=1);

namespace SharedKernel\Tests\Contracts;

use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every canonical example message must validate against the envelope schema
 * and its own payload schema — services' publisher and consumer contract
 * tests run against these exact fixtures (PLAN.md §5), so a broken fixture
 * must never be tagged.
 */
final class ContractExamplesTest extends TestCase
{
    private const string CONTRACTS_DIR = __DIR__ . '/../../contracts';

    /**
     * @return iterable<string, array{string}>
     */
    public static function examples(): iterable
    {
        $paths = glob(self::CONTRACTS_DIR . '/examples/*.json');
        foreach ($paths === false ? [] : $paths as $path) {
            yield basename($path) => [$path];
        }
    }

    #[DataProvider('examples')]
    public function testExampleMatchesEnvelopeAndPayloadSchema(string $examplePath): void
    {
        $fileName = basename($examplePath);
        $schemaPath = self::CONTRACTS_DIR . '/events/' . $fileName;
        self::assertFileExists($schemaPath, sprintf('No payload schema for example %s', $fileName));

        $example = $this->decode($examplePath);
        self::assertIsString($example->eventType);
        self::assertIsInt($example->schemaVersion);
        self::assertSame(
            $fileName,
            sprintf('%s.v%d.json', $example->eventType, $example->schemaVersion),
            'Example file name must be <eventType>.v<schemaVersion>.json',
        );
        self::assertIsObject($example->payload);

        $validator = new Validator();

        $envelope = $validator->validate($example, $this->decode(self::CONTRACTS_DIR . '/events/envelope.v1.json'));
        self::assertTrue($envelope->isValid(), $this->describe($envelope->error()));

        $payload = $validator->validate($example->payload, $this->decode($schemaPath));
        self::assertTrue($payload->isValid(), $this->describe($payload->error()));
    }

    public function testEveryPayloadSchemaHasAnExample(): void
    {
        $schemaPaths = glob(self::CONTRACTS_DIR . '/events/*.json');
        foreach ($schemaPaths === false ? [] : $schemaPaths as $schemaPath) {
            $fileName = basename($schemaPath);
            if (str_starts_with($fileName, 'envelope.')) {
                continue;
            }

            self::assertFileExists(
                self::CONTRACTS_DIR . '/examples/' . $fileName,
                sprintf('Schema %s has no example fixture', $fileName),
            );
        }
    }

    private function decode(string $path): \stdClass
    {
        $decoded = json_decode((string) file_get_contents($path), false, flags: \JSON_THROW_ON_ERROR);
        if (!$decoded instanceof \stdClass) {
            throw new \UnexpectedValueException(sprintf('%s must contain a JSON object', $path));
        }

        return $decoded;
    }

    private function describe(?ValidationError $error): string
    {
        if (!$error instanceof \Opis\JsonSchema\Errors\ValidationError) {
            return '';
        }

        return (string) json_encode(new ErrorFormatter()->format($error), \JSON_PRETTY_PRINT);
    }
}
