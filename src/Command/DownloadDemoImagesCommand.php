<?php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:demo:download-images', description: 'Download ACG venue photos, fetch stock images and generate club logos for demo data.')]
final class DownloadDemoImagesCommand extends Command
{
    private string $mediaDir;
    private array $mapping;

    public function __construct(#[Autowire('%kernel.project_dir%')] private string $projectDir)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('pexels-key', null, InputOption::VALUE_REQUIRED, 'Pexels API key for stock images')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Re-download even if files exist');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->mediaDir = $this->projectDir.'/data/demo-media';
        $this->mapping = json_decode(file_get_contents($this->projectDir.'/data/deree/demo-images.json'), true, 512, JSON_THROW_ON_ERROR);
        $force = $input->getOption('force');
        $pexelsKey = $input->getOption('pexels-key');

        foreach (['venues', 'facilities', 'events', 'clubs'] as $dir) {
            $path = $this->mediaDir.'/'.$dir;
            if (!is_dir($path)) mkdir($path, 0755, true);
        }

        $output->writeln('<info>Downloading ACG venue images...</info>');
        $acgCount = $this->downloadAcgImages($output, $force);
        $output->writeln(sprintf('  %d ACG images processed.', $acgCount));

        $output->writeln('<info>Copying reused facility images...</info>');
        $facilityCount = $this->copyFacilityReuses($output, $force);
        $output->writeln(sprintf('  %d facility images processed.', $facilityCount));

        if ($pexelsKey) {
            $output->writeln('<info>Fetching Pexels stock images...</info>');
            $stockCount = $this->downloadPexelsImages($output, $pexelsKey, $force);
            $output->writeln(sprintf('  %d stock images processed.', $stockCount));
        } else {
            $output->writeln('<comment>Skipping Pexels downloads (no --pexels-key provided). Venues/events needing stock images will use fallback.</comment>');
        }

        $output->writeln('<info>Generating club logos...</info>');
        $clubCount = $this->generateClubLogos($output, $force);
        $output->writeln(sprintf('  %d club logos generated.', $clubCount));

        $output->writeln('<info>Done. Run php scripts/validate_demo_data.php to verify.</info>');
        return self::SUCCESS;
    }

    private function downloadAcgImages(OutputInterface $output, bool $force): int
    {
        $count = 0;
        foreach ($this->mapping['venues'] as $name => $entry) {
            if (($entry['source'] ?? null) !== 'acg') continue;
            $target = $this->mediaDir.'/'.$entry['file'];
            if (!$force && is_file($target)) { $count++; continue; }
            $data = @file_get_contents($entry['url']);
            if (!$data) { $output->writeln('  <comment>Failed to download: '.$name.'</comment>'); continue; }
            $resized = $this->resizeJpeg($data);
            if ($resized === null) { $output->writeln('  <comment>Could not process image: '.$name.'</comment>'); continue; }
            file_put_contents($target, $resized);
            $output->writeln('  Downloaded: '.$name);
            $count++;
        }
        return $count;
    }

    private function copyFacilityReuses(OutputInterface $output, bool $force): int
    {
        $count = 0;
        foreach ($this->mapping['facilities'] as $name => $entry) {
            if (!isset($entry['reuse'])) continue;
            $source = $this->mediaDir.'/'.$entry['reuse'];
            $target = $this->mediaDir.'/'.$entry['file'];
            if (!$force && is_file($target)) { $count++; continue; }
            if (!is_file($source)) { $output->writeln('  <comment>Source not found for '.$name.': '.$entry['reuse'].'</comment>'); continue; }
            copy($source, $target);
            $count++;
        }
        return $count;
    }

    private function downloadPexelsImages(OutputInterface $output, string $apiKey, bool $force): int
    {
        $count = 0;
        // Venue stock images
        foreach ($this->mapping['venues'] as $name => $entry) {
            if (($entry['source'] ?? null) !== 'pexels') continue;
            $target = $this->mediaDir.'/'.$entry['file'];
            if (!$force && is_file($target)) { $count++; continue; }
            $url = $this->pexelsSearch($apiKey, $entry['query']);
            if (!$url) { $output->writeln('  <comment>No Pexels result for venue: '.$name.'</comment>'); continue; }
            $data = @file_get_contents($url);
            if (!$data) continue;
            $resized = $this->resizeJpeg($data);
            if ($resized) { file_put_contents($target, $resized); $count++; $output->writeln('  Stock: '.$name); }
        }
        // Facility stock images
        foreach ($this->mapping['facilities'] as $name => $entry) {
            if (($entry['source'] ?? null) !== 'pexels') continue;
            $target = $this->mediaDir.'/'.$entry['file'];
            if (!$force && is_file($target)) { $count++; continue; }
            $url = $this->pexelsSearch($apiKey, $entry['query']);
            if (!$url) { $output->writeln('  <comment>No Pexels result for facility: '.$name.'</comment>'); continue; }
            $data = @file_get_contents($url);
            if (!$data) continue;
            $resized = $this->resizeJpeg($data);
            if ($resized) { file_put_contents($target, $resized); $count++; $output->writeln('  Stock: '.$name); }
        }
        // Event category images
        foreach ($this->mapping['event_categories'] as $category => $images) {
            foreach ($images as $i => $entry) {
                $target = $this->mediaDir.'/'.$entry['file'];
                if (!$force && is_file($target)) { $count++; continue; }
                $url = $this->pexelsSearch($apiKey, $entry['query'], $i);
                if (!$url) { $output->writeln('  <comment>No Pexels result for event: '.$category.' #'.($i+1).'</comment>'); continue; }
                $data = @file_get_contents($url);
                if (!$data) continue;
                $resized = $this->resizeJpeg($data);
                if ($resized) { file_put_contents($target, $resized); $count++; $output->writeln('  Stock: '.$category.' #'.($i+1)); }
            }
        }
        return $count;
    }

    private function pexelsSearch(string $apiKey, string $query, int $offset = 0): ?string
    {
        $context = stream_context_create(['http' => ['header' => 'Authorization: '.$apiKey, 'timeout' => 15]]);
        $url = 'https://api.pexels.com/v1/search?query='.urlencode($query).'&per_page=3&page=1&orientation=landscape';
        $response = @file_get_contents($url, false, $context);
        if (!$response) return null;
        $data = json_decode($response, true);
        $photo = $data['photos'][$offset] ?? $data['photos'][0] ?? null;
        return $photo['src']['large'] ?? null;
    }

    private function generateClubLogos(OutputInterface $output, bool $force): int
    {
        $directory = json_decode(file_get_contents($this->projectDir.'/data/deree/directory.json'), true, 512, JSON_THROW_ON_ERROR);
        $colorMap = $this->mapping['club_colors'];
        $categories = ['Academic', 'Arts & Culture', 'Community', 'Career', 'Sports', 'Technology', 'Sustainability'];
        $count = 0;
        $orgIndex = 0;

        foreach ($directory as $row) {
            if (!in_array($row['kind'], ['club', 'society', 'organization'])) continue;
            $slug = $this->clubSlug($row['name']);
            $target = $this->mediaDir.'/clubs/'.$slug.'.png';
            if (!$force && is_file($target)) { $count++; $orgIndex++; continue; }

            $initials = $this->extractInitials($row['name']);
            $category = $categories[$orgIndex % count($categories)];

            if ($row['kind'] === 'society') {
                $bgColor = $colorMap['society'];
            } elseif ($row['kind'] === 'organization') {
                $bgColor = $colorMap['organization'];
            } else {
                $bgColor = $colorMap['club']['categories'][$category] ?? $colorMap['club']['default'];
            }

            $this->generateLogo($target, $initials, $bgColor);
            $count++;
            $orgIndex++;
        }
        return $count;
    }

    private function generateLogo(string $path, string $initials, string $hexColor): void
    {
        $size = 400;
        $image = imagecreatetruecolor($size, $size);
        [$r, $g, $b] = sscanf($hexColor, '#%02x%02x%02x');
        $bg = imagecolorallocate($image, $r, $g, $b);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bg);

        // Draw a subtle lighter circle in the center
        $lighter = imagecolorallocate($image, min(255, $r + 25), min(255, $g + 25), min(255, $b + 25));
        imagefilledellipse($image, $size / 2, $size / 2, $size - 60, $size - 60, $lighter);

        // Draw initials centered using built-in font
        $fontSize = 5; // largest built-in font
        $charWidth = imagefontwidth($fontSize);
        $charHeight = imagefontheight($fontSize);
        $textWidth = $charWidth * strlen($initials);

        // Scale up: draw at 2x then we keep the 400px size
        $x = (int)(($size - $textWidth * 3) / 2);
        $y = (int)(($size - $charHeight * 3) / 2);

        // Draw each character scaled 3x for visibility
        for ($i = 0; $i < strlen($initials); $i++) {
            $char = $initials[$i];
            $charImg = imagecreatetruecolor($charWidth, $charHeight);
            imagefill($charImg, 0, 0, imagecolorallocate($charImg, min(255, $r + 25), min(255, $g + 25), min(255, $b + 25)));
            imagechar($charImg, $fontSize, 0, 0, $char, imagecolorallocate($charImg, 255, 255, 255));
            $scaled = imagecreatetruecolor($charWidth * 3, $charHeight * 3);
            imagecopyresized($scaled, $charImg, 0, 0, 0, 0, $charWidth * 3, $charHeight * 3, $charWidth, $charHeight);
            imagecopy($image, $scaled, $x + $i * $charWidth * 3, $y, 0, 0, $charWidth * 3, $charHeight * 3);
            imagedestroy($charImg);
            imagedestroy($scaled);
        }

        imagepng($image, $path, 6);
        imagedestroy($image);
    }

    private function extractInitials(string $name): string
    {
        // Remove common suffixes and prefixes
        $clean = preg_replace('/\b(Club|Society|Organization|Team)\b/i', '', $name);
        $clean = preg_replace('/[^a-zA-Z\s]/', '', $clean);
        $words = preg_split('/\s+/', trim($clean));
        // Skip small words
        $significant = array_filter($words, fn($w) => strlen($w) > 2 || strtoupper($w) === $w);
        if (!$significant) $significant = $words;
        $initials = '';
        foreach (array_values($significant) as $word) {
            $initials .= strtoupper($word[0]);
            if (strlen($initials) >= 3) break;
        }
        return $initials ?: strtoupper(substr($name, 0, 2));
    }

    private function clubSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }

    private function resizeJpeg(string $data, int $maxWidth = 1200, int $quality = 75): ?string
    {
        $source = @imagecreatefromstring($data);
        if (!$source) return null;
        $w = imagesx($source);
        $h = imagesy($source);
        if ($w > $maxWidth) {
            $newW = $maxWidth;
            $newH = (int)round($h * ($maxWidth / $w));
            $resized = imagecreatetruecolor($newW, $newH);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $w, $h);
            imagedestroy($source);
            $source = $resized;
        }
        ob_start();
        imagejpeg($source, null, $quality);
        $result = ob_get_clean();
        imagedestroy($source);
        return $result;
    }
}
