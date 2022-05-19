<?php

declare(strict_types=1);

namespace App\Service\Data;

use Carbon\Carbon;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\LexerConfig;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * @package App\Service\Data
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
abstract class AbstractImportService
{
    abstract protected function parseRow(array $row);

    abstract protected function getResourceName(): string;

    public function execute(): void
    {
        $config = new LexerConfig();
        $config->setIgnoreHeaderLine(true);
        $config->setEnclosure('\'');
        $lexer       = new Lexer($config);
        $interpreter = new Interpreter();
        $interpreter->addObserver(
            function (array $row) {
                $this->parseRow($row);
            }
        );

        $localFilePath = $this->saveCsvLocally();
        $lexer->parse($localFilePath, $interpreter);

        File::delete($localFilePath);
    }

    protected function sanitizeValue(string $value): ?string
    {
        if ($value === 'NULL' || $value === '') {
            return null;
        }

        return $value;
    }

    protected function convertUpdatedAtTimestamp(string $timestamp, ?string $createdAtTimestamp = null): ?Carbon
    {
        if ($timestamp === 'NULL' || $timestamp === '0') {
            if ( ! (null === $createdAtTimestamp)) {
                return $this->convertUpdatedAtTimestamp($createdAtTimestamp);
            }

            return null;
        }

        return Carbon::createFromTimestamp($timestamp);
    }

    protected function getUrl(string $resourceName, string $language = 'en', ?int $type = null): string
    {
        $params = [
            'api_key' => env('CAR2DB_API_KEY'),
        ];
        if (null !== $type) {
            $params['id_type'] = (string) $type;
        }

        return sprintf(
            '%s/%s.getAll.csv.%s?%s',
            env('CAR2DB_API_HOST'),
            $resourceName,
            $language,
            implode(
                '&',
                array_map(
                    function ($value, $key) {
                        return sprintf('%s=%s', $key, $value);
                    },
                    $params,
                    array_keys($params)
                )
            )
        );
    }

    private function saveCsvLocally(): string
    {
        $filePath = public_path(sprintf('%s.csv', $this->getResourceName()));

        $rh = fopen($this->getUrl($this->getResourceName(), 'en', 1), 'rb');
        $wh = fopen($filePath, 'wb');

        while ( ! feof($rh)) {
            if (fwrite($wh, fread($rh, 1024)) === false) {
                return '';
            }
        }

        fclose($rh);
        fclose($wh);

        return $filePath;
    }
}
