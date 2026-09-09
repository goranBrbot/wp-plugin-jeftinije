<#
.SYNOPSIS
  Builds an installable WordPress plugin zip into dist/, containing only
  runtime files (no .git, dev tooling, vendor/, or repo-only docs).

.USAGE
  powershell -File scripts\build-zip.ps1
#>

$ErrorActionPreference = "Stop"

$root       = Split-Path -Parent $PSScriptRoot
$pluginSlug = "wp-plugin-jeftinije"
$outDir     = Join-Path $root "dist"
$outZip     = Join-Path $outDir "$pluginSlug.zip"

# Top-level paths that never belong in the installable plugin.
$excludedTop = @(
    ".git", ".gitignore", ".github",
    "vendor", "dist", "scripts",
    "composer.json", "composer.lock",
    "README.md"
)

if (-not (Test-Path $outDir)) {
    New-Item -ItemType Directory -Force -Path $outDir | Out-Null
}
if ([System.IO.File]::Exists($outZip)) {
    [System.IO.File]::Delete($outZip)
}

# Stamp the plugin header's "Datum azuriranja verzije" with today's date.
# Read/write as UTF-8 without BOM explicitly: shoppers_mind.php starts with
# "<?php" and a BOM before that would break the plugin (output before headers).
$mainFile = Join-Path $root "shoppers_mind.php"
$today = Get-Date -Format "yyyy-MM-dd"
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
$content = [System.IO.File]::ReadAllText($mainFile, [System.Text.Encoding]::UTF8)
$content = [System.Text.RegularExpressions.Regex]::Replace($content, 'verzije:\s*\d{4}-\d{2}-\d{2}', "verzije: $today")
[System.IO.File]::WriteAllText($mainFile, $content, $utf8NoBom)

$files = Get-ChildItem -Path $root -Recurse -File | Where-Object {
    $relative = $_.FullName.Substring($root.Length + 1)
    $topSegment = $relative.Split([System.IO.Path]::DirectorySeparatorChar)[0]
    -not ($excludedTop -contains $topSegment)
}

Add-Type -AssemblyName System.IO.Compression
$fs = New-Object System.IO.FileStream($outZip, [System.IO.FileMode]::Create)
$archive = New-Object System.IO.Compression.ZipArchive($fs, [System.IO.Compression.ZipArchiveMode]::Create)

foreach ($file in $files) {
    $relative = $file.FullName.Substring($root.Length + 1)
    $forwardSlashRelative = $relative.Replace([System.IO.Path]::DirectorySeparatorChar, [char]47)
    $entryName = "$pluginSlug/$forwardSlashRelative"
    $entry = $archive.CreateEntry($entryName, [System.IO.Compression.CompressionLevel]::Optimal)
    $entryStream = $entry.Open()
    $bytes = [System.IO.File]::ReadAllBytes($file.FullName)
    $entryStream.Write($bytes, 0, $bytes.Length)
    $entryStream.Close()
}

$archive.Dispose()
$fs.Dispose()

Write-Output "Built $outZip ($($files.Count) files)"
