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
    private const CONTRACTS_DIR = __DIR__.'/../../contracts';

    /**
     * @return iterable<string, array{string}>
     */
    public static function examples(): iterable
    {
        foreach (glob(self::CONTRACTS_DIR.'/examples/*.json') ?: [] as $path) {
            yield basename($path) => [$path];
        }
    }

    #[DataProvider('examples')]
    public function testExampleMatchesEnvelopeAndPayloadSchema(string $examplePath): void
    {
        $fileName = basename($examplePath);
        $schemaPath = self::CONTRACTS_DIR.'/events/'.$fileName;
        self::assertFileExists($schemaPath, sprintf('No payload schema for example %s', $fileName));

        $example = self::decode($examplePath);
        self::assertSame(
            $fileName,
            sprintf('%s.v%d.json', $example->eventType, $example->schemaVersion),
            'Example file name must be <eventType>.v<schemaVersion>.json',
        );

        $validator = new Validator();

        $envelope = $validator->validate($example, self::decode(self::CONTRACTS_DIR.'/events/envelope.v1.json'));
        self::assertTrue($envelope->isValid(), self::describe($envelope->error()));

        $payload = $validator->validate($example->payload, self::decode($schemaPath));
        self::assertTrue($payload->isValid(), self::describe($payload->error()));
    }

    public function testEveryPayloadSchemaHasAnExample(): void
    {
        foreach (glob(self::CONTRACTS_DIR.'/events/*.json') ?: [] as $schemaPath) {
            $fileName = basename($schemaPath);
            if (str_starts_with($fileName, 'envelope.')) {
                continue;
            }

            self::assertFileExists(
                self::CONTRACTS_DIR.'/examples/'.$fileName,
                sprintf('Schema %s has no example fixture', $fileName),
            );
        }
    }

    private static function decode(string $path): mixed
    {
        return json_decode((string) file_get_contents($path), false, flags: \JSON_THROW_ON_ERROR);
    }

    private static function describe(?ValidationError $error): string
    {
        if (null === $error) {
            return '';
        }

        return (string) json_encode((new ErrorFormatter())->format($error), \JSON_PRETTY_PRINT);
    }
}
