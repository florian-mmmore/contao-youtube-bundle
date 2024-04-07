<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein-Youtube-Bundle.
 *
 * (c) Werbeagentur Dreibein GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Dreibein\YoutubeBundle\Youtube;

use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\Database;
use Contao\Files;
use Contao\FilesModel;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\KernelInterface;

class PreviewImage
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Database
     */
    private $database;

    public function __construct(KernelInterface $kernel, LoggerInterface $logger)
    {
        $this->rootDir = $kernel->getContainer()->getParameter('kernel.project_dir');
        $this->logger = $logger;
        $this->database = Database::getInstance();
    }

    public function getImageById(string $youtubeId): ?FilesModel
    {
        try {
            $data = file_get_contents('https://img.youtube.com/vi/' . $youtubeId . '/hqdefault.jpg');
        } catch (\Exception $e) {
            return null;
        }

        if (false === $data) {
            // image could not be loaded from youtube
            return null;
        }

        $dir = 'files/video-preview';
        $fileName = 'preview-' . $youtubeId . '.jpg';

        // Check if the file is already in the file system
        $fileModel = FilesModel::findByPath($dir . '/' . $fileName);
        if (null === $fileModel) {
            $fileModel = $this->createPreviewImage($dir, $fileName, $data);

            if (null === $fileModel) {
                // file model could not be created
                return null;
            }
        }

        return $fileModel;
    }

    /**
     * Try to create the preview image in the file system and save the new entry in the database.
     *
     * @param string $dir
     * @param string $fileName
     * @param string $data
     *
     * @return FilesModel|null
     */
    private function createPreviewImage(string $dir, string $fileName, string $data): ?FilesModel
    {
        // check if the directory exists
        $dirModel = FilesModel::findByPath($dir);
        if (null === $dirModel) {
            // try to create the directory
            // if this fails, stop the function
            if (!Files::getInstance()->mkdir($dir)) {
                $this->logger->log(LogLevel::ERROR, 'Error on creating YouTube-preview-directory', ['contao' => new ContaoContext(__METHOD__, 'ERROR')]);

                return null;
            }

            $dirModel = new FilesModel();
            $dirModel->pid = null;
            $dirModel->tstamp = time();
            $dirModel->name = 'video_preview';
            $dirModel->type = 'folder';
            $dirModel->path = $dir;
            $dirModel->extension = '';
            $dirModel->hash = md5_file($this->rootDir . '/' . $dir);
            $dirModel->uuid = $this->database->getUuid();
            $dirModel = $dirModel->save();
        }

        // Create the file
        try {
            $fileResource = fopen($this->rootDir . '/' . $dir . '/' . $fileName, 'wb');
            fwrite($fileResource, $data);
            fclose($fileResource);
        } catch (\Throwable $e) {
            $this->logger->log(LogLevel::ERROR, 'Error on creating YouTube-preview-image: ' . $e->getMessage(), ['contao' => new ContaoContext(__METHOD__, 'ERROR')]);
        }

        $fileModel = new FilesModel();
        $fileModel->pid = $dirModel->id;
        $fileModel->tstamp = time();
        $fileModel->name = $fileName;
        $fileModel->type = 'file';
        $fileModel->path = $dir . '/' . $fileName;
        $fileModel->extension = 'jpg';
        $fileModel->hash = md5_file($this->rootDir . '/' . $dir . '/' . $fileName);
        $fileModel->uuid = $this->database->getUuid();

        return $fileModel->save();
    }
}
